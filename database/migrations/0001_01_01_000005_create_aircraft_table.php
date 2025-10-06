<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Stores aircraft information including specifications and seating configurations
     */
    public function up(): void
    {
        Schema::create('aircraft', function (Blueprint $table) {
            $table->id();
            $table->string('registration_number')->unique(); // Tail number like 'N123AA'
            $table->string('aircraft_type'); // Boeing 737, Airbus A320, etc.
            $table->string('model'); // Specific model variant
            $table->foreignId('airline_id')->constrained()->onDelete('cascade');
            $table->integer('total_seats');
            $table->integer('first_class_seats')->default(0);
            $table->integer('business_class_seats')->default(0);
            $table->integer('economy_class_seats');
            $table->string('manufacturer'); // Boeing, Airbus, etc.
            $table->year('manufacturing_year');
            $table->enum('status', ['active', 'maintenance', 'retired'])->default('active');
            $table->date('last_maintenance_date')->nullable();
            $table->date('next_maintenance_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('aircraft');
    }
};