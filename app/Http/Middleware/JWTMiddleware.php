<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class JWTMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            if (!$user)
                return response()->json(['error' => 'User not found'], STATUS_NOT_FOUND);

        } catch (\Throwable $exception) {

            if ($exception instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException) {
                return response()->json(['error' => 'Invalid Token'], STATUS_CODE_UNPROCESSABLE);
            } else if ($exception instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException) {
                return response()->json(['error' => 'Token Expired'], STATUS_CODE_UNPROCESSABLE);
            } else {
                return response()->json(['error' => 'Something went wrong'], STATUS_CODE_ERROR);
            }
        }
        return $next($request);
    }
}
