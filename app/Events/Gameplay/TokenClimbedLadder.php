<?php

namespace App\Events\Gameplay;

use Thunk\Verbs\Event;

class TokenClimbedLadder extends Event
{
    public function __construct(
        public int $game_id,
        public int $token_id,
        public int $start,
        public int $end,
    ) {}
}
