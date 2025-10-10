<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('baggages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained();
            $table->string('baggage_tag')->unique();
            $table->decimal('weight_kg', 6, 2)->nullable();
            $table->string('baggage_type')->nullable(); // e.g., checked, cabin, oversized
            $table->string('special_handling')->nullable(); // e.g., fragile, priority
            $table->string('current_location')->nullable();
            $table->string('status')->default('in_transit'); // flexible string status
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('baggages');
    }
};
