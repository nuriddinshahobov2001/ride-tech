<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_register_passenger(): void
    {
        $payload = [
            'name' => 'Пассажир Пассажиров',
            'email' => 'passenger@example.com',
            'phone' => '+992987671091',
            'password' => 'password',
            'role' => 'passenger',
        ];

        $response = $this->postJson('/api/register', $payload);

        $response->assertStatus(201);

        $this->assertDatabaseHas('users', [
            'email' => 'passenger@example.com',
            'role' => UserRole::PASSENGER->value,
        ]);
    }

    public function test_register_driver(): void
    {
        $payload = [
            'name' => 'Водитель Водителей',
            'email' => 'driver@example.com',
            'phone' => '+992000207747',
            'password' => 'password',
            'role' => 'driver',
        ];

        $response = $this->postJson('/api/register', $payload);

        $response->assertStatus(201);

        $this->assertDatabaseHas('users', [
            'email' => 'driver@example.com',
            'role' => UserRole::DRIVER->value,
        ]);
    }

    public function test_login_with_email(): void
    {
        User::factory()->create([
            'email' => 'login_test@example.com',
            'phone' => '+998903333333',
            'password' => 'password',
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'login_test@example.com',
            'password' => 'password',
        ]);

        $response->assertStatus(200);
    }

    public function test_logout(): void
    {
        $user = User::factory()->create([
            'phone' => '+998907777777',
        ]);
        $token = $user->createToken('auth_token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/logout');

        $response->assertStatus(200);

        $this->assertDatabaseCount('personal_access_tokens', 0);
    }
}
