<?php

namespace App\Http\Controllers;

use App\Services\FilmService;
use App\Http\Requests\PromoAddRequest;
use App\Models\Film;
use Illuminate\Contracts\Support\Responsable;

class PromoController extends Controller
{
    /**
     * adding promo to a film
     * 
     * @param PromoAddRequest $request
     * @return Responsable
     */
    public function add(PromoAddRequest $request, Film $film)
    {
        $film->update(['promo' => $request->boolean('promo')]);
        cache()->forget(Film::CACHE_PROMO_KEY);
        return $this->success(null, 201);
    }

    /**
     * getting the movie's promo
     * 
     * @return Responsable
     */
    public function get(FilmService $service)
    {
        $promo = cache()->remember(Film::CACHE_PROMO_KEY, now()->addDay(), fn() => $service->getPromo());

        return $this->success($promo);
    }
}
