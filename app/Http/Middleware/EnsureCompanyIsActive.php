<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureCompanyIsActive
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->company && $user->company->isBlocked()) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Your company has been blocked. Contact support.',
                ], 403);
            }
            if (!$user->isSuperAdmin()) {
                auth()->logout();
                return redirect('/login')->with('error', 'Your company has been blocked. Contact support.');
            }
        }

        return $next($request);
    }
}
