<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\UserCredit;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<UserCredit>
 */
class UserCreditFactory extends Factory
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
            'balance' => config('app.default_credit_available', 1000),
            'reset_type' => config('app.default_credit_type', 'daily'),
        ];
    }
}
