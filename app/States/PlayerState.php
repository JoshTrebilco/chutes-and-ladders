<?php

namespace App\States;

use Thunk\Verbs\State;

class PlayerState extends State
{
    public bool $setup = false;

    public int $position;

    public int $previous_position;

    public string $name;

    public ?string $color = null;
}
