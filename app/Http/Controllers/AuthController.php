<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{
    public function index(Request $request)
    {
        return view('login', [
            'game_id' => $request->input('game_id'),
        ]);
    }

    public function store(Request $request)
    {
        Session::put('user', ['name' => $request->input('name')]);

        if ($request->has('game_id')) {
            return redirect()->route('games.show', ['game_id' => $request->input('game_id')]);
        }

        return redirect()->route('games.index');
    }
}
