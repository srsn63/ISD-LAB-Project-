<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Tracks baggage movement and status changes throughout handling process
     */
    public function up(): void
    {
        Schema::create('baggage_tracking', function (Blueprint $table) {
            $table->id();
            $table->foreignId('baggage_id')->constrained()->onDelete('cascade');
            $table->enum('status', ['checked_in', 'loaded', 'in_transit', 'arrived', 'collected', 'lost']);
            $table->string('location'); // Specific location where status was updated
            $table->datetime('scanned_at');
            $table->string('scanned_by')->nullable(); // Staff member or system
            $table->text('notes')->nullable(); // Additional handling notes
            $table->timestamps();
            
            $table->index(['baggage_id', 'scanned_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('baggage_tracking');
    }
};