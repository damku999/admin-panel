<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPerformanceIndexes extends Migration
{
    /**
     * Run the migrations.
     * 
     * Phase 2: Performance Optimization - Strategic Indexes
     * 
     * @return void
     */
    public function up()
    {
        // Customer table performance indexes
        $this->addCustomerIndexes();
        
        // Customer insurances performance indexes  
        $this->addCustomerInsuranceIndexes();
        
        // Authentication and security indexes
        $this->addAuthenticationIndexes();
        
        // Business logic query indexes
        $this->addBusinessLogicIndexes();
        
        // Audit and reporting indexes
        $this->addAuditIndexes();
    }
    
    /**
     * Customer table performance indexes
     */
    private function addCustomerIndexes()
    {
        Schema::table('customers', function (Blueprint $table) {
            // Family group access patterns (very frequent in customer portal)
            if (Schema::hasColumn('customers', 'family_group_id')) {
                $table->index(['family_group_id', 'status'], 'idx_customers_family_status');
            }
            
            // Email authentication (critical for login performance)
            $table->index(['email', 'status'], 'idx_customers_email_status');
            
            // Mobile number lookups
            $table->index(['mobile_number', 'status'], 'idx_customers_mobile_status');
            
            // Multi-column search optimization
            $table->index(['name', 'email', 'mobile_number', 'status'], 'idx_customers_search');
            
            // Date-based queries
            $table->index(['date_of_birth'], 'idx_customers_dob');
            $table->index(['wedding_anniversary_date'], 'idx_customers_wedding');
            
            // Status and type filtering
            $table->index(['type', 'status'], 'idx_customers_type_status');
        });
    }
    
    /**
     * Customer insurances performance indexes
     */
    private function addCustomerInsuranceIndexes()
    {
        Schema::table('customer_insurances', function (Blueprint $table) {
            // Customer policy lookup (extremely frequent)
            $table->index(['customer_id', 'status'], 'idx_ci_customer_status');
            
            // Expiry tracking (business critical for renewals)
            $table->index(['expired_date', 'status'], 'idx_ci_expiry_status');
            $table->index(['start_date', 'expired_date', 'status'], 'idx_ci_dates_status');
            
            // Policy management
            $table->index(['policy_no'], 'idx_ci_policy_no');
            $table->index(['registration_no'], 'idx_ci_registration_no');
            
            // Insurance company analysis
            $table->index(['insurance_company_id', 'status', 'start_date'], 'idx_ci_company_analysis');
            
            // Commission calculations
            $table->index(['broker_id', 'status', 'start_date'], 'idx_ci_broker_commission');
            $table->index(['relationship_manager_id', 'status'], 'idx_ci_rm_status');
            
            // Premium type reporting
            $table->index(['premium_type_id', 'status', 'start_date'], 'idx_ci_premium_reporting');
            
            // Complex family access pattern
            if (Schema::hasColumn('customer_insurances', 'customer_id')) {
                $table->index(['customer_id', 'status', 'expired_date'], 'idx_ci_family_access');
            }
            
            // Renewal workflow optimization
            $table->index(['expired_date', 'status', 'customer_id'], 'idx_ci_renewal_workflow');
        });
    }
    
    /**
     * Authentication and security indexes
     */
    private function addAuthenticationIndexes()
    {
        // Users table authentication
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                $table->index(['email', 'status'], 'idx_users_email_status');
                $table->index(['mobile_number', 'status'], 'idx_users_mobile_status');
                $table->index(['role_id', 'status'], 'idx_users_role_status');
            });
        }
        
        // Family groups for shared access
        if (Schema::hasTable('family_groups')) {
            Schema::table('family_groups', function (Blueprint $table) {
                $table->index(['family_head_customer_id', 'status'], 'idx_fg_head_status');
                if (Schema::hasColumn('family_groups', 'created_by')) {
                    $table->index(['created_by', 'status'], 'idx_fg_created_status');
                }
            });
        }
    }
    
    /**
     * Business logic query indexes
     */
    private function addBusinessLogicIndexes()
    {
        // Quotations performance
        if (Schema::hasTable('quotations')) {
            Schema::table('quotations', function (Blueprint $table) {
                $table->index(['customer_id', 'status'], 'idx_quotations_customer_status');
                $table->index(['status', 'created_at'], 'idx_quotations_status_date');
                
                if (Schema::hasColumn('quotations', 'quotation_date')) {
                    $table->index(['quotation_date', 'status'], 'idx_quotations_date_status');
                }
            });
        }
        
        // Quotation companies ranking
        if (Schema::hasTable('quotation_companies')) {
            Schema::table('quotation_companies', function (Blueprint $table) {
                $table->index(['quotation_id', 'ranking'], 'idx_qc_quotation_ranking');
                $table->index(['insurance_company_id', 'status'], 'idx_qc_company_status');
            });
        }
        
        // Branch performance
        if (Schema::hasTable('branches')) {
            Schema::table('branches', function (Blueprint $table) {
                $table->index(['status', 'name'], 'idx_branches_status_name');
            });
        }
        
        // Broker performance
        Schema::table('brokers', function (Blueprint $table) {
            $table->index(['status', 'name'], 'idx_brokers_status_name');
        });
        
        // Insurance companies
        Schema::table('insurance_companies', function (Blueprint $table) {
            $table->index(['status', 'name'], 'idx_ic_status_name');
        });
    }
    
    /**
     * Audit and reporting indexes
     */
    private function addAuditIndexes()
    {
        // Customer audit logs
        if (Schema::hasTable('customer_audit_logs')) {
            Schema::table('customer_audit_logs', function (Blueprint $table) {
                $table->index(['customer_id', 'action', 'created_at'], 'idx_cal_customer_action_date');
                $table->index(['action', 'created_at'], 'idx_cal_action_date');
                $table->index(['ip_address', 'created_at'], 'idx_cal_ip_date');
            });
        }
        
        // Activity log (Spatie package)
        if (Schema::hasTable('activity_log')) {
            Schema::table('activity_log', function (Blueprint $table) {
                // Subject-based queries
                $table->index(['subject_type', 'subject_id', 'created_at'], 'idx_activity_subject_date');
                
                // Causer-based queries
                $table->index(['causer_type', 'causer_id', 'created_at'], 'idx_activity_causer_date');
                
                // Log name filtering
                $table->index(['log_name', 'created_at'], 'idx_activity_log_date');
            });
        }
        
        // General created_at indexes for all main tables
        $mainTables = [
            'customers', 'customer_insurances', 'quotations', 'brokers', 
            'insurance_companies', 'branches'
        ];
        
        foreach ($mainTables as $tableName) {
            if (Schema::hasTable($tableName)) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->index(['created_at'], "idx_{$table->getTable()}_created_at");
                    $table->index(['updated_at'], "idx_{$table->getTable()}_updated_at");
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Drop indexes in reverse order
        $this->dropAuditIndexes();
        $this->dropBusinessLogicIndexes();
        $this->dropAuthenticationIndexes();
        $this->dropCustomerInsuranceIndexes();
        $this->dropCustomerIndexes();
    }
    
    private function dropCustomerIndexes()
    {
        Schema::table('customers', function (Blueprint $table) {
            $indexes = [
                'idx_customers_family_status',
                'idx_customers_email_status',
                'idx_customers_mobile_status',
                'idx_customers_search',
                'idx_customers_dob',
                'idx_customers_wedding',
                'idx_customers_type_status'
            ];
            
            foreach ($indexes as $index) {
                try {
                    $table->dropIndex($index);
                } catch (\Exception $e) {
                    // Continue if index doesn't exist
                }
            }
        });
    }
    
    private function dropCustomerInsuranceIndexes()
    {
        Schema::table('customer_insurances', function (Blueprint $table) {
            $indexes = [
                'idx_ci_customer_status',
                'idx_ci_expiry_status',
                'idx_ci_dates_status',
                'idx_ci_policy_no',
                'idx_ci_registration_no',
                'idx_ci_company_analysis',
                'idx_ci_broker_commission',
                'idx_ci_rm_status',
                'idx_ci_premium_reporting',
                'idx_ci_family_access',
                'idx_ci_renewal_workflow'
            ];
            
            foreach ($indexes as $index) {
                try {
                    $table->dropIndex($index);
                } catch (\Exception $e) {
                    // Continue if index doesn't exist
                }
            }
        });
    }
    
    private function dropAuthenticationIndexes()
    {
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropIndex('idx_users_email_status');
                $table->dropIndex('idx_users_mobile_status');
                $table->dropIndex('idx_users_role_status');
            });
        }
        
        if (Schema::hasTable('family_groups')) {
            Schema::table('family_groups', function (Blueprint $table) {
                $table->dropIndex('idx_fg_head_status');
                if (Schema::hasColumn('family_groups', 'created_by')) {
                    $table->dropIndex('idx_fg_created_status');
                }
            });
        }
    }
    
    private function dropBusinessLogicIndexes()
    {
        $tablesToIndexMap = [
            'quotations' => ['idx_quotations_customer_status', 'idx_quotations_status_date', 'idx_quotations_date_status'],
            'quotation_companies' => ['idx_qc_quotation_ranking', 'idx_qc_company_status'],
            'branches' => ['idx_branches_status_name'],
            'brokers' => ['idx_brokers_status_name'],
            'insurance_companies' => ['idx_ic_status_name']
        ];
        
        foreach ($tablesToIndexMap as $tableName => $indexes) {
            if (Schema::hasTable($tableName)) {
                Schema::table($tableName, function (Blueprint $table) use ($indexes) {
                    foreach ($indexes as $index) {
                        try {
                            $table->dropIndex($index);
                        } catch (\Exception $e) {
                            // Continue if index doesn't exist
                        }
                    }
                });
            }
        }
    }
    
    private function dropAuditIndexes()
    {
        if (Schema::hasTable('customer_audit_logs')) {
            Schema::table('customer_audit_logs', function (Blueprint $table) {
                $table->dropIndex('idx_cal_customer_action_date');
                $table->dropIndex('idx_cal_action_date');
                $table->dropIndex('idx_cal_ip_date');
            });
        }
        
        if (Schema::hasTable('activity_log')) {
            Schema::table('activity_log', function (Blueprint $table) {
                $table->dropIndex('idx_activity_subject_date');
                $table->dropIndex('idx_activity_causer_date');
                $table->dropIndex('idx_activity_log_date');
            });
        }
        
        $mainTables = ['customers', 'customer_insurances', 'quotations', 'brokers', 'insurance_companies', 'branches'];
        
        foreach ($mainTables as $tableName) {
            if (Schema::hasTable($tableName)) {
                Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                    try {
                        $table->dropIndex("idx_{$tableName}_created_at");
                        $table->dropIndex("idx_{$tableName}_updated_at");
                    } catch (\Exception $e) {
                        // Continue if index doesn't exist
                    }
                });
            }
        }
    }
}