<?php

namespace App\Events\Setup;

use Thunk\Verbs\Event;
use App\States\GameState;
use App\States\PlayerState;
use App\Events\BroadcastEvent;
use Illuminate\Support\Facades\Auth;
use Thunk\Verbs\Attributes\Autodiscovery\AppliesToState;

#[AppliesToState(GameState::class)]
#[AppliesToState(PlayerState::class)]
class PlayerJoinedGame extends Event
{
    public function __construct(
        public int $game_id,
        public int $player_id,
    ) {}

    public function validateGame(GameState $game)
    {
        $this->assert($game->created, 'Game must be created before a player can join.');
        $this->assert(! $game->isInProgress(), 'The game is already in progress.');
    }

    public function validatePlayer(PlayerState $player)
    {
        $this->assert(! $player->setup, 'Player has already joined game.');
    }

    public function applyToGame(GameState $game)
    {
        $game->player_ids[] = $this->player_id;
    }

    public function applyToPlayers(PlayerState $player)
    {
        $player->position = 1;
        $player->setup = true;
        $player->name = Auth::user()?->name ?? 'Unknown Player';
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
