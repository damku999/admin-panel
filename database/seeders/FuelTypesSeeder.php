<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FuelTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing data
        DB::table('fuel_types')->truncate();

        // Insert fuel types data
        DB::table('fuel_types')->insert([
            [
                'name' => 'PETROL',
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
                'deleted_at' => null,
                'created_by' => null,
                'updated_by' => null,
                'deleted_by' => null
            ],
            [
                'name' => 'DIESEL',
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
                'deleted_at' => null,
                'created_by' => null,
                'updated_by' => null,
                'deleted_by' => null
            ],
            [
                'name' => 'CNG',
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
                'deleted_at' => null,
                'created_by' => null,
                'updated_by' => null,
                'deleted_by' => null
            ],
            [
                'name' => 'EV',
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