<?php

namespace App\Http\Middleware;

use Closure;

class CheckUserSession
{
    public function handle($request, Closure $next)
    {
        if (session('is_client')) 
        {     
            // user value cannot be found in session
            return redirect('/client');
        }

        return $next($request);
    }
}
