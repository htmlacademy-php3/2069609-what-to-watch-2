<?php

namespace Database\Seeders;

use App\Models\Film;
use App\Models\FilmGenre;
use App\Models\Genre;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Seeder;

class FilmGenreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        FilmGenre::factory()
            ->count(20)
            ->state(new Sequence(
                fn ($sequence) => ['film_id' => Film::all()->random(), 'genre_id' => Genre::all()->random()],
            ))
            ->create();
    }
}
