<?php

namespace App\Events\Setup;

use App\States\GameState;
use App\States\PlayerState;
use Thunk\Verbs\Attributes\Autodiscovery\AppliesToState;
use Thunk\Verbs\Event;

#[AppliesToState(GameState::class)]
#[AppliesToState(PlayerState::class)]
class PlayerColorSelected extends Event
{
    public function __construct(
        public int $game_id,
        public int $player_id,
        public string $color,
    ) {}

    public function validatePlayer(PlayerState $player)
    {
        $this->assert($player->color === null, 'Player has already selected a color.');
    }

    public function validateGame(GameState $game)
    {
        $this->assert($game->available_colors, 'Game must have available colors before a player can select a color.');
        $this->assert(in_array($this->color, $game->available_colors), 'Color is not available.');
    }

    public function applyToPlayers(PlayerState $player)
    {
        $player->color = $this->color;
    }

    public function applyToGame(GameState $game)
    {
        $game->available_colors = array_diff($game->available_colors, [$this->color]);
    }
}
