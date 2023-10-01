<?php

namespace Tests\Unit;
use Illuminate\Support\Facades\Queue;
use App\Jobs\GetComments;
use App\Models\Comment;
use App\Models\Film;
use App\Support\CommentsRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery\MockInterface;
use Tests\TestCase;

class GetCommentsJobTest extends TestCase
{
    use RefreshDatabase;

    public function test_add_comments_to_film()
    {   
        $film = Film::factory()->create(['status' => Film::STATUS_READY]);
        $comments = Comment::factory(5)->external()->make();

        $repository = $this->mock(CommentsRepository::class, function (MockInterface $mock) use ($comments) {
            $mock->shouldReceive('getComments')
                ->once()
                ->andReturn($comments);
        });

        (new GetComments($film))->handle($repository);

        $this->assertDatabaseCount(Comment::class, 5);
        $this->assertDatabaseHas('comments', [
            'text' => $comments->first()->text, 
            'film_id' => $film->id, 
            'user_id' => null,
        ]);
    }
}