<?php

namespace App\Http\Controllers;

use App\Models\Airport;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;

class AirportController extends Controller
{
    /**
     * Display a listing of airports
     */
    public function index(Request $request): JsonResponse
    {
        $query = Airport::query();
        
        // Search functionality
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('iata_code', 'like', "%{$search}%")
                  ->orWhere('icao_code', 'like', "%{$search}%")
                  ->orWhere('city', 'like', "%{$search}%")
                  ->orWhere('country', 'like', "%{$search}%");
            });
        }
        
        // Filter by country
        if ($request->has('country')) {
            $query->where('country', $request->input('country'));
        }
        
        // Filter by city
        if ($request->has('city')) {
            $query->where('city', $request->input('city'));
        }
        
        // Filter by international status
        if ($request->has('international')) {
            $query->where('international', $request->boolean('international'));
        }
        
        // Filter by active status
        if ($request->has('active')) {
            $query->where('active', $request->boolean('active'));
        }
        
        $airports = $query->paginate(15);
        
        return response()->json($airports);
    }

    /**
     * Store a newly created airport
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'iata_code' => 'required|string|size:3|unique:airports,iata_code',
            'icao_code' => 'required|string|size:4|unique:airports,icao_code',
            'name' => 'required|string|max:255',
            'city' => 'required|string|max:100',
            'country' => 'required|string|max:100',
            'timezone' => 'required|string|max:50',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'elevation_feet' => 'nullable|integer|min:0',
            'total_terminals' => 'integer|min:1',
            'total_runways' => 'integer|min:1',
            'international' => 'boolean',
            'active' => 'boolean',
        ]);

        $airport = Airport::create($validated);

        return response()->json([
            'message' => 'Airport created successfully',
            'data' => $airport
        ], 201);
    }

    /**
     * Display the specified airport
     */
    public function show(Airport $airport): JsonResponse
    {
        return response()->json($airport);
    }

    /**
     * Update the specified airport
     */
    public function update(Request $request, Airport $airport): JsonResponse
    {
        $validated = $request->validate([
            'iata_code' => ['sometimes', 'string', 'size:3', Rule::unique('airports', 'iata_code')->ignore($airport->id)],
            'icao_code' => ['sometimes', 'string', 'size:4', Rule::unique('airports', 'icao_code')->ignore($airport->id)],
            'name' => 'sometimes|string|max:255',
            'city' => 'sometimes|string|max:100',
            'country' => 'sometimes|string|max:100',
            'timezone' => 'sometimes|string|max:50',
            'latitude' => 'sometimes|numeric|between:-90,90',
            'longitude' => 'sometimes|numeric|between:-180,180',
            'elevation_feet' => 'sometimes|nullable|integer|min:0',
            'total_terminals' => 'sometimes|integer|min:1',
            'total_runways' => 'sometimes|integer|min:1',
            'international' => 'sometimes|boolean',
            'active' => 'sometimes|boolean',
        ]);

        $airport->update($validated);

        return response()->json([
            'message' => 'Airport updated successfully',
            'data' => $airport
        ]);
    }

    /**
     * Remove the specified airport
     */
    public function destroy(Airport $airport): JsonResponse
    {
        $airport->delete();

        return response()->json([
            'message' => 'Airport deleted successfully'
        ]);
    }

    /**
     * Get airports by country
     */
    public function byCountry(string $country): JsonResponse
    {
        $airports = Airport::where('country', $country)
            ->where('active', true)
            ->orderBy('name')
            ->get();
        
        return response()->json($airports);
    }

    /**
     * Get international airports only
     */
    public function international(): JsonResponse
    {
        $airports = Airport::where('international', true)
            ->where('active', true)
            ->orderBy('name')
            ->get();
        
        return response()->json($airports);
    }

    /**
     * Search airports by IATA or ICAO code
     */
    public function searchByCode(string $code): JsonResponse
    {
        $code = strtoupper($code);
        
        $airport = Airport::where('iata_code', $code)
            ->orWhere('icao_code', $code)
            ->first();

        if (!$airport) {
            return response()->json([
                'message' => 'Airport not found with the provided code'
            ], 404);
        }

        return response()->json($airport);
    }

    /**
     * Get nearby airports based on coordinates
     */
    public function nearby(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'radius' => 'integer|min:1|max:1000', // radius in kilometers
        ]);

        $latitude = $validated['latitude'];
        $longitude = $validated['longitude'];
        $radius = $validated['radius'] ?? 100; // default 100km

        // Using Haversine formula to calculate distance
        $airports = Airport::selectRaw("
                *,
                (6371 * ACOS(
                    COS(RADIANS(?)) * 
                    COS(RADIANS(latitude)) * 
                    COS(RADIANS(longitude) - RADIANS(?)) + 
                    SIN(RADIANS(?)) * 
                    SIN(RADIANS(latitude))
                )) AS distance
            ", [$latitude, $longitude, $latitude])
            ->having('distance', '<=', $radius)
            ->where('active', true)
            ->orderBy('distance')
            ->get();

        return response()->json([
            'message' => "Airports within {$radius}km",
            'data' => $airports
        ]);
    }

    /**
     * Get airport gates
     */
    public function gates(Airport $airport): JsonResponse
    {
        $gates = $airport->gates()->get();
        
        return response()->json($gates);
    }
}