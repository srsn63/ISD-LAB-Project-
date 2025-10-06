<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Handles payment transactions and financial records for bookings
     */
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->onDelete('cascade');
            $table->string('transaction_id')->unique(); // Payment gateway transaction ID
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('USD');
            $table->enum('payment_method', ['credit_card', 'debit_card', 'paypal', 'bank_transfer', 'cash']);
            $table->enum('payment_status', ['pending', 'processing', 'completed', 'failed', 'cancelled', 'refunded']);
            $table->string('gateway_reference')->nullable(); // Reference from payment processor
            $table->text('payment_details')->nullable(); // Encrypted card details or other info
            $table->datetime('processed_at')->nullable();
            $table->text('failure_reason')->nullable();
            $table->decimal('refund_amount', 10, 2)->default(0);
            $table->datetime('refunded_at')->nullable();
            $table->timestamps();
            
            $table->index(['booking_id', 'payment_status']);
            $table->index(['transaction_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};