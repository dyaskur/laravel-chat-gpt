<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ResetUserCoins extends Command
{
    protected $signature = 'coins:reset';

    protected $description = 'Reset user coin credits daily at 00:00 GMT';

    public function handle(): void
    {
        $count = 0;
        $plans = config('cashier.plans');
        $plans_by_product_id = [];
        foreach ($plans as $key => $item) {
            $product_id = $item['product_id'];
            if (! isset($plans_by_product_id[$product_id])) {
                $plans_by_product_id[$product_id] = [
                    'product_id' => $product_id,
                    'coin' => $item['coin'],
                    'interval' => $item['interval'] ?? null,
                ];
            }
        }

        // todo: investigate whether use filter by last reset  and reset type, or keep this, which is more performance
        User::all()->each(function (User $user) use (&$count, $plans_by_product_id) {
            $now = now()->timezone('UTC')->add('5 minutes');
            if (! $user->last_coin_reset || $now->diffInDays($user->last_coin_reset) <= -1) {
                $active_subscriptions = $user->subscriptions()->where('status', 'active')->get();
                $user->creditTransactions()->create([
                    'amount' => -$user->coin_balance,
                    'type' => 'reset',
                    'description' => 'Credits reset to default',
                ]);
                $coin_gains = 0;

                if ($active_subscriptions->count() > 0) {
                    foreach ($active_subscriptions as $subscription) {
                        foreach ($subscription->items as $item) {
                            $plan = $plans_by_product_id[$item->product_id];
                            if (! $plan) {
                                Log::error('plan not defined', $plan);
                            }
                            $coin_gains += $plan['coin'];
                        }
                    }
                } else {
                    $coin_gains = config('app.default_credit_available') ?? 10;
                }
                $user->update(
                    ['coin_balance' => $coin_gains,
                        'last_coin_reset' => $now, ]
                );
                $user->creditTransactions()->create(
                    [
                        'amount' => $coin_gains,
                        'type' => 'added',
                        'description' => 'Credits reset to default',
                    ]
                );
                $count++;
            }

        });

        $this->info($count.' user credits have been reset at 00:00 GMT.');
    }
}
