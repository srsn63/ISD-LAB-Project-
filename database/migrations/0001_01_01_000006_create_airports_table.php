<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Airport information including codes, location, and operational data
     */
    public function up(): void
    {
        Schema::create('airports', function (Blueprint $table) {
            $table->id();
            $table->string('iata_code', 3)->unique(); // JFK, LAX, LHR
            $table->string('icao_code', 4)->unique(); // KJFK, KLAX, EGLL
            $table->string('name');
            $table->string('city');
            $table->string('country');
            $table->string('timezone');
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->integer('elevation_feet')->nullable(); // Airport elevation
            $table->integer('total_terminals')->default(1);
            $table->integer('total_runways')->default(1);
            $table->boolean('international')->default(false);
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('airports');
    }
};