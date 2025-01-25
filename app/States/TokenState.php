<?php

namespace App\States;

use Thunk\Verbs\State;

class TokenState extends State
{
    public int $position = 1;

    public int $number;
}
