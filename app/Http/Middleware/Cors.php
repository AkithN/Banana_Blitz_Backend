<?php

namespace App\Http\Middleware;

use Closure;

class Cors
{
    public function handle($request, Closure $next)
    {
        $response = $next($request);

        // CORS headers
        $response->headers->set('Access-Control-Allow-Origin', 'http://localhost:3000');
        $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, PUT, PATCH, DELETE, OPTIONS');
        $response->headers->set('Access-Control-Allow-Headers', 'Accept, Content-Type, Authorization, X-Requested-With, X-XSRF-TOKEN');
        $response->headers->set('Access-Control-Allow-Credentials', 'true');

        // Immediately return for OPTIONS method
        if ($request->getMethod() === "OPTIONS") {
            return response('', 204, $response->headers->all());
        }

        return $response;
    }
}
