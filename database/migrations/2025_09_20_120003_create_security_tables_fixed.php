<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 2FA verification attempts table
        Schema::create('two_factor_attempts', function (Blueprint $table) {
            $table->id();
            $table->string('authenticatable_type');
            $table->unsignedBigInteger('authenticatable_id');
            $table->string('code_type'); // totp, recovery, sms
            $table->string('ip_address');
            $table->string('user_agent');
            $table->boolean('successful')->default(false);
            $table->string('failure_reason')->nullable();
            $table->timestamp('attempted_at');
            $table->timestamps();

            $table->index(['authenticatable_type', 'authenticatable_id'], '2fa_attempts_auth_idx');
            $table->index(['attempted_at', 'successful'], '2fa_attempts_time_idx');
        });

        // Security settings table for user preferences
        Schema::create('security_settings', function (Blueprint $table) {
            $table->id();
            $table->string('settingable_type');
            $table->unsignedBigInteger('settingable_id');
            $table->boolean('two_factor_enabled')->default(false);
            $table->boolean('device_tracking_enabled')->default(true);
            $table->boolean('login_notifications')->default(true);
            $table->boolean('security_alerts')->default(true);
            $table->integer('session_timeout')->default(7200); // seconds
            $table->integer('device_trust_duration')->default(30); // days
            $table->json('notification_preferences')->nullable();
            $table->timestamps();

            $table->index(['settingable_type', 'settingable_id'], 'security_settings_auth_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('security_settings');
        Schema::dropIfExists('two_factor_attempts');
    }
};