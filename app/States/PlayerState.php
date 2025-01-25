<?php

namespace App\States;

use Illuminate\Support\Collection;
use Thunk\Verbs\State;

class PlayerState extends State
{
    public bool $setup = false;

    /** @var array<int, int> Map of token IDs */
    public array $token_ids = [];

    public string $name;

    public ?string $color = null;

    /** @return Collection<int, TokenState> */
    public function tokens(): Collection
    {
        return collect($this->token_ids)->map(fn (int $id) => TokenState::load($id));
    }
}
