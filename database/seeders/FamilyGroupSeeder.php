<?php

namespace Database\Seeders;

use App\Models\FamilyGroup;
use App\Models\FamilyMember;
use App\Models\Customer;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class FamilyGroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create test customers first
        $customers = [
            [
                'name' => 'Rajesh Sharma',
                'email' => 'rajesh.sharma@example.com',
                'mobile_number' => '9876543210',
                'date_of_birth' => '1980-05-15',
                'type' => 'Retail',
                'password' => Hash::make('password123'),
                'status' => true,
            ],
            [
                'name' => 'Priya Sharma',
                'email' => 'priya.sharma@example.com',
                'mobile_number' => '9876543211',
                'date_of_birth' => '1985-08-20',
                'type' => 'Retail',
                'password' => Hash::make('password123'),
                'status' => true,
            ],
            [
                'name' => 'Arjun Sharma',
                'email' => 'arjun.sharma@example.com',
                'mobile_number' => '9876543212',
                'date_of_birth' => '2010-12-10',
                'type' => 'Retail',
                'password' => Hash::make('password123'),
                'status' => true,
            ],
            [
                'name' => 'Amit Kumar',
                'email' => 'amit.kumar@example.com',
                'mobile_number' => '9876543213',
                'date_of_birth' => '1975-03-25',
                'type' => 'Retail',
                'password' => Hash::make('password123'),
                'status' => true,
            ],
            [
                'name' => 'Sunita Kumar',
                'email' => 'sunita.kumar@example.com',
                'mobile_number' => '9876543214',
                'date_of_birth' => '1978-11-08',
                'type' => 'Retail',
                'password' => Hash::make('password123'),
                'status' => true,
            ],
        ];

        $createdCustomers = [];
        foreach ($customers as $customerData) {
            $createdCustomers[] = Customer::create($customerData);
        }

        // Create Family Group 1: Sharma Family
        $sharmaFamily = FamilyGroup::create([
            'name' => 'Sharma Family',
            'family_head_id' => $createdCustomers[0]->id, // Rajesh as head
            'status' => true,
            'created_by' => 1, // Assuming admin user ID 1
        ]);

        // Create Family Group 2: Kumar Family
        $kumarFamily = FamilyGroup::create([
            'name' => 'Kumar Family',
            'family_head_id' => $createdCustomers[3]->id, // Amit as head
            'status' => true,
            'created_by' => 1, // Assuming admin user ID 1
        ]);

        // Update customers with family group IDs
        $createdCustomers[0]->update(['family_group_id' => $sharmaFamily->id]); // Rajesh
        $createdCustomers[1]->update(['family_group_id' => $sharmaFamily->id]); // Priya
        $createdCustomers[2]->update(['family_group_id' => $sharmaFamily->id]); // Arjun
        $createdCustomers[3]->update(['family_group_id' => $kumarFamily->id]);  // Amit
        $createdCustomers[4]->update(['family_group_id' => $kumarFamily->id]);  // Sunita

        // Create Family Members for Sharma Family
        $sharmaMembers = [
            [
                'family_group_id' => $sharmaFamily->id,
                'customer_id' => $createdCustomers[0]->id,
                'relationship' => 'Head',
                'is_head' => true,
                'status' => true,
                'created_by' => 1,
            ],
            [
                'family_group_id' => $sharmaFamily->id,
                'customer_id' => $createdCustomers[1]->id,
                'relationship' => 'Spouse',
                'is_head' => false,
                'status' => true,
                'created_by' => 1,
            ],
            [
                'family_group_id' => $sharmaFamily->id,
                'customer_id' => $createdCustomers[2]->id,
                'relationship' => 'Child',
                'is_head' => false,
                'status' => true,
                'created_by' => 1,
            ],
        ];

        // Create Family Members for Kumar Family
        $kumarMembers = [
            [
                'family_group_id' => $kumarFamily->id,
                'customer_id' => $createdCustomers[3]->id,
                'relationship' => 'Head',
                'is_head' => true,
                'status' => true,
                'created_by' => 1,
            ],
            [
                'family_group_id' => $kumarFamily->id,
                'customer_id' => $createdCustomers[4]->id,
                'relationship' => 'Spouse',
                'is_head' => false,
                'status' => true,
                'created_by' => 1,
            ],
        ];

        // Insert family members
        foreach (array_merge($sharmaMembers, $kumarMembers) as $memberData) {
            FamilyMember::create($memberData);
        }

        $this->command->info('Family groups and members seeded successfully!');
        $this->command->info('Test Login Credentials:');
        $this->command->info('Family Head 1: rajesh.sharma@example.com / password123');
        $this->command->info('Family Head 2: amit.kumar@example.com / password123');
        $this->command->info('Family Member: priya.sharma@example.com / password123');
    }
}
