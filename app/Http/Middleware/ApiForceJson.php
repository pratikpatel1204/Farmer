<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * App API hamesha JSON me jawab de (login nahi = 401 JSON, galat URL = 404 JSON),
 * chahe app Accept header bhejna bhool jaye.
 */
class ApiForceJson
{
    public function handle(Request $request, Closure $next)
    {
        $request->headers->set('Accept', 'application/json');

        return $next($request);
    }
}
