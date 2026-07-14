<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsNotSuperAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user() && $request->user()->isSuperAdmin()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Admin cannot create quotations. Only company users can.',
            ], 403);
        }

        return $next($request);
    }
}
