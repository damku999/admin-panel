<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customer_audit_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id');
            $table->string('action');
            $table->string('resource_type');
            $table->unsignedBigInteger('resource_id')->nullable();
            $table->text('description')->nullable();
            $table->text('metadata')->nullable();
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->string('session_id')->nullable();
            $table->boolean('success')->default(true);
            $table->text('failure_reason')->nullable();
            $table->timestamps();

            $table->index(['customer_id', 'action', 'created_at'], 'customer_action_created_idx');
            $table->index(['action', 'created_at'], 'action_created_idx');
            $table->index(['resource_type', 'resource_id'], 'resource_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customer_audit_logs');
    }
};