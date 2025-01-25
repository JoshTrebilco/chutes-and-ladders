<?php

namespace App\Events\Setup;

use App\States\PlayerState;
use App\States\TokenState;
use Thunk\Verbs\Attributes\Autodiscovery\AppliesToState;
use Thunk\Verbs\Event;

#[AppliesToState(PlayerState::class)]
#[AppliesToState(TokenState::class)]
class TokenCreated extends Event
{
    public function __construct(
        public int $player_id,
        public int $token_id,
        public int $number,
    ) {}

    public function applyToPlayer(PlayerState $player)
    {
        $player->token_ids[] = $this->token_id;
    }

    public function applyToToken(TokenState $token)
    {
        $token->number = $this->number;
        $token->position = 1;
    }
}
