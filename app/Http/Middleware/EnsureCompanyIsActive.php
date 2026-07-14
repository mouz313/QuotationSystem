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
            return response()->json([
                'status'  => 'error',
                'message' => 'Your company has been blocked. Contact support.',
            ], 403);
        }

        return $next($request);
    }
}
