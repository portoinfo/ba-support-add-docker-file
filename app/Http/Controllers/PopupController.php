<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Tools\Builderall\Logger;
use App\Tools\Client;
use Illuminate\Http\Request;

class PopupController extends Controller
{
    public function login(Request $request, $company) {

        if ($request->isMethod('post'))
        {
            try {
                $response = Client::access($request->all(), $company, $request);
                $login = [
                    'success' => true,
                    'redir'   => route($response['route'], $response['params'], true),
                ];
            } catch (\Exception $e) {
                return $e;
                Logger::reportException($e, [], ['home-controller', 'login-client'], false);
                $login = [
                    'success' => false,
                    'value'   => 'not_email',
                ];
            }
            return $login;
        } else {
            $company = Company::where('hash_code', $company)->first();
            if (!$company) {
                return response()->view('errors.404', [], 404);
            }
            // return view('layouts2.loginClient', ['company' => $company->toArray()]);
            return view('client.auth.login', ['company' => $company->toArray()]);
        }
    }
}
