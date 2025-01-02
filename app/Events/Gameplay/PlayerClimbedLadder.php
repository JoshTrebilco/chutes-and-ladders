<?php

namespace App\Events\Gameplay;

use Thunk\Verbs\Event;

class PlayerClimbedLadder extends Event
{
    public function __construct(
        public int $game_id,
        public int $player_id,
        public int $start,
        public int $end,
    ) {}
}
