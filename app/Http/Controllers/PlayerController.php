<?php

namespace App\Http\Controllers;

use App\Game\Board;
use App\States\GameState;
use App\States\PlayerState;
use Illuminate\Http\Request;
use App\Events\BroadcastEvent;
use App\Events\Gameplay\EndedTurn;
use Illuminate\Routing\Controller;
use App\Events\Gameplay\RolledDice;
use App\Events\Gameplay\PlayerMoved;
use App\Events\Gameplay\PlayerWonGame;
use App\Events\Setup\PlayerJoinedGame;
use Illuminate\Support\Facades\Auth;
use App\Events\Setup\GameStarted;
use App\Events\Setup\PlayerColorSelected;
use App\Events\Gameplay\PlayerClimbedLadder;
use App\Events\Gameplay\PlayerFellDownChute;

class PlayerController extends Controller
{
    public function join(Request $request, int $game_id)
    {
        $player_id = snowflake_id();
        $user = Auth::user();

        $user->update(['current_player_id' => $player_id]);

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
        if (Auth::user()->current_player_id != $player_id) {
            return redirect()->route('games.show', $game_id);
        }

        $die = rand(1, 6);

        RolledDice::commit(
            game_id: $game_id,
            player_id: $player_id,
            die: $die,
        );

        $player = PlayerState::load($player_id);

        $position = $player->position + $die;

        PlayerMoved::commit(
            game_id: $game_id,
            player_id: $player_id,
            position: $position,
            previous_position: $player->position,
        );

        $board = new Board;

        if ($board->hasChute($position)) {
            PlayerFellDownChute::commit(
                game_id: $game_id,
                player_id: $player_id,
                start: $position,
                end: $board->chute($position),
            );

            $position = $board->chute($position);
        }

        if ($board->hasLadder($position)) {
            PlayerClimbedLadder::commit(
                game_id: $game_id,
                player_id: $player_id,
                start: $position,
                end: $board->ladder($position),
            );

            $position = $board->ladder($position);
        }

        if ($position > 100) {
            $position = 100;
        }

        if ($die !== 6) {
            EndedTurn::commit(
                game_id: $game_id,
                player_id: $player_id,
            );
        }

        if ($position === 100) {
            PlayerWonGame::commit(
                game_id: $game_id,
                player_id: $player_id,
            );
        }
    }

    public function startGame(int $game_id)
    {
        $game = GameState::load($game_id);

        $player_id = $game->players()->random()->id;

        event(new GameStarted(
            game_id: $game_id,
            player_id: $player_id,
        ));

        return redirect()->route('games.show', $game_id);
    }
}
