<?php

namespace App\Support;

use App\Models\Comment;
use App\Models\Film;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

class AcademyCommentsRepository implements CommentsRepository
{
    /**
     * @inhericDoc
     */
    public function getComments(string $film_id): ?Collection
    {
        $data = Http::get(config('services.academy.comments.url', '/') . '/' . $film_id);

        if ($data->clientError()) {
            return null;
        }

        return $data->collect()->map(function ($value) use ($film_id) {
            return Comment::firstOrNew([
                'film_id' => $film_id,
                'text' => $value['comment'],
                'created_at' => Carbon::parse($value['date'])->toDateTimeString(),
            ]);
        });
    }
}