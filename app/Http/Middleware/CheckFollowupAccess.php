<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckFollowupAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user() || !$request->user()->canAccessFollowup()) {
            abort(403, 'Akses ditolak. Anda tidak memiliki izin untuk mengakses fitur Follow-up.');
        }

        return $next($request);
    }
}
