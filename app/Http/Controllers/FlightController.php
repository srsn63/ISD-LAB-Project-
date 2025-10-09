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

        // Optional search filters
        if ($request->filled('origin')) {
            $query->where('origin', 'like', '%'.$request->string('origin')->trim().'%');
        }
        if ($request->filled('destination')) {
            $query->where('destination', 'like', '%'.$request->string('destination')->trim().'%');
        }
        if ($request->filled('date')) {
            // Filter by date part of departure_at
            $query->whereDate('departure_at', $request->date('date'));
        }
        if ($request->filled('class')) {
            $class = $request->input('class');
            if (in_array($class, ['first','business','economy'])) {
                $column = match($class) {
                    'first' => 'first_class_seats',
                    'business' => 'business_class_seats',
                    default => 'economy_class_seats',
                };
                $query->where($column, '>', 0);
            }
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
            'seats' => ['required','integer','min:120'], // enforce minimum seats
        ]);

        // Distribute seats: First=20, Business=40, Economy=rest
        $total = (int) $validated['seats'];
        $first = 20;
        $business = 40;
        $economy = max($total - ($first + $business), 0);

        $data = array_merge($validated, [
            'first_class_seats' => $first,
            'business_class_seats' => $business,
            'economy_class_seats' => $economy,
        ]);

        AdminFlight::create($data);

        return back()->with('status', 'Flight created successfully.');
    }
}