<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsCompanyAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user || (!$user->isCompanyAdmin() && !$user->isSuperAdmin())) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Unauthorized. Company admin access required.',
                ], 403);
            }
            return redirect('/dashboard')->with('error', 'Unauthorized. Company admin access required.');
        }

        return $next($request);
    }
}
