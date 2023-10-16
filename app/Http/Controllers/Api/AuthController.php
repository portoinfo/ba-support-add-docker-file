<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\LoginRequest;
use App\Http\Requests\Api\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\Company;
use App\Tools\Client;
use App\Tools\Crypt;
use App\Tools\Crypt\RC4;
use App\Tools\SystemState;

class AuthController extends Controller
{
    public function loginClient (LoginRequest $request)
    {
        if($request->loginUnknown){
            $requestFake = Client::loginUnknown($request->all(), $request->company);
            $token = Client::apiAccess($requestFake, $requestFake['company']);
            return (new UserResource(auth('api')->user()))->additional($token);
        } else {
            $token = Client::apiAccess($request->all(), $request->company);
            return (new UserResource(auth('api')->user()))->additional($token);
        }
    }

    public function registerClient(RegisterRequest $request)
    {
        Client::create($request->all(), $request->company);
        $token = Client::apiAccess($request->all(), $request->company);
        return (new UserResource(auth('api')->user()))->additional($token);
    }

    public function me ()
    {
        return new UserResource(auth('api')->user());
    }

    public function getCacheForApi()
    {
        $cache = SystemState::getCacheForApi(auth('api')->user()->id, 'companyselected', null);

        if ($cache) {
            return response()->json([
                'success' => true,
                'cache' => $cache
            ]);
        } else {
            return response()->json([
                'success' => false,
            ]);
        }
    }

    public function refresh ()
    {
        $token = Client::refreshApiAuth();
        return (new UserResource(auth('api')->user()))->additional($token);
    }

    public function logout ()
    {
        auth('api')->logout(true);
        return response()->json(['success' => true]);
    }

    public function invalidate ()
    {
        auth('api')->invalidate(true);
        return response()->json(['success' => true]);
    }
}
