<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('baggage_trackings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('baggage_id')->constrained('baggages')->onDelete('cascade');
            $table->string('location');
            $table->string('status');
            $table->timestamp('scan_time');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index(['baggage_id', 'scan_time']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('baggage_trackings');
    }
};
