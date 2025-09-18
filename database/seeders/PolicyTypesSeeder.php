<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PolicyTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing data
        DB::table('policy_types')->truncate();

        // Insert policy types data
        DB::table('policy_types')->insert([
            [
                'name' => 'FRESH',
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
                'deleted_at' => null,
                'created_by' => null,
                'updated_by' => null,
                'deleted_by' => null
            ],
            [
                'name' => 'ROLLOVER',
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
                'deleted_at' => null,
                'created_by' => null,
                'updated_by' => null,
                'deleted_by' => null
            ],
            [
                'name' => 'RENEWAL',
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