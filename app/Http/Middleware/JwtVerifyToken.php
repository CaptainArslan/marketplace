<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

class JwtVerifyToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $token = isset($request->token) ? $request->token : $request->header('authorization');
        // If token not found in headers, check in request parameters

        if (empty($token)) {
            return response()->json([
                'success' => false,
                'message' => 'Token not found!'
            ], 401);
        }
        
        try {
            $jwt = JWTAuth::parseToken()->authenticate();
        } catch (Exception $e) {
            if ($e instanceof TokenInvalidException) {
                return response()->json([
                    'success' => false,
                    'message' => 'Token is Invalid'
                ], 401);
            } elseif ($e instanceof TokenExpiredException) {
                return response()->json([
                    'success' => false,
                    'message' => 'Token is Expired'
                ], 401);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Authorization Token not found'
                ], 401);
            }
        }
        return $next($request);
    }
}
