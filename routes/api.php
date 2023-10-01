<?php

use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\Middleware;
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



Route::post('/register', [\App\Http\Controllers\AuthController::class, 'register'])->name('auth.register');
Route::post('/login', [\App\Http\Controllers\AuthController::class, 'login'])->name('auth.login');
Route::post('/logout', [\App\Http\Controllers\AuthController::class, 'logout'])->middleware('auth:sanctum')->name('auth.logout');

Route::controller(UserController::class)
    ->middleware('auth:sanctum')
    ->group(function () {
        Route::get('/user', 'get')->name('user.get');
        Route::patch('/user', 'update')->name('user.update');
    });

Route::get('/films/{film}/similar', [\App\Http\Controllers\FilmController::class, 'similar'])->name('films.similar');
Route::get('/films', [\App\Http\Controllers\FilmController::class, 'index'])->name('films.index');
Route::post('/films', [\App\Http\Controllers\FilmController::class, 'add'])->middleware('auth:sanctum')->name('films.add'); 
Route::get('/films/{film}', [\App\Http\Controllers\FilmController::class, 'get'])->name('films.get'); 
Route::patch('/films/{film}', [\App\Http\Controllers\FilmController::class, 'update'])->middleware('auth:sanctum')->name('films.update');

Route::get('/genres', [\App\Http\Controllers\GenreController::class, 'index'])->name('genres.index');
Route::patch('/genres/{genre}', [\App\Http\Controllers\GenreController::class, 'update'])->middleware(['auth:sanctum', 'role:isModerator'])->name('genres.update'); 

Route::controller(FavoriteController::class)
    ->middleware('auth:sanctum')
    ->group(function () {
        Route::get('/favorite', 'index')->name('favorite.index'); 
        Route::post('/films/{film}/favorite', 'add')->name('favorite.add'); 
        Route::delete('/films/{film}/favorite', 'delete')->name('favorite.delete');
    });


Route::get('/films/{film}/comments', [\App\Http\Controllers\CommentController::class, 'index'])->name('comments.index');
Route::post('/films/{film}/comments', [\App\Http\Controllers\CommentController::class, 'add'])->middleware('auth:sanctum')->name('comments.add'); 
Route::patch('/comments/{comment}', [\App\Http\Controllers\CommentController::class, 'update'])->middleware('auth:sanctum')->name('comments.update'); 
Route::delete('/comments/{comment}', [\App\Http\Controllers\CommentController::class, 'delete'])->middleware('auth:sanctum')->name('comments.delete');
Route::get('/promo', [\App\Http\Controllers\PromoController::class, 'get'])->name('promo.get');
Route::post('/promo/{film}', [\App\Http\Controllers\PromoController::class, 'add'])->middleware(['auth:sanctum', 'role:isModerator'])->name('promo.add'); 