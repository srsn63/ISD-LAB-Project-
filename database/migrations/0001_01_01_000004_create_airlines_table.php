<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Manages airline company information and their operational details
     */
    public function up(): void
    {
        Schema::create('airlines', function (Blueprint $table) {
            $table->id();
            $table->string('airline_code', 3)->unique(); // IATA code like 'BA', 'AA'
            $table->string('icao_code', 4)->unique(); // ICAO code like 'BAW', 'AAL'
            $table->string('name');
            $table->string('country');
            $table->string('headquarters');
            $table->string('website')->nullable();
            $table->string('logo_url')->nullable();
            $table->boolean('active')->default(true);
            $table->text('contact_info')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('airlines');
    }
};