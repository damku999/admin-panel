<?php

namespace Database\Factories;

use App\Models\Claim;
use App\Models\CustomerInsurance;
use Illuminate\Database\Eloquent\Factories\Factory;

class ClaimFactory extends Factory
{
    protected $model = Claim::class;

    public function definition()
    {
        return [
            'customer_insurance_id' => CustomerInsurance::factory(),
            'claim_number' => 'CL-' . $this->faker->unique()->numerify('########'),
            'insurance_type' => $this->faker->randomElement(['Health', 'Vehicle']),
            'incident_date' => $this->faker->dateTimeBetween('-30 days', 'now'),
            'description' => $this->faker->paragraph(),
            'whatsapp_number' => $this->faker->phoneNumber(),
            'status' => $this->faker->boolean(80), // 80% chance of being active
            'send_email_notifications' => $this->faker->boolean(30), // 30% chance of email notifications
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}