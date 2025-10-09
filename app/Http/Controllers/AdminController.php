<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index(Request $request)
    {
        // Simple pagination of users; if none, pass empty collection
        $users = User::query()
            ->orderByDesc('id')
            ->paginate(10);

        // Lightweight bookings summary for visibility
        $bookings = \App\Models\TicketBooking::query()
            ->with('flight')
            ->orderByDesc('id')
            ->limit(50)
            ->get();

        return view('admin', compact('users', 'bookings'));
    }
}
