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
        Schema::table('claims', function (Blueprint $table) {
            // Remove internal claim_number and related fields only
            $table->dropColumn('claim_number');
            $table->dropColumn('claim_number_assigned');
            $table->dropColumn('claim_number_assigned_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('claims', function (Blueprint $table) {
            // Restore internal claim_number and related fields
            $table->string('claim_number')->nullable()->unique()->after('policy_no');
            $table->boolean('claim_number_assigned')->default(false)->after('document_request_sent_at');
            $table->timestamp('claim_number_assigned_at')->nullable()->after('claim_number_assigned');
        });
    }
};
