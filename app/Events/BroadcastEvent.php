<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

class BroadcastEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $event;

    public $gameState;

    public $playerState;

    public function __construct()
    {
        $this->event = null;
        $this->gameState = null;
        $this->playerState = null;
    }

    public function broadcastOn()
    {
        return new Channel('test-channel');
    }

    public function getEvent()
    {
        return $this->event;
    }

    public function getGameState()
    {
        return $this->gameState;
    }

    public function getPlayerState()
    {
        return $this->playerState;
    }

    public function setEvent($event)
    {
        $this->event = $event;
    }

    public function setGameState($game)
    {
        $this->gameState = $game;
    }

    public function setPlayerState($player)
    {
        $this->playerState = $player;
    }
}
