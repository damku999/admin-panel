<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Two-factor authentication secrets table
        Schema::create('two_factor_auth', function (Blueprint $table) {
            $table->id();
            $table->morphs('authenticatable'); // Polymorphic relation (user/customer)
            $table->string('secret')->nullable(); // TOTP secret key
            $table->json('recovery_codes')->nullable(); // Backup recovery codes
            $table->timestamp('enabled_at')->nullable(); // When 2FA was enabled
            $table->timestamp('confirmed_at')->nullable(); // When 2FA setup was confirmed
            $table->boolean('is_active')->default(false);
            $table->string('backup_method')->nullable(); // sms, email
            $table->string('backup_destination')->nullable(); // phone/email for backup
            $table->timestamps();

            $table->index(['authenticatable_type', 'authenticatable_id']);
        });

        // Trusted devices table
        Schema::create('trusted_devices', function (Blueprint $table) {
            $table->id();
            $table->morphs('authenticatable'); // Polymorphic relation (user/customer)
            $table->string('device_id')->unique(); // Device fingerprint
            $table->string('device_name'); // User-friendly device name
            $table->string('device_type')->nullable(); // mobile, desktop, tablet
            $table->string('browser')->nullable();
            $table->string('platform')->nullable();
            $table->string('ip_address');
            $table->string('user_agent');
            $table->timestamp('last_used_at');
            $table->timestamp('trusted_at');
            $table->timestamp('expires_at')->nullable(); // Optional device expiration
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['authenticatable_type', 'authenticatable_id']);
            $table->index(['device_id', 'is_active']);
        });

        // 2FA verification attempts table
        Schema::create('two_factor_attempts', function (Blueprint $table) {
            $table->id();
            $table->morphs('authenticatable'); // Polymorphic relation (user/customer)
            $table->string('code_type'); // totp, recovery, sms
            $table->string('ip_address');
            $table->string('user_agent');
            $table->boolean('successful')->default(false);
            $table->string('failure_reason')->nullable();
            $table->timestamp('attempted_at');
            $table->timestamps();

            $table->index(['authenticatable_type', 'authenticatable_id']);
            $table->index(['attempted_at', 'successful']);
        });

        // Security settings table for user preferences
        Schema::create('security_settings', function (Blueprint $table) {
            $table->id();
            $table->morphs('settingable'); // Polymorphic relation (user/customer)
            $table->boolean('two_factor_enabled')->default(false);
            $table->boolean('device_tracking_enabled')->default(true);
            $table->boolean('login_notifications')->default(true);
            $table->boolean('security_alerts')->default(true);
            $table->integer('session_timeout')->default(7200); // seconds
            $table->integer('device_trust_duration')->default(30); // days
            $table->json('notification_preferences')->nullable();
            $table->timestamps();

            $table->index(['settingable_type', 'settingable_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('security_settings');
        Schema::dropIfExists('two_factor_attempts');
        Schema::dropIfExists('trusted_devices');
        Schema::dropIfExists('two_factor_auth');
    }
};