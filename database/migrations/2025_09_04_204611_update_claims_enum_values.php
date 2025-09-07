<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update the claim_status enum to match the controller and views
        DB::statement("ALTER TABLE claims MODIFY COLUMN claim_status ENUM(
            'Initiated', 
            'Documents Collected', 
            'Submitted to Insurance', 
            'Under Review', 
            'Approved', 
            'Rejected', 
            'Closed'
        ) NOT NULL DEFAULT 'Initiated'");

        // Update existing 'Open' records to 'Initiated'
        DB::statement("UPDATE claims SET claim_status = 'Initiated' WHERE claim_status = 'Open'");

        // Update existing 'In Progress' records to 'Documents Collected'  
        DB::statement("UPDATE claims SET claim_status = 'Documents Collected' WHERE claim_status = 'In Progress'");

        // Add missing fields that were in the requirements but not in the original migration
        Schema::table('claims', function (Blueprint $table) {
            // Add missing fields from requirements
            $table->date('incident_date')->nullable()->after('vehicle_number');
            $table->decimal('claim_amount', 12, 2)->nullable()->after('incident_date');
            $table->string('insurance_claim_number')->nullable()->after('claim_number');
            
            // Health Insurance additional fields
            $table->integer('patient_age')->nullable()->after('patient_name');
            $table->string('patient_relation')->nullable()->after('patient_age');
            $table->date('discharge_date')->nullable()->after('admission_date');
            $table->text('disease_diagnosis')->nullable()->after('illness');
            
            // Truck Insurance additional fields
            $table->string('driver_name')->nullable()->after('driver_contact_number');
            $table->string('accident_location')->nullable()->after('driver_name');
            $table->string('police_station')->nullable()->after('accident_location');
            $table->string('fir_number')->nullable()->after('police_station');
            
            // Additional common fields
            $table->text('description')->nullable()->after('remarks');
        });
        
        // Update indexes
        Schema::table('claims', function (Blueprint $table) {
            $table->index(['insurance_claim_number']);
            $table->index(['incident_date']);
            $table->index(['claim_amount']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert the enum values
        DB::statement("ALTER TABLE claims MODIFY COLUMN claim_status ENUM(
            'Open', 
            'In Progress', 
            'Under Review', 
            'Approved', 
            'Rejected', 
            'Closed'
        ) NOT NULL DEFAULT 'Open'");

        // Update records back
        DB::statement("UPDATE claims SET claim_status = 'Open' WHERE claim_status = 'Initiated'");
        DB::statement("UPDATE claims SET claim_status = 'In Progress' WHERE claim_status = 'Documents Collected'");
        
        // Drop the added columns
        Schema::table('claims', function (Blueprint $table) {
            $table->dropColumn([
                'incident_date', 'claim_amount', 'insurance_claim_number',
                'patient_age', 'patient_relation', 'discharge_date', 'disease_diagnosis',
                'driver_name', 'accident_location', 'police_station', 'fir_number',
                'description'
            ]);
            
            $table->dropIndex(['insurance_claim_number']);
            $table->dropIndex(['incident_date']);
            $table->dropIndex(['claim_amount']);
        });
    }
};
