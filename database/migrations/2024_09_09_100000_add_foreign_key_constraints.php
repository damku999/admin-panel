<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeyConstraints extends Migration
{
    /**
     * Run the migrations.
     * 
     * Phase 1: Critical Data Integrity - Foreign Key Constraints
     * 
     * @return void
     */
    public function up()
    {
        // Skip for testing environment entirely - foreign keys not needed for tests
        if (app()->environment() === 'testing') {
            return;
        }

        // Fix data type inconsistencies first (required before adding FK constraints)
        $this->fixDataTypes();

        // Add foreign key constraints for audit fields across all tables
        $this->addAuditFieldConstraints();

        // Add business logic foreign key constraints
        $this->addBusinessLogicConstraints();
    }
    
    /**
     * Fix data type inconsistencies before adding constraints
     */
    private function fixDataTypes()
    {
        // Customer table audit fields
        Schema::table('customers', function (Blueprint $table) {
            $table->unsignedBigInteger('created_by')->nullable()->change();
            $table->unsignedBigInteger('updated_by')->nullable()->change();
            $table->unsignedBigInteger('deleted_by')->nullable()->change();
        });
        
        // Customer insurances foreign key fields
        Schema::table('customer_insurances', function (Blueprint $table) {
            $table->unsignedBigInteger('insurance_company_id')->nullable()->change();
            $table->unsignedBigInteger('policy_type_id')->nullable()->change();
            $table->unsignedBigInteger('premium_type_id')->nullable()->change();
            $table->unsignedBigInteger('fuel_type_id')->nullable()->change();
            $table->unsignedBigInteger('created_by')->nullable()->change();
            $table->unsignedBigInteger('updated_by')->nullable()->change();
            $table->unsignedBigInteger('deleted_by')->nullable()->change();
        });
        
        // Brokers audit fields
        Schema::table('brokers', function (Blueprint $table) {
            $table->unsignedBigInteger('created_by')->nullable()->change();
            $table->unsignedBigInteger('updated_by')->nullable()->change();
            $table->unsignedBigInteger('deleted_by')->nullable()->change();
        });
        
        // Insurance companies audit fields
        Schema::table('insurance_companies', function (Blueprint $table) {
            $table->unsignedBigInteger('created_by')->nullable()->change();
            $table->unsignedBigInteger('updated_by')->nullable()->change();
            $table->unsignedBigInteger('deleted_by')->nullable()->change();
        });
        
        // Additional tables with audit fields
        $auditTables = [
            'branches', 'premium_types', 'policy_types', 'fuel_types',
            'relationship_managers', 'reference_users', 'addon_covers',
            'family_groups', 'quotations', 'quotation_companies'
        ];
        
        foreach ($auditTables as $tableName) {
            if (Schema::hasTable($tableName)) {
                Schema::table($tableName, function (Blueprint $table) {
                    if (Schema::hasColumn($table->getTable(), 'created_by')) {
                        $table->unsignedBigInteger('created_by')->nullable()->change();
                    }
                    if (Schema::hasColumn($table->getTable(), 'updated_by')) {
                        $table->unsignedBigInteger('updated_by')->nullable()->change();
                    }
                    if (Schema::hasColumn($table->getTable(), 'deleted_by')) {
                        $table->unsignedBigInteger('deleted_by')->nullable()->change();
                    }
                });
            }
        }
    }
    
    /**
     * Add foreign key constraints for audit fields
     */
    private function addAuditFieldConstraints()
    {
        $auditTables = [
            'customers', 'customer_insurances', 'brokers', 'insurance_companies',
            'branches', 'premium_types', 'policy_types', 'fuel_types',
            'relationship_managers', 'reference_users', 'addon_covers',
            'family_groups', 'quotations', 'quotation_companies'
        ];
        
        foreach ($auditTables as $tableName) {
            if (Schema::hasTable($tableName)) {
                Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                    $prefix = substr($tableName, 0, 3);
                    
                    if (Schema::hasColumn($tableName, 'created_by')) {
                        $table->foreign('created_by', "fk_{$prefix}_created_by")
                              ->references('id')->on('users')
                              ->onDelete('set null');
                    }
                    if (Schema::hasColumn($tableName, 'updated_by')) {
                        $table->foreign('updated_by', "fk_{$prefix}_updated_by")
                              ->references('id')->on('users')
                              ->onDelete('set null');
                    }
                    if (Schema::hasColumn($tableName, 'deleted_by')) {
                        $table->foreign('deleted_by', "fk_{$prefix}_deleted_by")
                              ->references('id')->on('users')
                              ->onDelete('set null');
                    }
                });
            }
        }
    }
    
    /**
     * Add business logic foreign key constraints
     */
    private function addBusinessLogicConstraints()
    {
        // Customer insurances constraints
        Schema::table('customer_insurances', function (Blueprint $table) {
            $table->foreign('customer_id', 'fk_ci_customer')
                  ->references('id')->on('customers')
                  ->onDelete('cascade');
                  
            $table->foreign('branch_id', 'fk_ci_branch')
                  ->references('id')->on('branches')
                  ->onDelete('set null');
                  
            $table->foreign('broker_id', 'fk_ci_broker')
                  ->references('id')->on('brokers')
                  ->onDelete('set null');
                  
            $table->foreign('relationship_manager_id', 'fk_ci_relationship_manager')
                  ->references('id')->on('relationship_managers')
                  ->onDelete('set null');
                  
            $table->foreign('insurance_company_id', 'fk_ci_insurance_company')
                  ->references('id')->on('insurance_companies')
                  ->onDelete('set null');
                  
            $table->foreign('policy_type_id', 'fk_ci_policy_type')
                  ->references('id')->on('policy_types')
                  ->onDelete('set null');
                  
            $table->foreign('premium_type_id', 'fk_ci_premium_type')
                  ->references('id')->on('premium_types')
                  ->onDelete('set null');
                  
            $table->foreign('fuel_type_id', 'fk_ci_fuel_type')
                  ->references('id')->on('fuel_types')
                  ->onDelete('set null');
                  
        });
        
        // Family groups constraints
        if (Schema::hasTable('family_groups')) {
            Schema::table('customers', function (Blueprint $table) {
                if (Schema::hasColumn('customers', 'family_group_id')) {
                    $table->foreign('family_group_id', 'fk_customers_family_group')
                          ->references('id')->on('family_groups')
                          ->onDelete('set null');
                }
            });
        }
        
        // Quotations constraints
        if (Schema::hasTable('quotations')) {
            Schema::table('quotations', function (Blueprint $table) {
                if (Schema::hasColumn('quotations', 'customer_id')) {
                    $table->foreign('customer_id', 'fk_quotations_customer')
                          ->references('id')->on('customers')
                          ->onDelete('cascade');
                }
            });
        }
        
        // Quotation companies constraints
        if (Schema::hasTable('quotation_companies')) {
            Schema::table('quotation_companies', function (Blueprint $table) {
                if (Schema::hasColumn('quotation_companies', 'quotation_id')) {
                    $table->foreign('quotation_id', 'fk_qc_quotation')
                          ->references('id')->on('quotations')
                          ->onDelete('cascade');
                }
                if (Schema::hasColumn('quotation_companies', 'insurance_company_id')) {
                    $table->foreign('insurance_company_id', 'fk_qc_insurance_company')
                          ->references('id')->on('insurance_companies')
                          ->onDelete('cascade');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Drop foreign key constraints in reverse order
        $this->dropBusinessLogicConstraints();
        $this->dropAuditFieldConstraints();
    }
    
    private function dropBusinessLogicConstraints()
    {
        // Customer insurances constraints
        Schema::table('customer_insurances', function (Blueprint $table) {
            $table->dropForeign('fk_ci_customer');
            $table->dropForeign('fk_ci_branch');
            $table->dropForeign('fk_ci_broker');
            $table->dropForeign('fk_ci_relationship_manager');
            $table->dropForeign('fk_ci_insurance_company');
            $table->dropForeign('fk_ci_policy_type');
            $table->dropForeign('fk_ci_premium_type');
            $table->dropForeign('fk_ci_fuel_type');
        });
        
        // Other constraints
        if (Schema::hasTable('family_groups') && Schema::hasColumn('customers', 'family_group_id')) {
            Schema::table('customers', function (Blueprint $table) {
                $table->dropForeign('fk_customers_family_group');
            });
        }
        
        if (Schema::hasTable('quotations') && Schema::hasColumn('quotations', 'customer_id')) {
            Schema::table('quotations', function (Blueprint $table) {
                $table->dropForeign('fk_quotations_customer');
            });
        }
        
        if (Schema::hasTable('quotation_companies')) {
            Schema::table('quotation_companies', function (Blueprint $table) {
                if (Schema::hasColumn('quotation_companies', 'quotation_id')) {
                    $table->dropForeign('fk_qc_quotation');
                }
                if (Schema::hasColumn('quotation_companies', 'insurance_company_id')) {
                    $table->dropForeign('fk_qc_insurance_company');
                }
            });
        }
    }
    
    private function dropAuditFieldConstraints()
    {
        $auditTables = [
            'customers', 'customer_insurances', 'brokers', 'insurance_companies',
            'branches', 'premium_types', 'policy_types', 'fuel_types',
            'relationship_managers', 'reference_users', 'addon_covers',
            'family_groups', 'quotations', 'quotation_companies'
        ];
        
        foreach ($auditTables as $tableName) {
            if (Schema::hasTable($tableName)) {
                Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                    $prefix = substr($tableName, 0, 3);
                    
                    try {
                        if (Schema::hasColumn($tableName, 'created_by')) {
                            $table->dropForeign("fk_{$prefix}_created_by");
                        }
                        if (Schema::hasColumn($tableName, 'updated_by')) {
                            $table->dropForeign("fk_{$prefix}_updated_by");
                        }
                        if (Schema::hasColumn($tableName, 'deleted_by')) {
                            $table->dropForeign("fk_{$prefix}_deleted_by");
                        }
                    } catch (\Exception $e) {
                        // Continue if constraint doesn't exist
                    }
                });
            }
        }
    }
}