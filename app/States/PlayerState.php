<?php

namespace App\States;

use Thunk\Verbs\State;

class PlayerState extends State
{
    public bool $setup = false;

    public int $square;
}
