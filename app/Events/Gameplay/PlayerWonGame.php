<?php

namespace App\Events\Gameplay;

use Thunk\Verbs\Event;
use App\States\GameState;
use App\Events\BroadcastEvent;
use Thunk\Verbs\Attributes\Autodiscovery\AppliesToState;

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

    public function handle(GameState $game)
    {
        $broadcastEvent = new BroadcastEvent();
        $broadcastEvent->setGameState($game);
        $broadcastEvent->setEvent(self::class);
        event($broadcastEvent);
    }
}
