<?php

namespace App\Http\Controllers;

use App\Models\CrewMember;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;

class CrewMemberController extends Controller
{
    /**
     * Display a listing of crew members
     */
    public function index(Request $request): JsonResponse
    {
        $query = CrewMember::query();
        
        // Search functionality
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('employee_id', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }
        
        // Filter by position
        if ($request->has('position')) {
            $query->where('position', $request->input('position'));
        }
        
        // Filter by department
        if ($request->has('department')) {
            $query->where('department', $request->input('department'));
        }
        
        // Filter by active status
        if ($request->has('active')) {
            $query->where('is_active', $request->boolean('active'));
        }
        
        $crewMembers = $query->paginate(15);
        
        return response()->json($crewMembers);
    }

    /**
     * Store a newly created crew member
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'employee_id' => 'required|string|unique:crew_members,employee_id',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:crew_members,email',
            'phone' => 'required|string|max:20',
            'position' => 'required|string|max:100',
            'department' => 'required|string|max:100',
            'hire_date' => 'required|date',
            'license_number' => 'nullable|string|max:50',
            'license_expiry' => 'nullable|date|after:today',
            'base_location' => 'required|string|max:100',
            'is_active' => 'boolean',
        ]);

        $crewMember = CrewMember::create($validated);

        return response()->json([
            'message' => 'Crew member created successfully',
            'data' => $crewMember
        ], 201);
    }

    /**
     * Display the specified crew member
     */
    public function show(CrewMember $crewMember): JsonResponse
    {
        return response()->json($crewMember->load('flightAssignments.flight'));
    }

    /**
     * Update the specified crew member
     */
    public function update(Request $request, CrewMember $crewMember): JsonResponse
    {
        $validated = $request->validate([
            'employee_id' => ['sometimes', 'string', Rule::unique('crew_members', 'employee_id')->ignore($crewMember->id)],
            'first_name' => 'sometimes|string|max:255',
            'last_name' => 'sometimes|string|max:255',
            'email' => ['sometimes', 'email', Rule::unique('crew_members', 'email')->ignore($crewMember->id)],
            'phone' => 'sometimes|string|max:20',
            'position' => 'sometimes|string|max:100',
            'department' => 'sometimes|string|max:100',
            'hire_date' => 'sometimes|date',
            'license_number' => 'sometimes|nullable|string|max:50',
            'license_expiry' => 'sometimes|nullable|date|after:today',
            'base_location' => 'sometimes|string|max:100',
            'is_active' => 'sometimes|boolean',
        ]);

        $crewMember->update($validated);

        return response()->json([
            'message' => 'Crew member updated successfully',
            'data' => $crewMember
        ]);
    }

    /**
     * Remove the specified crew member
     */
    public function destroy(CrewMember $crewMember): JsonResponse
    {
        $crewMember->delete();

        return response()->json([
            'message' => 'Crew member deleted successfully'
        ]);
    }

    /**
     * Get crew members by position
     */
    public function byPosition(string $position): JsonResponse
    {
        $crewMembers = CrewMember::where('position', $position)
            ->where('is_active', true)
            ->get();

        return response()->json($crewMembers);
    }

    /**
     * Get crew members with expiring licenses
     */
    public function expiringLicenses(): JsonResponse
    {
        $expiringLicenses = CrewMember::whereNotNull('license_expiry')
            ->where('license_expiry', '<=', now()->addMonths(3))
            ->where('license_expiry', '>', now())
            ->where('is_active', true)
            ->get(['id', 'first_name', 'last_name', 'employee_id', 'position', 'license_number', 'license_expiry']);

        return response()->json([
            'message' => 'Crew members with licenses expiring within 3 months',
            'data' => $expiringLicenses
        ]);
    }

    /**
     * Get crew member assignments
     */
    public function assignments(CrewMember $crewMember): JsonResponse
    {
        $assignments = $crewMember->flightAssignments()
            ->with(['flight.airline', 'flight.route'])
            ->orderBy('assigned_at', 'desc')
            ->get();

        return response()->json($assignments);
    }

    /**
     * Get available crew members for a specific date and position
     */
    public function available(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'position' => 'sometimes|string|max:100',
            'base_location' => 'sometimes|string|max:100',
        ]);

        $query = CrewMember::where('is_active', true)
            ->whereDoesntHave('flightAssignments', function ($assignment) use ($validated) {
                $assignment->whereHas('flight', function ($flight) use ($validated) {
                    $flight->where('flight_date', $validated['date']);
                });
            });

        if (isset($validated['position'])) {
            $query->where('position', $validated['position']);
        }

        if (isset($validated['base_location'])) {
            $query->where('base_location', $validated['base_location']);
        }

        $availableCrewMembers = $query->get();

        return response()->json([
            'date' => $validated['date'],
            'available_crew_members' => $availableCrewMembers
        ]);
    }

    /**
     * Get crew statistics
     */
    public function statistics(): JsonResponse
    {
        $stats = [
            'total_crew_members' => CrewMember::count(),
            'active_crew_members' => CrewMember::where('is_active', true)->count(),
            'inactive_crew_members' => CrewMember::where('is_active', false)->count(),
            'by_position' => CrewMember::where('is_active', true)
                ->selectRaw('position, COUNT(*) as count')
                ->groupBy('position')
                ->get(),
            'by_department' => CrewMember::where('is_active', true)
                ->selectRaw('department, COUNT(*) as count')
                ->groupBy('department')
                ->get(),
            'licenses_expiring_soon' => CrewMember::whereNotNull('license_expiry')
                ->where('license_expiry', '<=', now()->addMonths(3))
                ->where('license_expiry', '>', now())
                ->count(),
        ];

        return response()->json($stats);
    }
}