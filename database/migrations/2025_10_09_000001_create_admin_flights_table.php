<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admin_flights', function (Blueprint $table) {
            $table->id();
            $table->string('flight_number');
            $table->string('airline');
            $table->enum('status', ['scheduled','boarding','departed','delayed','cancelled'])->default('scheduled');
            $table->string('origin');
            $table->string('destination');
            $table->dateTime('departure_at');
            $table->dateTime('arrival_at');
            $table->decimal('price', 10, 2);
            $table->unsignedInteger('seats');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admin_flights');
    }
};
