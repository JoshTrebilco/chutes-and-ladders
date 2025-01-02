<?php

namespace App\Events\Setup;

use App\States\GameState;
use Thunk\Verbs\Attributes\Autodiscovery\AppliesToState;
use Thunk\Verbs\Event;

#[AppliesToState(GameState::class)]
class FirstPlayerSelected extends Event
{
    public function __construct(
        public int $game_id,
        public int $player_id,
    ) {}

    public function validateGame(GameState $game)
    {
        $this->assert($game->activePlayer() === null, 'A player has already been selected.');
        $this->assert($game->hasPlayer($this->player_id), 'This player is not part of the game.');
        $this->assert($game->players()->count() > 1, 'There must be at least two players in the game.');
    }

    public function applyToGame(GameState $game)
    {
        $game->active_player_id = $this->player_id;
    }
}
