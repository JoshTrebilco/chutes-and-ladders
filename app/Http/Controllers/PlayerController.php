<?php

namespace App\Http\Controllers;

use App\Events\Gameplay\EndedTurn;
use App\Events\Gameplay\PlayerClimbedLadder;
use App\Events\Gameplay\PlayerFellDownChute;
use App\Events\Gameplay\PlayerMoved;
use App\Events\Gameplay\RolledDice;
use App\Events\Setup\FirstPlayerSelected;
use App\Events\Setup\PlayerColorSelected;
use App\Events\Setup\PlayerJoinedGame;
use App\Game\Board;
use App\States\GameState;
use App\States\PlayerState;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Session;

class PlayerController extends Controller
{
    public function join(Request $request, int $game_id)
    {
        $player_id = snowflake_id();

        Session::put('user.current_player_id', $player_id);

        event(new PlayerColorSelected(
            game_id: $game_id,
            player_id: $player_id,
            color: $request->color,
        ));

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

        $player = PlayerState::load($player_id);

        $position = $player->position + $die;

        $board = new Board;

        if ($board->hasChute($position)) {
            event(new PlayerFellDownChute(
                game_id: $game_id,
                player_id: $player_id,
                start: $position,
                end: $board->chute($position),
            ));

            $position = $board->chute($position);
        }

        if ($board->hasLadder($position)) {
            event(new PlayerClimbedLadder(
                game_id: $game_id,
                player_id: $player_id,
                start: $position,
                end: $board->ladder($position),
            ));

            $position = $board->ladder($position);
        }

        event(new PlayerMoved(
            game_id: $game_id,
            player_id: $player_id,
            position: $position,
        ));

        event(new EndedTurn(
            game_id: $game_id,
            player_id: $player_id,
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
