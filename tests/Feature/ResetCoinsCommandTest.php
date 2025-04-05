<?php

use App\Models\Team;
use App\Models\User;
use Illuminate\Support\Facades\Artisan;

it('resets user coins', function () {
    User::factory()->count(3)->create(['last_coin_reset' => now()->subDays(2), 'coin_balance' => 50]);

    Artisan::call('coins:reset user');

    expect(User::first()->coin_balance)->toEqual(config('app.default_coin_available', 10));
    expect(Artisan::output())->toContain('user credits have been reset. 3 successful and 0 skipped');
});

it('resets user coins with skipped', function () {
    User::factory()->count(3)->create(['last_coin_reset' => now()->subDays(1), 'coin_balance' => 50]);
    User::factory()->count(2)->create(['coin_balance' => 50]); // last_coin_reset = null
    User::factory()->count(2)->create(['last_coin_reset' => now(), 'coin_balance' => 50]);

    Artisan::call('coins:reset user');

    expect(User::first()->coin_balance)->toEqual(config('app.default_coin_available', 10));
    expect(Artisan::output())->toContain('user credits have been reset. 5 successful and 2 skipped');
});

it('resets team coins', function () {
    Team::factory()->count(2)->create(['last_coin_reset' => now()->subDays(8), 'coin_balance' => 100]);

    Artisan::call('coins:reset team');

    expect(Team::first()->coin_balance)->toEqual(config('app.default_coin_available', 10));
    expect(Artisan::output())->toContain('team credits have been reset. 2 successful and 0 skipped');
});

it('handles invalid entity argument', function () {
    Artisan::call('coins:reset invalid');
    expect(Artisan::output())->toContain('Invalid entity type');
});
