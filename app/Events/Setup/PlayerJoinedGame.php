<?php

namespace App\Events\Setup;

use App\States\GameState;
use App\States\PlayerState;
use Illuminate\Support\Facades\Session;
use Thunk\Verbs\Attributes\Autodiscovery\AppliesToState;
use Thunk\Verbs\Event;

#[AppliesToState(GameState::class)]
#[AppliesToState(PlayerState::class)]
class PlayerJoinedGame extends Event
{
    public function __construct(
        public int $game_id,
        public int $player_id,
    ) {}

    public function validateGame(GameState $game)
    {
        $this->assert($game->started, 'Game must be started before a player can join.');
        $this->assert(! $game->isInProgress(), 'The game is already in progress.');
    }

    public function validatePlayer(PlayerState $player)
    {
        $this->assert(! $player->setup, 'Player has already joined game.');
    }

    public function applyToGame(GameState $game)
    {
        $game->player_ids[] = $this->player_id;
    }

    public function applyToPlayers(PlayerState $player)
    {
        $player->position = 1;
        $player->setup = true;
        $player->name = Session::get('user.name');
    }
}
