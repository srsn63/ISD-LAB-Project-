<?php

namespace App\Http\Controllers;

use App\Models\Passenger;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;

class PassengerController extends Controller
{
    /**
     * Display a listing of passengers
     */
    public function index(Request $request): JsonResponse
    {
        $query = Passenger::query();
        
        // Search functionality
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('passport_number', 'like', "%{$search}%");
            });
        }
        
        // Filter by nationality
        if ($request->has('nationality')) {
            $query->where('nationality', $request->input('nationality'));
        }
        
        // Filter by frequent flyer status
        if ($request->has('frequent_flyer')) {
            $query->where('frequent_flyer', $request->boolean('frequent_flyer'));
        }
        
        $passengers = $query->paginate(15);
        
        return response()->json($passengers);
    }

    /**
     * Store a newly created passenger
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:passengers,email',
            'phone' => 'required|string|max:20',
            'date_of_birth' => 'required|date|before:today',
            'gender' => 'required|in:male,female,other',
            'nationality' => 'required|string|max:100',
            'passport_number' => 'required|string|unique:passengers,passport_number',
            'passport_expiry' => 'required|date|after:today',
            'emergency_contact_name' => 'required|string|max:255',
            'emergency_contact_phone' => 'required|string|max:20',
            'meal_preference' => 'in:regular,vegetarian,vegan,kosher,halal',
            'seat_preference' => 'in:window,aisle,middle,no_preference',
            'frequent_flyer' => 'boolean',
            'frequent_flyer_number' => 'nullable|string|max:50',
        ]);

        $passenger = Passenger::create($validated);

        return response()->json([
            'message' => 'Passenger created successfully',
            'data' => $passenger
        ], 201);
    }

    /**
     * Display the specified passenger
     */
    public function show(Passenger $passenger): JsonResponse
    {
        return response()->json($passenger);
    }

    /**
     * Update the specified passenger
     */
    public function update(Request $request, Passenger $passenger): JsonResponse
    {
        $validated = $request->validate([
            'first_name' => 'sometimes|string|max:255',
            'last_name' => 'sometimes|string|max:255',
            'email' => ['sometimes', 'email', Rule::unique('passengers', 'email')->ignore($passenger->id)],
            'phone' => 'sometimes|string|max:20',
            'date_of_birth' => 'sometimes|date|before:today',
            'gender' => 'sometimes|in:male,female,other',
            'nationality' => 'sometimes|string|max:100',
            'passport_number' => ['sometimes', 'string', Rule::unique('passengers', 'passport_number')->ignore($passenger->id)],
            'passport_expiry' => 'sometimes|date|after:today',
            'emergency_contact_name' => 'sometimes|string|max:255',
            'emergency_contact_phone' => 'sometimes|string|max:20',
            'meal_preference' => 'sometimes|in:regular,vegetarian,vegan,kosher,halal',
            'seat_preference' => 'sometimes|in:window,aisle,middle,no_preference',
            'frequent_flyer' => 'sometimes|boolean',
            'frequent_flyer_number' => 'nullable|string|max:50',
        ]);

        $passenger->update($validated);

        return response()->json([
            'message' => 'Passenger updated successfully',
            'data' => $passenger
        ]);
    }

    /**
     * Remove the specified passenger
     */
    public function destroy(Passenger $passenger): JsonResponse
    {
        $passenger->delete();

        return response()->json([
            'message' => 'Passenger deleted successfully'
        ]);
    }

    /**
     * Get passenger's booking history
     */
    public function bookings(Passenger $passenger): JsonResponse
    {
        $bookings = $passenger->bookings()->with(['flight', 'flight.airline'])->get();
        
        return response()->json($bookings);
    }

    /**
     * Check if passport is expiring soon (within 6 months)
     */
    public function checkPassportExpiry(): JsonResponse
    {
        $expiringPassports = Passenger::where('passport_expiry', '<=', now()->addMonths(6))
            ->where('passport_expiry', '>', now())
            ->get(['id', 'first_name', 'last_name', 'email', 'passport_number', 'passport_expiry']);

        return response()->json([
            'message' => 'Passengers with expiring passports (within 6 months)',
            'data' => $expiringPassports
        ]);
    }
}