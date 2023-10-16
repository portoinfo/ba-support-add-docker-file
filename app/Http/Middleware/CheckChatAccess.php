<?php

namespace App\Http\Middleware;

use Closure;

class CheckChatAccess
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
        if($request->session()->exists('restriction') && (session('restriction')[0]->chat_admin || session('restriction')[0]->chat_alllist)) {
            return redirect('/full-chat');
        }
        return $next($request);
    }
}
