<?php

namespace App\Tools\Builderall;

use Exception;
use GuzzleHttp\Client;

class MailCentral
{ 

    /**
     * Token used to integrate office
     */
    private const TOKEN = 'EB45FCF03B148957615B2D079B762AEFDE654893C360FBB25EF9372FBC076916';

    /**
     * @var string $uri
     * Endpoint URI
     */
    private $uri;
    
    /**
     * @var string $category
     * Slug of email category
     */
    private $category;
    
    /**
     * @var string $language
     * Language of message
     */
    private $language;

    /**
     * @var array $dinamic_content
     * Dinamic content of message
     */
    private $dinamic_content = [];

    /**
     * @var string $mail_to
     * Receiver email address
     */
    private $mail_to;

    /**
     * @var int $user_id
     * Office user id
     * WARNING: KEEP IT TO 0 IF YOU DON'T KNOW THE CORRECT ID
     */
    private $user_id = 0;

    /**
     * @var string $from_name
     * Receiver email address
     */
    private $from_name;

    /**
     * @var GuzzleHttp\Client
     * Instance of client
     */
    static $client;

    public function __construct()
    {
        $office = config('url.office');
        $this->uri = $office . '/us/office/emails-central/send-message';
        //$this->uri = $office['production'] . '/us/office/emails-central/send-message';
    }

    /**
     * Sets message category
     * @param string $category
     * @return MailCentral
     */
    public function setCategory(string $category)
    {
        $this->category = $category;
        return $this;
    }

    /**
     * Sets message language
     * @param string $language
     * @return MailCentral
     */
    public function setLanguage(string $language)
    {
        $this->language = $language;
        return $this;
    }

    /**
     * Sets message mail_to
     * @param string $mail_to
     * @return MailCentral
     */
    public function setMailTo(string $mail_to)
    {
        $this->mail_to = $mail_to;
        return $this;
    }

    /**
     * Sets message from_name
     * @param string $from_name
     * @return MailCentral
     */
    public function setFromName(string $from_name)
    {
        $this->from_name = $from_name;
        return $this;
    }


    /**
     * Sets message dinamic_content
     * @param array $dinamic_content
     * @return MailCentral
     */
    public function setDinamicContent(array $dinamic_content)
    {
        $this->dinamic_content = $dinamic_content;
        return $this;
    }

    /**
     * Sets message user_id
     * @param int $user_id
     * @return MailCentral
     */
    public function setUserId(int $user_id)
    {
        $this->user_id = $user_id;
        return $this;
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

    /**
     * Send a message
     * @return mixed
     */
    public function send()
    {
        try {
    
            $request_data = [
                'verify'          => false,
                'allow_redirects' => false,
                'form_params'     => [
                    'api_token'   => self::TOKEN,
                    'category'    => $this->category,
                    'to_email'    => $this->mail_to,
                    'from_name'   => $this->from_name,
                    'locale'      => $this->language,
                    'content'     => json_encode($this->dinamic_content),
                    'user_id'     => $this->user_id
                ],
            ];
            
            $response = $this->getClient()->post($this->uri, $request_data);
            $data = (array)json_decode($response->getBody()->getContents());
            return $data;
    
        } catch(Exception $e) {
            Logger::reportException($e, [], ['mail-central', 'integration'], false);
        }
    }

    /**
     * Send a message
     * @param  string $category
     * @param  string $mail_to
     * @param  string $language
     * @param  array  $dinamic_content
     * @param  int    $user_id
     * @return mixed
     */
    public static function sendMessage(string $category, string $mail_to, string $language, array $dinamic_content = [], int $user_id = 0, string $from_name = '')
    {
        $mail = new self();

        $response = $mail
            ->setCategory($category)
            ->setMailTo($mail_to)
            ->setFromName($from_name)
            ->setLanguage($language)
            ->setDinamicContent($dinamic_content)
            ->setUserId($user_id)
            ->send();

        return $response;
    }

}