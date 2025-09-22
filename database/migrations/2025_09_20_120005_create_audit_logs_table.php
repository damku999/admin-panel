<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->string('auditable_type'); // User, Customer, Policy, etc.
            $table->unsignedBigInteger('auditable_id')->nullable();
            $table->string('actor_type')->nullable(); // User, System, API
            $table->unsignedBigInteger('actor_id')->nullable();
            $table->string('event'); // created, updated, deleted, login, logout, etc.
            $table->string('event_category'); // authentication, authorization, data_access, system
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->json('metadata')->nullable(); // IP, user agent, risk score, etc.
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->string('session_id')->nullable();
            $table->string('request_id')->nullable();
            $table->integer('risk_score')->default(0); // 0-100 risk scoring
            $table->string('risk_level')->default('low'); // low, medium, high, critical
            $table->json('risk_factors')->nullable(); // Array of risk indicators
            $table->boolean('is_suspicious')->default(false);
            $table->string('location_country')->nullable();
            $table->string('location_city')->nullable();
            $table->decimal('location_lat', 10, 8)->nullable();
            $table->decimal('location_lng', 11, 8)->nullable();
            $table->timestamp('occurred_at');
            $table->timestamps();

            $table->index(['auditable_type', 'auditable_id'], 'audit_logs_auditable_idx');
            $table->index(['actor_type', 'actor_id'], 'audit_logs_actor_idx');
            $table->index(['event', 'event_category'], 'audit_logs_event_idx');
            $table->index(['occurred_at', 'risk_level'], 'audit_logs_time_risk_idx');
            $table->index(['is_suspicious', 'risk_score'], 'audit_logs_security_idx');
            $table->index(['ip_address', 'occurred_at'], 'audit_logs_ip_time_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};