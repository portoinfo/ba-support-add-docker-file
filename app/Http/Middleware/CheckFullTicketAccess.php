<?php

namespace App\Http\Middleware;

use Closure;

class CheckFullTicketAccess
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
        // if($request->session()->exists('restriction') && (!session('restriction')[0]->ticket_admin && !session('restriction')[0]->ticket_alllist)) {
        //     return redirect('/ticket');
        // }

        return $next($request);
    }
}
