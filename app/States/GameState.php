<?php

namespace App\States;

use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;
use Thunk\Verbs\State;

class GameState extends State
{
    public bool $started = false;

    public CarbonImmutable $started_at;

}
