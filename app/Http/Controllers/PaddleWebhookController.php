<?php

namespace App\Http\Controllers;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Laravel\Paddle\Events\SubscriptionCreated;
use Laravel\Paddle\Http\Controllers\WebhookController;
use Laravel\Paddle\Subscription;

class PaddleWebhookController extends WebhookController
{
    /**
     * Handle a Paddle subscription created event.
     */
    public function handleSubscriptionCreated(array $payload): void
    {
        Log::info('Custom Handling: Subscription Created', $payload);

        $data = $payload['data'];

        if ($this->subscriptionExists($data['id'])) {
            return;
        }

        /** @var User $billable */
        if (! $billable = $this->findBillable($data['customer_id'])) {
            return;
        }

        $subscription = $billable->subscriptions()->create([
            'type' => $data['custom_data']['subscription_type'] ?? Subscription::DEFAULT_TYPE,
            'paddle_id' => $data['id'],
            'status' => $data['status'],
            'trial_ends_at' => $data['status'] === Subscription::STATUS_TRIALING
                ? Carbon::parse($data['next_billed_at'], 'UTC')
                : null,
        ]);

        foreach ($data['items'] as $item) {
            $subscription->items()->create([
                'product_id' => $item['price']['product_id'],
                'price_id' => $item['price']['id'],
                'status' => $item['status'],
                'quantity' => $item['quantity'] ?? 1,
            ]);

            $credits = $item['price']['custom_data']['credits'] ?? 0;

            $credits *= $item['quantity'] ?? 1;
            if (! $credits) {
                Log::warning('no credits accredited', $payload);
            } else {
                $billable->increment('coin_balance', $credits);
                $billable->creditTransactions()->create([
                    'amount' => $credits,
                    'type' => 'added',
                    'description' => 'new subscription',
                ]);
            }
        }

        $billable->customer->update(['trial_ends_at' => null]);

        SubscriptionCreated::dispatch($billable, $subscription, $payload);
    }
}
