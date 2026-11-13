<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        try {
            $response = Http::post(env('API_URL') . '/api/auth/login', [
                'email' => $credentials['email'],
                'password' => $credentials['password'],
            ]);

            if ($response->successful()) {
                $data = $response->json();

                // Store JWT in session
                Session::put('jwt_token', $data['token']);
                Session::put('user_name', $data['user']['name'] ?? $data['user']['email']);
                Session::put('user_role', $data['user']['role']);
                Session::put('user_service', $data['user']['service'] ?? 'Service général');

                return redirect()->intended('/');
            }

            if ($response->status() === 401) {
                return back()->withErrors(['email' => 'Identifiants incorrects']);
            }

            return back()->withErrors(['email' => 'Connexion impossible. Veuillez réessayer.']);
        } catch (\Exception $e) {
            return back()->withErrors(['email' => 'Service indisponible. Veuillez réessayer plus tard.']);
        }
    }

    public function logout(Request $request)
    {
        Session::forget(['jwt_token', 'user_name', 'user_role', 'user_service']);
        return redirect('/login');
    }
}
