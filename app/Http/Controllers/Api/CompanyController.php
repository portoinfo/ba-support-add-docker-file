<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ApiGetCompanyDataRequest;
use App\Http\Resources\CompanyResource;
use App\Models\Company;
use App\Tools\CompanyAllowedDomains;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    public function index(ApiGetCompanyDataRequest $request) {

        $company = Company::where('hash_code', $request->hash_code)->first();

        if (config('app.env') != 'local')
        {
            CompanyAllowedDomains::validateOrigin($company, $request->origin);
        }

        return new CompanyResource($company);
    }
}
