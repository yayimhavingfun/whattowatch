<?php

namespace Tests\Unit;

use App\Models\Film;
use App\Support\AcademyFilmsRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AcademyFilmsRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private AcademyFilmsRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = new AcademyFilmsRepository();
    }

    public function test_getFilm()
    {
        $result = $this->repository->getFilm('12');
        $this->assertInstanceOf(Film::class, $result['film']);
        $this->assertIsString($result['genre']);
        $this->assertIsArray($result['links']);
        $this->assertFalse($result['film']->exists());
    }

    public function test_get_not_found()
    {
        Http::fake([
            '*' => Http::response('{}', 404),
        ]);

        $result = $this->repository->getFilm('12');
        $this->assertNull($result);
    }
}
