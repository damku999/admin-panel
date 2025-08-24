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
        Schema::create('family_members', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('family_group_id')->comment('Family group this member belongs to');
            $table->unsignedBigInteger('customer_id')->comment('Customer who is the family member');
            $table->string('relationship')->nullable()->comment('Relationship to family head (father/mother/child/spouse/etc)');
            $table->boolean('is_head')->default(false)->comment('Is this member the family head');
            $table->boolean('status')->default(true)->comment('Active/inactive status');
            $table->unsignedInteger('created_by')->nullable()->comment('Admin user who created this');
            $table->unsignedInteger('updated_by')->nullable()->comment('Admin user who last updated this');
            $table->unsignedInteger('deleted_by')->nullable()->comment('Admin user who deleted this');
            $table->timestamps();
            $table->softDeletes();
            
            // Add foreign key constraints
            $table->foreign('family_group_id')->references('id')->on('family_groups')->onDelete('cascade');
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
            
            // Ensure unique customer per family group
            $table->unique(['family_group_id', 'customer_id'], 'family_customer_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('family_members');
    }
};
