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

        // Insert insurance companies data (production data)
        DB::table('insurance_companies')->insert([
            [
                'id' => 1,
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
                'id' => 2,
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
                'id' => 3,
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
                'id' => 4,
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
                'id' => 5,
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
                'id' => 6,
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
                'id' => 7,
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
                'id' => 8,
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
                'id' => 9,
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
                'id' => 10,
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
                'id' => 11,
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
                'id' => 12,
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
                'id' => 13,
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
                'id' => 14,
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
                'id' => 15,
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
                'id' => 16,
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
                'id' => 17,
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
                'id' => 18,
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
                'id' => 19,
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
                'id' => 20,
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