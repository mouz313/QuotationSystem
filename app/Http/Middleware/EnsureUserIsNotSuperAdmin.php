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
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Admin cannot create quotations. Only company users can.',
                ], 403);
            }
            return redirect('/admin/dashboard')->with('error', 'Admin cannot access company panel.');
        }

        return $next($request);
    }
}
