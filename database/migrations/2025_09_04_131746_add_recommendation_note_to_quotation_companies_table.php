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
            $table->text('recommendation_note')->nullable()->after('is_recommended')
                  ->comment('Note explaining why this quote is recommended');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quotation_companies', function (Blueprint $table) {
            $table->dropColumn('recommendation_note');
        });
    }
};
