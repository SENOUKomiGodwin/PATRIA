<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        try {
            $response = Http::withToken(Session::get('jwt_token'))
                ->get(env('API_URL') . '/api/reports/dashboard');

            if ($response->successful()) {
                $data = $response->json();
                return view('dashboard.index', [
                    'dashboard' => $data,
                ]);
            }

            return view('dashboard.index', [
                'dashboard' => null,
                'error' => 'Impossible de charger les données du tableau de bord',
            ]);
        } catch (\Exception $e) {
            return view('dashboard.index', [
                'dashboard' => null,
                'error' => 'Service indisponible',
            ]);
        }
    }
}
