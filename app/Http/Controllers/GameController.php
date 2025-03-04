<?php

namespace App\Http\Controllers;

use App\Game\Board;
use App\States\GameState;
use App\States\PlayerState;
use Illuminate\Http\Request;
use App\Events\Setup\GameStarted;
use Illuminate\Routing\Controller;

class GameController extends Controller
{
    public function index()
    {
        return view('game.index');
    }

    public function show(Request $request, int $game_id)
    {
        if (! $request->session()->has('user')) {
            return redirect()->route('login.index', ['game_id' => $game_id]);
        }

        $authPlayer = null;

        if ($request->session()->has('user.current_player_id')) {
            $authPlayer = PlayerState::load($request->session()->get('user.current_player_id'));
        }

        $game = GameState::load($game_id);
        $board = new Board;

        return view('game.show', [
            'game' => $game,
            'board' => $board,
            'authPlayer' => $authPlayer,
        ]);
    }

    public function store(Request $request)
    {
        $event = GameStarted::fire();

        return redirect()->route('games.show', $event->game_id);
    }
}
