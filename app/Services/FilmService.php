<?php

namespace App\Services;

use App\Models\Film;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class FilmService
{
    public function getSimilar(Film $film, array $fields = ['*'])
    {
        return Film::select($fields)
            ->whereHas('genres', function ($query) use ($film) {
                $query->whereIn('genres.id', $film->genres()->pluck('genres.id'));
            })
            ->where('id', '!=', $film->id)
            ->take(config('app.api.films.similar.limit', 4))
            ->get();
    }

    public function getPromo()
    {
        return Film::promo()->latest('updated_at')->first();
    }

    public function saveFile(string $url, string $type, string $name): string
    {
        $file = Http::get($url)->body();
        $ext = pathinfo($url, PATHINFO_EXTENSION);
        $path = $type . DIRECTORY_SEPARATOR . $name . ".$ext";

        Storage::disk('public')->put($path, $file);

        return Storage::url($path);
    }
}