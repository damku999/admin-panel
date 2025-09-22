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
        Schema::table('two_factor_auth', function (Blueprint $table) {
            // Increase secret column size to accommodate encrypted values
            $table->text('secret')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('two_factor_auth', function (Blueprint $table) {
            // Revert back to varchar(125)
            $table->string('secret', 125)->nullable()->change();
        });
    }
};
