<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Film;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class FavoritesTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_favs_list()
    {
        $user = User::factory()->has(Film::factory(2))->create();
        Sanctum::actingAs($user);

        Film::factory()->create();

        $response = $this->getJson(route('favorite.index'));

        $response->assertStatus(200);
        $response->assertJsonCount(2, 'data');
    }

    public function test_add_fav_for_user()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $film = Film::factory()->create();

        $response = $this->postJson(route('favorite.add', $film));

        $response->assertStatus(201);
        $this->assertEquals($film->id, $user->films->first()->id);
    }

    public function test_add_fav_for_user_again()
    {
        $film = Film::factory()->create();
        $user = User::factory()->hasAttached($film)->create();
        Sanctum::actingAs($user);

        $response = $this->postJson(route('favorite.add', $film));

        $response->assertStatus(400);
        $this->assertCount(1, $user->films);
    }

    public function test_remove_fav_from_user()
    {
        $film = Film::factory()->create();

        $user = User::factory()->hasAttached($film)->create();
        Sanctum::actingAs($user);

        $response = $this-> deleteJson(route('favorite.delete', $film));

        $response->assertStatus(201);
        $this->assertEmpty($user->films);
    }
}
