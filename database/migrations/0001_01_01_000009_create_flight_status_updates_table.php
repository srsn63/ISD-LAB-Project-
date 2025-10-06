<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Tracks real-time flight status updates and notifications
     */
    public function up(): void
    {
        Schema::create('flight_status_updates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('flight_id')->constrained()->onDelete('cascade');
            $table->enum('previous_status', ['scheduled', 'boarding', 'departed', 'in_flight', 'landed', 'cancelled', 'delayed'])->nullable();
            $table->enum('current_status', ['scheduled', 'boarding', 'departed', 'in_flight', 'landed', 'cancelled', 'delayed']);
            $table->text('update_message')->nullable(); // Human readable status message
            $table->timestamp('updated_at');
            $table->string('updated_by')->nullable(); // System or staff member
            
            $table->index(['flight_id', 'updated_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('flight_status_updates');
    }
};