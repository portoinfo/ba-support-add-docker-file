<?php

namespace App\Http\Middleware;

use App\Exceptions\RouteNotAllowedAgent;
use Closure;
use Exception;

class CheckSessionForAgents
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
        if ($request->session()->exists('is_client')) 
        {     
            throw new RouteNotAllowedAgent();
        }
        return $next($request);
    }
}
