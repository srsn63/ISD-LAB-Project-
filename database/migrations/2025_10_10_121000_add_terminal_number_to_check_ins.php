<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('check_ins', function (Blueprint $table) {
            $table->unsignedTinyInteger('terminal_number')->default(1)->after('status'); // 1..5
            $table->index('terminal_number');
        });
    }

    public function down(): void
    {
        Schema::table('check_ins', function (Blueprint $table) {
            $table->dropIndex(['terminal_number']);
            $table->dropColumn('terminal_number');
        });
    }
};
