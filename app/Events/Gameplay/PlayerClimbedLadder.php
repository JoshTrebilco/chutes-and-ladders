<?php

namespace App\Events\Gameplay;

use Thunk\Verbs\Event;
use App\States\GameState;
use App\States\PlayerState;
use App\Events\BroadcastEvent;
use Thunk\Verbs\Attributes\Autodiscovery\AppliesToState;

#[AppliesToState(GameState::class)]
#[AppliesToState(PlayerState::class)]
class PlayerClimbedLadder extends Event
{
    public function __construct(
        public int $game_id,
        public int $player_id,
        public int $start,
        public int $end,
    ) {}

    public function applyToPlayer(PlayerState $player)
    {
        $player->position = $this->end;
    }

    public function handle(GameState $game, PlayerState $player)
    {
        $broadcastEvent = new BroadcastEvent();
        $broadcastEvent->setGameState($game);
        $broadcastEvent->setPlayerState($player);
        $broadcastEvent->setEvent(self::class);
        event($broadcastEvent);
    }
}
