<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Manages system notifications for passengers and staff
     */
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->string('recipient_email'); // Who receives the notification
            $table->enum('type', ['flight_delay', 'flight_cancellation', 'gate_change', 'boarding_call', 'payment_confirmation', 'booking_confirmation', 'check_in_reminder']);
            $table->string('subject');
            $table->text('message');
            $table->foreignId('flight_id')->nullable()->constrained(); // Related flight if applicable
            $table->foreignId('booking_id')->nullable()->constrained(); // Related booking if applicable
            $table->enum('status', ['pending', 'sent', 'failed'])->default('pending');
            $table->datetime('scheduled_for')->nullable(); // When to send the notification
            $table->datetime('sent_at')->nullable();
            $table->text('error_message')->nullable(); // If sending failed
            $table->json('metadata')->nullable(); // Additional data like SMS numbers, push tokens
            $table->timestamps();
            
            $table->index(['recipient_email', 'status']);
            $table->index(['scheduled_for', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};