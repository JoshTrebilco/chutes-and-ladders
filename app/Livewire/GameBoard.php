<?php

namespace App\Livewire;

use App\Game\Board;
use App\States\GameState;
use App\States\PlayerState;
use Livewire\Component;

class GameBoard extends Component
{
    public $gameId;

    public $authPlayerId;

    public function mount(int $gameId, $authPlayerId)
    {
        $this->gameId = $gameId;
        $this->authPlayerId = $authPlayerId;
    }

    #[On('pollGameState')]
    public function pollGameState()
    {
        $this->render();
    }

    public function render()
    {
        return view('livewire.game-board', [
            'game' => GameState::load($this->gameId),
            'board' => new Board,
            'authPlayer' => $this->authPlayerId ? PlayerState::load($this->authPlayerId) : null,
        ]);
    }
}
