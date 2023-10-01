<?php

namespace Tests\Feature;

use App\Models\Film;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class PromoTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_route()
    {
        $film = Film::factory()->create(['promo' => true, 'updated_at' => now()]);
        Film::factory()->create(['promo' => true, 'updated_at' => now()->subDay()]);

        $response = $this->getJson(route('promo.get'));
        $response->assertStatus(200);
        $response->assertJsonFragment(['id' => $film->id]);
    }

    public function test_add_route_user()
    {
        Sanctum::actingAs(User::factory()->create());

        $film = Film::factory()->create(['promo' => false]);
        $response = $this->postJson(route('promo.add', $film), ['promo' => true]);
        $response->assertStatus(403);
        $this->assertDatabaseMissing('films', [
            'id' => $film->id,
            'promo' => true,
        ]);
    }

    public function test_add_route_moderator()
    {
        Sanctum::actingAs(User::factory()->moderator()->create());

        $film = Film::factory()->create(['promo' => false]);
        $response = $this->postJson(route('promo.add', $film), ['promo' => true]);
        $response->assertStatus(201);
        $this->assertDatabaseHas('films', [
            'id' => $film->id,
            'promo' => true,
        ]);
    }

    public function test_remove_promo()
    {
        Sanctum::actingAs(User::factory()->moderator()->create());

        $film = Film::factory()->create(['promo' => true]);
        $response = $this->postJson(route('promo.add', $film), ['promo' => false]);
        $response->assertStatus(201);
        $this->assertDatabaseHas('films', [
            'id' => $film->id,
            'promo' => false,
        ]);
    }
}