<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddFilmRequest;
use App\Http\Requests\UpdateFilmRequest;
use App\Services\FilmService;
use App\Models\Film;
use Illuminate\Http\Request;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Support\Facades\DB;

class FilmController extends Controller
{
    /**
     * getting a list of films
     * 
     * @return Responsable
     */
    public function index(Request $request)
    {
        $order = $request->get('order_to');
        if ($order == null) {
            $order = 'asc';
        }
        $films = Film::select(Film::LIST_FIELDS)
            ->when($request->has('genre'), function ($query) use ($request) {
                $query->whereRelation('genres', 'name', $request->get('genre'));
            })
            ->when($request->has('status') && $request->user()->isModerator(),
                function ($query) use ($request) {
                    $query->whereStatus($request->get('status'));
                },
                function ($query) {
                    $query->whereStatus(Film::STATUS_READY);
                }
            )
            ->ordered($request->get('order_by'), $request->get('order_to'))
            ->paginate(8);

        return $this->paginate($films);
    }

    /**
     * adding the movie to the database
     * 
     * @return Responsable
     */
    public function add(AddFilmRequest $request)
    {
        Film::create([
            'imdb_id' => $request->input('imdb'),
            'status' => Film::STATUS_PENDING,
        ]);
        return $this->success(null, 201);
    }

    /**
     * getting movie info
     * 
     * @return Responsable
     */
    public function get(Film $film)
    {
        return $this->success($film->append('rating')->loadCount('scores'));
    }

    /**
     * editing movie info
     * 
     * @return Responsable
     */
    public function update(UpdateFilmRequest $request, Film $film)
    {
        $film->update($request->validated());
        $this->success([]);
    }

    /**
     * getting a list of similar movies
     * 
     * @return \App\Http\Responses\Success
     */
    public function similar(Film $film, FilmService $service)
    {
        return $this->success($service->getSimilar($film, Film::LIST_FIELDS));
    }
}
