<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Maintains activity logs for audit trails and system monitoring
     */
    public function up(): void
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->string('user_email')->nullable(); // Who performed the action
            $table->string('action'); // What was done (created, updated, deleted, etc.)
            $table->string('model_type')->nullable(); // Which model was affected
            $table->unsignedBigInteger('model_id')->nullable(); // ID of the affected record
            $table->json('old_values')->nullable(); // Previous data
            $table->json('new_values')->nullable(); // New data
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->enum('severity', ['info', 'warning', 'error', 'critical'])->default('info');
            $table->text('description')->nullable(); // Human readable description
            $table->timestamps();
            
            $table->index(['user_email', 'created_at']);
            $table->index(['model_type', 'model_id']);
            $table->index(['action', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};