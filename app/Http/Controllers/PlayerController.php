<?php

namespace App\Http\Controllers;

use App\Events\Gameplay\EndedTurn;
use App\Events\Gameplay\PlayerWonGame;
use App\Events\Gameplay\RolledDice;
use App\Events\Gameplay\TokenClimbedLadder;
use App\Events\Gameplay\TokenFellDownChute;
use App\Events\Gameplay\TokenMoved;
use App\Events\Setup\FirstPlayerSelected;
use App\Events\Setup\PlayerColorSelected;
use App\Events\Setup\PlayerJoinedGame;
use App\Events\Setup\TokenCreated;
use App\Game\Board;
use App\States\GameState;
use App\States\TokenState;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Session;

class PlayerController extends Controller
{
    public function join(Request $request, int $game_id)
    {
        $player_id = snowflake_id();

        // Store player ID mapped to this specific game
        Session::put("user.player_ids.{$game_id}", $player_id);

        event(new PlayerColorSelected(
            game_id: $game_id,
            player_id: $player_id,
            color: $request->color,
        ));

        event(new PlayerJoinedGame(
            game_id: $game_id,
            player_id: $player_id,
        ));

        // Create 4 tokens for the player
        for ($i = 1; $i <= 4; $i++) {
            $token_id = snowflake_id();

            event(new TokenCreated(
                player_id: $player_id,
                token_id: $token_id,
                number: $i,
            ));
        }

        return redirect()->route('games.show', $game_id);
    }

    public function rollDice(int $game_id, int $player_id, int $token_id)
    {
        $die = rand(1, 6);

        event(new RolledDice(
            game_id: $game_id,
            player_id: $player_id,
            token_id: $token_id,
            die: $die,
        ));

        $token = TokenState::load($token_id);
        $position = $token->position + $die;

        $board = new Board;

        if ($board->hasChute($position)) {
            event(new TokenFellDownChute(
                game_id: $game_id,
                token_id: $token_id,
                start: $position,
                end: $board->chute($position),
            ));

            $position = $board->chute($position);
        }

        if ($board->hasLadder($position)) {
            event(new TokenClimbedLadder(
                game_id: $game_id,
                token_id: $token_id,
                start: $position,
                end: $board->ladder($position),
            ));

            $position = $board->ladder($position);
        }

        if ($position > 100) {
            $position = 100;
        }

        event(new TokenMoved(
            game_id: $game_id,
            token_id: $token_id,
            position: $position,
        ));

        if ($die !== 6) {
            event(new EndedTurn(
                game_id: $game_id,
                player_id: $player_id,
            ));
        }

        if ($position === 100) {
            event(new PlayerWonGame(
                game_id: $game_id,
                player_id: $player_id,
            ));
        }

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
