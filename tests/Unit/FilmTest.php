<?php

namespace Tests\Unit;

use App\Models\Comment;
use App\Models\Film;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
//use PHPUnit\Framework\TestCase;
use Tests\TestCase;

class FilmTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Тест, проверяющий, действительно ли свойство rating возвращает правильный рейтинг,
     * который основыывается на оценках этого фильма, оставленных пользователями.
     */
    public function test_film_rating(): void
    {
        $film = Film::factory()->create();

        $testCountScores = 10;
        $testRating = 5;

        $testAverageRating = ($testCountScores * $testRating)/$testCountScores;

        for ($i = 1; $i <= $testCountScores; $i++) {
            User::factory()->create();
            Comment::factory()->create([
                'film_id' => $film->id,
                'rating' => $testRating
            ]);;
        }

        $averageRating = $film->getRating();

        $this->assertEquals($testAverageRating, $averageRating);
    }
}
