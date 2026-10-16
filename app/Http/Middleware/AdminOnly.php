<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminOnly
{
    public function handle(Request $request, Closure $next): Response
    {
        // Wajib sudah login (middleware 'auth') dan berperan admin
        abort_unless($request->user()?->isAdmin(), 403, 'Khusus administrator.');

        return $next($request);
    }
}
