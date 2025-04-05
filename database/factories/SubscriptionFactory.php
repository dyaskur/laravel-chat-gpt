<?php

namespace Database\Factories;

use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Laravel\Paddle\Subscription;

class SubscriptionFactory extends Factory
{
    protected $model = Subscription::class;

    public function definition()
    {
        // Randomly select either a User or a Team for the billable model
        $billableModel = $this->faker->randomElement([User::class, Team::class]);

        return ['billable_id' => User::factory(),  // Assuming User is the billable model
            'billable_type' => User::class,
            'type' => $this->faker->word,  // Example subscription type (e.g., 'premium', 'basic')
            'paddle_id' => $this->faker->unique()->uuid,  // Unique Paddle subscription ID
            'status' => $this->faker->randomElement(['active', 'paused', 'cancelled']),  // Subscription status
            'trial_ends_at' => $this->faker->optional()->dateTimeThisYear(),  // Optional trial end date
            'paused_at' => $this->faker->optional()->dateTimeThisYear(),  // Optional paused date
            'ends_at' => $this->faker->optional()->dateTimeThisYear(),  // Optional end date
        ];
    }
}
