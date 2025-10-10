<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\CheckIn;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;

class CheckInController extends Controller
{
    public function create()
    {
        return view('checkin');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'booking_reference' => ['required','string','max:32'],
            'email' => ['required','email'],
            'check_in_method' => ['required','in:online,mobile,kiosk,counter'],
            'terminal_number' => ['required','integer','min:1','max:5'],
            'seat_number' => ['nullable','string','max:10'],
            'priority_boarding' => ['sometimes','boolean'],
            'special_assistance' => ['nullable','string','max:1000'],
        ]);

        $booking = Booking::where('booking_reference', strtoupper($data['booking_reference']))
            ->where('booked_by_email', $data['email'])
            ->with('flight')
            ->first();

        if (!$booking) {
            return back()->withErrors(['booking_reference' => 'Booking not found for the provided reference and email.'])->withInput();
        }

        // Prevent duplicate check-in
        if (CheckIn::where('booking_id', $booking->id)->exists()) {
            return back()->with('status', 'You are already checked-in.')->withInput();
        }

        // Assign boarding pass number
        $bp = 'BP-' . now()->format('ymd') . '-' . Str::upper(Str::random(6));

        // Create check-in
        $checkIn = CheckIn::create([
            'booking_id' => $booking->id,
            'check_in_time' => now(),
            'check_in_method' => $data['check_in_method'],
            'boarding_pass_number' => $bp,
            'seat_number' => $data['seat_number'] ?? ($booking->seatAssignments()->value('seat_number') ?? 'TBD'),
            'gate' => $booking->flight?->departure_gate,
            'boarding_time' => $booking->flight?->scheduled_departure,
            'priority_boarding' => (bool)($data['priority_boarding'] ?? false),
            'status' => 'checked_in',
            'special_assistance' => $data['special_assistance'] ?? null,
            'terminal_number' => (int)$data['terminal_number'],
        ]);

        return redirect()->route('checkin.create')
            ->with('success', 'Check-in successful. Boarding Pass: ' . $checkIn->boarding_pass_number)
            ->with('bp', $checkIn->boarding_pass_number);
    }
}
