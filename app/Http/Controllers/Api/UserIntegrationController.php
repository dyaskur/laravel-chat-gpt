<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UserIntegrationController extends Controller
{
    // Create user with initial credits
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'external_id' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'platform' => 'required|string',
            'metadata' => 'array|nullable',
        ]);


        DB::beginTransaction();

        try {
            $user = User::create([
                'name' => $validated['name'],
                'password' => '',
                'email' => $validated['email'],
            ]);

            $user->instegrations()->create([
                'platform' => $validated['platform'],
                'external_id' => $validated['external_id'],
                'external_email' => $validated['email'],
                'metadata' => $validated['metadata'] ?? [],
            ]);


            $user_credit = $user->credit()->create([
                'balance' => config('app.default_credit_available') ?? 1000,
                'reset_type' => config('app.default_credit_type') ?? 'daily',
            ]);

            if ($user_credit->balance > 0) {
                $user->transactions()->create([
                    'amount' => $user_credit->balance,
                    'type' => 'added',
                    'description' => 'Initial credit balance',
                ]);
            }
            // 🔹 Commit Transaction
            DB::commit();

            return response()->json(['message' => 'User created', 'data' => $user], 201);
        } catch (\Exception $e) {
            // 🔹 Rollback Transaction if Error Occurs
            DB::rollBack();
            Log::error("User creation failed. Email {$validated['email']} Error: {$e->getMessage()}", $e->getTrace());
            return response()->json(['message' => 'User creation failed', 'error' => $e->getMessage()], 500);
        }
    }
}
