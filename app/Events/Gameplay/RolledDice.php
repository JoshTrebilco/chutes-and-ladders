<?php

namespace App\Events\Gameplay;

use App\States\GameState;
use App\States\PlayerState;
use InvalidArgumentException;
use Thunk\Verbs\Attributes\Autodiscovery\AppliesToState;
use Thunk\Verbs\Event;

#[AppliesToState(GameState::class)]
#[AppliesToState(PlayerState::class)]
class RolledDice extends Event
{
    // use PlayerAction;

    public function __construct(
        public int $game_id,
        public int $player_id,
        public int $die,
    ) {
        if ($die < 1 || $die > 6) {
            throw new InvalidArgumentException('You must roll six-sided dice.');
        }
    }

    public function validateGame(GameState $game)
    {
        $this->assert($game->last_roll === null, 'You already rolled the dice.');
    }

    public function applyToGame(GameState $game)
    {
        $game->last_roll = $this->die;
    }
}
