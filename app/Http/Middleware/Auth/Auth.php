<?php

namespace App\Http\Middleware\Auth;

use Closure;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenBlacklistedException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Facades\JWTAuth;

class Auth
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {

        try {

            /**
             * Redirect the user to the expected request
             */
            if (auth()->check()) {
                //Continue to the request
                return $next($request);
            }


            if (!$token = JWTAuth::parseToken()) {
                //throw an exception
                return response()->json([
                    'success' => false,
                    'error_code' => '401',
                    'message' => 'You are not authenticated!',
                ], '401');
            }
        } catch (\Exception $e) {
            if ($e instanceof TokenInvalidException) {
                //throw an exception
                return response()->json([
                    'success' => false,
                    'error_code' => 'INVALID_TOKEN',
                    'message' => $e->getMessage(),
                ], '401');
            } else if ($e instanceof TokenExpiredException) {
                //throw an exception
                return response()->json([
                    'success' => false,
                    'error_code' => 'TOKEN_EXPIRED',
                    'message' => $e->getMessage(),
                ], '401');
            } else if ($e instanceof TokenBlacklistedException) {
                //throw an exception
                return response()->json([
                    'success' => false,
                    'error_code' => 'TOKEN_BLACKLISTED',
                    'message' => $e->getMessage(),
                ], '401');
            } else if ($e instanceof JWTException) {
                //throw an exception
                return response()->json([
                    'success' => false,
                    'error_code' => 'JWT_ERROR',
                    'message' => $e->getMessage(),
                ], '401');
            } else {
                //throw an exception
                return response()->json([
                    'success' => false,
                    'error_code' => '401',
                    'message' => $e->getMessage(),
                ], '401');
            }
        }

        return response()->json([
            'success' => false,
            'error_code' => '401',
            'message' => 'You are not authenticated!',
        ], '401');
    }
}
