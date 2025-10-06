<?php

namespace App\Http\Controllers;

use App\Models\Route;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class RouteController extends Controller
{
    /**
     * Display a listing of routes
     */
    public function index(Request $request): JsonResponse
    {
        $query = Route::with(['departureAirport', 'arrivalAirport']);
        
        // Search functionality
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->whereHas('departureAirport', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('iata_code', 'like', "%{$search}%")
                  ->orWhere('city', 'like', "%{$search}%");
            })->orWhereHas('arrivalAirport', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('iata_code', 'like', "%{$search}%")
                  ->orWhere('city', 'like', "%{$search}%");
            });
        }
        
        // Filter by departure airport
        if ($request->has('departure_airport_id')) {
            $query->where('departure_airport_id', $request->input('departure_airport_id'));
        }
        
        // Filter by arrival airport
        if ($request->has('arrival_airport_id')) {
            $query->where('arrival_airport_id', $request->input('arrival_airport_id'));
        }
        
        // Filter by active status
        if ($request->has('active')) {
            $query->where('is_active', $request->boolean('active'));
        }
        
        $routes = $query->paginate(15);
        
        return response()->json($routes);
    }

    /**
     * Store a newly created route
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'departure_airport_id' => 'required|exists:airports,id',
            'arrival_airport_id' => 'required|exists:airports,id|different:departure_airport_id',
            'distance_km' => 'required|integer|min:1',
            'estimated_duration' => 'required|integer|min:1', // in minutes
            'is_active' => 'boolean',
        ]);

        // Check if route already exists
        $existingRoute = Route::where('departure_airport_id', $validated['departure_airport_id'])
            ->where('arrival_airport_id', $validated['arrival_airport_id'])
            ->first();

        if ($existingRoute) {
            return response()->json([
                'message' => 'Route already exists between these airports',
                'errors' => ['route' => ['A route already exists between these airports']]
            ], 422);
        }

        $route = Route::create($validated);

        return response()->json([
            'message' => 'Route created successfully',
            'data' => $route->load(['departureAirport', 'arrivalAirport'])
        ], 201);
    }

    /**
     * Display the specified route
     */
    public function show(Route $route): JsonResponse
    {
        return response()->json($route->load(['departureAirport', 'arrivalAirport', 'flights']));
    }

    /**
     * Update the specified route
     */
    public function update(Request $request, Route $route): JsonResponse
    {
        $validated = $request->validate([
            'departure_airport_id' => 'sometimes|exists:airports,id',
            'arrival_airport_id' => 'sometimes|exists:airports,id',
            'distance_km' => 'sometimes|integer|min:1',
            'estimated_duration' => 'sometimes|integer|min:1',
            'is_active' => 'sometimes|boolean',
        ]);

        // Ensure departure and arrival airports are different
        if (isset($validated['departure_airport_id']) && isset($validated['arrival_airport_id'])) {
            if ($validated['departure_airport_id'] === $validated['arrival_airport_id']) {
                return response()->json([
                    'message' => 'Departure and arrival airports must be different',
                    'errors' => ['arrival_airport_id' => ['Cannot be the same as departure airport']]
                ], 422);
            }
        }

        $route->update($validated);

        return response()->json([
            'message' => 'Route updated successfully',
            'data' => $route->load(['departureAirport', 'arrivalAirport'])
        ]);
    }

    /**
     * Remove the specified route
     */
    public function destroy(Route $route): JsonResponse
    {
        $route->delete();

        return response()->json([
            'message' => 'Route deleted successfully'
        ]);
    }

    /**
     * Get routes from a specific airport
     */
    public function fromAirport(int $airportId): JsonResponse
    {
        $routes = Route::with(['arrivalAirport'])
            ->where('departure_airport_id', $airportId)
            ->where('is_active', true)
            ->get();

        return response()->json($routes);
    }

    /**
     * Get routes to a specific airport
     */
    public function toAirport(int $airportId): JsonResponse
    {
        $routes = Route::with(['departureAirport'])
            ->where('arrival_airport_id', $airportId)
            ->where('is_active', true)
            ->get();

        return response()->json($routes);
    }

    /**
     * Search routes between airports
     */
    public function searchRoutes(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'departure_airport' => 'required|string|size:3',
            'arrival_airport' => 'required|string|size:3',
        ]);

        $routes = Route::whereHas('departureAirport', function ($q) use ($validated) {
                $q->where('iata_code', $validated['departure_airport']);
            })
            ->whereHas('arrivalAirport', function ($q) use ($validated) {
                $q->where('iata_code', $validated['arrival_airport']);
            })
            ->where('is_active', true)
            ->with(['departureAirport', 'arrivalAirport', 'flights' => function ($query) {
                $query->where('flight_date', '>=', today())
                      ->where('status', 'scheduled')
                      ->orderBy('flight_date')
                      ->orderBy('scheduled_departure');
            }])
            ->get();

        return response()->json([
            'departure_airport' => $validated['departure_airport'],
            'arrival_airport' => $validated['arrival_airport'],
            'routes' => $routes
        ]);
    }

    /**
     * Get popular routes
     */
    public function popular(): JsonResponse
    {
        $popularRoutes = Route::with(['departureAirport', 'arrivalAirport'])
            ->withCount(['flights' => function ($query) {
                $query->where('flight_date', '>=', now()->subMonths(3));
            }])
            ->having('flights_count', '>', 0)
            ->orderBy('flights_count', 'desc')
            ->limit(10)
            ->get();

        return response()->json([
            'message' => 'Top 10 popular routes (based on flights in last 3 months)',
            'data' => $popularRoutes
        ]);
    }

    /**
     * Get route statistics
     */
    public function statistics(): JsonResponse
    {
        $stats = [
            'total_routes' => Route::count(),
            'active_routes' => Route::where('is_active', true)->count(),
            'inactive_routes' => Route::where('is_active', false)->count(),
            'routes_with_flights' => Route::has('flights')->count(),
            'average_distance' => Route::where('is_active', true)->avg('distance_km'),
            'average_duration' => Route::where('is_active', true)->avg('estimated_duration'),
            'longest_route' => Route::with(['departureAirport', 'arrivalAirport'])
                ->where('is_active', true)
                ->orderBy('distance_km', 'desc')
                ->first(),
            'shortest_route' => Route::with(['departureAirport', 'arrivalAirport'])
                ->where('is_active', true)
                ->orderBy('distance_km', 'asc')
                ->first(),
        ];

        return response()->json($stats);
    }

    /**
     * Bulk create routes
     */
    public function bulkCreate(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'routes' => 'required|array|min:1',
            'routes.*.departure_airport_id' => 'required|exists:airports,id',
            'routes.*.arrival_airport_id' => 'required|exists:airports,id',
            'routes.*.distance_km' => 'required|integer|min:1',
            'routes.*.estimated_duration' => 'required|integer|min:1',
        ]);

        $routes = [];
        $errors = [];

        foreach ($validated['routes'] as $index => $routeData) {
            // Check for same departure and arrival
            if ($routeData['departure_airport_id'] === $routeData['arrival_airport_id']) {
                $errors["routes.{$index}"] = 'Departure and arrival airports must be different';
                continue;
            }

            // Check if route already exists
            $exists = Route::where('departure_airport_id', $routeData['departure_airport_id'])
                ->where('arrival_airport_id', $routeData['arrival_airport_id'])
                ->exists();

            if ($exists) {
                $errors["routes.{$index}"] = 'Route already exists between these airports';
                continue;
            }

            $routeData['is_active'] = true;
            $routeData['created_at'] = now();
            $routeData['updated_at'] = now();
            
            $routes[] = $routeData;
        }

        if (!empty($errors)) {
            return response()->json([
                'message' => 'Some routes could not be created',
                'errors' => $errors
            ], 422);
        }

        Route::insert($routes);

        return response()->json([
            'message' => 'Routes created successfully',
            'count' => count($routes)
        ], 201);
    }
}