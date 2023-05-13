<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\Favorite;
use App\Models\Film;
use App\Models\Genre;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Seeder;

class CommentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Comment::factory(1)
            ->count(6)
            ->state(new Sequence(
                fn($sequence) => ['film_id' => 1, 'user_id' => 15, 'comment_id' => 4],
            ))
            ->create();
    }
}
