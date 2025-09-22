<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('api_keys', function (Blueprint $table) {
            $table->id();
            $table->string('keyable_type'); // User, Customer, etc.
            $table->unsignedBigInteger('keyable_id');
            $table->string('name'); // Human readable name
            $table->string('key', 80)->unique(); // The actual API key
            $table->string('secret', 100)->nullable(); // Optional secret for HMAC
            $table->json('abilities')->nullable(); // Scoped permissions
            $table->json('restrictions')->nullable(); // IP restrictions, etc.
            $table->integer('rate_limit')->default(1000); // Requests per hour
            $table->integer('rate_limit_window')->default(3600); // Window in seconds
            $table->timestamp('last_used_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['keyable_type', 'keyable_id'], 'api_keys_keyable_idx');
            $table->index(['key', 'is_active'], 'api_keys_key_active_idx');
            $table->index(['expires_at', 'is_active'], 'api_keys_expiry_idx');
        });

        // API key usage tracking
        Schema::create('api_key_usage', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('api_key_id');
            $table->string('endpoint');
            $table->string('method');
            $table->string('ip_address');
            $table->integer('response_code');
            $table->json('request_data')->nullable();
            $table->json('response_data')->nullable();
            $table->decimal('response_time', 8, 3)->nullable(); // in seconds
            $table->timestamp('requested_at');
            $table->timestamps();

            $table->foreign('api_key_id')->references('id')->on('api_keys')->onDelete('cascade');
            $table->index(['api_key_id', 'requested_at'], 'api_usage_key_time_idx');
            $table->index(['endpoint', 'method'], 'api_usage_endpoint_idx');
            $table->index(['ip_address', 'requested_at'], 'api_usage_ip_time_idx');
        });

        // Rate limiting tracking
        Schema::create('rate_limit_attempts', function (Blueprint $table) {
            $table->id();
            $table->string('identifier'); // API key, IP address, user ID
            $table->string('identifier_type'); // api_key, ip, user
            $table->string('endpoint');
            $table->integer('attempts')->default(1);
            $table->timestamp('window_start');
            $table->timestamp('last_attempt');
            $table->timestamps();

            $table->index(['identifier', 'identifier_type', 'endpoint'], 'rate_limit_identifier_idx');
            $table->index(['window_start', 'last_attempt'], 'rate_limit_time_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rate_limit_attempts');
        Schema::dropIfExists('api_key_usage');
        Schema::dropIfExists('api_keys');
    }
};