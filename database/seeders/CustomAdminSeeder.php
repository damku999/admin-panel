<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CustomAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create Custom Admin User
        User::updateOrCreate(
            ['email' => 'parthrawal89@gmail.com'],
            [
                'first_name'    => 'Parth',
                'last_name'     => 'Rawal',
                'email'         => 'parthrawal89@gmail.com',
                'mobile_number' => '9999999999',
                'password'      => Hash::make('Devyaan@1967'),
                'role_id'       => 1,
                'status'        => 1
            ]
        );
    }
}