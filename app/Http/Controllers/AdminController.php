<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\ContactMessage;
use App\Models\CheckIn;
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

        // Recent contact messages for admin visibility
        $messages = ContactMessage::query()
            ->orderByDesc('id')
            ->limit(20)
            ->get();

        // Recent check-ins
        $checkins = CheckIn::query()
            ->orderByDesc('id')
            ->limit(20)
            ->get();

        return view('admin', compact('users', 'bookings', 'messages', 'checkins'));
    }
}
