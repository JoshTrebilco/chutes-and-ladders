<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\GameController;
use App\Http\Controllers\PlayerController;
use Illuminate\Support\Facades\Route;

Route::get('/', [GameController::class, 'index'])->name('games.index');
Route::get('/login', [AuthController::class, 'index'])->name('login.index');
Route::post('/login', [AuthController::class, 'store'])->name('login.store');
Route::post('/games', [GameController::class, 'store'])->name('games.store');
Route::get('/games/{game_id}', [GameController::class, 'show'])->name('games.show');
Route::post('/games/{game_id}/join', [PlayerController::class, 'join'])->name('players.join');
Route::post('/games/{game_id}/players/{player_id}/roll-dice', [PlayerController::class, 'rollDice'])->name('players.rollDice');
Route::post('/games/{game_id}/start-game', [PlayerController::class, 'startGame'])->name('players.startGame');
