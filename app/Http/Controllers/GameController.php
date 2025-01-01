<?php

namespace App\Http\Controllers;

use App\Events\Setup\GameStarted;
use App\Game\Board;
use App\States\GameState;

class GameController extends Controller
{
    public function index()
    {
        return view('game.index');
    }

    public function show(Request $request, int $game_id)
    {
        if (! $request->session()->has('user')) {
            return redirect('/login');
        }

        return view('game.show', [
            'board' => new Board,
            'game' => GameState::load($game_id),
        ]);
    }

    public function store(Request $request)
    {
        $event = GameStarted::fire();

        return redirect("/games/{$event->game_id}");
    }
}
