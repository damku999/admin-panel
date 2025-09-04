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
        Schema::table('addon_covers', function (Blueprint $table) {
            $table->integer('order_no')->default(0)->after('description')
                  ->comment('Display order for addon covers (lower numbers appear first)');
            
            // Add index for better query performance
            $table->index(['status', 'order_no'], 'addon_covers_status_order_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('addon_covers', function (Blueprint $table) {
            $table->dropIndex('addon_covers_status_order_index');
            $table->dropColumn('order_no');
        });
    }
};
