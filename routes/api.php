<?php

use App\Http\Controllers\Api\UserIntegrationController;
use App\Http\Middleware\ApiKeyMiddleware;
use Illuminate\Support\Facades\Route;

Route::middleware(ApiKeyMiddleware::class)->group(function () {
    Route::post('/users', [UserIntegrationController::class, 'store']);
});
