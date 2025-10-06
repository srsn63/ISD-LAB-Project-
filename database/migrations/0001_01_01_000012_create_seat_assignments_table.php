<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Links bookings to specific seat assignments on flights
     */
    public function up(): void
    {
        Schema::create('seat_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->onDelete('cascade');
            $table->foreignId('flight_id')->constrained()->onDelete('cascade');
            $table->foreignId('seat_id')->constrained();
            $table->datetime('assigned_at');
            $table->string('assigned_by')->nullable(); // system, passenger, or staff member
            $table->timestamps();
            
            // Prevent double booking of seats on same flight
            $table->unique(['flight_id', 'seat_id']);
            $table->index(['booking_id', 'flight_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seat_assignments');
    }
};