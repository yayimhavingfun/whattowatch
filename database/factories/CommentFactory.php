<?php

namespace Database\Factories;

use App\Models\Film;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Comment>
 */
class CommentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'film_id' => Film::factory(),
            'user_id' => User::factory(),
            'text' => $this->faker->sentences(2, true),
            'rating' => random_int(1, 10),
        ];
    }

    public function unrated()
    {
        return $this->state(function (array $attributes) {
            return [
                'rating' => null,
            ];
        });
    }

    public function external()
    {
        return $this->state(function (array $attributes) {
            return [
                'film_id' => null,
                'user_id' => null,
                'rating' => null,
                'created_at' => $this->faker->dateTimeBetween(),
            ];
        });
    }
}
