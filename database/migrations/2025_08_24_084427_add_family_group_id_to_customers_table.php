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
        Schema::table('customers', function (Blueprint $table) {
            $table->unsignedBigInteger('family_group_id')->nullable()->after('gst_path')->comment('Family group this customer belongs to');
            $table->string('password')->nullable()->after('family_group_id')->comment('Password for customer login');
            $table->timestamp('email_verified_at')->nullable()->after('password')->comment('Email verification timestamp');
            
            // Add foreign key constraint
            $table->foreign('family_group_id')->references('id')->on('family_groups')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropForeign(['family_group_id']);
            $table->dropColumn(['family_group_id', 'password', 'email_verified_at']);
        });
    }
};
