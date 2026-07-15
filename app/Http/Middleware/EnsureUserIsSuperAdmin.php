<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsSuperAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user() || !$request->user()->isSuperAdmin()) {
            if ($request->expectsJson() || $request->is('api/*') || $request->is('admin/*')) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Unauthorized. Super admin access required.',
                ], 403);
            }
            return redirect('/dashboard')->with('error', 'Unauthorized. Super admin access required.');
        }

        return $next($request);
    }
}
