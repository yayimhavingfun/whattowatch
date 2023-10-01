<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Film;
use App\Jobs\UpdateFilm;
use App\Jobs\UpdateFilms;
use Illuminate\Support\Facades\Queue;
use Illuminate\Foundation\Testing\RefreshDatabase;
class UpdateFilmsJobTest extends TestCase
{
    use RefreshDatabase;

    public function test_generate_jobs()
    {
        Queue::fake();

        Film::factory(3)->pending()->create();
        $ready = Film::factory()->create(['status' => Film::STATUS_READY]);

        (new UpdateFilms())->handle();

        Queue::assertPushed(UpdateFilm::class, 3);
        Queue::assertNotPushed(function (UpdateFilm $job) use ($ready) {
            return $job->film === $ready;
        });
    }
}
