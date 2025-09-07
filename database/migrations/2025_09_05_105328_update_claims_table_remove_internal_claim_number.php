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
            // Remove internal claim_number and related fields
            $table->dropColumn('claim_number');
            $table->dropColumn('claim_number_assigned');
            $table->dropColumn('claim_number_assigned_at');
            
            // Add insurance_claim_number field (from insurance company)
            $table->string('insurance_claim_number')->nullable()->after('policy_no');
            
            // Add missing fields that should be in the table
            $table->date('incident_date')->nullable()->after('insurance_claim_number');
            $table->decimal('claim_amount', 12, 2)->nullable()->after('incident_date');
            $table->text('description')->nullable()->after('claim_amount');
            
            // Add missing health insurance fields
            $table->integer('patient_age')->nullable()->after('patient_name');
            $table->string('patient_relation')->nullable()->after('patient_age');
            $table->date('discharge_date')->nullable()->after('admission_date');
            $table->text('disease_diagnosis')->nullable()->after('illness');
            
            // Add missing truck insurance fields
            $table->string('driver_name')->nullable()->after('accident_description');
            $table->string('accident_location')->nullable()->after('driver_name');
            $table->string('police_station')->nullable()->after('accident_location');
            $table->string('fir_number')->nullable()->after('police_station');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('claims', function (Blueprint $table) {
            // Restore internal claim_number and related fields
            $table->string('claim_number')->nullable()->unique()->after('policy_no');
            $table->boolean('claim_number_assigned')->default(false)->after('document_request_sent_at');
            $table->timestamp('claim_number_assigned_at')->nullable()->after('claim_number_assigned');
            
            // Remove insurance_claim_number and added fields
            $table->dropColumn([
                'insurance_claim_number',
                'incident_date', 
                'claim_amount',
                'description',
                'patient_age',
                'patient_relation', 
                'discharge_date',
                'disease_diagnosis',
                'driver_name',
                'accident_location',
                'police_station',
                'fir_number'
            ]);
        });
    }
};
