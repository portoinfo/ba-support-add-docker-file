<?php

namespace App\Http\Middleware;

use Closure;

class FrameHeadersMiddleware
{
    // Enumerate unwanted headers
    private $unwantedHeaderList = [
        'X-Frame-Options',
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure                 $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $response = $next($request);
        $this->removeUnwantedHeaders($this->unwantedHeaderList);
        return $response;
    }

    /**
     * @param $headerList
     */
    private function removeUnwantedHeaders($headerList)
    {
        foreach ($headerList as $header)
            header_remove($header);
    }
}
