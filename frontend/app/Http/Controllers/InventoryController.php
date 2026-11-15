<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        return view('inventories.index');
    }

    public function show($id)
    {
        return view('inventories.show', ['id' => $id]);
    }

    public function report($id)
    {
        return view('inventories.report', ['id' => $id]);
    }
}
