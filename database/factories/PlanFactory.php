<?php

namespace Database\Factories;

use App\Enums\BillingInterval;
use App\Models\Organization;
use App\Models\Plan;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Plan>
 */
class PlanFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'organization_id' => Organization::factory(),
            'name' => fake()->unique()->words(2, true),
            'price' => fake()->randomFloat(2, 10, 100),
            'billing_interval' => fake()->randomElement(BillingInterval::cases()),
        ];
    }
}
