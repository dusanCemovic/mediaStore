<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiTokenMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Accept either Authorization: Bearer <token> OR X-API-TOKEN header
        $token = null;

        // 1. find and remove substring to keep only token
        $authHeader = $request->header('Authorization');
        if ($authHeader && str_starts_with($authHeader, 'Bearer ')) {
            $token = substr($authHeader, 7);
        }

        // 1.1. if we don't have token, we check api token form env file
        if (!$token) {
            $token = $request->header('X-API-TOKEN');
        }

        // 2. Checking token with env api token.
        if (!$token || $token !== env('API_TOKEN')) {
            return response()->json(
                [
                    'message' => 'Unauthorized'
                ], 401);
        }

        // allow to continue
        return $next($request);
    }
}
