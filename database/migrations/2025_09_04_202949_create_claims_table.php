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
        Schema::create('claims', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('customer_insurance_id');
            $table->string('policy_no')->nullable();
            $table->string('claim_number')->nullable()->unique();
            $table->string('vehicle_number')->nullable();
            $table->enum('insurance_type', ['Health', 'Truck'])->default('Health');
            $table->enum('liability_type', ['Cashless', 'Reimbursement'])->nullable();
            $table->string('current_stage')->default('Initiated');
            $table->enum('claim_status', ['Open', 'In Progress', 'Under Review', 'Approved', 'Rejected', 'Closed'])->default('Open');
            
            // Health Insurance specific fields
            $table->string('patient_name')->nullable();
            $table->string('contact_number')->nullable();
            $table->date('admission_date')->nullable();
            $table->string('treating_doctor_name')->nullable();
            $table->string('hospital_name')->nullable();
            $table->text('hospital_address')->nullable();
            $table->text('illness')->nullable();
            $table->integer('approx_hospitalization_days')->nullable();
            $table->decimal('approx_cost', 12, 2)->nullable();
            
            // Truck Insurance specific fields
            $table->string('driver_contact_number')->nullable();
            $table->text('spot_location_address')->nullable();
            $table->boolean('fir_required')->default(false);
            $table->boolean('third_party_injury')->default(false);
            $table->text('accident_description')->nullable();
            
            // Common fields
            $table->text('remarks')->nullable();
            $table->boolean('document_request_sent')->default(false);
            $table->timestamp('document_request_sent_at')->nullable();
            $table->boolean('claim_number_assigned')->default(false);
            $table->timestamp('claim_number_assigned_at')->nullable();
            $table->timestamp('intimation_date')->useCurrent();
            $table->timestamp('closed_at')->nullable();
            $table->text('closure_reason')->nullable();
            $table->boolean('status')->default(true);
            
            $table->timestamps();
            $table->softDeletes();
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->integer('deleted_by')->nullable();
            
            // Indexes for better performance
            $table->index(['customer_id', 'claim_status']);
            $table->index(['policy_no']);
            $table->index(['vehicle_number']);
            $table->index(['insurance_type', 'claim_status']);
            $table->index(['intimation_date']);
            
            // Foreign key constraints
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
            $table->foreign('customer_insurance_id')->references('id')->on('customer_insurances')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('claims');
    }
};
