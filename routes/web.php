<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\FlightController;
use App\Http\Controllers\StatusController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\CheckInController;
use App\Http\Controllers\BaggageController;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\ContactMessage;

Route::get('/', function () {
    return view('welcome');
})->name('home');

// Flight Status page (dummy data, themed)
Route::view('/flight-status', 'flight_status')->name('flight_status');
// Baggage Track page (live search by tag or email)
Route::get('/baggage-track', [BaggageController::class, 'index'])->name('baggage_track');

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
    return app(AdminController::class)->index($request);
})->name('admin');

// Contact routes
Route::get('/contact', [ContactController::class, 'create'])->name('contact');
Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');

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

// Check-In routes
Route::get('/check-in', [CheckInController::class, 'create'])->name('checkin.create');
Route::post('/check-in', [CheckInController::class, 'store'])->name('checkin.store');

// Dev: seed a minimal booking to test check-in without PNR
Route::get('/dev/seed-checkin-demo', function () {
    // Guard: only allow in local environment
    if (!app()->environment(['local', 'development'])) {
        abort(403, 'Forbidden');
    }

    $email = 'demo.checkin@example.com';

    // Create base data if missing
    $airline = \App\Models\Airline::firstOrCreate(
        ['name' => 'Lalon Air'],
        [
            'airline_code' => 'LA',
            'icao_code' => 'LAL',
            'country' => 'Bangladesh',
            'headquarters' => 'Dhaka',
            'website' => 'https://lalonair.example',
            'active' => true,
            'contact_info' => 'info@lalonair.example',
        ]
    );

    $aircraft = \App\Models\Aircraft::firstOrCreate(
        ['registration_number' => 'S2-A100'],
        [
            'aircraft_type' => 'A320',
            'model' => 'A320-200',
            'airline_id' => $airline->id,
            'total_seats' => 180,
            'first_class_seats' => 12,
            'business_class_seats' => 24,
            'economy_class_seats' => 144,
            'manufacturer' => 'Airbus',
            'manufacturing_year' => 2018,
            'status' => 'active',
            'last_maintenance_date' => now()->subMonths(1),
            'next_maintenance_date' => now()->addMonths(5),
        ]
    );

    $origin = \App\Models\Airport::firstOrCreate(
        ['iata_code' => 'DAC'],
        [
            'icao_code' => 'VGHS',
            'name' => 'Hazrat Shahjalal International Airport',
            'city' => 'Dhaka',
            'country' => 'Bangladesh',
            'timezone' => 'Asia/Dhaka',
            'latitude' => 23.8433,
            'longitude' => 90.3978,
            'elevation_feet' => 30,
            'total_terminals' => 3,
            'total_runways' => 2,
            'international' => true,
            'active' => true,
        ]
    );

    $dest = \App\Models\Airport::firstOrCreate(
        ['iata_code' => 'CXB'],
        [
            'icao_code' => 'VGCB',
            'name' => 'Cox\'s Bazar Airport',
            'city' => 'Cox\'s Bazar',
            'country' => 'Bangladesh',
            'timezone' => 'Asia/Dhaka',
            'latitude' => 21.4522,
            'longitude' => 91.9639,
            'elevation_feet' => 12,
            'total_terminals' => 1,
            'total_runways' => 1,
            'international' => false,
            'active' => true,
        ]
    );

    $route = \App\Models\Route::firstOrCreate(
        [
            'origin_airport_id' => $origin->id,
            'destination_airport_id' => $dest->id,
        ],
        [
            'distance_km' => 300,
            'estimated_duration_minutes' => 55,
            'active' => true,
        ]
    );

    $flight = \App\Models\Flight::firstOrCreate(
        [
            'flight_number' => 'LA102',
            'airline_id' => $airline->id,
            'aircraft_id' => $aircraft->id,
            'route_id' => $route->id,
            'flight_date' => today()->addDay(),
        ],
        [
            'scheduled_departure' => '09:30',
            'scheduled_arrival' => '10:30',
            'status' => 'scheduled',
            'departure_gate' => 'A2',
            'arrival_gate' => 'B1',
            'available_seats' => 180,
            'base_price' => 250.00,
        ]
    );

    $passenger = \App\Models\Passenger::firstOrCreate(
        ['email' => $email],
        [
            'first_name' => 'Rahman',
            'last_name' => 'Karim',
            'phone' => '+8801000000000',
            'date_of_birth' => '1995-01-01',
            'gender' => 'male',
            'nationality' => 'Bangladeshi',
            'passport_number' => 'P12345678',
            'passport_expiry' => now()->addYears(5)->toDateString(),
            'emergency_contact_name' => 'Hasan',
            'emergency_contact_phone' => '+8801999999999',
            // Must be one of: regular, vegetarian, vegan, kosher, halal
            'meal_preference' => 'regular',
            'seat_preference' => 'window',
            'frequent_flyer' => false,
        ]
    );

    // Generate a unique 6-char booking reference
    do {
        $ref = \Illuminate\Support\Str::upper(\Illuminate\Support\Str::random(6));
    } while (\App\Models\Booking::where('booking_reference', $ref)->exists());

    $booking = \App\Models\Booking::firstOrCreate(
        [
            'passenger_id' => $passenger->id,
            'flight_id' => $flight->id,
            'booked_by_email' => $email,
        ],
        [
            'booking_reference' => $ref,
            'booking_status' => 'confirmed',
            'booking_date' => now(),
            'booking_class' => 'economy',
            'total_amount' => 250.00,
            'payment_status' => 'completed',
            'payment_method' => 'card',
            'travel_insurance' => false,
        ]
    );

    return response()->json([
        'ok' => true,
        'email' => $email,
        'booking_reference' => $booking->booking_reference,
        'message' => 'Seeded a demo booking. Use this email on the Check-In form.',
        'checkin_url' => route('checkin.create', ['terminal' => 2]),
    ]);
})->name('dev.seed_checkin_demo');

