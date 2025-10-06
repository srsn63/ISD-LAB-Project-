<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Stores crew member information and their qualifications
     */
    public function up(): void
    {
        Schema::create('crew_members', function (Blueprint $table) {
            $table->id();
            $table->string('employee_id')->unique();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->unique();
            $table->string('phone');
            $table->enum('position', ['captain', 'first_officer', 'flight_engineer', 'cabin_crew_chief', 'flight_attendant']);
            $table->foreignId('airline_id')->constrained();
            $table->json('aircraft_certifications')->nullable(); // Aircraft types they're certified for
            $table->date('license_expiry');
            $table->integer('flight_hours')->default(0);
            $table->date('last_medical_checkup')->nullable();
            $table->enum('status', ['active', 'on_leave', 'suspended', 'retired'])->default('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('crew_members');
    }
};