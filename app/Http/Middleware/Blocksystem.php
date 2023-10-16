<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class Blocksystem
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
        $result = DB::table('company')
				->select('status')
				->where('hash_code', session('companyselected')['hash_code'])
				->first();

        if ($result->status == "ACTIVE") {
            return $next($request);
        }else{
            abort(404);
        }
    }
}
