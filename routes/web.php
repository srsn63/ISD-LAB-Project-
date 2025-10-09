<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\FlightController;
use App\Http\Controllers\StatusController;
use Illuminate\Http\Request;
use App\Models\User;

Route::get('/', function () {
    return view('welcome');
})->name('home');

// Static pages from footer quick links
Route::view('/about', 'pages.about')->name('about');
Route::view('/careers', 'pages.careers')->name('careers');
Route::view('/news', 'pages.news')->name('news');
Route::view('/investor-relations', 'pages.investor')->name('investor');

// Status page
Route::get('/status', [StatusController::class, 'index'])->name('status');

// Service pages
Route::view('/services/flight-booking', 'services.flight_booking')->name('services.flight_booking');
Route::view('/services/online-checkin', 'services.online_checkin')->name('services.online_checkin');
Route::view('/services/baggage-services', 'services.baggage_services')->name('services.baggage_services');
Route::view('/services/lounges', 'services.lounges')->name('services.lounges');

Route::get('/login', function () {
    return view('login_dashboard');
})->name('login.dashboard');

// Handle login form submission (the form uses route('login'))
Route::post('/login', [AuthController::class, 'login'])->name('login');

// Logout route (optional for header/menu forms)
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/signup', function () {
    return view('signup');
})->name('signup');

// Handle signup form submission
Route::post('/signup', [AuthController::class, 'register'])->name('register');

// Admin authentication routes
Route::get('/admin/login', function () {
    return view('admin_login');
})->name('admin.login');

Route::post('/admin/login', function (Request $request) {
    $data = $request->validate([
        'email' => ['required', 'email'],
        'password' => ['required', 'string'],
    ]);

    $user = User::where('email', $data['email'])
        ->where('role', 'admin') // adjust if you use a different admin flag
        ->first();

    if (!$user || !Hash::check($data['password'], $user->password)) {
        return back()->withErrors(['email' => 'Invalid admin credentials'])->withInput();
    }

    $request->session()->put('admin_id', $user->id);
    $request->session()->regenerate();

    return redirect()->route('admin');
})->name('admin.login.submit');

Route::post('/admin/logout', function (Request $request) {
    $request->session()->forget('admin_id');
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect()->route('admin.login');
})->name('admin.logout');

// Admin dashboard (protected: redirect to admin login if not authenticated)
Route::get('/admin', function (Request $request) {
    if (!$request->session()->has('admin_id')) {
        return redirect()->route('admin.login');
    }
    // Delegate to controller to render the dashboard (users list, etc.)
    return app(AdminController::class)->index($request);
})->name('admin');

// Users management
Route::post('/users', [UserManagementController::class, 'store'])->name('users.store');
Route::put('/users/{user}', [UserManagementController::class, 'update'])->name('users.update');
Route::delete('/users/{user}', [UserManagementController::class, 'destroy'])->name('users.destroy');

// Flights management
Route::post('/flights', [FlightController::class, 'store'])->name('flights.store');
Route::get('/flights', [FlightController::class, 'index'])->name('flights.index');

// Booking endpoint: decrement seats by quantity, capture seat class, and record the booking summary
Route::post('/bookings', function(Request $request){
    $data = $request->validate([
        'flight_id' => ['required','integer','exists:admin_flights,id'],
        'seat_class' => ['required','in:economy,business,first'],
        'quantity' => ['required','integer','min:1','max:20'],
    ]);

    $user = auth()->user();
    $bookedBy = $user?->email ?? $request->input('email', 'guest@example.com');

    $result = \DB::transaction(function() use ($data, $user, $bookedBy) {
        /** @var \App\Models\AdminFlight|null $flight */
        $flight = \App\Models\AdminFlight::lockForUpdate()->find($data['flight_id']);
        if (!$flight) return ['ok' => false, 'msg' => 'Flight not found.'];

        if (!in_array($flight->status, ['scheduled','delayed'])) {
            return ['ok' => false, 'msg' => 'This flight is not open for booking.'];
        }

        $qty = (int)$data['quantity'];
        if ((int)$flight->seats < $qty) {
            return ['ok' => false, 'msg' => 'Not enough seats available.'];
        }

        // Decrement seats by quantity
        $flight->decrement('seats', $qty);

        // Price multiplier by class (simple logic; can be adjusted later)
        $mult = match($data['seat_class']) {
            'business' => 1.6,
            'first' => 2.2,
            default => 1.0,
        };
        $unitPrice = round(((float)$flight->price) * $mult, 2);
        $total = round($unitPrice * $qty, 2);

        // Persist a compact booking record for admin visibility
        \App\Models\TicketBooking::create([
            'admin_flight_id' => $flight->id,
            'user_id' => $user?->id,
            'booked_by_email' => $bookedBy,
            'seat_class' => $data['seat_class'],
            'quantity' => $qty,
            'unit_price' => $unitPrice,
            'total_amount' => $total,
        ]);

        return ['ok' => true, 'msg' => "Booked {$qty} {$data['seat_class']} ticket(s) successfully."];
    });

    return back()->with('status', $result['msg']);
})->name('bookings.store');
