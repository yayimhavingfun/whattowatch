<?php

namespace Tests\Unit;

use App\Jobs\GetComments;
use App\Jobs\UpdateFilm;
use App\Models\Film;
use App\Models\Genre;
use App\Services\FilmService;
use App\Support\AcademyFilmsRepository;
use App\Support\Import\FilmsRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Mockery\MockInterface;
use Tests\TestCase;

class UpdateFilmJobTest extends TestCase
{
    use RefreshDatabase;

    public function test_UpdateFilm()
    {
        Queue::fake();

        $local_file_url = 'http://example.localhost/storage/file.ext';
        $external_file_url = 'http://example.com/file.ext';

        $genres = Genre::factory(3)->create();
        $film = Film::factory()->pending()->create();
        $data = [
            'film' => $film,
            'genres' => $genres->pluck('name')->toArray(),
            'links' => [
                'poster_image' => $external_file_url,
                'preview_image' => $external_file_url,
                'background_image' => $external_file_url,
                'video_link' => $external_file_url,
                'preview_video_link' => $external_file_url,
            ],
        ];

        $repository = $this->mock(AcademyFilmsRepository::class, function (MockInterface $mock) use ($data) {
            $mock->shouldReceive('getFilm')->once()->andReturn($data);
        });

        $service = $this->mock(FilmService::class, function (MockInterface $mock) use ($local_file_url) {
            $mock->shouldReceive('saveFile')->times(5)->andReturn($local_file_url);
        });

        (new UpdateFilm($film))->handle($repository, $service);

        $this->assertDatabaseHas('films', [
            'id' => $film->id,
            'status' => Film::STATUS_ON_MODERATION, 
            'poster_image' => $local_file_url,
        ]);

        Queue::assertPushed(function (GetComments $job) use ($film) {
                return $job->film === $film;
        });
    }
}
