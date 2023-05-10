<?php

namespace Tests\Feature;

use App\Models\User;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class UserRouteTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Тест, проверяющий роут user - получение профиля пользователя GET
     *
     * @throws Exception
     */
    public function test_get_user(): void
    {
        // Пользователь не залогинен
        $response = $this->getJson(route('user.show'));
        $response->assertUnauthorized();

        // Пользователь залогинен
        $user = Sanctum::actingAs(User::factory()->create());

        $response = $this->getJson(route('user.show'));
        $response->assertOk();
        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'email',
                'avatar',
                'role'
            ]
        ]);
        $response->assertJsonFragment([
            'name' => $user->name,
            'email' => $user->email,
            'role' => 'Пользователь'
        ]);
    }

    /**
     * Тест, проверяющий роут user - обновление профиля пользователя PATCH
     *
     * @throws Exception
     */
    public function test_update_user(): void
    {
        $newName = 'Testtesttest';
        $user = User::factory()->create();

        // Пользователь не залогинен
        $response = $this->getJson(route('user.update'), ['name' => $newName, 'email' => $user->email]);
        $response->assertUnauthorized();

        // Пользователь залогинен
        Sanctum::actingAs($user);

        $response = $this->getJson(route('user.update'), ['name' => $newName, 'email' => $user->email]);
        $response->assertOk();
        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'email',
                'avatar',
                'role'
            ]
        ]);
        $response->assertJsonFragment([
            'name' => $user->name,
            'email' => $user->email,
            'role' => 'Пользователь'
        ]);
    }

}

