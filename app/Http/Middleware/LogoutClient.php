<?php

namespace App\Http\Middleware;

use App\Models\Company;
use App\Tools\Builderall\Logger;
use Closure;
use Illuminate\Support\Facades\DB;
use App\Tools\Crypt;

class LogoutClient
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

        $host_cookie = preg_replace('/^(www|ba-support)\./', '', $_SERVER['HTTP_HOST']);

        // check if the cookie is set
        if (isset($_COOKIE['client_logout_url']))
        {
            unset($_COOKIE['client_logout_url']); 
            setcookie('client_logout_url', 'null', -1, $host_cookie, false, false);
        }

        $company = null;
        if (isset(session('companyselected')['company_id']))
        {
            $company = Company::find(Crypt::decrypt(session('companyselected')['company_id']));
        }

        $logout_url =  '/logout';
        
        if ($company)
        {
            $settings = $company->settings;

            if ($settings)
            {
                $logout_url = $settings->getClientLogoutUrl();
            }
        }
        
        setcookie('client_logout_url', $logout_url, time() + 60 * 60 *24 * 30, $host_cookie, false, false);
        return $next($request);
    }
}
