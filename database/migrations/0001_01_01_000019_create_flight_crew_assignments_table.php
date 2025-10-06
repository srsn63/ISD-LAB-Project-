<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Links crew members to specific flights for duty assignments
     */
    public function up(): void
    {
        Schema::create('flight_crew_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('flight_id')->constrained()->onDelete('cascade');
            $table->foreignId('crew_member_id')->constrained()->onDelete('cascade');
            $table->enum('role', ['captain', 'first_officer', 'flight_engineer', 'cabin_crew_chief', 'flight_attendant']);
            $table->datetime('assigned_at');
            $table->string('assigned_by'); // Who made the assignment
            $table->boolean('standby')->default(false); // Is this a standby assignment
            $table->timestamps();
            
            // Prevent duplicate role assignments per flight
            $table->unique(['flight_id', 'crew_member_id']);
            $table->index(['flight_id', 'role']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('flight_crew_assignments');
    }
};