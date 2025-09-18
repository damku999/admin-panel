<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class OptimizeEnumCompatibility extends Migration
{
    /**
     * Run the migrations.
     * 
     * Phase 3: Enum Compatibility - Convert problematic enums to VARCHAR
     * 
     * @return void
     */
    public function up()
    {
        // Skip for testing environment
        if (app()->environment() === 'testing') {
            return;
        }

        // Convert enum fields to VARCHAR for better MySQL 8+ compatibility
        $this->convertCustomerEnums();
        $this->convertCustomerInsuranceEnums();
        $this->convertQuotationEnums();
        
        // Create lookup tables for standardized values
        $this->createLookupTables();
    }
    
    /**
     * Convert customer table enums
     */
    private function convertCustomerEnums()
    {
        Schema::table('customers', function (Blueprint $table) {
            // Convert type enum to VARCHAR
            $table->string('type', 20)->nullable()->change();
        });
        
        // Note: Data consistency handled by seeders
    }
    
    /**
     * Convert customer insurance enums
     */
    private function convertCustomerInsuranceEnums()
    {
        Schema::table('customer_insurances', function (Blueprint $table) {
            // Convert commission_on enum to VARCHAR
            if (Schema::hasColumn('customer_insurances', 'commission_on')) {
                $table->string('commission_on', 20)->nullable()->change();
            }
        });
        
        // Note: Data consistency handled by seeders
    }
    
    /**
     * Convert quotation table enums
     */
    private function convertQuotationEnums()
    {
        if (Schema::hasTable('quotations')) {
            Schema::table('quotations', function (Blueprint $table) {
                // Convert fuel_type enum to VARCHAR
                if (Schema::hasColumn('quotations', 'fuel_type')) {
                    $table->string('fuel_type', 20)->nullable()->change();
                }
                
                // Convert policy_type enum to VARCHAR
                if (Schema::hasColumn('quotations', 'policy_type')) {
                    $table->string('policy_type', 30)->nullable()->change();
                }
                
                // Convert status enum to VARCHAR
                if (Schema::hasColumn('quotations', 'status')) {
                    $table->string('status', 20)->default('Draft')->change();
                }
            });
            
            // Note: Data consistency handled by seeders
        }
    }
    
    /**
     * Create lookup tables for standardized values
     */
    private function createLookupTables()
    {
        // Customer types lookup table
        Schema::create('customer_types', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50)->unique();
            $table->string('description')->nullable();
            $table->boolean('status')->default(1);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            
            // Audit fields
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->softDeletes();
            
            // Foreign key constraints
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('deleted_by')->references('id')->on('users')->onDelete('set null');
            
            // Indexes
            $table->index(['name', 'status']);
            $table->index(['status', 'sort_order']);
        });
        
        // Commission types lookup table
        Schema::create('commission_types', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50)->unique();
            $table->string('description')->nullable();
            $table->boolean('status')->default(1);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            
            // Audit fields
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->softDeletes();
            
            // Foreign key constraints
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('deleted_by')->references('id')->on('users')->onDelete('set null');
            
            // Indexes
            $table->index(['name', 'status']);
            $table->index(['status', 'sort_order']);
        });
        
        // Quotation statuses lookup table
        Schema::create('quotation_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50)->unique();
            $table->string('description')->nullable();
            $table->string('color', 7)->default('#6c757d'); // Bootstrap colors
            $table->boolean('is_active')->default(1);
            $table->boolean('is_final')->default(0); // Whether this status is final
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            
            // Audit fields
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->softDeletes();
            
            // Foreign key constraints
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('deleted_by')->references('id')->on('users')->onDelete('set null');
            
            // Indexes
            $table->index(['name', 'is_active']);
            $table->index(['is_active', 'sort_order']);
        });
        
        // Note: Default data moved to seeders (CustomerTypesSeeder, CommissionTypesSeeder, QuotationStatusesSeeder)
    }
    

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Drop lookup tables
        Schema::dropIfExists('quotation_statuses');
        Schema::dropIfExists('commission_types');
        Schema::dropIfExists('customer_types');
        
        // Revert enum changes (Note: This is complex and may require data migration)
        // For safety, we'll keep the VARCHAR fields but note the original enum values in comments
        
        Schema::table('customers', function (Blueprint $table) {
            // Original: enum('Corporate', 'Retail')
            // Keeping as VARCHAR for safety
            $table->string('type', 20)->nullable()->change();
        });
        
        if (Schema::hasTable('customer_insurances')) {
            Schema::table('customer_insurances', function (Blueprint $table) {
                // Original: enum('net_premium', 'od_premium', 'tp_premium')
                // Keeping as VARCHAR for safety
                if (Schema::hasColumn('customer_insurances', 'commission_on')) {
                    $table->string('commission_on', 20)->nullable()->change();
                }
            });
        }
        
        if (Schema::hasTable('quotations')) {
            Schema::table('quotations', function (Blueprint $table) {
                // Original enums:
                // fuel_type: enum('Petrol', 'Diesel', 'CNG', 'Electric', 'Hybrid')
                // policy_type: enum('Comprehensive', 'Own Damage', 'Third Party')
                // status: enum('Draft', 'Generated', 'Sent', 'Accepted', 'Rejected')
                // Keeping as VARCHAR for safety
                
                if (Schema::hasColumn('quotations', 'fuel_type')) {
                    $table->string('fuel_type', 20)->nullable()->change();
                }
                if (Schema::hasColumn('quotations', 'policy_type')) {
                    $table->string('policy_type', 30)->nullable()->change();
                }
                if (Schema::hasColumn('quotations', 'status')) {
                    $table->string('status', 20)->default('Draft')->change();
                }
            });
        }
    }
}