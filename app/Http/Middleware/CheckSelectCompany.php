<?php

namespace App\Http\Middleware;

use Closure;

class CheckSelectCompany
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
        if (!$request->session()->exists('companyselected')) {
            // user value cannot be found in session
            return redirect('select-company');
        }

        return $next($request);
    }
}
