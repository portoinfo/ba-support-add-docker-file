<?php

namespace App\Tools\Builderall;

use Exception;
use GuzzleHttp\Client;

class Office {

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
        $this->base_uri = config('url.office', 'https://office.builderall.com');        
    }

    /**
     * Returns user data
     * @param string uuid
     * @return array
     */
    public function getUserData($uuid)
    {
        $data = [
            'verify'          => false,
            'allow_redirects' => false,
        ];

        $uri = $this->base_uri . "/us/office/get-data-uuid/$uuid?".http_build_query(['ct' => 'ba_helpdesk']);
        $response = $this->getClient()->get($uri, $data);
        $response_data = json_decode($response->getBody()->getContents(), true);

        $user_data = [];
        if ($response_data['success'])
        {
            $user_data = (array)$response_data['data'];
        }

        return $user_data;
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