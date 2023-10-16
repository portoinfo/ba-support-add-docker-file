<?php

namespace App\Http\Middleware;

use App\Models\Company;
use App\Tools\Builderall\Logger;
use App\Tools\Crypt\RC4;
use App\Tools\Traits\DomainTrait;
use Closure;
use Illuminate\Routing\Route;

class CompanyApiAccess
{
    use DomainTrait;

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $company_hash = $request->route()->parameter('company');
        $data = $request->access_key ? RC4::decryptClientAccess(config('app.rc4_key'), $request->access_key) : $request->all();
        $office_request = isset($data['office_request']) ? $data['office_request'] : false;
        
        if($office_request){
            return $next($request);
        }
      
        if (!$request->route()->named('client-access') && !$request->route()->named('client-new-access')) 
        {
            $api_token    = $request->bearerToken();
            $company = Company::where('hash_code', $company_hash)->where('api_token', $api_token)->first();
        }
        else
        {
            $company_hash = $request->route()->parameter('company');
            $company = Company::where('hash_code', $company_hash)->first();
        }
        

        if (config('app.env') != 'production')
        {
            return $next($request);
        }

        if (!empty($company))
        {   
            
            $released_domains = [];
            $blocked_domains  = [];
            
            if ($company->settings)
            {
                $released_domains =  $company->settings->getRealesedDomainsArray();
                $blocked_domains  =  $company->settings->getBlockedDomainsArray();
            }

            $domain = $this->getOrigin();
            $ip     = $this->getIpAddress();

            if ($ip)
            {
                $is_released = empty($released_domains) || $this->inList($released_domains, $domain) || $this->inList($released_domains, $ip);
                $is_blocked  = !empty($blocked_domains) && ($this->inList($blocked_domains, $domain) || $this->inList($blocked_domains, $ip));
            }
            else
            {
                $is_released = empty($released_domains) || $this->inList($released_domains, $domain);
                $is_blocked  = !empty($blocked_domains) && $this->inList($blocked_domains, $domain);
            }
            
            if (!$is_released || $is_blocked)
            {
                $error = [
                    'message'          => 'Unauthorized',
                    'domain'           => $domain,
                    'ip'               => $ip,
                    'released_domains' => $released_domains,
                    'blocked_domains'  => $blocked_domains,
                ];
                
                Logger::reportWarning('Try to access Company API by invalid IP adrress.', $error);

                return response($error, 401);

            }
          
            
            return $next($request);
        }

        
        return response([
            'message' => 'Unauthenticated'
        ], 403);
    }

    /**
     * Get IP from request
     * @return string|null
     */
    private function getIpAddress()
    {

        if (empty($this->server['HTTP_X_FORWARDED_FOR']) && empty($this->server['REMOTE_ADDR']))
        {
            return '';
        } 

        if (!empty($this->server['HTTP_X_FORWARDED_FOR']))
        {
            return $this->server['HTTP_X_FORWARDED_FOR'];
        } 

        return $this->server['REMOTE_ADDR'];
    }

    /**
     * Get origin from request
     * @return string
     */
    private function getOrigin()
    {
        if (array_key_exists('HTTP_ORIGIN', $_SERVER)) 
        {
            $origin = $_SERVER['HTTP_ORIGIN'];
        }
        else if (array_key_exists('HTTP_REFERER', $_SERVER)) 
        {
            $origin = $_SERVER['HTTP_REFERER'];
        } else 
        {
            $origin = $_SERVER['REMOTE_ADDR'];
        }

        return $this->sanitizeDomain($origin);
    }

    /**
     * Check if origin (ip or domain) is in the list, applying the '*' rules
     * @param array $list
     * @param string $origin
     * 
     * @return boolean 
     */
    private function inList($list, $origin)
    {
        if (!empty($list))
        {
            foreach ($list as $i => $value) 
            {
                if (strpos($value, '*') !== false)
                {   
                    $target = str_replace('*', '', $value);
                    if (strpos($origin, $target) !== false)
                    {
                        return true;
                    }
                }
                else
                {
                    if ($origin == $value)
                    {
                        return true;
                    }
                }
            }
        }

        return false;
    }
}
