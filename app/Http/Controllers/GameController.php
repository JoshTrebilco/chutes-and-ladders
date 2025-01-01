<?php

namespace App\Http\Controllers;

use App\Game\Board;

class GameController extends Controller
{
    public function index()
    {
        return view('game.index');
    }
            'board' => new Board,
        ]);
    }

    public function store(Request $request)
    {
        $event = GameStarted::fire();

        return redirect("/games/{$event->game_id}");
    }
}
