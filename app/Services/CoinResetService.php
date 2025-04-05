<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CoinResetService
{
    protected array $plans_by_product_id = [];

    public function __construct()
    {
        $this->loadPlans();
    }

    private function loadPlans(): void
    {
        $plans = config('cashier.plans');
        foreach ($plans as $item) {
            $product_id = $item['product_id'];
            $this->plans_by_product_id[$product_id] = [
                'product_id' => $product_id,
                'coin' => $item['coin'],
                'interval' => $item['interval'] ?? null,
            ];
        }
    }

    public function resetCoins($entity, $interval): bool
    {

        $now = now()->timezone('UTC')->addMinutes(5);
        if (! $entity->last_coin_reset || $now->diffInDays($entity->last_coin_reset) <= -$interval) {
            DB::beginTransaction();
            try {
                $active_subscriptions = $entity->subscriptions()->where('status', 'active')->get();
                $entity->coinTransactions()->create([
                    'amount' => -$entity->coin_balance,
                    'type' => 'reset',
                    'description' => 'Coins reset',
                ]);
                $coin_gains = $this->calculateCoinGains($active_subscriptions);
                $entity->update([
                    'coin_balance' => $coin_gains,
                    'last_coin_reset' => $now,
                ]);
                $has_subscriptions = $active_subscriptions->count() > 0;

                $entity->coinTransactions()->create([
                    'amount' => $coin_gains,
                    'type' => 'added',
                    'description' => $has_subscriptions ? 'Coins gain from subscriptions' : 'Free coin reset',
                ]);
                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Failed to reset coins', [
                    'entity_id' => $entity->id,
                    'entity_type' => get_class($entity),
                    'error' => $e->getMessage(),
                ]);

                return false;
            }

            return true;
        }

        return false;
    }

    private function calculateCoinGains($subscriptions): int
    {
        if ($subscriptions->count() > 0) {
            return $subscriptions->sum(function ($subscription) {
                return $subscription->items->sum(function ($item) {
                    $plan = $this->plans_by_product_id[$item->product_id] ?? null;
                    if (! $plan) {
                        Log::error('Plan not defined', ['product_id' => $item->product_id]);

                        return 0;
                    }

                    return $plan['coin'];
                });
            });
        }

        return config('app.default_coin_available', 10);
    }
}
