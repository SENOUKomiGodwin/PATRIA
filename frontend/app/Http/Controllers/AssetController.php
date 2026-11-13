<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AssetController extends Controller
{
    public function index(Request $request)
    {
        return view('assets.index');
    }

    public function create()
    {
        return view('assets.create');
    }

    public function store(Request $request)
    {
        return redirect()->route('assets.index');
    }

    public function show($id)
    {
        return view('assets.show', ['id' => $id]);
    }

    public function edit($id)
    {
        return view('assets.edit', ['id' => $id]);
    }

    public function update(Request $request, $id)
    {
        return redirect()->route('assets.show', $id);
    }
}
