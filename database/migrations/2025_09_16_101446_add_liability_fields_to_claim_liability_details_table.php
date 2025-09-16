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
        Schema::table('claim_liability_details', function (Blueprint $table) {
            // Add fields for Cashless type
            $table->decimal('claim_amount', 12, 2)->nullable()->after('claim_type');
            $table->decimal('salvage_amount', 12, 2)->nullable()->after('claim_amount');
            $table->decimal('less_claim_charge', 12, 2)->nullable()->after('salvage_amount');
            $table->decimal('amount_to_be_paid', 12, 2)->nullable()->after('less_claim_charge');

            // Add fields for Reimbursement type
            $table->decimal('less_salvage_amount', 12, 2)->nullable()->after('amount_to_be_paid');
            $table->decimal('less_deductions', 12, 2)->nullable()->after('less_salvage_amount');
            $table->decimal('claim_amount_received', 12, 2)->nullable()->after('less_deductions');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('claim_liability_details', function (Blueprint $table) {
            $table->dropColumn([
                'claim_amount',
                'salvage_amount',
                'less_claim_charge',
                'amount_to_be_paid',
                'less_salvage_amount',
                'less_deductions',
                'claim_amount_received'
            ]);
        });
    }
};
