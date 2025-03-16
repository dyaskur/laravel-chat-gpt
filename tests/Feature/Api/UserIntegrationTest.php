<?php

use App\Models\User;

test('example', function () {
    $response = $this->get('/');

    $response->assertStatus(200);
});


it('creates a user with initial credits', function () {
    $payload = [
        'name' => 'John Doe',
        'external_id' => 'ext_1234',
        'email' => 'john@example.com',
        'platform' => 'test_platform',
    ];

    $response = $this->postJson('/api/users', $payload);

    $response->assertStatus(201)
        ->assertJsonStructure([
            'message',
            'user' => ['id', 'name', 'email'],
        ]);

    $user = User::where('email', 'john@example.com')->first();
    expect($user->name)->toBe('John Doe')
        ->and($user->credit->balance)->toBe(config('app.default_credit_available'));
});

it('fails to create a user with a duplicate email', function () {
    User::factory()->create(['email' => 'john@example.com']);

    $payload = [
        'name' => 'John Doe',
        'external_id' => 'ext_1234',
        'email' => 'john@example.com',
        'platform' => 'test_platform',
    ];

    $response = $this->postJson('/api/users', $payload);

    $response->assertStatus(422)
        ->assertJsonValidationErrors('email');
});
