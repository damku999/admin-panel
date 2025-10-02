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
        Schema::table('quotation_companies', function (Blueprint $table) {
            // Add missing columns after quote_number
            $table->string('policy_type', 125)->nullable()->after('quote_number');
            $table->integer('policy_tenure_years')->default(1)->after('policy_type');
            $table->decimal('idv_vehicle', 12, 2)->nullable()->after('policy_tenure_years');
            $table->decimal('idv_trailer', 12, 2)->default(0.00)->after('idv_vehicle');
            $table->decimal('idv_cng_lpg_kit', 12, 2)->default(0.00)->after('idv_trailer');
            $table->decimal('idv_electrical_accessories', 12, 2)->default(0.00)->after('idv_cng_lpg_kit');
            $table->decimal('idv_non_electrical_accessories', 12, 2)->default(0.00)->after('idv_electrical_accessories');
            $table->decimal('total_idv', 12, 2)->nullable()->after('idv_non_electrical_accessories');

            // Add recommendation_note after is_recommended
            $table->text('recommendation_note')->nullable()->after('is_recommended');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quotation_companies', function (Blueprint $table) {
            $table->dropColumn([
                'policy_type',
                'policy_tenure_years',
                'idv_vehicle',
                'idv_trailer',
                'idv_cng_lpg_kit',
                'idv_electrical_accessories',
                'idv_non_electrical_accessories',
                'total_idv',
                'recommendation_note',
            ]);
        });
    }
};
