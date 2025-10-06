<?php

namespace App\Http\Controllers;

use App\Models\Passenger;
use App\Models\Airline;
use App\Models\Aircraft;
use App\Models\Airport;
use App\Models\Flight;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\CrewMember;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Get dashboard overview statistics
     */
    public function overview(): JsonResponse
    {
        $stats = [
            // Passenger Statistics
            'passengers' => [
                'total' => Passenger::count(),
                'frequent_flyers' => Passenger::where('frequent_flyer', true)->count(),
                'new_this_month' => Passenger::whereMonth('created_at', now()->month)->count(),
            ],

            // Flight Statistics
            'flights' => [
                'total_today' => Flight::whereDate('flight_date', today())->count(),
                'departed_today' => Flight::whereDate('flight_date', today())
                    ->where('status', 'departed')->count(),
                'delayed_today' => Flight::whereDate('flight_date', today())
                    ->where('status', 'delayed')->count(),
                'cancelled_today' => Flight::whereDate('flight_date', today())
                    ->where('status', 'cancelled')->count(),
            ],

            // Booking Statistics
            'bookings' => [
                'total' => Booking::count(),
                'confirmed' => Booking::where('booking_status', 'confirmed')->count(),
                'pending' => Booking::where('booking_status', 'pending')->count(),
                'today' => Booking::whereDate('booking_date', today())->count(),
            ],

            // Revenue Statistics
            'revenue' => [
                'total' => Payment::where('payment_status', 'completed')->sum('amount'),
                'today' => Payment::where('payment_status', 'completed')
                    ->whereDate('payment_date', today())->sum('amount'),
                'this_month' => Payment::where('payment_status', 'completed')
                    ->whereMonth('payment_date', now()->month)->sum('amount'),
            ],

            // Fleet Statistics  
            'fleet' => [
                'total_aircraft' => Aircraft::count(),
                'active_aircraft' => Aircraft::where('status', 'active')->count(),
                'in_maintenance' => Aircraft::where('status', 'maintenance')->count(),
                'maintenance_due_soon' => Aircraft::where('next_maintenance_date', '<=', now()->addDays(30))
                    ->where('next_maintenance_date', '>', now())->count(),
            ],

            // Airline Statistics
            'airlines' => [
                'total' => Airline::count(),
                'active' => Airline::where('active', true)->count(),
            ],

            // Airport Statistics
            'airports' => [
                'total' => Airport::count(),
                'international' => Airport::where('international', true)->count(),
                'active' => Airport::where('active', true)->count(),
            ],

            // Crew Statistics
            'crew' => [
                'total' => CrewMember::count(),
                'active' => CrewMember::where('is_active', true)->count(),
                'licenses_expiring' => CrewMember::whereNotNull('license_expiry')
                    ->where('license_expiry', '<=', now()->addMonths(3))
                    ->where('license_expiry', '>', now())->count(),
            ],

            // Notification Statistics
            'notifications' => [
                'total' => Notification::count(),
                'unread' => Notification::where('is_read', false)->count(),
                'today' => Notification::whereDate('created_at', today())->count(),
            ],
        ];

        return response()->json([
            'message' => 'Dashboard overview statistics',
            'data' => $stats,
            'generated_at' => now()
        ]);
    }

    /**
     * Get recent activities
     */
    public function recentActivities(): JsonResponse
    {
        $activities = [
            'recent_bookings' => Booking::with(['passenger', 'flight.airline'])
                ->orderBy('booking_date', 'desc')
                ->limit(10)
                ->get(),

            'recent_flights' => Flight::with(['airline', 'route.departureAirport', 'route.arrivalAirport'])
                ->where('flight_date', '>=', today())
                ->orderBy('flight_date')
                ->orderBy('scheduled_departure')
                ->limit(10)
                ->get(),

            'recent_payments' => Payment::with(['booking.passenger'])
                ->where('payment_status', 'completed')
                ->orderBy('payment_date', 'desc')
                ->limit(10)
                ->get(),

            'recent_notifications' => Notification::orderBy('created_at', 'desc')
                ->limit(10)
                ->get(),
        ];

        return response()->json($activities);
    }

    /**
     * Get flight status board
     */
    public function flightBoard(): JsonResponse
    {
        $flights = Flight::with(['airline', 'route.departureAirport', 'route.arrivalAirport'])
            ->whereDate('flight_date', today())
            ->orderBy('scheduled_departure')
            ->get()
            ->groupBy('status');

        return response()->json([
            'date' => today()->format('Y-m-d'),
            'flights_by_status' => $flights
        ]);
    }

    /**
     * Get system alerts
     */
    public function systemAlerts(): JsonResponse
    {
        $alerts = [];

        // Check for maintenance due soon
        $maintenanceDue = Aircraft::where('next_maintenance_date', '<=', now()->addDays(7))
            ->where('next_maintenance_date', '>', now())
            ->count();

        if ($maintenanceDue > 0) {
            $alerts[] = [
                'type' => 'maintenance',
                'severity' => 'warning',
                'message' => "{$maintenanceDue} aircraft need maintenance within 7 days",
                'count' => $maintenanceDue
            ];
        }

        // Check for license expiry
        $licensesExpiring = CrewMember::whereNotNull('license_expiry')
            ->where('license_expiry', '<=', now()->addDays(30))
            ->where('license_expiry', '>', now())
            ->count();

        if ($licensesExpiring > 0) {
            $alerts[] = [
                'type' => 'license_expiry',
                'severity' => 'warning',
                'message' => "{$licensesExpiring} crew member licenses expire within 30 days",
                'count' => $licensesExpiring
            ];
        }

        // Check for delayed flights today
        $delayedFlights = Flight::whereDate('flight_date', today())
            ->where('status', 'delayed')
            ->count();

        if ($delayedFlights > 0) {
            $alerts[] = [
                'type' => 'flight_delays',
                'severity' => 'info',
                'message' => "{$delayedFlights} flights are delayed today",
                'count' => $delayedFlights
            ];
        }

        // Check for pending payments
        $pendingPayments = Payment::where('payment_status', 'pending')
            ->where('created_at', '<=', now()->subHours(24))
            ->count();

        if ($pendingPayments > 0) {
            $alerts[] = [
                'type' => 'pending_payments',
                'severity' => 'warning',
                'message' => "{$pendingPayments} payments pending for over 24 hours",
                'count' => $pendingPayments
            ];
        }

        return response()->json([
            'alerts' => $alerts,
            'total_alerts' => count($alerts)
        ]);
    }

    /**
     * Get performance metrics
     */
    public function performanceMetrics(): JsonResponse
    {
        $metrics = [
            'on_time_performance' => [
                'total_flights_today' => Flight::whereDate('flight_date', today())->count(),
                'on_time_flights' => Flight::whereDate('flight_date', today())
                    ->whereIn('status', ['departed', 'landed'])
                    ->whereRaw('actual_departure <= scheduled_departure')
                    ->count(),
                'delayed_flights' => Flight::whereDate('flight_date', today())
                    ->where('status', 'delayed')
                    ->count(),
            ],

            'booking_conversion' => [
                'total_bookings_today' => Booking::whereDate('booking_date', today())->count(),
                'confirmed_bookings_today' => Booking::whereDate('booking_date', today())
                    ->where('booking_status', 'confirmed')->count(),
                'cancelled_bookings_today' => Booking::whereDate('booking_date', today())
                    ->where('booking_status', 'cancelled')->count(),
            ],

            'payment_success_rate' => [
                'total_payments_today' => Payment::whereDate('created_at', today())->count(),
                'successful_payments_today' => Payment::whereDate('created_at', today())
                    ->where('payment_status', 'completed')->count(),
                'failed_payments_today' => Payment::whereDate('created_at', today())
                    ->where('payment_status', 'failed')->count(),
            ],

            'fleet_utilization' => [
                'total_aircraft' => Aircraft::where('status', 'active')->count(),
                'aircraft_in_use_today' => Aircraft::whereHas('flights', function ($query) {
                    $query->whereDate('flight_date', today());
                })->count(),
            ],
        ];

        // Calculate percentages
        if ($metrics['on_time_performance']['total_flights_today'] > 0) {
            $metrics['on_time_performance']['on_time_percentage'] = round(
                ($metrics['on_time_performance']['on_time_flights'] / 
                 $metrics['on_time_performance']['total_flights_today']) * 100, 2
            );
        }

        if ($metrics['booking_conversion']['total_bookings_today'] > 0) {
            $metrics['booking_conversion']['conversion_rate'] = round(
                ($metrics['booking_conversion']['confirmed_bookings_today'] / 
                 $metrics['booking_conversion']['total_bookings_today']) * 100, 2
            );
        }

        if ($metrics['payment_success_rate']['total_payments_today'] > 0) {
            $metrics['payment_success_rate']['success_rate'] = round(
                ($metrics['payment_success_rate']['successful_payments_today'] / 
                 $metrics['payment_success_rate']['total_payments_today']) * 100, 2
            );
        }

        if ($metrics['fleet_utilization']['total_aircraft'] > 0) {
            $metrics['fleet_utilization']['utilization_rate'] = round(
                ($metrics['fleet_utilization']['aircraft_in_use_today'] / 
                 $metrics['fleet_utilization']['total_aircraft']) * 100, 2
            );
        }

        return response()->json($metrics);
    }
}