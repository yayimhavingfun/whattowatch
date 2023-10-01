<?php

namespace App\Http\Controllers;

use App\Models\Genre;
use App\Http\Requests\GenreRequest;
use Illuminate\Contracts\Support\Responsable;

class GenreController extends Controller
{
    /**
     * getting a genre list
     * 
     * @return Responsable
     */
    public function index()
    {
        return $this->success(Genre::all());
    }

    /**
     * edit genre
     * 
     * @param GenreRequest $request
     * @param Genre $genre
     * @return Responsable
     */
    public function update(GenreRequest $request, Genre $genre)
    {
        $genre->update($request->validated());

        return $this->success($genre->fresh());
    }
}
