<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/subscribe', function (Request $request) {
    $subscriptions = $request->user()->subscriptions()->get();
    dd($request->user()->customer);
    //    dd($subscriptions->first()->asPaddleSubscription());

    $checkout = $request->user()->checkout(config('cashier.plans.price_basic_monthly'))
        ->returnTo(route('dashboard'));

    return view('subscribe', ['checkout' => $checkout]);
})->name('subscribe');

Route::post('/paddle/webhook', \App\Http\Controllers\PaddleWebhookController::class);

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
