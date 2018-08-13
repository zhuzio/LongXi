<?php

namespace App\Http\Middleware;

use Closure;

class CrosAccess
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
        $response = $next($request);
        return $response->header('Access-Control-Allow-Origin', '*')
            ->header('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Origin, Authorization, Accept, Client-Security-Token, Accept-Encoding')
            ->header('Access-Control-Allow-Methods', 'POST, GET, OPTIONS');
    }
}
