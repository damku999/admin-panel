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
        Schema::create('family_groups', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('Family name or identifier');
            $table->unsignedBigInteger('family_head_id')->nullable()->comment('Customer ID who is the family head');
            $table->boolean('status')->default(true)->comment('Active/inactive status');
            $table->unsignedInteger('created_by')->nullable()->comment('Admin user who created this');
            $table->unsignedInteger('updated_by')->nullable()->comment('Admin user who last updated this');
            $table->unsignedInteger('deleted_by')->nullable()->comment('Admin user who deleted this');
            $table->timestamps();
            $table->softDeletes();
            
            // Add foreign key constraint for family head
            $table->foreign('family_head_id')->references('id')->on('customers')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('family_groups');
    }
};
