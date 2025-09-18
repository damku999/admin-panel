<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CommissionTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing data
        DB::table('commission_types')->truncate();

        // Insert commission types data
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
    }
}