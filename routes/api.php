<?php


use App\Http\Controllers\Api\UserIntegrationController;
use Illuminate\Support\Facades\Route;

Route::post('/users', [UserIntegrationController::class, 'store']); // Create user with credits
