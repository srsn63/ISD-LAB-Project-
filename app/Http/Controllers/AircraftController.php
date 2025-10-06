<?php

namespace App\Http\Controllers;

use App\Models\Aircraft;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;

class AircraftController extends Controller
{
    /**
     * Display a listing of aircraft
     */
    public function index(Request $request): JsonResponse
    {
        $query = Aircraft::with('airline');
        
        // Search functionality
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('registration_number', 'like', "%{$search}%")
                  ->orWhere('aircraft_type', 'like', "%{$search}%")
                  ->orWhere('model', 'like', "%{$search}%")
                  ->orWhere('manufacturer', 'like', "%{$search}%");
            });
        }
        
        // Filter by airline
        if ($request->has('airline_id')) {
            $query->where('airline_id', $request->input('airline_id'));
        }
        
        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->input('status'));
        }
        
        // Filter by manufacturer
        if ($request->has('manufacturer')) {
            $query->where('manufacturer', $request->input('manufacturer'));
        }
        
        $aircraft = $query->paginate(15);
        
        return response()->json($aircraft);
    }

    /**
     * Store a newly created aircraft
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'registration_number' => 'required|string|unique:aircraft,registration_number',
            'aircraft_type' => 'required|string|max:100',
            'model' => 'required|string|max:100',
            'airline_id' => 'required|exists:airlines,id',
            'total_seats' => 'required|integer|min:1',
            'first_class_seats' => 'integer|min:0',
            'business_class_seats' => 'integer|min:0',
            'economy_class_seats' => 'required|integer|min:1',
            'manufacturer' => 'required|string|max:100',
            'manufacturing_year' => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'status' => 'in:active,maintenance,retired',
            'last_maintenance_date' => 'nullable|date',
            'next_maintenance_date' => 'nullable|date|after:today',
        ]);

        // Validate that seat classes add up to total seats
        $totalClassSeats = ($validated['first_class_seats'] ?? 0) + 
                          ($validated['business_class_seats'] ?? 0) + 
                          $validated['economy_class_seats'];
        
        if ($totalClassSeats != $validated['total_seats']) {
            return response()->json([
                'message' => 'The sum of class seats must equal total seats',
                'errors' => ['total_seats' => ['Seat class distribution does not match total seats']]
            ], 422);
        }

        $aircraft = Aircraft::create($validated);

        return response()->json([
            'message' => 'Aircraft created successfully',
            'data' => $aircraft->load('airline')
        ], 201);
    }

    /**
     * Display the specified aircraft
     */
    public function show(Aircraft $aircraft): JsonResponse
    {
        return response()->json($aircraft->load('airline'));
    }

    /**
     * Update the specified aircraft
     */
    public function update(Request $request, Aircraft $aircraft): JsonResponse
    {
        $validated = $request->validate([
            'registration_number' => ['sometimes', 'string', Rule::unique('aircraft', 'registration_number')->ignore($aircraft->id)],
            'aircraft_type' => 'sometimes|string|max:100',
            'model' => 'sometimes|string|max:100',
            'airline_id' => 'sometimes|exists:airlines,id',
            'total_seats' => 'sometimes|integer|min:1',
            'first_class_seats' => 'sometimes|integer|min:0',
            'business_class_seats' => 'sometimes|integer|min:0',
            'economy_class_seats' => 'sometimes|integer|min:1',
            'manufacturer' => 'sometimes|string|max:100',
            'manufacturing_year' => 'sometimes|integer|min:1900|max:' . (date('Y') + 1),
            'status' => 'sometimes|in:active,maintenance,retired',
            'last_maintenance_date' => 'sometimes|nullable|date',
            'next_maintenance_date' => 'sometimes|nullable|date|after:today',
        ]);

        $aircraft->update($validated);

        return response()->json([
            'message' => 'Aircraft updated successfully',
            'data' => $aircraft->load('airline')
        ]);
    }

    /**
     * Remove the specified aircraft
     */
    public function destroy(Aircraft $aircraft): JsonResponse
    {
        $aircraft->delete();

        return response()->json([
            'message' => 'Aircraft deleted successfully'
        ]);
    }

    /**
     * Get aircraft seats configuration
     */
    public function seats(Aircraft $aircraft): JsonResponse
    {
        $seats = $aircraft->seats()->orderBy('seat_number')->get();
        
        return response()->json($seats);
    }

    /**
     * Get aircraft maintenance schedule
     */
    public function maintenanceSchedule(): JsonResponse
    {
        $maintenanceDue = Aircraft::where('next_maintenance_date', '<=', now()->addDays(30))
            ->where('next_maintenance_date', '>', now())
            ->with('airline')
            ->get();

        return response()->json([
            'message' => 'Aircraft due for maintenance within 30 days',
            'data' => $maintenanceDue
        ]);
    }

    /**
     * Update maintenance dates
     */
    public function updateMaintenance(Request $request, Aircraft $aircraft): JsonResponse
    {
        $validated = $request->validate([
            'last_maintenance_date' => 'required|date|before_or_equal:today',
            'next_maintenance_date' => 'required|date|after:today',
        ]);

        $aircraft->update($validated);

        return response()->json([
            'message' => 'Maintenance dates updated successfully',
            'data' => $aircraft
        ]);
    }

    /**
     * Get aircraft by status
     */
    public function byStatus(string $status): JsonResponse
    {
        $validStatuses = ['active', 'maintenance', 'retired'];
        
        if (!in_array($status, $validStatuses)) {
            return response()->json([
                'message' => 'Invalid status. Valid statuses are: ' . implode(', ', $validStatuses)
            ], 400);
        }

        $aircraft = Aircraft::where('status', $status)->with('airline')->get();
        
        return response()->json($aircraft);
    }
}