<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Auth;
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

        if (empty($token)) {
            abort(404, 'Token not found.');
        }

        try {
            JWTAuth::parseToken()->authenticate();
            // Auth::login(auth('user')->user());
        } catch (TokenInvalidException $e) {
            info("Invalid token exception" . $e->getMessage());
            return abort(401, 'Token is Invalid.');
        } catch (TokenExpiredException $e) {
            info("Token expiration exception" . $e->getMessage());
            return abort(419, 'Token is Expired.');
        } catch (Exception $e) {
            info("Invalidated Token" . $e->getMessage());
            // Handle other exceptions as needed
            return abort(419);
        }

        return $next($request);
    }
}
