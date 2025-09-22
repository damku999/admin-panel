<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Enhanced device tracking table
        Schema::create('device_tracking', function (Blueprint $table) {
            $table->id();
            $table->string('trackable_type'); // User, Customer, etc.
            $table->unsignedBigInteger('trackable_id');
            $table->string('device_id')->unique(); // Unique device fingerprint
            $table->string('device_name')->nullable(); // User-defined name
            $table->string('device_type'); // mobile, desktop, tablet
            $table->string('browser'); // Chrome, Firefox, Safari, etc.
            $table->string('browser_version')->nullable();
            $table->string('operating_system'); // Windows, macOS, iOS, Android
            $table->string('os_version')->nullable();
            $table->string('platform'); // web, mobile_app, api
            $table->json('screen_resolution')->nullable(); // {width: 1920, height: 1080}
            $table->json('hardware_info')->nullable(); // CPU, memory, etc.
            $table->string('user_agent');
            $table->json('fingerprint_data'); // Canvas, WebGL, fonts, etc.
            $table->integer('trust_score')->default(0); // 0-100 trust rating
            $table->boolean('is_trusted')->default(false);
            $table->timestamp('first_seen_at');
            $table->timestamp('last_seen_at');
            $table->timestamp('trusted_at')->nullable();
            $table->timestamp('trust_expires_at')->nullable();
            $table->json('location_history')->nullable(); // Array of locations
            $table->json('ip_history')->nullable(); // Array of IP addresses
            $table->integer('login_count')->default(0);
            $table->integer('failed_login_attempts')->default(0);
            $table->timestamp('last_failed_login_at')->nullable();
            $table->boolean('is_blocked')->default(false);
            $table->string('blocked_reason')->nullable();
            $table->timestamp('blocked_at')->nullable();
            $table->timestamps();

            $table->index(['trackable_type', 'trackable_id'], 'device_tracking_trackable_idx');
            $table->index(['device_id', 'is_trusted'], 'device_tracking_device_trust_idx');
            $table->index(['trust_score', 'is_trusted'], 'device_tracking_trust_score_idx');
            $table->index(['last_seen_at', 'is_trusted'], 'device_tracking_activity_idx');
            $table->index(['is_blocked', 'blocked_at'], 'device_tracking_blocked_idx');
        });

        // Device sessions tracking
        Schema::create('device_sessions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('device_tracking_id');
            $table->string('session_id');
            $table->string('ip_address');
            $table->string('location_country')->nullable();
            $table->string('location_city')->nullable();
            $table->decimal('location_lat', 10, 8)->nullable();
            $table->decimal('location_lng', 11, 8)->nullable();
            $table->timestamp('started_at');
            $table->timestamp('last_activity_at');
            $table->timestamp('ended_at')->nullable();
            $table->integer('duration_seconds')->nullable();
            $table->json('activity_summary')->nullable(); // Pages visited, actions taken
            $table->boolean('is_suspicious')->default(false);
            $table->json('risk_factors')->nullable();
            $table->timestamps();

            $table->foreign('device_tracking_id')->references('id')->on('device_tracking')->onDelete('cascade');
            $table->index(['device_tracking_id', 'started_at'], 'device_sessions_device_time_idx');
            $table->index(['session_id', 'ended_at'], 'device_sessions_session_idx');
            $table->index(['is_suspicious', 'started_at'], 'device_sessions_suspicious_idx');
            $table->index(['ip_address', 'started_at'], 'device_sessions_ip_time_idx');
        });

        // Device security events
        Schema::create('device_security_events', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('device_tracking_id');
            $table->string('event_type'); // login_attempt, trust_granted, trust_revoked, blocked, etc.
            $table->string('event_severity'); // low, medium, high, critical
            $table->text('description');
            $table->json('event_data')->nullable(); // Additional context
            $table->string('ip_address');
            $table->string('user_agent');
            $table->timestamp('occurred_at');
            $table->boolean('is_resolved')->default(false);
            $table->timestamp('resolved_at')->nullable();
            $table->string('resolved_by')->nullable();
            $table->text('resolution_notes')->nullable();
            $table->timestamps();

            $table->foreign('device_tracking_id')->references('id')->on('device_tracking')->onDelete('cascade');
            $table->index(['device_tracking_id', 'occurred_at'], 'device_events_device_time_idx');
            $table->index(['event_type', 'event_severity'], 'device_events_type_severity_idx');
            $table->index(['is_resolved', 'occurred_at'], 'device_events_resolution_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('device_security_events');
        Schema::dropIfExists('device_sessions');
        Schema::dropIfExists('device_tracking');
    }
};