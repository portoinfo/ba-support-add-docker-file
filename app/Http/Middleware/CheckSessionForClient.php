<?php

namespace App\Http\Middleware;

use App\Exceptions\RouteNotAllowedAgent;
use App\Exceptions\RouteNotAllowedClient;
use Closure;

class CheckSessionForClient
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
        if (!$request->session()->exists('is_client')) 
        {     
            throw new RouteNotAllowedClient();
        }
        return $next($request);
    }
}
