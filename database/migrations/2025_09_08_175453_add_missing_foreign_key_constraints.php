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
        // Fix customers table constraints
        Schema::table('customers', function (Blueprint $table) {
            // Change audit fields to unsignedBigInteger for proper foreign key setup
            $table->unsignedBigInteger('created_by')->nullable()->change();
            $table->unsignedBigInteger('updated_by')->nullable()->change();
            $table->unsignedBigInteger('deleted_by')->nullable()->change();
            
            // Add foreign key constraints
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('deleted_by')->references('id')->on('users')->onDelete('set null');
        });

        // Fix customer_insurances table constraints
        Schema::table('customer_insurances', function (Blueprint $table) {
            // Add missing foreign key constraints
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
            $table->foreign('insurance_company_id')->references('id')->on('insurance_companies')->onDelete('set null');
            $table->foreign('policy_type_id')->references('id')->on('policy_types')->onDelete('set null');
            $table->foreign('premium_type_id')->references('id')->on('premium_types')->onDelete('set null');
            $table->foreign('fuel_type_id')->references('id')->on('fuel_types')->onDelete('set null');
            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('set null');
            $table->foreign('broker_id')->references('id')->on('brokers')->onDelete('set null');
            $table->foreign('relationship_manager_id')->references('id')->on('relationship_managers')->onDelete('set null');
            $table->foreign('reference_by')->references('id')->on('reference_users')->onDelete('set null');
            
            // Change audit fields to unsignedBigInteger
            $table->unsignedBigInteger('created_by')->nullable()->change();
            $table->unsignedBigInteger('updated_by')->nullable()->change();
            $table->unsignedBigInteger('deleted_by')->nullable()->change();
            
            // Add audit foreign keys
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('deleted_by')->references('id')->on('users')->onDelete('set null');
        });

        // Add foreign keys to family_groups if it exists
        if (Schema::hasTable('family_groups')) {
            Schema::table('family_groups', function (Blueprint $table) {
                if (Schema::hasColumn('family_groups', 'created_by')) {
                    $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
                    $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
                    $table->foreign('deleted_by')->references('id')->on('users')->onDelete('set null');
                }
            });
        }

        // Add foreign keys to family_members if it exists
        if (Schema::hasTable('family_members')) {
            Schema::table('family_members', function (Blueprint $table) {
                if (Schema::hasColumn('family_members', 'family_group_id')) {
                    $table->foreign('family_group_id')->references('id')->on('family_groups')->onDelete('cascade');
                }
                if (Schema::hasColumn('family_members', 'customer_id')) {
                    $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
                }
            });
        }

        // Add family_group_id foreign key to customers
        if (Schema::hasColumn('customers', 'family_group_id')) {
            Schema::table('customers', function (Blueprint $table) {
                $table->foreign('family_group_id')->references('id')->on('family_groups')->onDelete('set null');
            });
        }

        // Add foreign keys to quotation_companies if it exists
        if (Schema::hasTable('quotation_companies')) {
            Schema::table('quotation_companies', function (Blueprint $table) {
                if (Schema::hasColumn('quotation_companies', 'quotation_id')) {
                    $table->foreign('quotation_id')->references('id')->on('quotations')->onDelete('cascade');
                }
                if (Schema::hasColumn('quotation_companies', 'insurance_company_id')) {
                    $table->foreign('insurance_company_id')->references('id')->on('insurance_companies')->onDelete('cascade');
                }
            });
        }

        // Add foreign keys to customer_audit_logs if it exists
        if (Schema::hasTable('customer_audit_logs')) {
            Schema::table('customer_audit_logs', function (Blueprint $table) {
                if (Schema::hasColumn('customer_audit_logs', 'customer_id')) {
                    $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
                }
            });
        }

        // Add foreign keys to addon_covers if it exists
        if (Schema::hasTable('addon_covers')) {
            Schema::table('addon_covers', function (Blueprint $table) {
                if (Schema::hasColumn('addon_covers', 'created_by')) {
                    $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
                    $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
                    $table->foreign('deleted_by')->references('id')->on('users')->onDelete('set null');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop foreign keys from customers table
        Schema::table('customers', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
            $table->dropForeign(['updated_by']);
            $table->dropForeign(['deleted_by']);
            if (Schema::hasColumn('customers', 'family_group_id')) {
                $table->dropForeign(['family_group_id']);
            }
        });

        // Drop foreign keys from customer_insurances table
        Schema::table('customer_insurances', function (Blueprint $table) {
            $table->dropForeign(['customer_id']);
            $table->dropForeign(['insurance_company_id']);
            $table->dropForeign(['policy_type_id']);
            $table->dropForeign(['premium_type_id']);
            $table->dropForeign(['fuel_type_id']);
            $table->dropForeign(['branch_id']);
            $table->dropForeign(['broker_id']);
            $table->dropForeign(['relationship_manager_id']);
            $table->dropForeign(['reference_by']);
            $table->dropForeign(['created_by']);
            $table->dropForeign(['updated_by']);
            $table->dropForeign(['deleted_by']);
        });

        // Drop other foreign keys conditionally
        if (Schema::hasTable('family_groups')) {
            Schema::table('family_groups', function (Blueprint $table) {
                $table->dropForeign(['created_by']);
                $table->dropForeign(['updated_by']);
                $table->dropForeign(['deleted_by']);
            });
        }

        if (Schema::hasTable('family_members')) {
            Schema::table('family_members', function (Blueprint $table) {
                $table->dropForeign(['family_group_id']);
                $table->dropForeign(['customer_id']);
            });
        }

        if (Schema::hasTable('quotation_companies')) {
            Schema::table('quotation_companies', function (Blueprint $table) {
                $table->dropForeign(['quotation_id']);
                $table->dropForeign(['insurance_company_id']);
            });
        }

        if (Schema::hasTable('customer_audit_logs')) {
            Schema::table('customer_audit_logs', function (Blueprint $table) {
                $table->dropForeign(['customer_id']);
            });
        }

        if (Schema::hasTable('addon_covers')) {
            Schema::table('addon_covers', function (Blueprint $table) {
                $table->dropForeign(['created_by']);
                $table->dropForeign(['updated_by']);
                $table->dropForeign(['deleted_by']);
            });
        }
    }
};
