<?php

namespace App\Events\Gameplay;

use Thunk\Verbs\Event;
use App\States\GameState;
use App\States\PlayerState;
use App\Events\BroadcastEvent;
use Thunk\Verbs\Attributes\Autodiscovery\AppliesToState;

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

    public function handle(GameState $game, PlayerState $player)
    {
        $broadcastEvent = new BroadcastEvent();
        $broadcastEvent->setGameState($game);
        $broadcastEvent->setPlayerState($player);
        $broadcastEvent->setEvent(self::class);
        event($broadcastEvent);
    }
}
