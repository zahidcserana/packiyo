<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class ApiLogMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        return $next($request);
    }

    public function terminate(Request $request, Response $response)
    {
        Log::channel('api')->info('app.requests', [
            'route' => $request->getRequestUri(),
            'request' => $request->all(),
            'code' => $response->getStatusCode(),
            'response' => $response->getContent(),
            'time' => microtime(true) - LARAVEL_START
        ]);
    }
}
