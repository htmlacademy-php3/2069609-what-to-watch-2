<?php

namespace Database\Seeders;

use App\Models\Actor;
use App\Models\ActorFilm;
use App\Models\Comment;
use App\Models\Favorite;
use App\Models\Film;
use App\Models\FilmGenre;
use App\Models\Genre;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Film::factory(10)->create();
        Actor::factory(10)->create();
        User::factory(10)->create();
        Genre::factory(10)->create();
        Comment::factory(20)->create();
    }
}
