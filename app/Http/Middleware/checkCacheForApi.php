<?php

namespace App\Http\Middleware;

use App\Tools\SystemState;
use Closure;

class checkCacheForApi
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
        try {

            $cache = SystemState::getCacheForApi(auth('api')->user()->id, 'companyselected', null);

            if ($cache) {
                return $next($request);
            } else {
                return response([
                    'message' => 'Unauthenticated'
                ], 403);
            }

        } catch (\Exception $e) {
            return response([
                'message' => 'Unauthenticated'
            ], 403);
        }

    }
}
