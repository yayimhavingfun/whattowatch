<?php 

namespace App\Jobs;

use App\Exceptions\FilmsRepositoryException;
use App\Models\Film;
use App\Models\Genre;
use App\Services\FilmService;
use App\Support\FilmsRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class UpdateFilm implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public Film $film)
    {}

    public function handle(FilmsRepository $repository, FilmService $service)
    {
        $data = $repository->getFilm($this->film->id);

        if (empty($data)) {
            throw new FilmsRepositoryException('Отсутствуют данные для обновления');
        }

        $this->film = $data['film'];

        foreach ($data['links'] as $field => $link) {
            if (!empty($link)) {
                $this->film->$field = $service->saveFile($link, $field, $this->film->id);
            }
        }

        DB::beginTransaction();

        $genres_ids = [];
        foreach ($data['genres'] as $genre) {
            $genres_ids[] = Genre::firstOrCreate(['name' => $genre])->id;
        }

        $this->film->status = Film::STATUS_ON_MODERATION;
        $this->film->save();
        $this->film->genres()->attach($genres_ids);

        DB::commit();

        GetComments::dispatch($this->film);
    }
}