// Dev: seed baggage and tracking records for demo booking
Route::get('/dev/seed-baggage-demo', function () {
    if (!app()->environment(['local', 'development'])) {
        abort(403, 'Forbidden');
    }

    $email = 'demo.checkin@example.com';
    $booking = \App\Models\Booking::where('booked_by_email', $email)
        ->latest('id')->first();
    if (!$booking) {
        return response()->json(['ok' => false, 'message' => 'Run /dev/seed-checkin-demo first to create a booking.'], 400);
    }

    $bag = \App\Models\Baggage::firstOrCreate(
        [
            'booking_id' => $booking->id,
            'baggage_tag' => 'LA-'.substr(strtoupper(bin2hex(random_bytes(4))), 0, 6),
        ],
        [
            'weight_kg' => 18.5,
            'baggage_type' => 'checked',
            'special_handling' => null,
            'current_location' => 'Sorting Area',
            'status' => 'in_transit',
        ]
    );

    // Add tracking events
    $now = now();
    \App\Models\BaggageTracking::updateOrCreate(
        ['baggage_id' => $bag->id, 'scan_time' => $now->copy()->subMinutes(30)],
        ['location' => 'Check-in Counter', 'status' => 'checked_in', 'notes' => 'Bag accepted']
    );
    \App\Models\BaggageTracking::updateOrCreate(
        ['baggage_id' => $bag->id, 'scan_time' => $now->copy()->subMinutes(20)],
        ['location' => 'Security Screening', 'status' => 'screened', 'notes' => 'Cleared']
    );
    \App\Models\BaggageTracking::updateOrCreate(
        ['baggage_id' => $bag->id, 'scan_time' => $now->copy()->subMinutes(10)],
        ['location' => 'Sorting Area', 'status' => 'in_transit', 'notes' => 'To Belt']
    );

    return response()->json([
        'ok' => true,
        'message' => 'Baggage and tracking events seeded for demo booking.',
        'baggage_tag' => $bag->baggage_tag,
        'track_url' => route('baggage_track', ['tag' => $bag->baggage_tag]),
    ]);
})->name('dev.seed_baggage_demo');

