<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\FlightController;
use Illuminate\Http\Request;

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

// Admin dashboard (controller provides users pagination)
Route::get('/admin', [AdminController::class, 'index'])->name('admin');

// Users management
Route::post('/users', [UserManagementController::class, 'store'])->name('users.store');
Route::put('/users/{user}', [UserManagementController::class, 'update'])->name('users.update');
Route::delete('/users/{user}', [UserManagementController::class, 'destroy'])->name('users.destroy');

// Flights management
Route::post('/flights', [FlightController::class, 'store'])->name('flights.store');
Route::get('/flights', [FlightController::class, 'index'])->name('flights.index');

// Minimal booking endpoint to satisfy Book Ticket button; stores nothing yet but flashes a message
Route::post('/bookings', function(Request $request){
    // In a future step, persist a booking and decrement seats.
    return back()->with('status', 'Booking request received for flight ID '.$request->input('flight_id'));
})->name('bookings.store');
