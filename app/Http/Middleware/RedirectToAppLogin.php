<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectToAppLogin
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->is('/') || $request->is('login')) {
            return redirect()->to('/app/login');
        }

        return $next($request);
    }
}