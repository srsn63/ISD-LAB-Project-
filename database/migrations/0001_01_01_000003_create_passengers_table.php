<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Stores passenger information including personal details, contact info, and travel preferences
     */
    public function up(): void
    {
        Schema::create('passengers', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->unique();
            $table->string('phone');
            $table->date('date_of_birth');
            $table->enum('gender', ['male', 'female', 'other']);
            $table->string('nationality');
            $table->string('passport_number')->unique();
            $table->date('passport_expiry');
            $table->string('emergency_contact_name');
            $table->string('emergency_contact_phone');
            $table->enum('meal_preference', ['regular', 'vegetarian', 'vegan', 'kosher', 'halal'])->default('regular');
            $table->enum('seat_preference', ['window', 'aisle', 'middle', 'no_preference'])->default('no_preference');
            $table->boolean('frequent_flyer')->default(false);
            $table->string('frequent_flyer_number')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('passengers');
    }
};