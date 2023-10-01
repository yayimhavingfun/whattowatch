<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_update_route_by_guest()
    {
        $response = $this->patchJson(route('user.update'), []);
        $response->assertStatus(401);
        $response->assertJsonFragment(['message' => "Запрос требует аутентификации."]);
    }

    public function test_validation_for_update_route()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->patchJson(route('user.update'), []);

        $response->assertStatus(422);
        $response->assertJsonStructure(['errors' => ['name', 'email']]);
        $response->assertJsonFragment([
            'name' => ['The name field is required.'],
            'email' => ['The email field is required.'],
        ]);
    }

    public function test_email_unique_validation_for_update_route()
    {
        $other_user = User::factory()->create();
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->patchJson(route('user.update'), ['email' => $other_user->email]);

        $response->assertStatus(422);
        $response->assertJsonFragment([
            'email' => ['The email has already been taken.']
        ]);
    }

    public function test_bypass_email_validation_for_update_route()
    {
        $user = User::factory()->create();
        $new_user = User::factory()->make();
        Sanctum::actingAs($user);

        $response = $this->patchJson(route('user.update'), ['email' => $user->email, 'name' => $new_user->name]);

        $response->assertStatus(200);
    }

    public function test_update_route_complete()
    {
        Storage::fake('public');

        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $new_user = User::factory()->make();
        $file = UploadedFile::fake()->image('photo1.jpg');

        $data = [
            'email' => $new_user->email,
            'name' => $new_user->name,
            'password' => $new_user->password,
            'password_confirmation' => $new_user->password,
            'file' => $file,
        ];

        $response = $this->patchJson(route('user.update'), $data);
        $response->assertJsonFragment([
            'name' => $new_user->name,
            'email' => $new_user->email,
            'avatar' => $file->hashName(),
        ]);
        $this->assertDatabaseCount('users', 1);
        $this->assertDatabaseHas('users', [
            'name' => $new_user->name, 
            'email' => $new_user->email,
            'avatar' => $file->hashName(),
        ]);
    }
}
