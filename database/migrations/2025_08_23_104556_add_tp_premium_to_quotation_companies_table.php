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
            $table->decimal('tp_premium', 10, 2)->nullable()->after('basic_od_premium');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quotation_companies', function (Blueprint $table) {
            $table->dropColumn('tp_premium');
        });
    }
};
