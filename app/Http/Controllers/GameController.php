<?php

namespace App\Http\Controllers;

use App\Events\Setup\GameStarted;
use Illuminate\Http\Request;
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

        $auth_player_id = null;

        if ($request->session()->has('user.current_player_id')) {
            $auth_player_id = $request->session()->get('user.current_player_id');
        }

        return view('game.show', [
            'game_id' => $game_id,
            'auth_player_id' => $auth_player_id,
        ]);
    }

    public function store(Request $request)
    {
        $event = GameStarted::fire();

        return redirect("/games/{$event->game_id}");
    }
}
