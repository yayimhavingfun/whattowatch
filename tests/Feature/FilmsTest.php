<?php

namespace Tests\Feature;

use App\Models\Comment;
use App\Models\Film;
use App\Models\Genre;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Routing\Route;
use Illuminate\Support\Arr;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class FilmsTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_route()
    {
        $count = random_int(2, 10);
        Film::factory()->count($count)->hasAttached(Genre::factory())->create();
        $response = $this->getJson(route('films.index'));

        $response->assertStatus(200);
        $response->assertJsonStructure(['data' => [], 'links' => [], 'total']);
        $response->assertJsonFragment(['total' => $count]);
    }

    public function test_index_route_by_genre()
    {
        $genre = Genre::factory()->create();
        $count = 2;
        $films = Film::factory($count)->hasAttached($genre)->create(['released' => 2000]);
        Film::factory(3)->create();

        $response = $this->getJson(route('films.index', ['genre' => $genre->name]));
        $result = $response->json('data');

        $response->assertStatus(200);
        $response->assertJsonFragment(['total' => $count]);
        $this->assertEquals($films->pluck('id')->toArray(), Arr::pluck($result, 'id'));
    }

    public function test_index_route_ready_films()
    {
        $film = Film::factory()->create(['status' => Film::STATUS_READY]);
        Film::factory()->create(['status' => Film::STATUS_PENDING]);

        $response = $this->getJson(route('films.index'));

        $response->assertStatus(200);
        $response->assertJsonCount(1, 'data');
        $response->assertJsonFragment(['id' => $film->id]);
    }

    public function test_get_not_ready_films_for_moderator()
    {
        Sanctum::actingAs(User::factory()->moderator()->create());

        $film = Film::factory()->create(['status' => Film::STATUS_ON_MODERATION]);
        Film::factory()->create(['status' => Film::STATUS_PENDING]);
        Film::factory()->create(['status' => Film::STATUS_READY]);

        $response = $this->getJson(route('films.index', ['status' => Film::STATUS_ON_MODERATION]));
        $response->assertStatus(200);
        $response->assertJsonCount(1, 'data');
        $response->assertJsonFragment(['id' => $film->id]);
    }

    public function test_index_route_order_films()
    {
        $film1 = Film::factory()
            ->has(Comment::factory()->state(['rating' => 5]))
            ->create(['released' => 2001]);
        $film2 = Film::factory()
            ->has(Comment::factory()->sequence(['rating' => 1]))
            ->create(['released' => 2002]);
        $film3 = Film::factory()
            ->has(Comment::factory()->sequence(['rating' => 3]))
            ->create(['released' => 2003]);

        $response = $this->getJson(route('films.index', ['order_by' => 'rating', 'order_to' => 'asc']));
        $result = $response->json('data');

        $response->assertStatus(200);
        $this->assertEquals([$film2->id, $film3->id, $film1->id], Arr::pluck($result, 'id'));
    }

    public function test_get_one_film_route()
    {
        $film = Film::factory()
            ->has(Comment::factory(3)->sequence(['rating' => 1], ['rating' => 2], ['rating' => 1]))
            ->create(['released' => 2001]);

        $response = $this->getJson(route('films.get', $film->id));

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'name' => $film->name,
            'scores_count' => 3, 
            'rating' => 1.3,
        ]);
    }

    public function test_get_similar_films()
    {
        $genre = Genre::factory()->create();
        $film1 = Film::factory()->hasAttached($genre)->create();
        $film2 = Film::factory()->hasAttached($genre)->create();
        $film3 = Film::factory()->hasAttached(Genre::factory())->create();

        $response = $this->getJson(route('films.similar', $film1->id));

        $response->assertStatus(200);
        $response->assertJsonCount(1);
        $response->assertJsonFragment(['id' => $film2->id]);
        $response->assertJsonMissing(['id' => $film3->id]);
    }

    public function test_wrong_route()
    {
        $response = $this->getJson(route('films.get', 404));

        $response->assertStatus(404);
        $response->assertJsonStructure(['message', 'exception']);
        $response->assertJsonFragment(['message' => 'Страница не найдена']);
    }
}
