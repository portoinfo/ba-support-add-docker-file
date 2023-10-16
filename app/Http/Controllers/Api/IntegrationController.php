<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Tools\Builderall\Logger;
use App\Tools\Traits\ApiTrait;
use Exception;
use Illuminate\Http\Request;

class IntegrationController extends Controller
{
    use ApiTrait;

    public function company(Request $request, $app)
    {
        try
        {
            $companies = Company::all()->toArray();
            $data = [];
            
            if (!empty($companies))
            {
                array_walk($companies, function($item, $key) use (&$data)
                {
                    array_push($data, ['name' => $item['name'], 'hash_code' => $item['hash_code']]);
                });
            }

            Logger::reportInfo("Integration request from $app.", ['app' => $app], ['api', 'integration-controller', 'company'], false);
            $response = $this->getApiResponse(true, ['count' => count($data), 'companies' => $data]);
        }
        catch(Exception $e)
        {
            $response = $this->getApiResponse(false, ['message' => $e->getMessage()]);
            Logger::reportException($e, ['app' => $app], ['api', 'integration-controller', 'company'], false);
        }

        return $response;
    }

    public function companyAgents(Request $request, $app, $company)
    {
        try
        {
            $company_obj = Company::where('hash_code', $company)->first();

            if (empty($company_obj))
            {
                throw new Exception('Company not found.');
            }

            $data = [];
            $agents = $company_obj->agents()->toArray();

            if(!empty($agents))
            {
                array_walk($agents, function($item, $key) use (&$data)
                {
                    array_push($data, ['name' => $item['name'], 'email' => $item['email'], 'hash_code' => $item['hash_code']]);
                });
            }

            $response = $this->getApiResponse(true, [
                'count' => count($data), 
                'company' => [
                    'name' => $company_obj->name, 
                    'hash_code' => $company_obj->hash_code
                ],
                'agents' => $data
                ]
            );

            Logger::reportInfo("Integration request from $app.", ['app' => $app], ['api', 'integration-controller', 'company-agents'], false);
        }
        catch(Exception $e)
        {
            $response = $this->getApiResponse(false, ['message' => $e->getMessage()]);
            Logger::reportException($e, ['app' => $app], ['api', 'integration-controller', 'company-agents'], false);
        }

        return $response;
    }

    public function accessInformation(Request $request, $company)
    {
        try
        {
            $company_obj = Company::where('hash_code', $company)->first();

            if (empty($company_obj))
            {
                throw new Exception('Company not found.');
            }

            $data = [
                'name'            => $company_obj->name,
                'hash_code'       => $company_obj->hash_code,
                'api_token'       => $company_obj->api_token,
                'register-client' => route('register-client', ['company' => $company]),
                'login-client'    => route('login-client', ['company' => $company]),
            ];

            $response = $this->getApiResponse(true, $data);
        }
        catch(Exception $e)
        {
            $response = $this->getApiResponse(false, ['message' => $e->getMessage()]);
            Logger::reportException($e, [], ['api', 'integration-controller', 'access-information'], false);
        }

        return $response;
    }
}
