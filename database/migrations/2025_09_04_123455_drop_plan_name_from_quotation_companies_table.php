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
            if (Schema::hasColumn('quotation_companies', 'plan_name')) {
                $table->dropColumn('plan_name');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quotation_companies', function (Blueprint $table) {
            $table->string('plan_name')->nullable()->after('quote_number');
        });
    }
};
