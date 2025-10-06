<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Manages baggage information and tracking throughout the journey
     */
    public function up(): void
    {
        Schema::create('baggage', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->onDelete('cascade');
            $table->string('baggage_tag')->unique(); // Unique baggage identification
            $table->enum('baggage_type', ['carry_on', 'checked', 'special']); // Different baggage categories
            $table->decimal('weight_kg', 5, 2); // Baggage weight in kilograms
            $table->string('dimensions')->nullable(); // Length x Width x Height
            $table->enum('status', ['checked_in', 'loaded', 'in_transit', 'arrived', 'collected', 'lost'])->default('checked_in');
            $table->text('contents_description')->nullable(); // Description of baggage contents
            $table->boolean('fragile')->default(false);
            $table->decimal('excess_fee', 8, 2)->default(0); // Fee for overweight/extra baggage
            $table->string('current_location')->nullable(); // Current baggage location
            $table->datetime('last_scanned_at')->nullable();
            $table->timestamps();
            
            $table->index(['baggage_tag']);
            $table->index(['booking_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('baggage');
    }
};