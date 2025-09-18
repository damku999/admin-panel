<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InsuranceCompaniesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing data
        DB::table('insurance_companies')->truncate();

        // Insert insurance companies data
        DB::table('insurance_companies')->insert([
            [
                'name' => 'CARE HEALTH INSURANCE LTD',
                'email' => null,
                'mobile_number' => null,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
                'deleted_at' => null,
                'created_by' => null,
                'updated_by' => null,
                'deleted_by' => null
            ],
            [
                'name' => 'BAJAJ ALLIANZ GIC LTD',
                'email' => null,
                'mobile_number' => null,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
                'deleted_at' => null,
                'created_by' => null,
                'updated_by' => null,
                'deleted_by' => null
            ],
            [
                'name' => 'TATA AIG GIC LTD',
                'email' => null,
                'mobile_number' => null,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
                'deleted_at' => null,
                'created_by' => null,
                'updated_by' => null,
                'deleted_by' => null
            ],
            [
                'name' => 'MAGMA HDI GIC LTD',
                'email' => null,
                'mobile_number' => null,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
                'deleted_at' => null,
                'created_by' => null,
                'updated_by' => null,
                'deleted_by' => null
            ],
            [
                'name' => 'GO DIGIT GENERAL INSURANCE LTD',
                'email' => null,
                'mobile_number' => null,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
                'deleted_at' => null,
                'created_by' => null,
                'updated_by' => null,
                'deleted_by' => null
            ],
            [
                'name' => 'RELIANCE GIC LTD',
                'email' => null,
                'mobile_number' => null,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
                'deleted_at' => null,
                'created_by' => null,
                'updated_by' => null,
                'deleted_by' => null
            ],
            [
                'name' => 'ICICI LOMBARD GIC LTD',
                'email' => null,
                'mobile_number' => null,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
                'deleted_at' => null,
                'created_by' => null,
                'updated_by' => null,
                'deleted_by' => null
            ],
            [
                'name' => 'THE NEW INDIA ASSURANCE CO LTD',
                'email' => null,
                'mobile_number' => null,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
                'deleted_at' => null,
                'created_by' => null,
                'updated_by' => null,
                'deleted_by' => null
            ],
            [
                'name' => 'TATA AIA LIFE INSURANCE CO LTD',
                'email' => null,
                'mobile_number' => null,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
                'deleted_at' => null,
                'created_by' => null,
                'updated_by' => null,
                'deleted_by' => null
            ],
            [
                'name' => 'ICICI PRU LIFE INSURANCE CO LTD',
                'email' => null,
                'mobile_number' => null,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
                'deleted_at' => null,
                'created_by' => null,
                'updated_by' => null,
                'deleted_by' => null
            ],
            [
                'name' => 'HDFC ERGO GIC LTD',
                'email' => null,
                'mobile_number' => null,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
                'deleted_at' => null,
                'created_by' => null,
                'updated_by' => null,
                'deleted_by' => null
            ],
            [
                'name' => 'LIBERTY GENERAL INSURANCE LTD',
                'email' => null,
                'mobile_number' => null,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
                'deleted_at' => null,
                'created_by' => null,
                'updated_by' => null,
                'deleted_by' => null
            ],
            [
                'name' => 'ZUNO GENERAL INSURANCE LTD',
                'email' => null,
                'mobile_number' => null,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
                'deleted_at' => null,
                'created_by' => null,
                'updated_by' => null,
                'deleted_by' => null
            ],
            [
                'name' => 'LIC OF INDIA',
                'email' => null,
                'mobile_number' => null,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
                'deleted_at' => null,
                'created_by' => null,
                'updated_by' => null,
                'deleted_by' => null
            ],
            [
                'name' => 'CHOLA MS GIC',
                'email' => null,
                'mobile_number' => null,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
                'deleted_at' => null,
                'created_by' => null,
                'updated_by' => null,
                'deleted_by' => null
            ],
            [
                'name' => 'ROYAL SUNDARAM GIC LTD',
                'email' => null,
                'mobile_number' => null,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
                'deleted_at' => null,
                'created_by' => null,
                'updated_by' => null,
                'deleted_by' => null
            ],
            [
                'name' => 'THE ORIENTAL INSURANCE COMPANY LIMITED',
                'email' => null,
                'mobile_number' => null,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
                'deleted_at' => null,
                'created_by' => null,
                'updated_by' => null,
                'deleted_by' => null
            ],
            [
                'name' => 'ADITYA BIRLA HEALTH INSURANCE CO LTD',
                'email' => null,
                'mobile_number' => null,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
                'deleted_at' => null,
                'created_by' => null,
                'updated_by' => null,
                'deleted_by' => null
            ],
            [
                'name' => 'KOTAK GIC LTD',
                'email' => null,
                'mobile_number' => null,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
                'deleted_at' => null,
                'created_by' => null,
                'updated_by' => null,
                'deleted_by' => null
            ],
            [
                'name' => 'SBI GIC LTD',
                'email' => null,
                'mobile_number' => null,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
                'deleted_at' => null,
                'created_by' => null,
                'updated_by' => null,
                'deleted_by' => null
            ]
        ]);
    }
}