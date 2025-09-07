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
        Schema::create('claim_liabilities', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('claim_id');
            $table->enum('liability_type', ['Cashless', 'Reimbursement']);
            
            // Common amounts
            $table->decimal('claim_amount', 12, 2)->nullable();
            $table->decimal('salvage_amount', 12, 2)->nullable();
            
            // Cashless specific fields
            $table->decimal('claim_charge', 12, 2)->nullable();
            $table->decimal('amount_to_be_paid_by_customer', 12, 2)->nullable(); // Calculated field
            
            // Reimbursement specific fields
            $table->decimal('deductions', 12, 2)->nullable();
            $table->decimal('claim_amount_received', 12, 2)->nullable(); // Calculated field
            
            // Payment details
            $table->string('payment_method')->nullable();
            $table->string('payment_reference_number')->nullable();
            $table->date('payment_date')->nullable();
            $table->text('payment_notes')->nullable();
            $table->enum('payment_status', ['Pending', 'Processed', 'Completed', 'Failed'])->default('Pending');
            
            $table->text('remarks')->nullable();
            $table->boolean('is_final')->default(false);
            $table->boolean('status')->default(true);
            
            $table->timestamps();
            $table->softDeletes();
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->integer('deleted_by')->nullable();
            
            // Indexes for better performance
            $table->index(['claim_id', 'liability_type']);
            $table->index(['payment_status']);
            $table->index(['payment_date']);
            $table->index(['is_final']);
            
            // Foreign key constraints
            $table->foreign('claim_id')->references('id')->on('claims')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('claim_liabilities');
    }
};
