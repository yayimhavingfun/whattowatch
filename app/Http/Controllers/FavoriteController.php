<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\JsonResponse;
use App\Models\Film;
use App\Models\User;
use App\Exceptions\RequestException;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    /**
     * getting a list of movies in favorites
     * 
     * @return Responsable
     */
    public function index()
    {
        $films = Auth::user()->films()->get(Film::LIST_FIELDS)->toArray();

        return $this->success($films);
    }

    /**
     * adding a movie to favorites
     * 
     * @param Request $request
     * @return Responsable
     */
    public function add(Film $film)
    {
        $user = Auth::user();
        throw_if($user->hasFilm($film), new RequestException("Переданный фильм уже в избранном", 400));

        $user->films()->attach($film);
        return $this->success(null, 201);
    }

    /**
     * deleting a movie from favorites
     * 
     * @return Responsable
     */
    public function delete(Film $film)
    {$user = Auth::user();
        throw_unless($user->hasFilm($film), new RequestException("Переданный фильм не находится избранном"));

        $user->films()->detach($film);

        return $this->success(null, 201);
    }
}
