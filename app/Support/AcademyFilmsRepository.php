<?php

namespace App\Support;


use App\Models\Film;
use Illuminate\Support\Facades\Http;

class AcademyFilmsRepository implements FilmsRepository
{   
    /**
     * @inheritDoc
     */
    public function getFilm(string $film_id)
    {
        $data = Http::get(trim(config('services.academy.films.url'), '/') . '/' . $film_id);

        if ($data->clientError()) {
            return null;
        }

        $film = Film::firstOrNew(['id' => $film_id]);

        $film->fill([
            'name' => $data['name'],
            'description' => $data['description'],
            'director' => $data['director'],
            'starring' => $data['starring'],
            'run_time' => $data['runTime'],
            'released' => $data['released'],
        ]);

        $links = [
            'poster_image' => $data['posterImage'],
            'preview_image' => $data['previewImage'],
            'background_image' => $data['backgroundImage'],
            'video_link' => $data['videoLink'],
            'preview_video_link' => $data['previewVideoLink'],
        ];

        return [
            'film' => $film,
            'genre' => $data['genre'],
            'links' => $links,
        ];
    }
}