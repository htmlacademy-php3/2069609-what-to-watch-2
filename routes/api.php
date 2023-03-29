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
Route::middleware('auth:sanctum')->get('/user', [UserController::class, 'show']);
//авторизация
Route::middleware('auth:sanctum')->patch('/user', [UserController::class, 'update']);
//для всех
Route::get('/films', [FilmController::class, 'index']);
//Модератор
Route::middleware('auth:sanctum')->post('/films', [FilmController::class, 'store']);
//доступ для всех
Route::get('/films/{id}', [FilmController::class, 'show']);
//Модератор
Route::middleware('auth:sanctum')->patch('/films/{id}', [FilmController::class, 'update']);
//для всех
Route::get('/films/{id}/similar', [FilmController::class, 'getSimilar']);
//авторизация
Route::middleware('auth:sanctum')->post('/films/{id}/favorite', [FavoriteController::class, 'store']);
//авторизация
Route::middleware('auth:sanctum')->delete('/films/{id}/favorite', [FavoriteController::class, 'destroy']);
//для всех
Route::get('/films/{id}/comments', [CommentController::class, 'index']);
//авторизация
Route::middleware('auth:sanctum')->post('/films/{id}/comments', [CommentController::class, 'store']);
//доступ для всех
Route::get('/genres', [GenreController::class, 'index']);
//Модератор
Route::middleware('auth:sanctum')->patch('/genres/{genre}', [GenreController::class, 'update']);
//для всех
Route::get('/promo', [PromoController::class, 'index']);
//Модератор
Route::middleware('auth:sanctum')->post('/promo/{id}', [PromoController::class, 'store']);
//авторизация
Route::middleware('auth:sanctum')->get('/favorite', [FavoriteController::class, 'index']);
//авторизация + Модератор
Route::middleware('auth:sanctum')->patch('/comments/{comment}', [CommentController::class, 'update']);
//авторизация + Модератор
Route::middleware('auth:sanctum')->delete('/comments/{comment}', [CommentController::class, 'destroy']);
