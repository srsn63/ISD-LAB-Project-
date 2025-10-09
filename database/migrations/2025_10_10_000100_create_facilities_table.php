<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('facilities', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // e.g., checkin, kiosk, baggage_drop, security, immigration, customs, gate, duty_free, restaurant, lounge, jet_bridge, apron, baggage_belt, info, currency, atm, medical, prayer, car_rental, taxi, shuttle
            $table->string('code')->nullable(); // A1, B2, CI-01, etc.
            $table->string('name');
            $table->enum('status', ['open', 'active', 'closed', 'busy', 'final_call', 'boarding', 'on_time'])->default('open');
            $table->unsignedInteger('today_count')->default(0); // pax/bags served etc.
            $table->json('meta')->nullable(); // arbitrary extra data e.g., wait_time, occupancy
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('facilities');
    }
};
