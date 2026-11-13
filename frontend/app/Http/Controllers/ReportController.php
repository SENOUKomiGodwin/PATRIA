<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        return view('reports.index');
    }

    public function depreciation(Request $request)
    {
        return view('reports.depreciation');
    }

    public function exports(Request $request)
    {
        return view('reports.exports');
    }
}
