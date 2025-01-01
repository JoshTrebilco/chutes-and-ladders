<?php

namespace App\Http\Controllers;

use App\Game\Board;

class GameController extends Controller
{
    public function index()
    {
        $board = new Board;

        return view('welcome', [
            'numbers' => $board->generateBoardNumbers(),
            'ladders' => $board->getLadders(),
            'chutes' => $board->getChutes(),
            'squareSize' => $board->calculateSquareSize(),
        ]);
    }
}
