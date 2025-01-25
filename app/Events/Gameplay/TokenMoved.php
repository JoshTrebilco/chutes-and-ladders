<?php

namespace App\Events\Gameplay;

use App\States\TokenState;
use Thunk\Verbs\Attributes\Autodiscovery\AppliesToState;
use Thunk\Verbs\Event;

#[AppliesToState(TokenState::class)]
class TokenMoved extends Event
{
    public function __construct(
        public int $game_id,
        public int $token_id,
        public int $position,
    ) {}

    public function applyToToken(TokenState $token)
    {
        $token->position = $this->position;
    }
}
