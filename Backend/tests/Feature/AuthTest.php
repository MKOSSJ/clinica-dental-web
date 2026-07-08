<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_login_with_valid_credentials(): void
    {
        $user = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => 'password123',
            'role' => 'admin',
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'admin@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    'token',
                    'user' => [
                        'id',
                        'name',
                        'email',
                        'role',
                    ],
                ],
            ])
            ->assertJsonPath('data.user.email', $user->email)
            ->assertJsonPath('data.user.role', 'admin');
    }

    public function test_invalid_credentials_return_generic_message(): void
    {
        User::create([
            'name' => 'Staff User',
            'email' => 'staff@example.com',
            'password' => 'password123',
            'role' => 'staff',
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'staff@example.com',
            'password' => 'wrong-password',
        ]);

        $response->assertStatus(401)
            ->assertJsonPath('message', 'credenciales inválidas');
    }
}
