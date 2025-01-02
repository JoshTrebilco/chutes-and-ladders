<?php

namespace App\Events\Gameplay;

use App\States\GameState;
use Thunk\Verbs\Attributes\Autodiscovery\AppliesToState;
use Thunk\Verbs\Event;

#[AppliesToState(GameState::class)]
class PlayerWonGame extends Event
{
    // use PlayerAction;

    public function __construct(
        public int $game_id,
        public int $player_id,
    ) {}

    public function applyToGame(GameState $game)
    {
        $game->winner_id = $this->player_id;
    }
}
