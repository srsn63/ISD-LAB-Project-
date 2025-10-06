<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Main booking records containing passenger and flight information
     */
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string('booking_reference', 6)->unique(); // PNR like 'ABC123'
            $table->foreignId('passenger_id')->constrained();
            $table->foreignId('flight_id')->constrained();
            $table->enum('booking_status', ['confirmed', 'pending', 'cancelled', 'refunded'])->default('pending');
            $table->datetime('booking_date');
            $table->enum('booking_class', ['economy', 'business', 'first'])->default('economy');
            $table->decimal('total_amount', 10, 2);
            $table->string('payment_status')->default('pending'); // pending, completed, failed, refunded
            $table->string('payment_method')->nullable(); // credit_card, debit_card, paypal, etc.
            $table->text('special_requests')->nullable();
            $table->boolean('travel_insurance')->default(false);
            $table->string('booked_by_email'); // Who made the booking
            $table->timestamps();
            
            $table->index(['booking_reference']);
            $table->index(['passenger_id', 'booking_status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};