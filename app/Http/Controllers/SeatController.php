<?php

namespace App\Http\Controllers;

use App\Models\Seat;
use App\Models\Aircraft;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class SeatController extends Controller
{
    /**
     * Display a listing of seats
     */
    public function index(Request $request): JsonResponse
    {
        $query = Seat::with('aircraft');
        
        // Filter by aircraft
        if ($request->has('aircraft_id')) {
            $query->where('aircraft_id', $request->input('aircraft_id'));
        }
        
        // Filter by seat class
        if ($request->has('seat_class')) {
            $query->where('seat_class', $request->input('seat_class'));
        }
        
        // Filter by seat type
        if ($request->has('seat_type')) {
            $query->where('seat_type', $request->input('seat_type'));
        }
        
        // Filter by availability
        if ($request->has('available')) {
            $query->where('is_available', $request->boolean('available'));
        }
        
        $seats = $query->paginate(50);
        
        return response()->json($seats);
    }

    /**
     * Store a newly created seat
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'aircraft_id' => 'required|exists:aircraft,id',
            'seat_number' => 'required|string|max:10',
            'seat_class' => 'required|in:first,business,economy',
            'seat_type' => 'required|in:window,aisle,middle',
            'is_available' => 'boolean',
            'has_extra_legroom' => 'boolean',
            'extra_fee' => 'numeric|min:0',
        ]);

        // Check if seat number already exists for this aircraft
        $existingSeat = Seat::where('aircraft_id', $validated['aircraft_id'])
            ->where('seat_number', $validated['seat_number'])
            ->first();

        if ($existingSeat) {
            return response()->json([
                'message' => 'Seat number already exists for this aircraft',
                'errors' => ['seat_number' => ['Seat number must be unique per aircraft']]
            ], 422);
        }

        $seat = Seat::create($validated);

        return response()->json([
            'message' => 'Seat created successfully',
            'data' => $seat->load('aircraft')
        ], 201);
    }

    /**
     * Display the specified seat
     */
    public function show(Seat $seat): JsonResponse
    {
        return response()->json($seat->load('aircraft'));
    }

    /**
     * Update the specified seat
     */
    public function update(Request $request, Seat $seat): JsonResponse
    {
        $validated = $request->validate([
            'seat_number' => 'sometimes|string|max:10',
            'seat_class' => 'sometimes|in:first,business,economy',
            'seat_type' => 'sometimes|in:window,aisle,middle',
            'is_available' => 'sometimes|boolean',
            'has_extra_legroom' => 'sometimes|boolean',
            'extra_fee' => 'sometimes|numeric|min:0',
        ]);

        // Check uniqueness if seat_number is being updated
        if (isset($validated['seat_number'])) {
            $existingSeat = Seat::where('aircraft_id', $seat->aircraft_id)
                ->where('seat_number', $validated['seat_number'])
                ->where('id', '!=', $seat->id)
                ->first();

            if ($existingSeat) {
                return response()->json([
                    'message' => 'Seat number already exists for this aircraft',
                    'errors' => ['seat_number' => ['Seat number must be unique per aircraft']]
                ], 422);
            }
        }

        $seat->update($validated);

        return response()->json([
            'message' => 'Seat updated successfully',
            'data' => $seat->load('aircraft')
        ]);
    }

    /**
     * Remove the specified seat
     */
    public function destroy(Seat $seat): JsonResponse
    {
        $seat->delete();

        return response()->json([
            'message' => 'Seat deleted successfully'
        ]);
    }

    /**
     * Get seats by aircraft
     */
    public function byAircraft(Aircraft $aircraft): JsonResponse
    {
        $seats = $aircraft->seats()
            ->orderBy('seat_number')
            ->get()
            ->groupBy('seat_class');

        return response()->json([
            'aircraft' => $aircraft,
            'seats' => $seats
        ]);
    }

    /**
     * Get available seats for a specific flight
     */
    public function availableForFlight(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'flight_id' => 'required|exists:flights,id',
            'seat_class' => 'sometimes|in:first,business,economy',
        ]);

        $flight = \App\Models\Flight::with('aircraft')->findOrFail($validated['flight_id']);
        
        $query = $flight->aircraft->seats()
            ->where('is_available', true)
            ->whereDoesntHave('seatAssignments', function ($assignment) use ($validated) {
                $assignment->whereHas('booking', function ($booking) use ($validated) {
                    $booking->where('flight_id', $validated['flight_id'])
                           ->where('booking_status', '!=', 'cancelled');
                });
            });

        if (isset($validated['seat_class'])) {
            $query->where('seat_class', $validated['seat_class']);
        }

        $availableSeats = $query->orderBy('seat_number')->get();

        return response()->json([
            'flight' => $flight,
            'available_seats' => $availableSeats
        ]);
    }

    /**
     * Bulk create seats for aircraft
     */
    public function bulkCreate(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'aircraft_id' => 'required|exists:aircraft,id',
            'seats' => 'required|array|min:1',
            'seats.*.seat_number' => 'required|string|max:10',
            'seats.*.seat_class' => 'required|in:first,business,economy',
            'seats.*.seat_type' => 'required|in:window,aisle,middle',
            'seats.*.has_extra_legroom' => 'boolean',
            'seats.*.extra_fee' => 'numeric|min:0',
        ]);

        $aircraftId = $validated['aircraft_id'];
        $seats = [];

        foreach ($validated['seats'] as $seatData) {
            $seatData['aircraft_id'] = $aircraftId;
            $seatData['is_available'] = true;
            $seatData['has_extra_legroom'] = $seatData['has_extra_legroom'] ?? false;
            $seatData['extra_fee'] = $seatData['extra_fee'] ?? 0;
            $seatData['created_at'] = now();
            $seatData['updated_at'] = now();
            
            $seats[] = $seatData;
        }

        // Check for duplicate seat numbers
        $seatNumbers = collect($seats)->pluck('seat_number');
        if ($seatNumbers->count() !== $seatNumbers->unique()->count()) {
            return response()->json([
                'message' => 'Duplicate seat numbers found',
                'errors' => ['seats' => ['All seat numbers must be unique']]
            ], 422);
        }

        // Check if any seat numbers already exist for this aircraft
        $existingSeats = Seat::where('aircraft_id', $aircraftId)
            ->whereIn('seat_number', $seatNumbers)
            ->pluck('seat_number');

        if ($existingSeats->count() > 0) {
            return response()->json([
                'message' => 'Some seat numbers already exist for this aircraft',
                'errors' => ['seats' => ["Existing seat numbers: " . $existingSeats->implode(', ')]]
            ], 422);
        }

        Seat::insert($seats);

        return response()->json([
            'message' => 'Seats created successfully',
            'count' => count($seats)
        ], 201);
    }

    /**
     * Get seat map for aircraft
     */
    public function seatMap(Aircraft $aircraft): JsonResponse
    {
        $seats = $aircraft->seats()
            ->orderBy('seat_number')
            ->get()
            ->groupBy('seat_class');

        return response()->json([
            'aircraft' => $aircraft,
            'seat_map' => $seats,
            'summary' => [
                'total_seats' => $aircraft->seats->count(),
                'first_class' => $aircraft->seats->where('seat_class', 'first')->count(),
                'business_class' => $aircraft->seats->where('seat_class', 'business')->count(),
                'economy_class' => $aircraft->seats->where('seat_class', 'economy')->count(),
            ]
        ]);
    }
}