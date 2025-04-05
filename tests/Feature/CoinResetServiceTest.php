<?php

use App\Models\User;
use App\Models\UserCoinTransaction;
use App\Services\CoinResetService;
use Illuminate\Support\Facades\Config;

beforeEach(function () {
    Config::set('cashier.plans', [
        'basic' => ['product_id' => 'prod_123', 'coin' => 20],
    ]);
    $this->coinResetService = new CoinResetService;
});

it('resets coins without a previous reset', function () {
    $user = User::factory()->create([
        'coin_balance' => 50,
        'last_coin_reset' => null,
    ]);

    expect($this->coinResetService->resetCoins($user, 1))->toBeTrue()
        ->and(UserCoinTransaction::where('user_id', $user->id)->where('amount', -50)->where('type', 'reset')->exists())->toBeTrue()
        ->and(UserCoinTransaction::where('user_id', $user->id)->where('amount', config('app.default_credit_available', 10))->where('type', 'added')->exists())->toBeTrue();

    $user->refresh();
    expect($user->coin_balance)->toEqual(config('app.default_credit_available', 10));
});

it('resets coins with active subscriptions', function () {

    $user = User::factory()->create([
        'coin_balance' => 50,
        'last_coin_reset' => now()->subDays(2),
    ]);

    $subscription = (new \Database\Factories\SubscriptionFactory)->create(['billable_id' => $user->id, 'billable_type' => $user->getMorphClass(), 'type' => 'main', 'paddle_id' => 'sub_123', 'status' => 'active']);
    $subscription->items()->create(['product_id' => 'prod_123', 'price_id' => '1', 'status' => 'z', 'quantity' => 1]);

    expect($this->coinResetService->resetCoins($user, 1))->toBeTrue();

    $user->refresh();
    expect($user->coin_balance)->toEqual(20);
});

it('does not reset coins if not due', function () {
    $user = User::factory()->create([
        'coin_balance' => 50,
        'last_coin_reset' => now(),
    ]);

    expect($this->coinResetService->resetCoins($user, 1))->toBeFalse();

    expect(UserCoinTransaction::where('user_id', $user->id)->where('type', 'reset')->exists())->toBeFalse();
});
