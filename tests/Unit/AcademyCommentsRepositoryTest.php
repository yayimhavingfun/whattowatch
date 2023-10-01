<?php

namespace Tests\Unit;

use App\Models\Comment;
use App\Models\Film;
use App\Support\AcademyCommentsRepository;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Collection;
use Tests\TestCase;

class AcademyCommentsRepositoryTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    private AcademyCommentsRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new AcademyCommentsRepository();
    }

    public function test_getComments()
    {
        $result = $this->repository->getComments('12');

        $this->assertInstanceOf(Collection::class, $result);
        $this->assertInstanceOf(Comment::class, $result->first());
        $this->assertTrue(!empty($result));
    }
}
