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
            'space' => 'array|nullable',
        ]);

        DB::beginTransaction();
        try {
            /** @var User $user */
            $user = User::create([
                'name' => $validated['name'],
                'password' => '',
                'email' => $validated['email'],
                'coin_balance' => config('app.default_credit_available') ?? 10,
            ]);

            $integration = $user->instegrations()->create([
                'platform' => $validated['platform'],
                'external_id' => $validated['external_id'],
                'external_email' => $validated['email'],
                'metadata' => $validated['metadata'] ?? [],
            ]);

            if (! empty($validated['space'])) {
                $space_data = [
                    'name' => $validated['space']['displayName'],
                    'integration_id' => $validated['space']['name'],
                    'integration_name' => 'google_chat',
                    'integration_metadata' => json_encode($validated['space']),
                ];
                $user->teams()->firstOrCreate($space_data);
            }

            if ($user->coin_balance > 0) {
                $user->coinTransactions()->create([
                    'amount' => $user->coin_balance,
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

            return response()->json(['message' => 'User creation failed', 'error' => $e->getMessage(), 'z' => $e->getTrace()], 500);
        }
    }
}
