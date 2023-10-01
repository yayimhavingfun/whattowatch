<?php

namespace App\Support;

use Illuminate\Support\Collection;

interface CommentsRepository
{
    /**
     * Getting comments to a film
     * 
     * @param string $imdb_id
     * @return Collection|null
     */
    public function getComments(string $imdb_id): ?Collection;

}