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
        
        // Update existing data to ensure consistency
        DB::statement("UPDATE customers SET type = 'Corporate' WHERE type = 'Corporate'");
        DB::statement("UPDATE customers SET type = 'Retail' WHERE type = 'Retail'");
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
        
        // Update existing data
        DB::statement("UPDATE customer_insurances SET commission_on = 'net_premium' WHERE commission_on = 'net_premium'");
        DB::statement("UPDATE customer_insurances SET commission_on = 'od_premium' WHERE commission_on = 'od_premium'");
        DB::statement("UPDATE customer_insurances SET commission_on = 'tp_premium' WHERE commission_on = 'tp_premium'");
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
            
            // Update existing data
            DB::statement("UPDATE quotations SET fuel_type = 'Petrol' WHERE fuel_type = 'Petrol'");
            DB::statement("UPDATE quotations SET fuel_type = 'Diesel' WHERE fuel_type = 'Diesel'");
            DB::statement("UPDATE quotations SET fuel_type = 'CNG' WHERE fuel_type = 'CNG'");
            DB::statement("UPDATE quotations SET fuel_type = 'Electric' WHERE fuel_type = 'Electric'");
            DB::statement("UPDATE quotations SET fuel_type = 'Hybrid' WHERE fuel_type = 'Hybrid'");
            
            DB::statement("UPDATE quotations SET policy_type = 'Comprehensive' WHERE policy_type = 'Comprehensive'");
            DB::statement("UPDATE quotations SET policy_type = 'Own Damage' WHERE policy_type = 'Own Damage'");
            DB::statement("UPDATE quotations SET policy_type = 'Third Party' WHERE policy_type = 'Third Party'");
            
            DB::statement("UPDATE quotations SET status = 'Draft' WHERE status = 'Draft'");
            DB::statement("UPDATE quotations SET status = 'Generated' WHERE status = 'Generated'");
            DB::statement("UPDATE quotations SET status = 'Sent' WHERE status = 'Sent'");
            DB::statement("UPDATE quotations SET status = 'Accepted' WHERE status = 'Accepted'");
            DB::statement("UPDATE quotations SET status = 'Rejected' WHERE status = 'Rejected'");
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
        
        // Insert default data
        $this->insertDefaultLookupData();
    }
    
    /**
     * Insert default data for lookup tables
     */
    private function insertDefaultLookupData()
    {
        // Customer types
        DB::table('customer_types')->insert([
            [
                'name' => 'Corporate',
                'description' => 'Corporate customers with business insurance needs',
                'status' => 1,
                'sort_order' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Retail',
                'description' => 'Individual retail customers',
                'status' => 1,
                'sort_order' => 2,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
        
        // Commission types
        DB::table('commission_types')->insert([
            [
                'name' => 'net_premium',
                'description' => 'Commission calculated on net premium amount',
                'status' => 1,
                'sort_order' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'od_premium',
                'description' => 'Commission calculated on Own Damage premium',
                'status' => 1,
                'sort_order' => 2,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'tp_premium',
                'description' => 'Commission calculated on Third Party premium',
                'status' => 1,
                'sort_order' => 3,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
        
        // Quotation statuses
        DB::table('quotation_statuses')->insert([
            [
                'name' => 'Draft',
                'description' => 'Quotation is in draft mode',
                'color' => '#6c757d',
                'is_active' => 1,
                'is_final' => 0,
                'sort_order' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Generated',
                'description' => 'Quotation has been generated',
                'color' => '#17a2b8',
                'is_active' => 1,
                'is_final' => 0,
                'sort_order' => 2,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Sent',
                'description' => 'Quotation has been sent to customer',
                'color' => '#ffc107',
                'is_active' => 1,
                'is_final' => 0,
                'sort_order' => 3,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Accepted',
                'description' => 'Quotation has been accepted by customer',
                'color' => '#28a745',
                'is_active' => 1,
                'is_final' => 1,
                'sort_order' => 4,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Rejected',
                'description' => 'Quotation has been rejected by customer',
                'color' => '#dc3545',
                'is_active' => 1,
                'is_final' => 1,
                'sort_order' => 5,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
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