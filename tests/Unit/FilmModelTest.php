<?php

namespace Tests\Unit;

use App\Models\Comment;
use App\Models\Film;
use Database\Factories\FilmFactory;
use Tests\TestCase;

class FilmModelTest extends TestCase
{   
    public function setUp(): void
    {
        parent::setUp();
    }
    /**
     * A test that shows if the rating formula is correct
     */
    public function test_get_rating(): void
    {
        Film::factory()
            ->has(Comment::factory(3)->sequence(['rating' => 1], ['rating' => 2], ['rating' => 1]))
            ->create();

        $this->assertEquals(1.3, Film::first()->rating); // 4/3 = 1.3(3);
    }
}
