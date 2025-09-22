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
        Schema::table('audit_logs', function (Blueprint $table) {
            // Add missing columns based on the error message
            $table->string('auditable_type')->after('id');
            $table->unsignedBigInteger('auditable_id')->after('auditable_type');
            $table->string('event')->after('action');
            $table->string('event_category')->after('event');
            $table->json('old_values')->nullable()->after('properties');
            $table->json('new_values')->nullable()->after('old_values');
            $table->json('metadata')->nullable()->after('new_values');
            $table->string('session_id')->nullable()->after('user_agent');
            $table->string('request_id')->nullable()->after('session_id');
            $table->decimal('risk_score', 3, 2)->nullable()->after('severity');
            $table->string('risk_level')->nullable()->after('risk_score');
            $table->json('risk_factors')->nullable()->after('risk_level');
            $table->boolean('is_suspicious')->default(false)->after('risk_factors');

            // Add indexes for new columns
            $table->index(['auditable_type', 'auditable_id']);
            $table->index(['event']);
            $table->index(['event_category']);
            $table->index(['risk_level']);
            $table->index(['is_suspicious']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('audit_logs', function (Blueprint $table) {
            // Drop the added columns
            $table->dropColumn([
                'auditable_type',
                'auditable_id',
                'event',
                'event_category',
                'old_values',
                'new_values',
                'metadata',
                'session_id',
                'request_id',
                'risk_score',
                'risk_level',
                'risk_factors',
                'is_suspicious'
            ]);
        });
    }
};
