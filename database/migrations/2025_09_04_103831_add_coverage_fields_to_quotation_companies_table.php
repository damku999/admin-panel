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
            if (!Schema::hasColumn('quotation_companies', 'policy_type')) {
                $table->string('policy_type')->after('plan_name')->nullable();
            }
            if (!Schema::hasColumn('quotation_companies', 'policy_tenure_years')) {
                $table->integer('policy_tenure_years')->after('policy_type')->nullable();
            }
            if (!Schema::hasColumn('quotation_companies', 'ncb_percentage')) {
                $table->decimal('ncb_percentage', 5, 2)->after('policy_tenure_years')->nullable();
            }
            if (!Schema::hasColumn('quotation_companies', 'idv_vehicle')) {
                $table->decimal('idv_vehicle', 10, 2)->after('ncb_percentage')->nullable();
            }
            if (!Schema::hasColumn('quotation_companies', 'idv_trailer')) {
                $table->decimal('idv_trailer', 10, 2)->after('idv_vehicle')->default(0);
            }
            if (!Schema::hasColumn('quotation_companies', 'idv_cng_lpg_kit')) {
                $table->decimal('idv_cng_lpg_kit', 10, 2)->after('idv_trailer')->default(0);
            }
            if (!Schema::hasColumn('quotation_companies', 'idv_electrical_accessories')) {
                $table->decimal('idv_electrical_accessories', 10, 2)->after('idv_cng_lpg_kit')->default(0);
            }
            if (!Schema::hasColumn('quotation_companies', 'idv_non_electrical_accessories')) {
                $table->decimal('idv_non_electrical_accessories', 10, 2)->after('idv_electrical_accessories')->default(0);
            }
            if (!Schema::hasColumn('quotation_companies', 'total_idv')) {
                $table->decimal('total_idv', 10, 2)->after('idv_non_electrical_accessories')->nullable();
            }
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
                'ncb_percentage',
                'idv_vehicle',
                'idv_trailer',
                'idv_cng_lpg_kit',
                'idv_electrical_accessories',
                'idv_non_electrical_accessories',
                'total_idv'
            ]);
        });
    }
};
