<?php

namespace App\States;

use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;
use Thunk\Verbs\State;

class GameState extends State
{
    public bool $started = false;

    public array $player_ids = [];

    public ?int $active_player_id = null;

    public CarbonImmutable $started_at;

    /** @return Collection<int, PlayerState> */
    public function players(): Collection
    {
        return collect($this->player_ids)->map(fn (int $id) => PlayerState::load($id));
    }

    public function activePlayer(): ?PlayerState
    {
        return $this->active_player_id ? PlayerState::load($this->active_player_id) : null;
    }

    public function hasPlayer(PlayerState|int $player): bool
    {
        if ($player instanceof PlayerState) {
            $player = $player->id;
        }

        return in_array($player, $this->player_ids);
    }

    public function moveToNextPlayer(): static
    {
        $active_index = array_search($this->active_player_id, $this->player_ids);

        $this->active_player_id = $this->player_ids[$active_index + 1] ?? $this->player_ids[0];

        return $this;
    }
}
