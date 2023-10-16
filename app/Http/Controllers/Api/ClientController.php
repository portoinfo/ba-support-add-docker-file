<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Tools\Builderall\Logger;
use App\Tools\Client;
use App\Tools\Crypt\RC4;
use App\Tools\Traits\ApiTrait;
use Exception;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    use ApiTrait;

    public function accountExists(Request $request, $company, $email)
    {
        try
        {
            $company_obj = Company::where('hash_code', $company)->first();

            if (empty($company_obj))
            {
                throw new Exception('Company not found.');
            }

            $account_exists = Client::accountExists($email, $company_obj->id);

            $response = $this->getApiResponse(true, ['account_exists' => $account_exists]);
        }
        catch(Exception $e)
        {
            $response = $this->getApiResponse(false, ['message' => $e->getMessage()]);
            Logger::reportException($e, [], ['api', 'client-controller', 'account-exists'], false);
        }

        return $response;
    }

    public function accountCreate(Request $request, $company)
    {
        try
        {
            Client::create($request->all(), $company);
            $response = $this->getApiResponse(true, ['message' => 'Account created.']);
        }
        catch(Exception $e)
        {
            $response = $this->getApiResponse(false, ['message' => $e->getMessage()]);
            Logger::reportException($e, [], ['api', 'client-controller', 'create-account'], false);
        }

        return $response;
    }

    public function accountUpdate(Request $request, $company)
    {
        try
        {
            Client::update($request->all(), $company);
            $response = $this->getApiResponse(true, ['message' => 'Account updated.']);
        }
        catch(Exception $e)
        {
            $response = $this->getApiResponse(false, ['message' => $e->getMessage()]);
            Logger::reportException($e, [], ['api', 'client-controller', 'update-account'], false);
        }

        return $response;
    }

    public function access(Request $request, $company)
    {
        try
        {
            $data = $request->access_key ? RC4::decryptClientAccess(config('app.rc4_key'), $request->access_key) : $request->all();
            $response = Client::access($data, $company, $request);
            return redirect(route($response['route'], $response['params']));
        }
        catch(Exception $e)
        {
            // $response = $this->getApiResponse(false, ['message' => $e->getMessage()]);
            Logger::reportException($e, [], ['api', 'client-controller', 'access'], false);
            return response()->view('errors.404',[],404);
        }
    }

    public function newAccess(Request $request, $company)
    {
        try
        {
            $data = $request->access_key ? RC4::decryptClientAccess(config('app.rc4_key'), $request->access_key) : $request->all();
            $response = Client::newAccess($data, $company, $request);

            session(['tokenOffice' => $request->token]);
            session(['suporteType' => 'ba-support']);
            
            if(config('app')['is_helpdesk'] == true){
                return redirect(route($response['route'], $response['params'])); // PARA WRAPPER
            }else{
             
                if($response['route'] == 'fast-ticket'){
                    return redirect(route('client-fast-ticket', $response['route']));
                }else{
                    return redirect(route('client-new', $response['route']));
                }
            }
        }
        catch(Exception $e)
        {
            echo $e;
            // $response = $this->getApiResponse(false, ['message' => $e->getMessage()]);
            Logger::reportException($e, [], ['api', 'client-controller', 'access'], false);
            return response()->view('errors.404',[],404);
        }
    }
}
