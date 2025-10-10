<?php

// Seed demo data for baggage tracking with belts
use App\Models\Airline;
use App\Models\Aircraft;
use App\Models\Airport;
use App\Models\Route;
use App\Models\Flight;
use App\Models\Passenger;
use App\Models\Booking;
use App\Models\Baggage;

// Create airline
$airline = Airline::firstOrCreate(['name' => 'Lalon Air'], [
    'airline_code' => 'LA', 'icao_code' => 'LAL', 'country' => 'Bangladesh',
    'headquarters' => 'Dhaka', 'website' => 'https://lalonair.example',
    'active' => true, 'contact_info' => 'info@lalonair.example',
]);

// Create aircraft
$ac = Aircraft::firstOrCreate(['registration_number' => 'S2-A101'], [
    'aircraft_type' => 'A320', 'model' => 'A320-200', 'airline_id' => $airline->id,
    'total_seats' => 180, 'first_class_seats' => 12, 'business_class_seats' => 24,
    'economy_class_seats' => 144, 'manufacturer' => 'Airbus', 'manufacturing_year' => 2019,
    'status' => 'active', 'last_maintenance_date' => now()->subMonths(2), 'next_maintenance_date' => now()->addMonths(4),
]);

// Create airports
$from = Airport::firstOrCreate(['iata_code' => 'CGP'], [
    'icao_code' => 'VGEG','name' => 'Shah Amanat Intl','city' => 'Chattogram','country' => 'Bangladesh','timezone' => 'Asia/Dhaka',
    'latitude' => 22.2496,'longitude' => 91.8133,'elevation_feet' => 12,'total_terminals' => 1,'total_runways' => 1,'international' => true,'active' => true,
]);
$to = Airport::firstOrCreate(['iata_code' => 'DAC'], [
    'icao_code' => 'VGHS','name' => 'Hazrat Shahjalal Intl','city' => 'Dhaka','country' => 'Bangladesh','timezone' => 'Asia/Dhaka',
    'latitude' => 23.8433,'longitude' => 90.3978,'elevation_feet' => 30,'total_terminals' => 3,'total_runways' => 2,'international' => true,'active' => true,
]);

// Create route
$route = Route::firstOrCreate([
    'origin_airport_id' => $from->id,
    'destination_airport_id' => $to->id,
], [
    'distance_km' => 220, 'estimated_duration_minutes' => 45, 'active' => true,
]);

// Create landed flight
$flight = Flight::updateOrCreate([
    'flight_number' => 'LA220', 'flight_date' => today(),
], [
    'airline_id' => $airline->id, 'aircraft_id' => $ac->id, 'route_id' => $route->id,
    'scheduled_departure' => '09:00', 'scheduled_arrival' => '09:50',
    'actual_departure' => '09:10', 'actual_arrival' => '10:05',
    'status' => 'landed', 'departure_gate' => 'C1', 'arrival_gate' => 'Belt A',
    'available_seats' => 10, 'base_price' => 200.00,
]);

// Create passengers and baggage
$pax1 = Passenger::firstOrCreate(['email' => 'belt.demo1@example.com'], [
    'first_name' => 'Belt', 'last_name' => 'Demo1', 'phone' => '+8801111111111',
    'date_of_birth' => '1990-02-02', 'gender' => 'male', 'nationality' => 'Bangladeshi',
    'passport_number' => 'P11111111', 'passport_expiry' => now()->addYears(5)->toDateString(),
    'emergency_contact_name' => 'E1', 'emergency_contact_phone' => '+8801111111111',
    'meal_preference' => 'regular', 'seat_preference' => 'aisle', 'frequent_flyer' => false,
]);

$booking1 = Booking::firstOrCreate([
    'passenger_id' => $pax1->id, 'flight_id' => $flight->id, 'booked_by_email' => 'belt.demo1@example.com'
], [
    'booking_reference' => 'BELT01', 'booking_status' => 'confirmed', 'booking_date' => now(),
    'booking_class' => 'economy', 'total_amount' => 200.00, 'payment_status' => 'completed', 'payment_method' => 'card', 'travel_insurance' => false,
]);

$bag1 = Baggage::updateOrCreate([
    'booking_id' => $booking1->id, 'baggage_tag' => 'LA-BELT01'
], [
    'weight_kg' => 20.0, 'baggage_type' => 'checked', 'current_location' => 'Belt A', 'status' => 'on_belt'
]);

$pax2 = Passenger::firstOrCreate(['email' => 'belt.demo2@example.com'], [
    'first_name' => 'Belt', 'last_name' => 'Demo2', 'phone' => '+8801222222222',
    'date_of_birth' => '1988-03-03', 'gender' => 'female', 'nationality' => 'Bangladeshi',
    'passport_number' => 'P22222222', 'passport_expiry' => now()->addYears(5)->toDateString(),
    'emergency_contact_name' => 'E2', 'emergency_contact_phone' => '+8801222222222',
    'meal_preference' => 'regular', 'seat_preference' => 'window', 'frequent_flyer' => false,
]);

$booking2 = Booking::firstOrCreate([
    'passenger_id' => $pax2->id, 'flight_id' => $flight->id, 'booked_by_email' => 'belt.demo2@example.com'
], [
    'booking_reference' => 'BELT02', 'booking_status' => 'confirmed', 'booking_date' => now(),
    'booking_class' => 'economy', 'total_amount' => 200.00, 'payment_status' => 'completed', 'payment_method' => 'card', 'travel_insurance' => false,
]);

$bag2 = Baggage::updateOrCreate([
    'booking_id' => $booking2->id, 'baggage_tag' => 'LA-BELT02'
], [
    'weight_kg' => 19.2, 'baggage_type' => 'checked', 'current_location' => 'Belt B', 'status' => 'on_belt'
]);

echo "Demo data created successfully!\n";
echo "Flight: LA220 (landed)\n";
echo "Baggage tags: LA-BELT01 (Belt A), LA-BELT02 (Belt B)\n";
echo "Test URL: http://127.0.0.1:8000/baggage-track?flight=LA220\n";