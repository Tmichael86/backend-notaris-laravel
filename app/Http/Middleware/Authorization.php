<?php

namespace App\Http\Middleware;

use App\Http\Libraries\System;
use Closure;
use Illuminate\Http\Request;

class Authorization
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $akses = System::getAccess();
        if ($akses != null && $akses->read) {
            return $next($request);
        } else {
            return response()->json([
                'statusCode' => 403,
                'message' => 'You are not authorized to access this page',
                'debug' => $akses,
            ], 403);
        }
    }
}
