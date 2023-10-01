<?php

namespace App\Support;

interface FilmsRepository
{
    /**
     * @param string $imdb_id
     * @return array{show: \App\Models\Film, genres: array, links: array}|null
     */
    public function getFilm(string $imdb_id);
}