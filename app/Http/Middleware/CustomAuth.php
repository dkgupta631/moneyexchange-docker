<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth; // ← Use Auth facade

class CustomAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        $path = $request->path();

        // If already logged in, redirect away from login/register
        if (($path == 'login' || $path == 'register') && Auth::check()) {
            return redirect('/');
        }

        // If NOT logged in, redirect to login for protected pages
        if (
            ($path == 'moneyexchange' && !Auth::check()) ||
            ($path == 'money-transfer-in' && !Auth::check()) ||
            ($path == 'money-transfer-OUT' && !Auth::check())
        ) {
            return redirect('/login');
        }

        return $next($request);
    }
}