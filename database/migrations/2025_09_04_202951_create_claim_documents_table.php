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
        Schema::create('claim_documents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('claim_id');
            $table->string('document_name');
            $table->text('document_description')->nullable();
            $table->enum('document_status', ['Required', 'Received', 'Pending', 'Not Applicable'])->default('Required');
            $table->string('document_path')->nullable();
            $table->timestamp('received_at')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_mandatory')->default(true);
            $table->enum('insurance_type', ['Health', 'Truck'])->default('Health');
            $table->integer('order_no')->default(1);
            $table->boolean('status')->default(true);
            
            $table->timestamps();
            $table->softDeletes();
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->integer('deleted_by')->nullable();
            
            // Indexes for better performance
            $table->index(['claim_id', 'document_status']);
            $table->index(['insurance_type', 'is_mandatory']);
            $table->index(['order_no']);
            
            // Foreign key constraints
            $table->foreign('claim_id')->references('id')->on('claims')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('claim_documents');
    }
};
