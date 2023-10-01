<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\FilmService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class FilmServiceTest extends TestCase
{
    public function test_saveFile()
    {
        Storage::fake('public');
        $url = 'http://academy.localhost/files/poster/the-grand-budapest-hotel-poster.jpg';

        $service = new FilmService();

        $path = $service->saveFile($url, 'poster', '1');

        $this->assertEquals(Storage::url('poster/1.jpg'), $path);
        Storage::assertExists('poster/1.jpg');
    }
}
