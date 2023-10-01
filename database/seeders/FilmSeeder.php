<?php

namespace Database\Seeders;


use App\Models\Comment;
use App\Models\Film;
use App\Models\Genre;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FilmSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $films = Film::factory()
            ->count(5)
            ->create()
            ->each(fn($film) => $film->genres()->attach(
                Genre::inRandomOrder()->limit(3)->pluck('id')
            ));

        $films->random(3)
        ->each(fn($film) => $film->comments()->save(
            Comment::factory()->make()
        ));

        $films->random(2)
            ->each(fn($film) => $film->update(['promo' => true]));
    }
}
