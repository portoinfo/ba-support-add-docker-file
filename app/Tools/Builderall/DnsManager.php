<?php

namespace App\Tools\Builderall;

use Exception;
use GuzzleHttp\Client;
use App\Tools\Client as ClientHelper;

class DnsManager {

    /**
     * @var GuzzleHttp\Client
     * Instance of client
     */
    static $client;

    /**
     * @var string $uri
     * Endpoint URI
     */
    private $base_uri;

    public function __construct()
    {
        $this->base_uri = config('url.dns_manager', 'https://dns.builderall.com');        
    }

    /**
     * Returns user connected domains
     * @param string email
     * @return array
     */
    public function getUserConnectedDomains($email)
    {

        $email = ClientHelper::forceCleanEmail($email);

        $uri = $this->base_uri . "/api/domain/user-domains/$email";
        
        $options = [
            'verify'          => false,
            'allow_redirects' => false,
        ];

        $response = $this->getClient()->get($uri, $options);
        $response_data = json_decode($response->getBody()->getContents(), true);
        $data = $response_data['success'] ? (array)$response_data['data']['connected'] : [];
        return collect($data)->pluck('domain');
    }

    /**
     * Returns client
     * @return GuzzleHttp\Client
     */
    private function getClient()
    {
        if (static::$client){
            return static::$client;
        }

        static::$client = new Client();

        return static::$client;
    }
}