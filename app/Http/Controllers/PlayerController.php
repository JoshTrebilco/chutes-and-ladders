<?php

namespace App\Http\Controllers;

use App\Events\Setup\PlayerJoinedGame;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Session;

class PlayerController extends Controller
{
    public function store(int $game_id)
    {
        $player_id = snowflake_id();

        Session::put('user.current_player_id', $player_id);

        event(new PlayerJoinedGame(
            game_id: $game_id,
            player_id: $player_id,
        ));

        return redirect("/games/{$game_id}");
    }
}
