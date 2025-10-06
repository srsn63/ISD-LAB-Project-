<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Tracks passenger check-in records and boarding pass information
     */
    public function up(): void
    {
        Schema::create('check_ins', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->onDelete('cascade');
            $table->datetime('check_in_time');
            $table->enum('check_in_method', ['online', 'mobile', 'kiosk', 'counter']);
            $table->string('boarding_pass_number')->unique();
            $table->string('seat_number'); // Final confirmed seat
            $table->string('gate')->nullable();
            $table->time('boarding_time')->nullable();
            $table->boolean('priority_boarding')->default(false);
            $table->enum('status', ['checked_in', 'boarded', 'no_show'])->default('checked_in');
            $table->text('special_assistance')->nullable(); // wheelchair, unaccompanied minor, etc.
            $table->timestamps();
            
            $table->index(['booking_id']);
            $table->index(['boarding_pass_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('check_ins');
    }
};