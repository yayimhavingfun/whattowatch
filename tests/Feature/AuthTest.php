<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use function PHPSTORM_META\map;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_register_route()
    {
        $new_user = User::factory()->make();
        $data = [
            'email' => $new_user->email,
            'name' => $new_user->name,
            'password' => $new_user->password,
            'password_confirmation' => $new_user->password,
        ];

        $response = $this->postJson(route('auth.register'), $data);

        $response->assertStatus(201);
        $response->assertJsonStructure(['data' => ['token', 'user']]);

        $this->assertDatabaseHas('users', [
            'name' => $new_user->name,
            'email' => $new_user->email,
        ]);
    }

    public function test_login_route()
    {
        $user = User::factory()->create(['password' => '12345678']);
        $data = [
            'email' => $user->email,
            'password' => '12345678',
        ];

        $response = $this->postJson(route('auth.login'), $data);

        $response->assertStatus(200);
        $response->assertJsonStructure(['data' => ['token']]);
    }

    public function test_login_route_wrong_password()
    {
        $user = User::factory()->create(['password' => '12345678']);
        $data = [
            'email' => $user->email,
            'password' => '09876543',
        ];

        $response = $this->postJson(route('auth.login'), $data);

        $response->assertStatus(401);
        $response->assertJsonFragment(['message' => 'Неверное имя пользователя или пароль.']);
    }

    public function test_logout_route()
    {
        $user = User::factory()->create(['password' => '12345678']);
        $token = $user->createToken('auth-token');

        $response = $this->postJson(
            route('auth.logout'),
            [],
            ['Authorization' => 'Bearer ' . $token->plainTextToken]  
        );

        $response->assertStatus(204);
        $this->assertEmpty(User::first()->tokens()->get());
    }
}
