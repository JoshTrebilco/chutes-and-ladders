<?php

namespace App\Http\Controllers;

use App\Events\Gameplay\RolledDice;
use App\Events\Setup\FirstPlayerSelected;
use App\Events\Setup\PlayerJoinedGame;
use App\States\GameState;
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

        return redirect()->route('games.show', $game_id);
    }

    public function rollDice(int $game_id, int $player_id)
    {
        $die = rand(1, 6);

        event(new RolledDice(
            game_id: $game_id,
            player_id: $player_id,
            die: $die,
        ));

        return redirect()->route('games.show', $game_id);
    }

    public function startGame(int $game_id)
    {
        $game = GameState::load($game_id);

        $player_id = $game->players()->random()->id;

        event(new FirstPlayerSelected(
            game_id: $game_id,
            player_id: $player_id,
        ));

        return redirect()->route('games.show', $game_id);
    }
}
