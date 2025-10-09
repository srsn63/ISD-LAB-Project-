<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ticket_bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admin_flight_id')->constrained('admin_flights')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('booked_by_email');
            $table->enum('seat_class', ['economy','business','first'])->default('economy');
            $table->unsignedInteger('quantity');
            $table->decimal('unit_price', 10, 2);
            $table->decimal('total_amount', 10, 2);
            $table->timestamps();

            $table->index(['admin_flight_id']);
            $table->index(['booked_by_email']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ticket_bookings');
    }
};
