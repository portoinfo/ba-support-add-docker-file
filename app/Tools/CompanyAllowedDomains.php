<?php

namespace App\Tools;

use App\CompanyDomain;
use App\Exceptions\Api\CompanyNotFoundException;
use App\Exceptions\DomainAlreadyRegisteredException;
use App\Exceptions\DomainNotValidException;
use App\Exceptions\OperationNotAllowedException;
use App\Exceptions\OriginNotValidException;
use App\Exceptions\UserHasNoQuotas;
use MuriloPerosa\DomainTools\Name;
use App\Models\Company;
use App\Models\Company_user;
use Illuminate\Support\Str;

class CompanyAllowedDomains
{
    /**
     * Returns all allowed domains for company
     * @param mixed $company_ref
     * @throws App\Exceptions\CompanyNotFoundException
     * @return array
     */
    public function getCompanyDomains ($company_ref)
    {
        $company = is_int($company_ref) ? Company::find($company_ref) : Company::where('hash_code', $company_ref)->first();

        if (!$company)
        {
            throw new CompanyNotFoundException();
        }

        return $company->companyDomains;
    }

    /**
     * Adds a domain name for company
     * @param string $domain
     * @param string $company_hash
     * @throws App\Exceptions\OperationNotAllowedException
     * @throws App\Exceptions\CompanyNotFoundException
     * @throws App\Exceptions\DomainNotValidException
     * @return App\CompanyDomain
     */
    public function add(string $domain, string $company_hash)
    {
        $company = Company::where('hash_code', $company_hash)->first();

        if (!$company)
        {
            throw new CompanyNotFoundException();
        }

        // check user permission
        $this->checkPermission($company);

        // sanitize and validate domain
        $name = (new Name($domain))->sanitize(true)->idnToAscii();

        if (!$name->is_valid || count($name->parts) < 2)
        {
            throw new DomainNotValidException();
        }

        if ($company->companyDomains->contains("domain", $name->domain))
        {
            throw new DomainAlreadyRegisteredException();
        }

        /**
         * Check user quotas
         */
        $quotas = self::getUserQuotas(auth()->user()->id);

        if ($quotas['limit'] <= $quotas['used'])
        {
            throw new UserHasNoQuotas();
        }

        $data = [
            'company_id' => $company->id,
            'domain'     => $name->domain
        ];

        return CompanyDomain::create($data);
    }

    /**
     * Removes a domain name for company
     * @param string $domain
     * @param string $company_hash
     * @throws App\Exceptions\OperationNotAllowedException
     * @throws App\Exceptions\CompanyNotFoundException
     * @return bool
     */
    public function destroy(CompanyDomain $company_domain)
    {
        $company = Company::where('id', $company_domain->company_id)->first();

        if (!$company)
        {
            throw new CompanyNotFoundException();
        }

        // check user permission
        $this->checkPermission($company);

        return $company_domain->delete();

    }

    /**
     * Handle user permission
     * @param App\Models\Company $company
     * @return void
     * @throws App\Exceptions\OperationNotAllowedException
     */
    public function checkPermission(Company $company)
    {
        if(!auth()->user()->isCompanyOwner($company->id) && !auth()->user()->isPartOfCompany($company->id))
        {
            throw new OperationNotAllowedException();
        }
    }

    /**
     * Returns quotas of domains
     * @param int $user_id
     * @return array
     */
    public static function getUserQuotas(int $user_id)
    {
        $used  = CompanyDomain::select()
                ->join('company_user', 'company_user.company_id', '=', 'company_domains.company_id')
                ->where('company_user.user_auth_id', '=', $user_id)
                ->where('company_user.is_admin', '=', true)->count();
        /**
         * "Ilimitado" conforme Neri alinhou com Erick e Alê no dia 12/07/2021
         * Mantida implementação para possíveis mudanças no futuro
         */
        $limit = 9999999999;

        $result = [
            'used'  => $used,
            'limit' => $limit,
        ];

        return $result;
    }

    /**
     * Returns if origin is allowed for company
     * @param mixed $company_ref
     * @param string $origin
     * @throws App\Exceptions\CompanyNotFoundException
     * @throws App\Exceptions\DomainNotValidException
     * @throws App\Exceptions\OriginNotValidException
     * @return void
     */
    public static function validateOrigin($company_ref, string $origin)
    {

        if ($company_ref instanceof Company)
        {
            $company = $company_ref;
        }
        else
        {
            $company = is_int($company_ref) ? Company::find($company_ref) : Company::where('hash_code', $company_ref)->first();
        }

        if (!$company)
        {
            throw new CompanyNotFoundException();
        }

        if (Str::endsWith($origin, 'builderall.local'))
        {
            return;
        }
        // sanitize and validate domain
        $origin_name = (new Name($origin))->sanitize(true)->idnToAscii();

        if (!$origin_name->is_valid || count($origin_name->parts) < 2)
        {
            throw new DomainNotValidException();
        }

        $allowed =  $company->companyDomains->contains("domain", $origin_name->domain);

        if (!$allowed)
        {
            throw new OriginNotValidException();
        }

    }

    /**
     * Add default company domains
     * @param mixed $company_ref
     * @return void
     */
    public static function addDefaultDomains($company_ref)
    {
        $defaults = [
            'builderall.com',
        ];

        if ($company_ref instanceof Company)
        {
            $company = $company_ref;
        }
        else
        {
            $company = is_int($company_ref) ? Company::find($company_ref) : Company::where('hash_code', $company_ref)->first();
        }

        if ($company)
        {
            foreach ($defaults as $d)
            {
                if (!$company->companyDomains->contains("domain", $d))
                {

                CompanyDomain::create([
                        'company_id' => $company->id,
                        'domain'     => $d
                    ]);
                }
            }
        }
    }

    /**
     * Adds a froup of domains name for company
     * @param string $domain
     * @param string $company_hash
     */
    public static function addDomainGroup($domains, $company_ref)
    {
        if ($company_ref instanceof Company)
        {
            $company = $company_ref;
        }
        else
        {
            $company = is_int($company_ref) ? Company::find($company_ref) : Company::where('hash_code', $company_ref)->first();
        }

        if ($company)
        {
            foreach ($domains as $domain)
            {
                // sanitize and validate domain
                $name = (new Name($domain))->sanitize(true)->idnToAscii();

                if ($name->is_valid && !$company->companyDomains->contains("domain", $name->domain))
                {
                    CompanyDomain::create([
                        'company_id' => $company->id,
                        'domain'     => $name->domain
                    ]);
                }

            }
        }
    }
}
