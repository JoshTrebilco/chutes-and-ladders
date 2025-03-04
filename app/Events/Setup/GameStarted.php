<?php

namespace App\Events\Setup;

use App\Events\BroadcastEvent;
use Thunk\Verbs\Event;
use App\States\GameState;
use Thunk\Verbs\Attributes\Autodiscovery\AppliesToState;

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

    public function handle(GameState $game)
    {
        $broadcastEvent = new BroadcastEvent();
        $broadcastEvent->setGameState($game);
        $broadcastEvent->setEvent(self::class);
        event($broadcastEvent);
    }
}
