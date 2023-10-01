<?php

namespace Tests\Unit;

use App\Models\Comment;
use App\Models\User;
use Tests\TestCase;

class CommentModelTest extends TestCase
{
    /**
     * A basic unit test example.
     */
    public function test_author_name(): void
    {
        $user = User::factory()->create();
        $userComment = Comment::factory()->for($user)->create();
        $guestComment = Comment::factory()->create(['user_id' => null]);

        $this->assertEquals($user->name, $userComment->author);
        $this->assertEquals(Comment::DEFAULT_AUTHOR, $guestComment->author);
    }
}
