<?php

use App\Models\Team;
use App\Models\User;
use Illuminate\Support\Facades\Artisan;

it('resets user coins', function () {
    User::factory()->count(3)->create(['last_coin_reset' => now()->subDays(2), 'coin_balance' => 50]);

    Artisan::call('coins:reset user');

    expect(User::first()->coin_balance)->toEqual(config('app.default_coin_available', 10));
    expect(Artisan::output())->toContain('3 user credits have been reset.');
});

it('resets team coins', function () {
    Team::factory()->count(2)->create(['last_coin_reset' => now()->subDays(8), 'coin_balance' => 100]);

    Artisan::call('coins:reset team');

    expect(Team::first()->coin_balance)->toEqual(config('app.default_coin_available', 10));
    expect(Artisan::output())->toContain('2 team credits have been reset.');
});

it('handles invalid entity argument', function () {
    Artisan::call('coins:reset invalid');
    expect(Artisan::output())->toContain('Invalid entity type');
});
