<?php

namespace Database\Factories;

use App\Models\AuditLog;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AuditLog>
 */
class AuditLogFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'action' => 'subscription.'.fake()->randomElement(['created', 'updated', 'canceled']),
            'auditable_type' => Subscription::class,
            'auditable_id' => Subscription::factory(),
            'old_value' => ['status' => fake()->randomElement(['trialing', 'active'])],
            'new_value' => ['status' => fake()->randomElement(['active', 'past_due', 'canceled'])],
        ];
    }
}