// Dev: seed a landed flight with belts to show arrivals section and flight-number search
Route::get('/dev/seed-arrivals-demo', function () {
    if (!app()->environment(['local', 'development'])) {
        abort(403, 'Forbidden');
    }

    // Ensure airports/airline/route/aircraft via existing checkin seeder
    $seedCheckin = route('dev.seed_checkin_demo');

    $airline = \App\Models\Airline::firstOrCreate(['name' => 'Lalon Air'], [
        'airline_code' => 'LA', 'icao_code' => 'LAL', 'country' => 'Bangladesh',
        'headquarters' => 'Dhaka', 'website' => 'https://lalonair.example',
        'active' => true, 'contact_info' => 'info@lalonair.example',
    ]);
    $ac = \App\Models\Aircraft::firstOrCreate(['registration_number' => 'S2-A101'], [
        'aircraft_type' => 'A320', 'model' => 'A320-200', 'airline_id' => $airline->id,
        'total_seats' => 180, 'first_class_seats' => 12, 'business_class_seats' => 24,
        'economy_class_seats' => 144, 'manufacturer' => 'Airbus', 'manufacturing_year' => 2019,
        'status' => 'active', 'last_maintenance_date' => now()->subMonths(2), 'next_maintenance_date' => now()->addMonths(4),
    ]);
    $from = \App\Models\Airport::firstOrCreate(['iata_code' => 'CGP'], [
        'icao_code' => 'VGEG','name' => 'Shah Amanat Intl','city' => 'Chattogram','country' => 'Bangladesh','timezone' => 'Asia/Dhaka',
        'latitude' => 22.2496,'longitude' => 91.8133,'elevation_feet' => 12,'total_terminals' => 1,'total_runways' => 1,'international' => true,'active' => true,
    ]);
    $to = \App\Models\Airport::firstOrCreate(['iata_code' => 'DAC'], [
        'icao_code' => 'VGHS','name' => 'Hazrat Shahjalal Intl','city' => 'Dhaka','country' => 'Bangladesh','timezone' => 'Asia/Dhaka',
        'latitude' => 23.8433,'longitude' => 90.3978,'elevation_feet' => 30,'total_terminals' => 3,'total_runways' => 2,'international' => true,'active' => true,
    ]);
    $route = \App\Models\Route::firstOrCreate([
        'origin_airport_id' => $from->id,
        'destination_airport_id' => $to->id,
    ], [
        'distance_km' => 220, 'estimated_duration_minutes' => 45, 'active' => true,
    ]);

    $flight = \App\Models\Flight::updateOrCreate([
        'flight_number' => 'LA220', 'flight_date' => today(),
    ], [
        'airline_id' => $airline->id, 'aircraft_id' => $ac->id, 'route_id' => $route->id,
        'scheduled_departure' => '09:00', 'scheduled_arrival' => '09:50',
        'actual_departure' => '09:10', 'actual_arrival' => '10:05',
        'status' => 'landed', 'departure_gate' => 'C1', 'arrival_gate' => 'Belt A',
        'available_seats' => 10, 'base_price' => 200.00,
    ]);

    // Create a couple of bags on belts
    $pax = \App\Models\Passenger::firstOrCreate(['email' => 'belt.demo1@example.com'], [
        'first_name' => 'Belt', 'last_name' => 'Demo1', 'phone' => '+8801000000001',
        'date_of_birth' => '1990-02-02', 'gender' => 'male', 'nationality' => 'Bangladeshi',
        'passport_number' => 'P00000001', 'passport_expiry' => now()->addYears(5)->toDateString(),
        'emergency_contact_name' => 'E1', 'emergency_contact_phone' => '+8801111111111',
        'meal_preference' => 'regular', 'seat_preference' => 'aisle', 'frequent_flyer' => false,
    ]);
    $booking = \App\Models\Booking::firstOrCreate([
        'passenger_id' => $pax->id, 'flight_id' => $flight->id, 'booked_by_email' => 'belt.demo1@example.com'
    ], [
        'booking_reference' => 'BELT01', 'booking_status' => 'confirmed', 'booking_date' => now(),
        'booking_class' => 'economy', 'total_amount' => 200.00, 'payment_status' => 'completed', 'payment_method' => 'card', 'travel_insurance' => false,
    ]);
    $beltBag1 = \App\Models\Baggage::updateOrCreate([
        'booking_id' => $booking->id, 'baggage_tag' => 'LA-BELT01'
    ], [
        'weight_kg' => 20.0, 'baggage_type' => 'checked', 'current_location' => 'Belt A', 'status' => 'on_belt'
    ]);

    $pax2 = \App\Models\Passenger::firstOrCreate(['email' => 'belt.demo2@example.com'], [
        'first_name' => 'Belt', 'last_name' => 'Demo2', 'phone' => '+8801000000002',
        'date_of_birth' => '1988-03-03', 'gender' => 'female', 'nationality' => 'Bangladeshi',
        'passport_number' => 'P00000002', 'passport_expiry' => now()->addYears(5)->toDateString(),
        'emergency_contact_name' => 'E2', 'emergency_contact_phone' => '+8801222222222',
        'meal_preference' => 'regular', 'seat_preference' => 'window', 'frequent_flyer' => false,
    ]);
    $booking2 = \App\Models\Booking::firstOrCreate([
        'passenger_id' => $pax2->id, 'flight_id' => $flight->id, 'booked_by_email' => 'belt.demo2@example.com'
    ], [
        'booking_reference' => 'BELT02', 'booking_status' => 'confirmed', 'booking_date' => now(),
        'booking_class' => 'economy', 'total_amount' => 200.00, 'payment_status' => 'completed', 'payment_method' => 'card', 'travel_insurance' => false,
    ]);
    $beltBag2 = \App\Models\Baggage::updateOrCreate([
        'booking_id' => $booking2->id, 'baggage_tag' => 'LA-BELT02'
    ], [
        'weight_kg' => 19.2, 'baggage_type' => 'checked', 'current_location' => 'Belt B', 'status' => 'on_belt'
    ]);

    return response()->json([
        'ok' => true,
        'message' => 'Arrivals demo seeded with landed flight LA220 and belts A/B.',
        'flight' => 'LA220',
        'belts' => ['Belt A', 'Belt B'],
        'try' => [
            'search_flight_url' => route('baggage_track', ['flight' => 'LA220']),
        ],
    ]);
})->name('dev.seed_arrivals_demo');
