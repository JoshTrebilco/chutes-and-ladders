<?php

namespace App\Events\Gameplay;

use Thunk\Verbs\Event;
use App\States\GameState;
use App\States\PlayerState;
use App\Events\BroadcastEvent;
use Thunk\Verbs\Attributes\Autodiscovery\AppliesToState;

#[AppliesToState(GameState::class)]
#[AppliesToState(PlayerState::class)]
class PlayerMoved extends Event
{
    // use PlayerAction;

    public function __construct(
        public int $game_id,
        public int $player_id,
        public int $position,
        public int $previous_position,
    ) {}

    public function applyToPlayer(PlayerState $player)
    {
        $player->previous_position = $this->previous_position;
        $player->position = $this->position;
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
