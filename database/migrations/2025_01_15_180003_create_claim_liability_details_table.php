<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClaimLiabilityDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('claim_liability_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('claim_id');
            $table->enum('claim_type', ['Cashless', 'Reimbursement']);
            $table->string('hospital_name')->nullable(); // For health insurance
            $table->string('hospital_address')->nullable();
            $table->string('garage_name')->nullable(); // For vehicle insurance
            $table->string('garage_address')->nullable();
            $table->decimal('estimated_amount', 12, 2)->nullable();
            $table->decimal('approved_amount', 12, 2)->nullable();
            $table->decimal('final_amount', 12, 2)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->timestamp('deleted_at')->nullable();
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->integer('deleted_by')->nullable();

            // Foreign key constraints
            $table->foreign('claim_id')->references('id')->on('claims')->onDelete('cascade');

            // Indexes for performance
            $table->index('claim_id');
            $table->index('claim_type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('claim_liability_details');
    }
}