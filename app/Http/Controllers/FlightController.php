<?php

namespace App\Http\Controllers;

use App\Models\Flight;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;

class FlightController extends Controller
{
    /**
     * Display a listing of flights
     */
    public function index(Request $request): JsonResponse
    {
        $query = Flight::with(['airline', 'aircraft', 'route.departureAirport', 'route.arrivalAirport']);
        
        // Search functionality
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('flight_number', 'like', "%{$search}%")
                  ->orWhereHas('airline', function ($airline) use ($search) {
                      $airline->where('name', 'like', "%{$search}%");
                  });
            });
        }
        
        // Filter by date range
        if ($request->has('from_date')) {
            $query->where('flight_date', '>=', $request->input('from_date'));
        }
        
        if ($request->has('to_date')) {
            $query->where('flight_date', '<=', $request->input('to_date'));
        }
        
        // Filter by airline
        if ($request->has('airline_id')) {
            $query->where('airline_id', $request->input('airline_id'));
        }
        
        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->input('status'));
        }
        
        // Filter by departure/arrival airports via route
        if ($request->has('departure_airport')) {
            $query->whereHas('route', function ($route) use ($request) {
                $route->whereHas('departureAirport', function ($airport) use ($request) {
                    $airport->where('iata_code', $request->input('departure_airport'))
                           ->orWhere('icao_code', $request->input('departure_airport'));
                });
            });
        }
        
        if ($request->has('arrival_airport')) {
            $query->whereHas('route', function ($route) use ($request) {
                $route->whereHas('arrivalAirport', function ($airport) use ($request) {
                    $airport->where('iata_code', $request->input('arrival_airport'))
                           ->orWhere('icao_code', $request->input('arrival_airport'));
                });
            });
        }
        
        $flights = $query->paginate(20);
        
        return response()->json($flights);
    }

    /**
     * Store a newly created flight
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'flight_number' => 'required|string|max:10',
            'airline_id' => 'required|exists:airlines,id',
            'aircraft_id' => 'required|exists:aircraft,id',
            'route_id' => 'required|exists:routes,id',
            'flight_date' => 'required|date|after_or_equal:today',
            'scheduled_departure' => 'required|date_format:H:i',
            'scheduled_arrival' => 'required|date_format:H:i|after:scheduled_departure',
            'departure_gate' => 'nullable|string|max:10',
            'arrival_gate' => 'nullable|string|max:10',
            'available_seats' => 'required|integer|min:1',
            'base_price' => 'required|numeric|min:0',
        ]);

        $flight = Flight::create($validated);

        return response()->json([
            'message' => 'Flight created successfully',
            'data' => $flight->load(['airline', 'aircraft', 'route'])
        ], 201);
    }

    /**
     * Display the specified flight
     */
    public function show(Flight $flight): JsonResponse
    {
        return response()->json($flight->load([
            'airline', 
            'aircraft', 
            'route.departureAirport', 
            'route.arrivalAirport',
            'bookings.passenger'
        ]));
    }

    /**
     * Update the specified flight
     */
    public function update(Request $request, Flight $flight): JsonResponse
    {
        $validated = $request->validate([
            'flight_number' => 'sometimes|string|max:10',
            'airline_id' => 'sometimes|exists:airlines,id',
            'aircraft_id' => 'sometimes|exists:aircraft,id',
            'route_id' => 'sometimes|exists:routes,id',
            'flight_date' => 'sometimes|date',
            'scheduled_departure' => 'sometimes|date_format:H:i',
            'scheduled_arrival' => 'sometimes|date_format:H:i',
            'actual_departure' => 'sometimes|nullable|date_format:H:i',
            'actual_arrival' => 'sometimes|nullable|date_format:H:i',
            'status' => 'sometimes|in:scheduled,boarding,departed,in_flight,landed,cancelled,delayed',
            'departure_gate' => 'sometimes|nullable|string|max:10',
            'arrival_gate' => 'sometimes|nullable|string|max:10',
            'delay_reason' => 'sometimes|nullable|string',
            'available_seats' => 'sometimes|integer|min:0',
            'base_price' => 'sometimes|numeric|min:0',
        ]);

        $flight->update($validated);

        return response()->json([
            'message' => 'Flight updated successfully',
            'data' => $flight->load(['airline', 'aircraft', 'route'])
        ]);
    }

    /**
     * Remove the specified flight
     */
    public function destroy(Flight $flight): JsonResponse
    {
        $flight->delete();

        return response()->json([
            'message' => 'Flight deleted successfully'
        ]);
    }

    /**
     * Update flight status
     */
    public function updateStatus(Request $request, Flight $flight): JsonResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:scheduled,boarding,departed,in_flight,landed,cancelled,delayed',
            'actual_departure' => 'nullable|date_format:H:i',
            'actual_arrival' => 'nullable|date_format:H:i',
            'delay_reason' => 'nullable|string',
        ]);

        $flight->update($validated);

        // Create status update record
        $flight->statusUpdates()->create([
            'old_status' => $flight->getOriginal('status'),
            'new_status' => $validated['status'],
            'reason' => $validated['delay_reason'] ?? null,
            'updated_at' => now(),
        ]);

        return response()->json([
            'message' => 'Flight status updated successfully',
            'data' => $flight
        ]);
    }

    /**
     * Get flights by date
     */
    public function byDate(string $date): JsonResponse
    {
        $flights = Flight::with(['airline', 'route.departureAirport', 'route.arrivalAirport'])
            ->where('flight_date', $date)
            ->orderBy('scheduled_departure')
            ->get();

        return response()->json($flights);
    }

    /**
     * Get delayed flights
     */
    public function delayed(): JsonResponse
    {
        $delayedFlights = Flight::with(['airline', 'route.departureAirport', 'route.arrivalAirport'])
            ->where('status', 'delayed')
            ->orWhere(function ($query) {
                $query->whereNotNull('actual_departure')
                      ->whereRaw('actual_departure > scheduled_departure');
            })
            ->get();

        return response()->json($delayedFlights);
    }

    /**
     * Get available flights for booking
     */
    public function searchFlights(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'departure_airport' => 'required|string|size:3',
            'arrival_airport' => 'required|string|size:3',
            'departure_date' => 'required|date|after_or_equal:today',
            'passengers' => 'integer|min:1|max:9'
        ]);

        $passengers = $validated['passengers'] ?? 1;

        $flights = Flight::with(['airline', 'aircraft', 'route.departureAirport', 'route.arrivalAirport'])
            ->whereHas('route', function ($query) use ($validated) {
                $query->whereHas('departureAirport', function ($airport) use ($validated) {
                    $airport->where('iata_code', $validated['departure_airport']);
                })->whereHas('arrivalAirport', function ($airport) use ($validated) {
                    $airport->where('iata_code', $validated['arrival_airport']);
                });
            })
            ->where('flight_date', $validated['departure_date'])
            ->where('available_seats', '>=', $passengers)
            ->where('status', 'scheduled')
            ->orderBy('scheduled_departure')
            ->get();

        return response()->json([
            'message' => 'Available flights found',
            'data' => $flights
        ]);
    }

    /**
     * Get flight status updates
     */
    public function statusUpdates(Flight $flight): JsonResponse
    {
        $statusUpdates = $flight->statusUpdates()
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($statusUpdates);
    }
}