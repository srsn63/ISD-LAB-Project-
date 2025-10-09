<?php

namespace App\Http\Controllers;

use App\Models\AdminFlight;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class FlightController extends Controller
{
    public function index(Request $request)
    {
        $query = AdminFlight::query();

        // Optional search filters (case-insensitive contains)
        if ($request->filled('origin')) {
            $query->where('origin', 'like', '%'.$request->string('origin')->trim().'%');
        }
        if ($request->filled('destination')) {
            $query->where('destination', 'like', '%'.$request->string('destination')->trim().'%');
        }
        if ($request->filled('date')) {
            // Filter by date part of departure_at
            $query->whereDate('departure_at', $request->input('date'));
        }

        $flights = $query->orderBy('departure_at')->paginate(10)->appends($request->query());

        return view('flights', compact('flights'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'flight_number' => ['required','string','max:20'],
            'airline' => ['required','string','max:255'],
            'status' => ['required','in:scheduled,boarding,departed,delayed,cancelled'],
            'origin' => ['required','string','max:255'],
            'destination' => ['required','string','max:255'],
            'departure_at' => ['required','date'],
            'arrival_at' => ['required','date','after:departure_at'],
            'price' => ['required','numeric','min:0'],
            'seats' => ['required','integer','min:1'],
        ]);

        AdminFlight::create($validated);

        return back()->with('status', 'Flight created successfully.');
    }
}