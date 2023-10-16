<?php

namespace App\Tools\Traits;

trait DomainTrait {

    /**
     * Sanitize domain names
     * @param  string $domain
     * @return string
     */
    public function sanitizeDomain(string $domain)
    {
        $domain = strtolower($domain);
        $domain = str_replace('https://', '', $domain);
        $domain = str_replace('http://', '', $domain);
        $domain = str_replace('www.', '', $domain);
        $domain = explode('/', $domain)[0];

        return $domain;
    }
}