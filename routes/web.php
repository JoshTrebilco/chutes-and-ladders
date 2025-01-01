<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\GameController;
use Illuminate\Support\Facades\Route;

Route::get('/', [GameController::class, 'index']);
Route::get('/', [GameController::class, 'index'])->name('games.index');
Route::get('/login', AuthController::class)->name('login.index');
Route::post('/login', AuthController::class)->name('login.store');
Route::post('/games', [GameController::class, 'store'])->name('games.store');
