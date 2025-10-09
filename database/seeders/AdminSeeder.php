<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create Admin User
        $user = User::create([
            'first_name' => 'Darshan',
            'last_name' => 'Baraiya',
            'email' => 'webmonks.in@gmail.com',
            'mobile_number' => '8000071314',
            'password' => Hash::make('Webmonks239#'),
            'role_id' => 1,
            'email_verified_at' => now(),
            'status' => 1,
            'created_at' => now(),
            'updated_at' => now(),
            'created_by' => 1,
            'updated_by' => 1,
        ]);
    }
}
