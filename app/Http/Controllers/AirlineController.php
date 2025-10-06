<?php

namespace App\Http\Controllers;

use App\Models\Airline;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;

class AirlineController extends Controller
{
    /**
     * Display a listing of airlines
     */
    public function index(Request $request): JsonResponse
    {
        $query = Airline::query();
        
        // Search functionality
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('airline_code', 'like', "%{$search}%")
                  ->orWhere('icao_code', 'like', "%{$search}%")
                  ->orWhere('country', 'like', "%{$search}%");
            });
        }
        
        // Filter by country
        if ($request->has('country')) {
            $query->where('country', $request->input('country'));
        }
        
        // Filter by active status
        if ($request->has('active')) {
            $query->where('active', $request->boolean('active'));
        }
        
        $airlines = $query->paginate(15);
        
        return response()->json($airlines);
    }

    /**
     * Store a newly created airline
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'airline_code' => 'required|string|size:3|unique:airlines,airline_code',
            'icao_code' => 'required|string|size:4|unique:airlines,icao_code',
            'name' => 'required|string|max:255',
            'country' => 'required|string|max:100',
            'headquarters' => 'required|string|max:255',
            'website' => 'nullable|url',
            'logo_url' => 'nullable|url',
            'active' => 'boolean',
            'contact_info' => 'nullable|string',
        ]);

        $airline = Airline::create($validated);

        return response()->json([
            'message' => 'Airline created successfully',
            'data' => $airline
        ], 201);
    }

    /**
     * Display the specified airline
     */
    public function show(Airline $airline): JsonResponse
    {
        return response()->json($airline);
    }

    /**
     * Update the specified airline
     */
    public function update(Request $request, Airline $airline): JsonResponse
    {
        $validated = $request->validate([
            'airline_code' => ['sometimes', 'string', 'size:3', Rule::unique('airlines', 'airline_code')->ignore($airline->id)],
            'icao_code' => ['sometimes', 'string', 'size:4', Rule::unique('airlines', 'icao_code')->ignore($airline->id)],
            'name' => 'sometimes|string|max:255',
            'country' => 'sometimes|string|max:100',
            'headquarters' => 'sometimes|string|max:255',
            'website' => 'sometimes|nullable|url',
            'logo_url' => 'sometimes|nullable|url',
            'active' => 'sometimes|boolean',
            'contact_info' => 'sometimes|nullable|string',
        ]);

        $airline->update($validated);

        return response()->json([
            'message' => 'Airline updated successfully',
            'data' => $airline
        ]);
    }

    /**
     * Remove the specified airline
     */
    public function destroy(Airline $airline): JsonResponse
    {
        $airline->delete();

        return response()->json([
            'message' => 'Airline deleted successfully'
        ]);
    }

    /**
     * Get airline's aircraft fleet
     */
    public function aircraft(Airline $airline): JsonResponse
    {
        $aircraft = $airline->aircraft()->get();
        
        return response()->json($aircraft);
    }

    /**
     * Get airline's flights
     */
    public function flights(Airline $airline, Request $request): JsonResponse
    {
        $query = $airline->flights()->with(['route', 'aircraft']);
        
        // Filter by date range
        if ($request->has('from_date')) {
            $query->where('flight_date', '>=', $request->input('from_date'));
        }
        
        if ($request->has('to_date')) {
            $query->where('flight_date', '<=', $request->input('to_date'));
        }
        
        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->input('status'));
        }
        
        $flights = $query->paginate(20);
        
        return response()->json($flights);
    }

    /**
     * Get active airlines only
     */
    public function active(): JsonResponse
    {
        $airlines = Airline::where('active', true)->get();
        
        return response()->json($airlines);
    }
}