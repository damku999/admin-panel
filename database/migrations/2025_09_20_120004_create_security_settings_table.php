<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
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

            $table->index(['settingable_type', 'settingable_id'], 'security_settings_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('security_settings');
    }
};