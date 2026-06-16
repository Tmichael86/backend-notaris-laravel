<?php

namespace App\Http\Middleware\Authentication;

use Closure;
use Illuminate\Http\Request;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class JWTAuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        try {
            $token = JWTAuth::getToken();
            $decode = JWTAuth::getPayload($token)->toArray();

            $request->attributes->add((array) @$decode['payload']);
        } catch (\Throwable $th) {
            return response()->json([
                'statusCode' => 401,
                'message' => 'Token authentikasi tidak valid',
            ], 401, ['Content-type' => 'application/json'], JSON_PRETTY_PRINT);
        }

        return $next($request);
    }
}
