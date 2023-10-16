<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function redirectTo($request)
    {
        if (! $request->expectsJson()) {
            $request->session()->flush();
            /*
            $request->session()->forget('user');
            $request->session()->forget('is_admin');
            $request->session()->forget('companyselected');
            $request->session()->forget('settings');
            $request->session()->forget('departmentUser');
            $request->session()->forget('is_client');
            $request->session()->forget('user_departments_id');
            $request->session()->forget('company_user_company_departments');
            $request->session()->forget('restriction');
            */
            return route('login');
        }
    }
}
