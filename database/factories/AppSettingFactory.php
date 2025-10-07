<?php

namespace Database\Factories;

use App\Models\AppSetting;
use Illuminate\Database\Eloquent\Factories\Factory;

class AppSettingFactory extends Factory
{
    protected $model = AppSetting::class;

    public function definition()
    {
        $types = ['string', 'integer', 'boolean', 'json', 'text'];
        $categories = ['general', 'email', 'sms', 'whatsapp', 'notification', 'business', 'technical', 'security'];

        return [
            'key' => $this->faker->unique()->word() . '_setting',
            'value' => $this->faker->word(),
            'type' => $this->faker->randomElement($types),
            'category' => $this->faker->randomElement($categories),
            'description' => $this->faker->sentence(),
            'is_encrypted' => false,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    public function encrypted()
    {
        return $this->state(function (array $attributes) {
            return [
                'is_encrypted' => true,
            ];
        });
    }
}
