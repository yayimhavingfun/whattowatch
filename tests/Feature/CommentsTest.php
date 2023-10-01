<?php

namespace Tests\Feature;

use App\Models\Comment;
use App\Models\Film;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CommentsTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_route()
    {
        $count = random_int(2, 10);

        $film = Film::factory()
            ->has(Comment::factory($count))
            ->create();
        
            $response = $this->getJson(route('comments.index', $film));

            $response->assertStatus(200);
            $response->assertJsonCount($count, 'data');
            $response->assertJsonFragment(['text' => $film->comments->first()->text]);
    }

    public function test_add_route_with_guest()
    {
        $response = $this->postJson(route('comments.add', 1));

        $response->assertStatus(401);
    }

    public function test_add_route_with_user()
    {

        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $film = Film::factory()->create();
        $comment = Comment::factory()->make();
        
        $response = $this->postJson(route('comments.add', $film), $comment->toArray());

        $response->assertStatus(201);
        $this->assertDatabaseHas('comments', [
            'film_id' => $film->id,
            "user_id" => $user->id,
            'text' => $comment->text,
            'rating' => $comment->rating,
        ]);
    }

    public function test_update_route_by_guest()
    {
        $comment = Comment::factory()->create();

        $response = $this->patchJson(route('comments.update', $comment), []);

        $response->assertStatus(401);
    }


    public function test_update_route_common_user()
    {
        Sanctum::actingAs(User::factory()->create());

        $comment = Comment::factory()->create();

        $response = $this->patchJson(route('comments.update', $comment), []);

        $response->assertStatus(403);
    }

    public function test_update_route_author()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $comment = Comment::factory()->for($user)->create();

        $data = [
            'text' => 'new text for the comment',
        ];

        $response = $this->patchJson(route('comments.update', $comment), $data);
        $response->assertJsonFragment($data);
        $response->assertStatus(200);
    }

    public function test_delete_route_by_guest()
    {
        $comment = Comment::factory()->create();

        $response = $this->deleteJson(route('comments.delete', $comment->id));
        $response->assertStatus(401);
        $response->assertJsonFragment(['message' => "Unauthenticated."]);
        $this->assertDatabaseHas('comments', [
            'id' => $comment->id,
        ]);
    }

    public function test_delete_route_common_user()
    {
        Sanctum::actingAs(User::factory()->create());
        $comment = Comment::factory()->create();

        $response = $this->deleteJson(route('comments.delete', $comment));
        $response->assertStatus(403);
        $response->assertJsonFragment(['message' => "This action is unauthorized."]);
    }

    public function test_delete_route_comment_with_answers_and_by_author()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $comment = Comment::factory()->for($user)->create();
        Comment::factory(3)->for($comment, 'parent')->create();
        $response = $this->deleteJson(route('comments.delete', $comment));

        $response->assertStatus(403);
        $response->assertJsonFragment(['message' => "This action is unauthorized."]);
    }
    
    public function test_delete_route_by_author()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $comment = Comment::factory()->for($user)->create();

        $response = $this->deleteJson(route('comments.delete', $comment));

        $response->assertStatus(201);
        $this->assertDatabaseMissing('comments', [
            'id' => $comment->id,
        ]);
    }

    public function test_delete_route_by_moderator()
    {
        Sanctum::actingAs(User::factory()->moderator()->create());

        $comment = Comment::factory()->create();
        Comment::factory(3)->for($comment, 'parent')->create();

        $response = $this->deleteJson(route('comments.delete', $comment));

        $response->assertStatus(201);
        $this->assertDatabaseCount('comments', 0);


    }
}
