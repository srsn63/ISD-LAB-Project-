<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class NotificationController extends Controller
{
    /**
     * Display a listing of notifications
     */
    public function index(Request $request): JsonResponse
    {
        $query = Notification::query();
        
        // Filter by recipient
        if ($request->has('recipient_id')) {
            $query->where('recipient_id', $request->input('recipient_id'));
        }
        
        // Filter by recipient type
        if ($request->has('recipient_type')) {
            $query->where('recipient_type', $request->input('recipient_type'));
        }
        
        // Filter by notification type
        if ($request->has('type')) {
            $query->where('type', $request->input('type'));
        }
        
        // Filter by read status
        if ($request->has('is_read')) {
            $query->where('is_read', $request->boolean('is_read'));
        }
        
        // Filter by date range
        if ($request->has('from_date')) {
            $query->where('created_at', '>=', $request->input('from_date'));
        }
        
        if ($request->has('to_date')) {
            $query->where('created_at', '<=', $request->input('to_date'));
        }
        
        $notifications = $query->orderBy('created_at', 'desc')->paginate(20);
        
        return response()->json($notifications);
    }

    /**
     * Store a newly created notification
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'recipient_id' => 'required|integer',
            'recipient_type' => 'required|string|max:50',
            'type' => 'required|string|max:50',
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'data' => 'nullable|json',
            'scheduled_at' => 'nullable|date|after:now',
        ]);

        $notification = Notification::create($validated);

        return response()->json([
            'message' => 'Notification created successfully',
            'data' => $notification
        ], 201);
    }

    /**
     * Display the specified notification
     */
    public function show(Notification $notification): JsonResponse
    {
        return response()->json($notification);
    }

    /**
     * Update the specified notification
     */
    public function update(Request $request, Notification $notification): JsonResponse
    {
        $validated = $request->validate([
            'recipient_id' => 'sometimes|integer',
            'recipient_type' => 'sometimes|string|max:50',
            'type' => 'sometimes|string|max:50',
            'title' => 'sometimes|string|max:255',
            'message' => 'sometimes|string',
            'data' => 'sometimes|nullable|json',
            'is_read' => 'sometimes|boolean',
            'read_at' => 'sometimes|nullable|date',
            'scheduled_at' => 'sometimes|nullable|date',
        ]);

        $notification->update($validated);

        return response()->json([
            'message' => 'Notification updated successfully',
            'data' => $notification
        ]);
    }

    /**
     * Remove the specified notification
     */
    public function destroy(Notification $notification): JsonResponse
    {
        $notification->delete();

        return response()->json([
            'message' => 'Notification deleted successfully'
        ]);
    }

    /**
     * Mark notification as read
     */
    public function markAsRead(Notification $notification): JsonResponse
    {
        $notification->update([
            'is_read' => true,
            'read_at' => now()
        ]);

        return response()->json([
            'message' => 'Notification marked as read',
            'data' => $notification
        ]);
    }

    /**
     * Mark all notifications as read for a recipient
     */
    public function markAllAsRead(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'recipient_id' => 'required|integer',
            'recipient_type' => 'required|string|max:50',
        ]);

        $updated = Notification::where('recipient_id', $validated['recipient_id'])
            ->where('recipient_type', $validated['recipient_type'])
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now()
            ]);

        return response()->json([
            'message' => "Marked {$updated} notifications as read"
        ]);
    }

    /**
     * Get unread notifications for a recipient
     */
    public function unread(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'recipient_id' => 'required|integer',
            'recipient_type' => 'required|string|max:50',
        ]);

        $notifications = Notification::where('recipient_id', $validated['recipient_id'])
            ->where('recipient_type', $validated['recipient_type'])
            ->where('is_read', false)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($notifications);
    }

    /**
     * Send flight status notification
     */
    public function sendFlightStatusNotification(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'flight_id' => 'required|exists:flights,id',
            'status' => 'required|string',
            'message' => 'required|string',
        ]);

        $flight = \App\Models\Flight::with(['bookings.passenger'])->findOrFail($validated['flight_id']);
        $notifications = [];

        foreach ($flight->bookings as $booking) {
            $notification = Notification::create([
                'recipient_id' => $booking->passenger->id,
                'recipient_type' => 'passenger',
                'type' => 'flight_status_update',
                'title' => "Flight {$flight->flight_number} Status Update",
                'message' => $validated['message'],
                'data' => json_encode([
                    'flight_id' => $flight->id,
                    'flight_number' => $flight->flight_number,
                    'status' => $validated['status'],
                    'booking_reference' => $booking->booking_reference
                ])
            ]);
            
            $notifications[] = $notification;
        }

        return response()->json([
            'message' => 'Flight status notifications sent',
            'count' => count($notifications),
            'data' => $notifications
        ]);
    }

    /**
     * Send booking confirmation notification
     */
    public function sendBookingConfirmation(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'booking_id' => 'required|exists:bookings,id',
        ]);

        $booking = \App\Models\Booking::with(['passenger', 'flight'])->findOrFail($validated['booking_id']);

        $notification = Notification::create([
            'recipient_id' => $booking->passenger->id,
            'recipient_type' => 'passenger',
            'type' => 'booking_confirmation',
            'title' => 'Booking Confirmed',
            'message' => "Your booking {$booking->booking_reference} for flight {$booking->flight->flight_number} has been confirmed.",
            'data' => json_encode([
                'booking_id' => $booking->id,
                'booking_reference' => $booking->booking_reference,
                'flight_number' => $booking->flight->flight_number,
                'flight_date' => $booking->flight->flight_date
            ])
        ]);

        return response()->json([
            'message' => 'Booking confirmation notification sent',
            'data' => $notification
        ]);
    }

    /**
     * Get notification statistics
     */
    public function statistics(): JsonResponse
    {
        $stats = [
            'total_notifications' => Notification::count(),
            'unread_notifications' => Notification::where('is_read', false)->count(),
            'read_notifications' => Notification::where('is_read', true)->count(),
            'by_type' => Notification::selectRaw('type, COUNT(*) as count')
                ->groupBy('type')
                ->get(),
            'by_recipient_type' => Notification::selectRaw('recipient_type, COUNT(*) as count')
                ->groupBy('recipient_type')
                ->get(),
            'notifications_today' => Notification::whereDate('created_at', today())->count(),
            'notifications_this_week' => Notification::where('created_at', '>=', now()->startOfWeek())->count(),
        ];

        return response()->json($stats);
    }

    /**
     * Bulk delete old notifications
     */
    public function cleanupOldNotifications(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'days_old' => 'integer|min:1|max:365'
        ]);

        $daysOld = $validated['days_old'] ?? 30;
        
        $deleted = Notification::where('created_at', '<', now()->subDays($daysOld))
            ->where('is_read', true)
            ->delete();

        return response()->json([
            'message' => "Deleted {$deleted} old notifications (older than {$daysOld} days)"
        ]);
    }
}