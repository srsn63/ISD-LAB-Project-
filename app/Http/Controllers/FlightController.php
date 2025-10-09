<?php

namespace App\Http\Controllers;

use App\Models\AdminFlight;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class FlightController extends Controller
{
    public function index(Request $request)
    {
        $flights = AdminFlight::query()
            ->orderBy('departure_at')
            ->paginate(10);

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