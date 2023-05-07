<?php

namespace Tests\Feature;

use App\Models\Film;
use App\Models\User;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class PromoRouteTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Тест, проверяющий роут promo - получение промо-фильма GET
     *
     * @throws Exception
     */
    public function test_show_promo(): void
    {
        $film = Film::factory()->promo()->create();
        $response = $this->getJson(route('promo.index'));

        $response->assertOk();
        $response->assertJsonStructure([
            'data' =>  [
                'id',
                'name',
                'poster_image',
                'preview_image',
                'background_image',
                'background_color',
                'video_link',
                'preview_video_link',
                'description',
                'rating',
                'score_count',
                'director',
                'starring',
                'run_time',
                'genre',
                'released'
            ]
        ]);
        $response->assertJsonFragment([
            'name' => $film->name,
            'poster_image' => $film->poster_image,
        ]);
    }

    /**
     * Тест, проверяющий роут promo/{id} - установка промо-фильма POST
     *
     * @throws Exception
     */
    public function test_update_promo(): void
    {
        // Пользователь не залогинен
        $film = Film::factory()->promo()->create();

        $response = $this->postJson(route('promo.add', $film->id));
        $response->assertUnauthorized();

        // Пользователь залогинен, не модератор
        $film = Film::factory()->promo()->create();
        Sanctum::actingAs(User::factory()->create());
        $response = $this->postJson(route('promo.add', $film->id));
        $response->assertForbidden();

        // Пользователь зелогинен, модератор
        $film = Film::factory()->promo()->create();
        Sanctum::actingAs(User::factory()->moderator()->create());

        $response = $this->postJson(route('promo.add', $film->id));
        $response->assertOk();

        $response->assertJsonFragment([
            'message' => 'Фильм с id = ' . $film->id . ' стал новым промо-фильмом',
        ]);

    }

}

