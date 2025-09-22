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
        Schema::table('trusted_devices', function (Blueprint $table) {
            // Make last_used_at nullable with default value
            $table->datetime('last_used_at')->nullable()->default(null)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trusted_devices', function (Blueprint $table) {
            // Revert back to not nullable (if needed)
            $table->datetime('last_used_at')->nullable(false)->change();
        });
    }
};
