<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\FilmController;
use App\Http\Controllers\GenreController;
use App\Http\Controllers\PromoController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

//доступ для всех
Route::post('/register', [AuthController::class, 'register']);
//доступ для всех
Route::post('/login', [AuthController::class, 'login']);
//авторизация
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);
//авторизация
Route::prefix('user')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [UserController::class, 'show']);
    Route::patch('/', [UserController::class, 'update']);
});
//авторизация + Модератор
Route::prefix('comments')->middleware('auth:sanctum')->group(function () {
    Route::patch('/{comment}', [CommentController::class, 'update']);
    Route::delete('/{comment}', [CommentController::class, 'destroy']);
});
//авторизация
Route::prefix('films')->middleware('auth:sanctum')->group(function () {
    Route::post('/', [FilmController::class, 'store']);
    Route::patch('/{id}', [FilmController::class, 'update']);
    Route::post('/{id}/favorite', [FavoriteController::class, 'store']);
    Route::delete('/{id}/favorite', [FavoriteController::class, 'destroy']);
    Route::post('/{id}/comments', [CommentController::class, 'store']);
});
//для всех
Route::prefix('films')->group(function () {
    Route::get('/', [FilmController::class, 'index']);
    Route::get('/{id}', [FilmController::class, 'show']);
    Route::get('/{id}/similar', [FilmController::class, 'getSimilar']);
    Route::get('/{id}/comments', [CommentController::class, 'index']);
});
//для всех
Route::get('/genres', [GenreController::class, 'index']);
//Модератор
Route::middleware('auth:sanctum')->patch('/genres/{genre}', [GenreController::class, 'update']);
//для всех
Route::get('/promo', [PromoController::class, 'index']);
//Модератор
Route::middleware('auth:sanctum')->post('/promo/{id}', [PromoController::class, 'store']);
//авторизация
Route::middleware('auth:sanctum')->get('/favorite', [FavoriteController::class, 'index']);

