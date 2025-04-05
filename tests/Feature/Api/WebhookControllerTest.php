<?php

use App\Models\User;
use Laravel\Paddle\Cashier;
use Laravel\Paddle\Events\SubscriptionCreated;
use Laravel\Paddle\Subscription;

function createBillable(string $description = 'taylor', array $options = []): User
{
    $user = createUser($description);

    Cashier::fake([
        'customers*' => [
            'data' => [[
                'id' => 'cus_123456789',
                'name' => $user->name,
                'email' => $user->email,
            ]],
        ],
    ]);

    $user->createAsCustomer($options);

    return $user;
}

function createUser(string $description = 'taylor', array $options = []): User
{
    return User::create(array_merge([
        'email' => "{$description}@paddle-test.com",
        'name' => 'Taylor Otwell',
        'password' => bcrypt('password'),
    ], $options));
}

function generatePaddleSignature(array $data, string $secret): string
{
    // Generate current timestamp
    $timestamp = now()->timestamp;
    $jsonPayload = json_encode($data);
    // Create the signed data string
    $signedData = "{$timestamp}:{$jsonPayload}";

    // Compute HMAC hash
    $hash = hash_hmac('sha256', $signedData, $secret);

    return "ts={$timestamp};h1={$hash}";
    //    return hash_hmac('sha256', json_encode($data), $secret);
}
it('can handle a subscription created event', function () {
    Cashier::fake();

    $user = createBillable();

    $data = [
        'event_type' => 'subscription_created',
        'data' => [
            'id' => 'sub_123456789',
            'customer_id' => 'cus_123456789',
            'status' => Subscription::STATUS_ACTIVE,
            'custom_data' => [
                'subscription_type' => 'main',
            ],
            'items' => [
                [
                    'price' => [
                        'id' => 'pri_123456789',
                        'product_id' => 'pro_123456789',
                        'custom_data' => [
                            'credits' => 100,
                        ],
                    ],
                    'status' => 'active',
                    'quantity' => 2,
                ],
            ],
        ],
    ];
    $webhookSecret = config('cashier.webhook_secret');
    $signature = generatePaddleSignature($data, $webhookSecret);

    $response = $this->postJson('paddle/webhook', $data, [
        'Paddle-Signature' => $signature,
    ]);
    $response->assertOk();
    $this->assertDatabaseHas('customers', [
        'billable_id' => $user->id,
        'billable_type' => $user->getMorphClass(),
        'paddle_id' => 'cus_123456789',
    ]);

    $this->assertDatabaseHas('subscriptions', [
        'billable_id' => $user->id,
        'billable_type' => $user->getMorphClass(),
        'type' => 'main',
        'paddle_id' => 'sub_123456789',
        'status' => Subscription::STATUS_ACTIVE,
        'trial_ends_at' => null,
    ]);

    $this->assertDatabaseHas('subscription_items', [
        'subscription_id' => 1,
        'product_id' => 'pro_123456789',
        'price_id' => 'pri_123456789',
        'status' => 'active',
        'quantity' => 2,
    ]);

    $this->assertDatabaseHas('users', [
        'coin_balance' => 200,
    ]);

    $this->assertDatabaseHas('user_coin_transactions', [
        'amount' => 200,
        'type' => 'added',
        'description' => 'new subscription',
    ]);

    Cashier::assertSubscriptionCreated(function (SubscriptionCreated $event) use ($user) {
        return $event->billable->id === $user->id && $event->subscription->paddle_id === 'sub_123456789';
    });

    // repost with the same data
    $response = $this->postJson('paddle/webhook', $data, [
        'Paddle-Signature' => $signature,
    ]);
    // make sure the subscription is 1, so no duplicate data
    $this->assertDatabaseCount('subscriptions', 1);
});
