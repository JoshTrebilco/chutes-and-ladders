<?php

namespace App\Events\Gameplay;

use App\States\GameState;
use App\States\PlayerState;
use Thunk\Verbs\Attributes\Autodiscovery\AppliesToState;
use Thunk\Verbs\Event;

#[AppliesToState(GameState::class)]
#[AppliesToState(PlayerState::class)]
class EndedTurn extends Event
{
    // use PlayerAction;

    public function __construct(
        public int $game_id,
        public int $player_id,
    ) {}

    public function applyToGame(GameState $game)
    {
        $game->last_player_id = $this->player_id;
        $game->moveToNextPlayer();
    }
}
