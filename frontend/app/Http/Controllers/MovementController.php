<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MovementController extends Controller
{
    public function index(Request $request)
    {
        return view('movements.index');
    }
}
