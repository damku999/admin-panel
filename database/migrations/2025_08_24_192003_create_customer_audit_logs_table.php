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
        Schema::create('customer_audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->onDelete('cascade');
            $table->string('action', 100); // login, logout, view_policy, download_document, etc.
            $table->string('resource_type', 50)->nullable(); // policy, profile, family_data
            $table->unsignedBigInteger('resource_id')->nullable(); // ID of the resource being accessed
            $table->text('description')->nullable(); // Human readable description
            $table->json('metadata')->nullable(); // Additional data (IP, user agent, etc.)
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->string('session_id', 191)->nullable();
            $table->boolean('success')->default(true);
            $table->text('failure_reason')->nullable();
            $table->timestamps();
            
            // Indexes for performance
            $table->index(['customer_id', 'action', 'created_at']);
            $table->index(['action', 'created_at']);
            $table->index(['resource_type', 'resource_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_audit_logs');
    }
};
