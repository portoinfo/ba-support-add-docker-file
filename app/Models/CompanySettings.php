<?php

namespace App\Models;

use App\Tools\Traits\DomainTrait;
use Illuminate\Database\Eloquent\Model;

class CompanySettings extends Model
{

    use DomainTrait;

    protected $table = 'company_settings';


    protected $casts = [
        'released_domain' => 'array',
        'blocked_domain'  => 'array',
        'general'         => 'array'
    ];

    /**
     * Returns array of realesed domains sanitized
     * @return array
     */
    public function getRealesedDomainsArray()
    {
        $domains = [];
        foreach ($this->released_domain as $domain) {
            array_push($domains, $this->sanitizeDomain($domain['link']));
        }

        return $domains;
    }

    /**
     * Returns array of blocked domains sanitized
     * @return array
     */
    public function getBlockedDomainsArray()
    {
        $domains = [];
        foreach ($this->blocked_domain as $domain) {
            array_push($domains, $this->sanitizeDomain($domain['link']));
        }

        return $domains;
    }

    /**
     * Returns logout registered URL
     * @return mixed
     */
    public function getClientLogoutUrl()
    {
        $logout_url = null;

        if (isset($this->general['client_logout']) && $this->general['client_logout']['active']) {
            $logout_url = $this->general['client_logout']['link'] ? $this->general['client_logout']['link'] : null;
        }

        return $logout_url;
    }

    /**
     * Return default general dara
     * @return array
     */
    public static function getDefaultGeneral($base_url, $hash_code)
    {
        if ($base_url == null && $hash_code == null) {
            return  [
                'client_logout' => [
                    'link' => null,
                    'active' => null,
                ],
                'showChat' => false,
                'showTicket' => false,
                'showAdmin' => false,
                'editPerfilClient' => true,
                'editPerfilAttendants' => true,
                'acesso_anonymous' => false,
                'nameRobot' => null,
            ];
        } else {
            return  [
                'client_logout' => [
                    'link' => $base_url . "/client/" . $hash_code . "/login",
                    'active' => true,
                ],
                'showChat' => false,
                'showTicket' => false,
                'showAdmin' => false,
                'editPerfilClient' => true,
                'editPerfilAttendants' => true,
                'acesso_anonymous' => true,
                'nameRobot' => null,
            ];
        }
    }
}
