<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PaymentController extends Controller
{
    /**
     * Display a listing of payments
     */
    public function index(Request $request): JsonResponse
    {
        $query = Payment::with(['booking.passenger', 'booking.flight']);
        
        // Filter by payment status
        if ($request->has('status')) {
            $query->where('payment_status', $request->input('status'));
        }
        
        // Filter by payment method
        if ($request->has('payment_method')) {
            $query->where('payment_method', $request->input('payment_method'));
        }
        
        // Filter by date range
        if ($request->has('from_date')) {
            $query->where('payment_date', '>=', $request->input('from_date'));
        }
        
        if ($request->has('to_date')) {
            $query->where('payment_date', '<=', $request->input('to_date'));
        }
        
        // Search by transaction ID
        if ($request->has('transaction_id')) {
            $query->where('transaction_id', 'like', '%' . $request->input('transaction_id') . '%');
        }
        
        $payments = $query->orderBy('payment_date', 'desc')->paginate(20);
        
        return response()->json($payments);
    }

    /**
     * Store a newly created payment
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'booking_id' => 'required|exists:bookings,id',
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|string|max:50',
            'transaction_id' => 'nullable|string|max:100',
            'payment_status' => 'required|in:pending,completed,failed,refunded',
            'payment_date' => 'nullable|date',
        ]);

        $validated['payment_date'] = $validated['payment_date'] ?? now();

        $payment = Payment::create($validated);

        // Update booking payment status if payment is completed
        if ($validated['payment_status'] === 'completed') {
            $booking = Booking::find($validated['booking_id']);
            $booking->update([
                'payment_status' => 'completed',
                'booking_status' => 'confirmed'
            ]);
        }

        return response()->json([
            'message' => 'Payment created successfully',
            'data' => $payment->load('booking')
        ], 201);
    }

    /**
     * Display the specified payment
     */
    public function show(Payment $payment): JsonResponse
    {
        return response()->json($payment->load([
            'booking.passenger',
            'booking.flight.airline'
        ]));
    }

    /**
     * Update the specified payment
     */
    public function update(Request $request, Payment $payment): JsonResponse
    {
        $validated = $request->validate([
            'amount' => 'sometimes|numeric|min:0.01',
            'payment_method' => 'sometimes|string|max:50',
            'transaction_id' => 'sometimes|nullable|string|max:100',
            'payment_status' => 'sometimes|in:pending,completed,failed,refunded',
            'payment_date' => 'sometimes|nullable|date',
        ]);

        $payment->update($validated);

        // Update booking status based on payment status
        if (isset($validated['payment_status'])) {
            $booking = $payment->booking;
            switch ($validated['payment_status']) {
                case 'completed':
                    $booking->update(['payment_status' => 'completed', 'booking_status' => 'confirmed']);
                    break;
                case 'failed':
                    $booking->update(['payment_status' => 'failed']);
                    break;
                case 'refunded':
                    $booking->update(['payment_status' => 'refunded', 'booking_status' => 'refunded']);
                    break;
            }
        }

        return response()->json([
            'message' => 'Payment updated successfully',
            'data' => $payment->load('booking')
        ]);
    }

    /**
     * Remove the specified payment
     */
    public function destroy(Payment $payment): JsonResponse
    {
        $payment->delete();

        return response()->json([
            'message' => 'Payment deleted successfully'
        ]);
    }

    /**
     * Process payment for a booking
     */
    public function processPayment(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'booking_id' => 'required|exists:bookings,id',
            'payment_method' => 'required|string|max:50',
            'card_number' => 'required_if:payment_method,credit_card,debit_card|nullable|string',
            'card_expiry' => 'required_if:payment_method,credit_card,debit_card|nullable|string',
            'card_cvv' => 'required_if:payment_method,credit_card,debit_card|nullable|string',
        ]);

        $booking = Booking::findOrFail($validated['booking_id']);

        // Check if booking is already paid
        if ($booking->payment_status === 'completed') {
            return response()->json([
                'message' => 'Booking is already paid'
            ], 400);
        }

        // Simulate payment processing
        $paymentStatus = $this->simulatePaymentProcessing($validated);
        
        $payment = Payment::create([
            'booking_id' => $validated['booking_id'],
            'amount' => $booking->total_amount,
            'payment_method' => $validated['payment_method'],
            'transaction_id' => 'TXN_' . strtoupper(uniqid()),
            'payment_status' => $paymentStatus,
            'payment_date' => now(),
        ]);

        // Update booking status
        if ($paymentStatus === 'completed') {
            $booking->update([
                'payment_status' => 'completed',
                'booking_status' => 'confirmed'
            ]);
        } else {
            $booking->update(['payment_status' => 'failed']);
        }

        return response()->json([
            'message' => 'Payment processed',
            'data' => [
                'payment' => $payment,
                'booking' => $booking,
                'status' => $paymentStatus
            ]
        ]);
    }

    /**
     * Refund a payment
     */
    public function refund(Payment $payment): JsonResponse
    {
        if ($payment->payment_status !== 'completed') {
            return response()->json([
                'message' => 'Only completed payments can be refunded'
            ], 400);
        }

        $payment->update(['payment_status' => 'refunded']);
        
        $payment->booking->update([
            'payment_status' => 'refunded',
            'booking_status' => 'cancelled'
        ]);

        // Increase available seats
        $payment->booking->flight->increment('available_seats');

        return response()->json([
            'message' => 'Payment refunded successfully',
            'data' => $payment
        ]);
    }

    /**
     * Get payments by booking
     */
    public function byBooking(Booking $booking): JsonResponse
    {
        $payments = $booking->payments()
            ->orderBy('payment_date', 'desc')
            ->get();

        return response()->json($payments);
    }

    /**
     * Get payment statistics
     */
    public function statistics(): JsonResponse
    {
        $stats = [
            'total_payments' => Payment::count(),
            'completed_payments' => Payment::where('payment_status', 'completed')->count(),
            'pending_payments' => Payment::where('payment_status', 'pending')->count(),
            'failed_payments' => Payment::where('payment_status', 'failed')->count(),
            'refunded_payments' => Payment::where('payment_status', 'refunded')->count(),
            'total_revenue' => Payment::where('payment_status', 'completed')->sum('amount'),
            'revenue_today' => Payment::where('payment_status', 'completed')
                ->whereDate('payment_date', today())->sum('amount'),
            'revenue_this_month' => Payment::where('payment_status', 'completed')
                ->whereMonth('payment_date', now()->month)->sum('amount'),
        ];

        return response()->json($stats);
    }

    /**
     * Get payments by method
     */
    public function byMethod(): JsonResponse
    {
        $paymentsByMethod = Payment::where('payment_status', 'completed')
            ->selectRaw('payment_method, COUNT(*) as count, SUM(amount) as total_amount')
            ->groupBy('payment_method')
            ->get();

        return response()->json($paymentsByMethod);
    }

    /**
     * Simulate payment processing
     */
    private function simulatePaymentProcessing(array $paymentData): string
    {
        // Simulate payment processing logic
        // In real implementation, this would integrate with payment gateways
        
        // 90% success rate for simulation
        return rand(1, 10) <= 9 ? 'completed' : 'failed';
    }
}