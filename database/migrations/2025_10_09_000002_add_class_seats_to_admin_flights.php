<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('admin_flights', function (Blueprint $table) {
            $table->unsignedInteger('first_class_seats')->default(0)->after('seats');
            $table->unsignedInteger('business_class_seats')->default(0)->after('first_class_seats');
            $table->unsignedInteger('economy_class_seats')->default(0)->after('business_class_seats');
        });

        // Backfill existing rows: set defaults based on total seats
        // First: 20, Business: 40, Economy: seats - 60 (not less than 0)
        DB::statement('UPDATE admin_flights SET first_class_seats = 20, business_class_seats = 40, economy_class_seats = GREATEST(seats - 60, 0)');
    }

    public function down(): void
    {
        Schema::table('admin_flights', function (Blueprint $table) {
            $table->dropColumn(['first_class_seats', 'business_class_seats', 'economy_class_seats']);
        });
    }
};
