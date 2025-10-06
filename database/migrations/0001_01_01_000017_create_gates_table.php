<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Manages airport gate information and availability
     */
    public function up(): void
    {
        Schema::create('gates', function (Blueprint $table) {
            $table->id();
            $table->string('gate_number'); // A1, B12, C5, etc.
            $table->string('terminal'); // Terminal A, B, C, etc.
            $table->foreignId('airport_id')->constrained();
            $table->enum('gate_type', ['domestic', 'international', 'both'])->default('both');
            $table->enum('aircraft_size', ['small', 'medium', 'large', 'extra_large']); // Compatible aircraft sizes
            $table->boolean('jet_bridge')->default(false); // Has jet bridge connection
            $table->boolean('available')->default(true);
            $table->text('facilities')->nullable(); // Available facilities like shops, lounges
            $table->timestamps();
            
            // Ensure unique gate numbers per airport
            $table->unique(['airport_id', 'gate_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gates');
    }
};