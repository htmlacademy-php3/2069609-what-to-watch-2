<?php

namespace Database\Seeders;

use App\Models\Favorite;
use App\Models\Film;
use App\Models\Genre;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Seeder;

class FavoriteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Favorite::factory(1)
            ->count(1)
            ->state(new Sequence(
                fn($sequence) => ['film_id' => 2, 'user_id' => 15],
            ))
            ->create();
    }
}
