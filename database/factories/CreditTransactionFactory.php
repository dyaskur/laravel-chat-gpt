<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\UserCoinTransaction;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<UserCoinTransaction>
 */
class CreditTransactionFactory extends Factory
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
            'amount' => $this->faker->numberBetween(100, 1000),
            'type' => 'added',
            'description' => 'Test transaction',
        ];
    }
}
