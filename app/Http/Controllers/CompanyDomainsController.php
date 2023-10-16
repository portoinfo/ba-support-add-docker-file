<?php

namespace App\Http\Controllers;

use App\CompanyDomain;
use App\Http\Requests\StoreCompanyDomainRequest;
use App\Http\Resources\CompanyDomainResource;
use App\Models\Company;
use App\Tools\CompanyAllowedDomains;
use Illuminate\Http\Request;

class CompanyDomainsController extends Controller
{
    private $company_allowed_domains;

    public function __construct(CompanyAllowedDomains $company_allowed_domains)
    {
        $this->company_allowed_domains = $company_allowed_domains;    
    }

    public function index(Request $request, $comapny_hash_code)
    {
        $domains = $this->company_allowed_domains->getCompanyDomains($comapny_hash_code);
        return CompanyDomainResource::collection($domains);
    }

    public function store(StoreCompanyDomainRequest $request)
    {
       $company_domain = $this->company_allowed_domains->add($request->domain, $request->company);
       return new CompanyDomainResource($company_domain); 
    }

    public function destroy(CompanyDomain $company_domain)
    {
       $this->company_allowed_domains->destroy($company_domain);
       return response()->json(['success' => true]);    
    }
}
