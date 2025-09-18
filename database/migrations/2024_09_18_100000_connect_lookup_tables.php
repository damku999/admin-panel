<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class ConnectLookupTables extends Migration
{
    /**
     * Run the migrations.
     *
     * Convert enum fields to foreign key relationships with lookup tables
     *
     * @return void
     */
    public function up()
    {
        // Skip for testing environment
        if (app()->environment() === 'testing') {
            return;
        }

        // Step 1: Add foreign key columns to tables
        $this->addForeignKeyColumns();

        // Step 2: Migrate data from enum/varchar to lookup table IDs
        $this->migrateExistingData();

        // Step 3: Add foreign key constraints
        $this->addForeignKeyConstraints();

        // Step 4: Remove old enum/varchar columns (optional - commented out for safety)
        // $this->removeOldColumns();
    }

    /**
     * Add foreign key columns to tables
     */
    private function addForeignKeyColumns()
    {
        // Add customer_type_id to customers table
        if (Schema::hasTable('customers')) {
            Schema::table('customers', function (Blueprint $table) {
                $table->unsignedBigInteger('customer_type_id')->nullable()->after('type');
            });
        }

        // Add commission_type_id to customer_insurances table
        if (Schema::hasTable('customer_insurances')) {
            Schema::table('customer_insurances', function (Blueprint $table) {
                $table->unsignedBigInteger('commission_type_id')->nullable()->after('commission_on');
            });
        }

        // Add quotation_status_id to quotations table (only if it exists)
        if (Schema::hasTable('quotations')) {
            Schema::table('quotations', function (Blueprint $table) {
                $table->unsignedBigInteger('quotation_status_id')->nullable()->after('status');
            });
        }
    }

    /**
     * Migrate existing data from enum/varchar to lookup table IDs
     */
    private function migrateExistingData()
    {
        // Migrate customer types (only if customers table exists)
        if (Schema::hasTable('customers')) {
            $customerTypeMapping = [
                'Corporate' => 1,
                'Retail' => 2,
                'corporate' => 1,
                'retail' => 2,
            ];

            foreach ($customerTypeMapping as $typeName => $typeId) {
                DB::table('customers')
                    ->where('type', $typeName)
                    ->update(['customer_type_id' => $typeId]);
            }
        }

        // Migrate commission types (only if customer_insurances table exists)
        if (Schema::hasTable('customer_insurances')) {
            $commissionTypeMapping = [
                'net_premium' => 1,
                'od_premium' => 2,
                'tp_premium' => 3,
            ];

            foreach ($commissionTypeMapping as $commissionName => $commissionId) {
                DB::table('customer_insurances')
                    ->where('commission_on', $commissionName)
                    ->update(['commission_type_id' => $commissionId]);
            }
        }

        // Migrate quotation statuses (only if quotations table exists)
        if (Schema::hasTable('quotations')) {
            $quotationStatusMapping = [
                'Draft' => 1,
                'Generated' => 2,
                'Sent' => 3,
                'Accepted' => 4,
                'Rejected' => 5,
                'draft' => 1,
                'generated' => 2,
                'sent' => 3,
                'accepted' => 4,
                'rejected' => 5,
            ];

            foreach ($quotationStatusMapping as $statusName => $statusId) {
                DB::table('quotations')
                    ->where('status', $statusName)
                    ->update(['quotation_status_id' => $statusId]);
            }
        }
    }

    /**
     * Add foreign key constraints
     */
    private function addForeignKeyConstraints()
    {
        if (Schema::hasTable('customers')) {
            Schema::table('customers', function (Blueprint $table) {
                $table->foreign('customer_type_id', 'fk_customers_customer_type')
                      ->references('id')->on('customer_types')
                      ->onDelete('set null');
            });
        }

        if (Schema::hasTable('customer_insurances')) {
            Schema::table('customer_insurances', function (Blueprint $table) {
                $table->foreign('commission_type_id', 'fk_ci_commission_type')
                      ->references('id')->on('commission_types')
                      ->onDelete('set null');
            });
        }

        if (Schema::hasTable('quotations')) {
            Schema::table('quotations', function (Blueprint $table) {
                $table->foreign('quotation_status_id', 'fk_quotations_status')
                      ->references('id')->on('quotation_statuses')
                      ->onDelete('set null');
            });
        }
    }

    /**
     * Remove old enum/varchar columns (commented out for safety)
     * Uncomment after verifying the migration works correctly
     */
    private function removeOldColumns()
    {
        // Schema::table('customers', function (Blueprint $table) {
        //     $table->dropColumn('type');
        // });

        // Schema::table('customer_insurances', function (Blueprint $table) {
        //     $table->dropColumn('commission_on');
        // });

        // Schema::table('quotations', function (Blueprint $table) {
        //     $table->dropColumn('status');
        // });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Drop foreign key constraints
        if (Schema::hasTable('customers')) {
            Schema::table('customers', function (Blueprint $table) {
                try {
                    $table->dropForeign('fk_customers_customer_type');
                } catch (\Exception $e) {
                    // Continue if constraint doesn't exist
                }
            });
        }

        if (Schema::hasTable('customer_insurances')) {
            Schema::table('customer_insurances', function (Blueprint $table) {
                try {
                    $table->dropForeign('fk_ci_commission_type');
                } catch (\Exception $e) {
                    // Continue if constraint doesn't exist
                }
            });
        }

        if (Schema::hasTable('quotations')) {
            Schema::table('quotations', function (Blueprint $table) {
                try {
                    $table->dropForeign('fk_quotations_status');
                } catch (\Exception $e) {
                    // Continue if constraint doesn't exist
                }
            });
        }

        // Drop foreign key columns
        if (Schema::hasTable('customers')) {
            Schema::table('customers', function (Blueprint $table) {
                if (Schema::hasColumn('customers', 'customer_type_id')) {
                    $table->dropColumn('customer_type_id');
                }
            });
        }

        if (Schema::hasTable('customer_insurances')) {
            Schema::table('customer_insurances', function (Blueprint $table) {
                if (Schema::hasColumn('customer_insurances', 'commission_type_id')) {
                    $table->dropColumn('commission_type_id');
                }
            });
        }

        if (Schema::hasTable('quotations')) {
            Schema::table('quotations', function (Blueprint $table) {
                if (Schema::hasColumn('quotations', 'quotation_status_id')) {
                    $table->dropColumn('quotation_status_id');
                }
            });
        }
    }
}