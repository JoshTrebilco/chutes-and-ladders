<?php

namespace App\Http\Controllers;

use App\Game\Board;

class GameController extends Controller
{
    public function index()
    {
        return view('welcome', [
            'board' => new Board,
        ]);
    }
}
