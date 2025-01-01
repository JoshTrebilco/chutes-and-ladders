<?php

namespace App\Events\Setup;

use App\States\GameState;
use Thunk\Verbs\Attributes\Autodiscovery\AppliesToState;
use Thunk\Verbs\Event;

#[AppliesToState(GameState::class)]
class GameStarted extends Event
{
    public function __construct(
        public ?int $game_id = null,
    ) {}

    public function validate(GameState $game)
    {
        $this->assert(! $game->started, 'The game has already started');
    }

    public function applyToGame(GameState $game)
    {
        $game->started = true;
        $game->started_at = now()->toImmutable();
        $game->player_ids = [];
    }
}
