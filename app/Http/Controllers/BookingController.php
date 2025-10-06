<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Flight;
use App\Models\Passenger;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class BookingController extends Controller
{
    /**
     * Display a listing of bookings
     */
    public function index(Request $request): JsonResponse
    {
        $query = Booking::with(['passenger', 'flight.airline', 'flight.route']);
        
        // Search functionality
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('booking_reference', 'like', "%{$search}%")
                  ->orWhereHas('passenger', function ($passenger) use ($search) {
                      $passenger->where('first_name', 'like', "%{$search}%")
                               ->orWhere('last_name', 'like', "%{$search}%")
                               ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }
        
        // Filter by booking status
        if ($request->has('status')) {
            $query->where('booking_status', $request->input('status'));
        }
        
        // Filter by booking class
        if ($request->has('class')) {
            $query->where('booking_class', $request->input('class'));
        }
        
        // Filter by payment status
        if ($request->has('payment_status')) {
            $query->where('payment_status', $request->input('payment_status'));
        }
        
        // Filter by date range
        if ($request->has('from_date')) {
            $query->where('booking_date', '>=', $request->input('from_date'));
        }
        
        if ($request->has('to_date')) {
            $query->where('booking_date', '<=', $request->input('to_date'));
        }
        
        $bookings = $query->paginate(20);
        
        return response()->json($bookings);
    }

    /**
     * Store a newly created booking
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'passenger_id' => 'required|exists:passengers,id',
            'flight_id' => 'required|exists:flights,id',
            'booking_class' => 'required|in:economy,business,first',
            'total_amount' => 'required|numeric|min:0',
            'payment_method' => 'nullable|string|max:50',
            'special_requests' => 'nullable|string',
            'travel_insurance' => 'boolean',
            'booked_by_email' => 'required|email',
        ]);

        // Check flight availability
        $flight = Flight::findOrFail($validated['flight_id']);
        if ($flight->available_seats <= 0) {
            return response()->json([
                'message' => 'No available seats on this flight',
                'errors' => ['flight_id' => ['Flight is fully booked']]
            ], 422);
        }

        // Generate unique booking reference
        $validated['booking_reference'] = $this->generateBookingReference();
        $validated['booking_date'] = now();

        $booking = Booking::create($validated);

        // Decrease available seats
        $flight->decrement('available_seats');

        return response()->json([
            'message' => 'Booking created successfully',
            'data' => $booking->load(['passenger', 'flight.airline'])
        ], 201);
    }

    /**
     * Display the specified booking
     */
    public function show(Booking $booking): JsonResponse
    {
        return response()->json($booking->load([
            'passenger', 
            'flight.airline', 
            'flight.aircraft',
            'flight.route.departureAirport', 
            'flight.route.arrivalAirport',
            'seatAssignments.seat',
            'payments'
        ]));
    }

    /**
     * Update the specified booking
     */
    public function update(Request $request, Booking $booking): JsonResponse
    {
        $validated = $request->validate([
            'booking_status' => 'sometimes|in:confirmed,pending,cancelled,refunded',
            'booking_class' => 'sometimes|in:economy,business,first',
            'total_amount' => 'sometimes|numeric|min:0',
            'payment_status' => 'sometimes|string|max:50',
            'payment_method' => 'sometimes|nullable|string|max:50',
            'special_requests' => 'sometimes|nullable|string',
            'travel_insurance' => 'sometimes|boolean',
        ]);

        // If cancelling booking, increase available seats
        if (isset($validated['booking_status']) && 
            $validated['booking_status'] === 'cancelled' && 
            $booking->booking_status !== 'cancelled') {
            $booking->flight->increment('available_seats');
        }

        $booking->update($validated);

        return response()->json([
            'message' => 'Booking updated successfully',
            'data' => $booking->load(['passenger', 'flight'])
        ]);
    }

    /**
     * Remove the specified booking
     */
    public function destroy(Booking $booking): JsonResponse
    {
        // Increase available seats if booking wasn't already cancelled
        if ($booking->booking_status !== 'cancelled') {
            $booking->flight->increment('available_seats');
        }

        $booking->delete();

        return response()->json([
            'message' => 'Booking deleted successfully'
        ]);
    }

    /**
     * Find booking by reference number
     */
    public function findByReference(string $reference): JsonResponse
    {
        $booking = Booking::where('booking_reference', strtoupper($reference))
            ->with([
                'passenger', 
                'flight.airline', 
                'flight.route.departureAirport', 
                'flight.route.arrivalAirport'
            ])
            ->first();

        if (!$booking) {
            return response()->json([
                'message' => 'Booking not found with the provided reference'
            ], 404);
        }

        return response()->json($booking);
    }

    /**
     * Confirm booking payment
     */
    public function confirmPayment(Request $request, Booking $booking): JsonResponse
    {
        $validated = $request->validate([
            'payment_method' => 'required|string|max:50',
            'transaction_id' => 'nullable|string|max:100',
        ]);

        $booking->update([
            'payment_status' => 'completed',
            'payment_method' => $validated['payment_method'],
            'booking_status' => 'confirmed'
        ]);

        // Create payment record
        $booking->payments()->create([
            'amount' => $booking->total_amount,
            'payment_method' => $validated['payment_method'],
            'transaction_id' => $validated['transaction_id'] ?? null,
            'payment_status' => 'completed',
            'payment_date' => now(),
        ]);

        return response()->json([
            'message' => 'Payment confirmed successfully',
            'data' => $booking
        ]);
    }

    /**
     * Cancel booking
     */
    public function cancel(Booking $booking): JsonResponse
    {
        if ($booking->booking_status === 'cancelled') {
            return response()->json([
                'message' => 'Booking is already cancelled'
            ], 400);
        }

        $booking->update([
            'booking_status' => 'cancelled',
            'payment_status' => 'refunded'
        ]);

        // Increase available seats
        $booking->flight->increment('available_seats');

        return response()->json([
            'message' => 'Booking cancelled successfully',
            'data' => $booking
        ]);
    }

    /**
     * Get bookings for a specific flight
     */
    public function byFlight(Flight $flight): JsonResponse
    {
        $bookings = $flight->bookings()
            ->with(['passenger', 'seatAssignments.seat'])
            ->get();

        return response()->json($bookings);
    }

    /**
     * Get bookings for a specific passenger
     */
    public function byPassenger(Passenger $passenger): JsonResponse
    {
        $bookings = $passenger->bookings()
            ->with(['flight.airline', 'flight.route'])
            ->orderBy('booking_date', 'desc')
            ->get();

        return response()->json($bookings);
    }

    /**
     * Generate unique booking reference
     */
    private function generateBookingReference(): string
    {
        do {
            $reference = strtoupper(Str::random(6));
        } while (Booking::where('booking_reference', $reference)->exists());

        return $reference;
    }

    /**
     * Get booking statistics
     */
    public function statistics(): JsonResponse
    {
        $stats = [
            'total_bookings' => Booking::count(),
            'confirmed_bookings' => Booking::where('booking_status', 'confirmed')->count(),
            'pending_bookings' => Booking::where('booking_status', 'pending')->count(),
            'cancelled_bookings' => Booking::where('booking_status', 'cancelled')->count(),
            'total_revenue' => Booking::where('payment_status', 'completed')->sum('total_amount'),
            'bookings_today' => Booking::whereDate('booking_date', today())->count(),
            'bookings_this_month' => Booking::whereMonth('booking_date', now()->month)->count(),
        ];

        return response()->json($stats);
    }
}