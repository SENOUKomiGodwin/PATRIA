<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MaintenanceController extends Controller
{
    public function index(Request $request)
    {
        return view('maintenance.index');
    }

    public function showIntervention($id)
    {
        return view('maintenance.intervention', ['id' => $id]);
    }

    public function plans(Request $request)
    {
        return view('maintenance.plans');
    }
}
