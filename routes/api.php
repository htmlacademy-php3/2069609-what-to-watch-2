<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\FilmController;
use App\Http\Controllers\GenreController;
use App\Http\Controllers\PromoController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout']);

Route::get('/user', [UserController::class, 'show']);
Route::patch('/user', [UserController::class, 'update']);

Route::get('/films', [FilmController::class, 'index']);
Route::post('/films', [FilmController::class, 'store']);
Route::get('/films/{id}', [FilmController::class, 'show']);
Route::patch('/films/{id}', [FilmController::class, 'update']);
Route::get('/films/{id}/similar', [FilmController::class, 'getSimilar']);

Route::get('/genres', [GenreController::class, 'index']);
Route::patch('/genres/{genre}', [GenreController::class, 'update']);

Route::get('/promo', [PromoController::class, 'index']);
Route::post('/promo/{id}', [PromoController::class, 'store']);

Route::get('/favorite', [FavoriteController::class, 'index']);
Route::post('/films/{id}/favorite', [FavoriteController::class, 'store']);
Route::delete('/films/{id}/favorite', [FavoriteController::class, 'destroy']);

Route::get('/films/{id}/comments', [CommentController::class, 'index']);
Route::post('/films/{id}/comments', [CommentController::class, 'store']);
Route::patch('/comments/{comment}', [CommentController::class, 'update']);
Route::delete('/comments/{comment}', [CommentController::class, 'destroy']);
