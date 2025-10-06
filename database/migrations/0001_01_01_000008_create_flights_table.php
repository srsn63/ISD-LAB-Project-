<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Stores individual flight instances with schedules and current status
     */
    public function up(): void
    {
        Schema::create('flights', function (Blueprint $table) {
            $table->id();
            $table->string('flight_number'); // AA123, BA456
            $table->foreignId('airline_id')->constrained();
            $table->foreignId('aircraft_id')->constrained();
            $table->foreignId('route_id')->constrained();
            $table->date('flight_date');
            $table->time('scheduled_departure');
            $table->time('scheduled_arrival');
            $table->time('actual_departure')->nullable();
            $table->time('actual_arrival')->nullable();
            $table->enum('status', ['scheduled', 'boarding', 'departed', 'in_flight', 'landed', 'cancelled', 'delayed'])->default('scheduled');
            $table->string('departure_gate')->nullable();
            $table->string('arrival_gate')->nullable();
            $table->text('delay_reason')->nullable();
            $table->integer('available_seats');
            $table->decimal('base_price', 10, 2); // Starting ticket price
            $table->timestamps();
            
            // Composite index for faster flight searches
            $table->index(['flight_date', 'status']);
            $table->index(['airline_id', 'flight_number', 'flight_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('flights');
    }
};