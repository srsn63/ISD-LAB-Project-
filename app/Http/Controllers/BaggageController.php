<?php

namespace App\Http\Controllers;

use App\Models\Baggage;
use App\Models\Booking;
use App\Models\Flight;
use Illuminate\Http\Request;

class BaggageController extends Controller
{
    /**
     * Show baggage tracking page and handle GET search by tag or email.
     */
    public function index(Request $request)
    {
    $tag = trim((string) $request->query('tag', ''));
    $email = trim((string) $request->query('email', ''));
    $flightNumber = strtoupper(trim((string) $request->query('flight', '')));

        $bags = collect();
        $queryUsed = [];

        if ($flightNumber !== '') {
            $queryUsed['flight'] = $flightNumber;
            // Prefer latest flight with this number
            $flight = Flight::where('flight_number', $flightNumber)
                ->orderBy('flight_date', 'desc')
                ->first();
            if ($flight) {
                $bags = Baggage::with([
                        'booking.passenger',
                        'booking.flight.airline',
                        'booking.flight.route.departureAirport',
                        'booking.flight.route.arrivalAirport',
                        'trackingRecords' => function ($q) { $q->orderBy('scan_time'); },
                    ])
                    ->whereHas('booking', function ($q) use ($flight) {
                        $q->where('flight_id', $flight->id);
                    })
                    ->orderBy('id', 'desc')
                    ->get();
            }
        } elseif ($tag !== '') {
            $queryUsed['tag'] = $tag;
            $bags = Baggage::with([
                    'booking.passenger',
                    'booking.flight.airline',
                    'booking.flight.route.departureAirport',
                    'booking.flight.route.arrivalAirport',
                    'trackingRecords' => function ($q) { $q->orderBy('scan_time'); },
                ])
                ->where('baggage_tag', $tag)
                ->orderBy('id', 'desc')
                ->get();
        } elseif ($email !== '') {
            $queryUsed['email'] = $email;
            // Find bookings by booked_by_email or passenger email
            $bookingIds = Booking::query()
                ->when($email !== '', function ($q) use ($email) {
                    $q->where('booked_by_email', $email)
                      ->orWhereHas('passenger', function ($qq) use ($email) {
                          $qq->where('email', $email);
                      });
                })
                ->pluck('id');

            if ($bookingIds->isNotEmpty()) {
                $bags = Baggage::with([
                        'booking.passenger',
                        'booking.flight.airline',
                        'booking.flight.route.departureAirport',
                        'booking.flight.route.arrivalAirport',
                        'trackingRecords' => function ($q) { $q->orderBy('scan_time'); },
                    ])
                    ->whereIn('booking_id', $bookingIds)
                    ->orderBy('id', 'desc')
                    ->get();
            }
        }

        // Compute belts for recently landed flights (arrivals summary)
        $arrivals = Flight::with(['bookings.baggage', 'route.departureAirport', 'route.arrivalAirport'])
            ->where('status', 'landed')
            ->where('flight_date', '>=', now()->subDay()->toDateString())
            ->orderBy('flight_date', 'desc')
            ->limit(8)
            ->get()
            ->map(function ($f) {
                $belts = collect($f->bookings)
                    ->flatMap(function ($b) { return $b->baggage; })
                    ->pluck('current_location')
                    ->filter()
                    ->unique()
                    ->values();
                return [
                    'flight_number' => $f->flight_number,
                    'date' => $f->flight_date?->format('Y-m-d'),
                    'from' => $f->route?->departureAirport?->iata_code,
                    'to' => $f->route?->arrivalAirport?->iata_code,
                    'belts' => $belts,
                    'bags_count' => collect($f->bookings)->flatMap->baggage->count(),
                ];
            });

        return view('baggage_track', [
            'bags' => $bags,
            'queryUsed' => $queryUsed,
            'arrivals' => $arrivals,
            'flightUsed' => $flightNumber ?? null,
        ]);
    }
}
