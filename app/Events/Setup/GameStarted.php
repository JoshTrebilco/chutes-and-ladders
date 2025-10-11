<?php

namespace App\Events\Setup;

use Thunk\Verbs\Event;
use App\States\GameState;
use App\Events\BroadcastEvent;
use Thunk\Verbs\Attributes\Autodiscovery\AppliesToState;

#[AppliesToState(GameState::class)]
class GameStarted extends Event
{
    public function __construct(
        public int $game_id,
        public int $player_id,
    ) {}

    public function validate(GameState $game)
    {
        $this->assert($game->activePlayer() === null, 'A player has already been selected.');
        $this->assert($game->hasPlayer($this->player_id), 'This player is not part of the game.');
        $this->assert($game->hasEnoughPlayers(), 'There must be at least two players in the game.');
    }

    public function applyToGame(GameState $game)
    {
        $game->active_player_id = $this->player_id;
    }

    public function handle(GameState $game)
    {
        $broadcastEvent = new BroadcastEvent();
        $broadcastEvent->setGameState($game);
        $broadcastEvent->setEvent(self::class);
        event($broadcastEvent);
    }
}
