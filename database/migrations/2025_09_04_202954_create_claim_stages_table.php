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
        Schema::create('claim_stages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('claim_id');
            $table->string('stage_name');
            $table->text('stage_description')->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('stage_date')->useCurrent();
            $table->boolean('is_current')->default(false);
            $table->integer('stage_order')->default(1);
            $table->enum('stage_status', ['Pending', 'In Progress', 'Completed', 'On Hold', 'Cancelled'])->default('Pending');
            $table->boolean('whatsapp_sent')->default(false);
            $table->timestamp('whatsapp_sent_at')->nullable();
            $table->boolean('status')->default(true);
            
            $table->timestamps();
            $table->softDeletes();
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->integer('deleted_by')->nullable();
            
            // Indexes for better performance
            $table->index(['claim_id', 'is_current']);
            $table->index(['claim_id', 'stage_order']);
            $table->index(['stage_date']);
            $table->index(['stage_status']);
            
            // Foreign key constraints
            $table->foreign('claim_id')->references('id')->on('claims')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('claim_stages');
    }
};
