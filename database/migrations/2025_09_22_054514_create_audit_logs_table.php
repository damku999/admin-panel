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
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->string('actor_type')->nullable(); // User or Customer model
            $table->unsignedBigInteger('actor_id')->nullable(); // User/Customer ID
            $table->string('action'); // Action performed (login, 2fa_enable, etc.)
            $table->string('target_type')->nullable(); // Target model type
            $table->unsignedBigInteger('target_id')->nullable(); // Target model ID
            $table->json('properties')->nullable(); // Additional data
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamp('occurred_at');
            $table->string('severity')->default('info'); // info, warning, error
            $table->string('category')->nullable(); // authentication, security, etc.
            $table->timestamps();

            // Indexes for performance
            $table->index(['actor_type', 'actor_id']);
            $table->index(['occurred_at']);
            $table->index(['action']);
            $table->index(['ip_address']);
            $table->index(['severity']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
