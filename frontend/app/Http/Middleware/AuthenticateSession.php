<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class AuthenticateSession
{
    public function handle(Request $request, Closure $next)
    {
        if (!Session::has('jwt_token')) {
            return redirect('/login')->with('session_expired', true);
        }

        return $next($request);
    }
}
