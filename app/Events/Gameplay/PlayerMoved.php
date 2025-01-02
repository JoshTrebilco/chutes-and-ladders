<?php

namespace App\Events\Gameplay;

use App\States\GameState;
use App\States\PlayerState;
use Thunk\Verbs\Attributes\Autodiscovery\AppliesToState;
use Thunk\Verbs\Event;

#[AppliesToState(GameState::class)]
#[AppliesToState(PlayerState::class)]
class PlayerMoved extends Event
{
    // use PlayerAction;

    public function __construct(
        public int $game_id,
        public int $player_id,
        public int $position,
    ) {}

    public function applyToPlayer(PlayerState $player)
    {
        $player->position = $this->position;
    }
}
