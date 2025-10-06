<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Defines flight routes between airports with distance and duration info
     */
    public function up(): void
    {
        Schema::create('routes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('origin_airport_id')->constrained('airports');
            $table->foreignId('destination_airport_id')->constrained('airports');
            $table->integer('distance_km'); // Distance in kilometers
            $table->integer('estimated_duration_minutes'); // Flight time in minutes
            $table->boolean('active')->default(true);
            $table->timestamps();
            
            // Ensure unique route combinations
            $table->unique(['origin_airport_id', 'destination_airport_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('routes');
    }
};