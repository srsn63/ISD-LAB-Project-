<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Stores seat assignments and availability for each flight
     */
    public function up(): void
    {
        Schema::create('seats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('aircraft_id')->constrained();
            $table->string('seat_number'); // 1A, 12F, 23C, etc.
            $table->enum('seat_class', ['first', 'business', 'economy']);
            $table->enum('seat_type', ['window', 'aisle', 'middle']);
            $table->boolean('is_available')->default(true);
            $table->boolean('has_extra_legroom')->default(false);
            $table->decimal('extra_fee', 8, 2)->default(0); // Additional cost for premium seats
            $table->timestamps();
            
            // Ensure unique seat numbers per aircraft
            $table->unique(['aircraft_id', 'seat_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seats');
    }
};