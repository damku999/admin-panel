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
            // Drop foreign key constraints first
            $table->dropForeign(['customer_id']);
            $table->dropForeign(['customer_insurance_id']);
            
            // Make these fields nullable since only policy_no should be required
            $table->unsignedBigInteger('customer_id')->nullable()->change();
            $table->unsignedBigInteger('customer_insurance_id')->nullable()->change();
            
            // Make policy_no NOT nullable (this is the only required field)
            $table->string('policy_no')->nullable(false)->change();
            
            // Re-add foreign key constraints with nullable fields
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
            $table->foreign('customer_insurance_id')->references('id')->on('customer_insurances')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('claims', function (Blueprint $table) {
            // Rollback: Drop foreign keys first
            $table->dropForeign(['customer_id']);
            $table->dropForeign(['customer_insurance_id']);
            
            // Rollback: Make customer fields NOT nullable again
            $table->unsignedBigInteger('customer_id')->nullable(false)->change();
            $table->unsignedBigInteger('customer_insurance_id')->nullable(false)->change();
            
            // Rollback: Make policy_no nullable again  
            $table->string('policy_no')->nullable()->change();
            
            // Re-add original foreign key constraints
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
            $table->foreign('customer_insurance_id')->references('id')->on('customer_insurances')->onDelete('cascade');
        });
    }
};
