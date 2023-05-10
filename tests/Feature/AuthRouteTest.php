<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthRouteTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Тест, проверяющий роут register - регистрация на сайте POST
     *
     */
    public function test_register(): void
    {
        // Регистрация, когда почта уже существует
        $user = User::factory()->create();

        $response = $this->postJson(route('register'), ['name' => 'Testets', 'email' => $user->email, 'password' => '12345678', 'password_confirmation' => '12345678']);
        $response->assertUnprocessable();

        // Регистрация по правилам
        $response = $this->postJson(route('register'), ['name' => 'Testets', 'email' => 'email@mail.ru', 'password' => '12345678', 'password_confirmation' => '12345678']);
        $response->assertStatus(201);

        // Регистрация, когда подверждающий пароль не совпал
        $response = $this->postJson(route('register'), ['name' => 'Testets', 'email' => $user->email, 'password' => '12345678', 'password_confirmation' => '1234567899']);
        $response->assertUnprocessable();
    }

    /**
     * Тест, проверяющий роут login - вход на сайт POST
     *
     */
    public function test_login(): void
    {
        // Когда все верно
        $this->postJson(route('register'), ['name' => 'Testets', 'email' => 'email@mail.ru', 'password' => '12345678', 'password_confirmation' => '12345678']);
        $response = $this->postJson(route('login'), ['email' => 'email@mail.ru', 'password' => '12345678']);

        $response->assertOk();

        // Когда есть ошибка
        $response = $this->postJson(route('login'), ['email' => 'email@mail.ru', 'password' => '12345678000']);
        $response->assertStatus(500);
    }

    /**
     * Тест, проверяющий роут logout - выход с сайта POST
     *
     */
    public function test_logout(): void
    {
        // Если юзер не залогинен
        $response = $this->postJson(route('logout'));
        $response->assertUnauthorized();

        // Если юзер залогинен
        $this->postJson(route('register'), ['name' => 'Testets', 'email' => 'email@mail.ru', 'password' => '12345678', 'password_confirmation' => '12345678']);
        $this->postJson(route('login'), ['email' => 'email@mail.ru', 'password' => '12345678']);
        $response = $this->postJson(route('logout'));
        $response->assertOk();
    }
}

