<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\FlightController;
use Illuminate\Http\Request;
use App\Models\User;

Route::get('/', function () {
    return view('welcome');
})->name('home');

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

// Booking endpoint: decrement total seats if available
Route::post('/bookings', function(Request $request){
    $data = $request->validate([
        'flight_id' => ['required','integer','exists:admin_flights,id'],
    ]);

    $updated = \DB::transaction(function() use ($data) {
        $flight = \App\Models\AdminFlight::lockForUpdate()->find($data['flight_id']);
        if (!$flight) return false;
        if ((int)$flight->seats <= 0) return false;
        $flight->decrement('seats');
        return true;
    });

    if (!$updated) {
        return back()->with('status', 'This flight is sold out.');
    }

    return back()->with('status', 'Booking confirmed. A seat has been reserved.');
})->name('bookings.store');
