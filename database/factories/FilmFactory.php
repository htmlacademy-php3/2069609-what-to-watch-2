<?php

namespace Database\Factories;

use App\Models\Film;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Film>
 */
class FilmFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Film::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->words(3, true),
            'poster_image' => $this->faker->imageUrl(),
            'preview_image' => $this->faker->imageUrl(),
            'background_image' => $this->faker->imageUrl(),
            'background_color' => $this->faker->hexColor(),
            'video_link' => $this->faker->url(),
            'preview_video_link' => $this->faker->url(),
            'description' => $this->faker->paragraph(),
            'director' => $this->faker->name(),
            'run_time' => $this->faker->numberBetween(20, 500),
            'released' => $this->faker->year(),
            'imdb_id' => 'tt' . $this->faker->unique()->randomNumber(7, true),
            'status' => $this->faker->randomElement(['pending','on moderation','ready']),
        ];
    }
}
