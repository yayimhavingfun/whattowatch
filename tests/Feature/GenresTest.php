<?php

namespace Tests\Feature;

use App\Models\Genre;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class GenresTest extends TestCase
{
    use RefreshDatabase;
    public function test_index_route(): void
    {
        $count = random_int(2, 10);
        Genre::factory()->count($count)->create();

        $response = $this->getJson(route('genres.index'));

        $response->assertStatus(200);
        $response->assertJsonCount($count, 'data');
        $response->assertJsonStructure(['data' => [['id', 'name']]]);
        $response->assertJsonMissing(['links' => []]);
    }

    public function test_update_route_moderator()
    {
        Sanctum::actingAs(User::factory()->moderator()->create());

        $genre = Genre::factory()->create();
        $new_genre = Genre::factory()->make();
        
        $response = $this->patchJson(route('genres.update', $genre), $new_genre->toArray());
        $response->assertStatus(200);
        $this->assertDatabaseHas('genres', [
            'id' => $genre->id,
            'name' => $new_genre->name,
        ]);
    }

    public function test_update_route_user()
    {
        $genre = Genre::factory()->create();

        Sanctum::actingAs(User::factory()->create());

        $response = $this->patchJson(route('genres.update', $genre->id), []);

        $response->assertStatus(403);
        $response->assertJsonFragment(['message' => "This action is unauthorized."]);
    
    }
}
