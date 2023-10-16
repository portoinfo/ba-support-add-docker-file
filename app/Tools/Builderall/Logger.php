<?php

namespace App\Tools\Builderall;

use Exception;
use Illuminate\Support\Facades\Route;
use GuzzleHttp\Client;
use Throwable;

class Logger
{
    /**
     * Allowed report levels
     * debug   => system debug
     * info    => general information
     * warning => warning messages
     * error   => system errors
     */
    private const EXPECTED_LEVELS = ['debug', 'info', 'warning', 'error'];

    /**
     * @var string
     * Micro Service URI
     */
    private $uri = 'http://65.111.191.141:8080/api/logs';

    /**
     * @var array
     * Messages to not report
     */
    private $skip_report = [];

    /**
     * @var string
     * Report Level
     */
    private $level = 'info';

    /**
     * @var string
     * Report Message
     */
    private $message;

     /**
     * @var array
     * Report Payload
     */
    private $payload;

    /**
     * @var array
     * Report Tags
     */
    private $tags;

    /**
     * @var GuzzleHttp\Client
     * Instance of client
     */
    static $client;

    /**
     * @var JSON
     * Json body
     */
    static $json_body;

    public function __construct(){}

    /**
     * Sets logger level
     * @param string $level
     * @return Logger
     */
    public function level($level)
    {
        if (!in_array($level, self::EXPECTED_LEVELS))
        {
            throw new Exception("Unexpected log level: $level");
        }

        $this->level = $level;
        return $this;
    }

    /**
     * Sets logger message
     * @param string $message
     * @return Logger
     */
    public function message($message)
    {
        $this->message = $message;
        return $this;
    }

    /**
     * Sets logger payload
     * @param array $payload
     * @return Logger
     */
    public function payload($payload)
    {
        $this->payload = $payload;
        return $this;
    }

    /**
     * Sets logger tags
     * @return Logger
     */
    public function tags()
    {
        $tags = func_get_args();
        $defaults = $this->tagsDefaults();
        $this->tags = array_merge($defaults, $tags);
        return $this;
    }

    /**
     * Return the default application tags
     * @return array
     */
    public function tagsDefaults()
    {
        $current_route = Route::current();

        $controller = 'undefined-controller';
        $uri        = 'undefined-uri';

        if (!empty($current_route))
        {
            $controller    = isset($current_route->action['controller']) ? $current_route->action['controller'] : 'undefined-controller';
            $uri           = isset($current_route->uri) ? $current_route->uri : 'undefined-uri';
        }

        $tags =  [
            config('app.logger_key', 'support-system'),
            config('app.env'),
            $uri,
            $controller,
        ];

        return $tags;
    }

    /**
     * Sets logger to debug
     * @param string $message
     * @return Logger
     */
    public function debug($message)
    {
        $this->level('debug');
        $this->message($message);
        return $this;
    }

    /**
     * Sets logger to info
     * @param string $message
     * @return Logger
     */
    public function info($message)
    {
        $this->level('info');
        $this->message($message);
        return $this;
    }

    /**
     * Sets logger to warning
     * @param string $message
     * @return Logger
     */
    public function warning($message)
    {
        $this->level('warning');
        $this->message($message);
        return $this;
    }

    /**
     * Sets logger to error
     * @param string $message
     * @return Logger
     */
    public function error($message)
    {
        $this->level('error');
        $this->message($message);
        return $this;
    }

    /**
     * Validate instance before send
     * @throws Exception
     */
    private function validate()
    {
        if (empty($this->tags)) {
            $this->tags();
        }

        if (empty($this->message)) {
            throw new Exception('Message field is required');
        }
    }

    /**
     * Returns data to request
     * @return JSON
     */
    private function getRequestData()
    {
        $data = [
            'level'   => $this->level,
            'message' => $this->message,
            'payload' => $this->payload,
            'tags'    => $this->tags
        ];

        return json_encode($data);
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
     * Return Json Body
     * @return JSON
     */
    public function getJsonBody()
    {
        if (static::$json_body)
        {
            return static::$json_body;
        }

        $input = file_get_contents('php://input');
        static::$json_body = json_decode($input);
        return static::$json_body;
    }

    /**
     * @return array
     */
    public function getPayload()
    {
        return [
            'environment'  => config('app.env'),
            'session'      => !empty($_SESSION) ? $_SESSION : null,
            'route'        => [
                'defaults' => Route::current(),
                'domain'   => $_SERVER['SERVER_NAME'],
                'path'     => $_SERVER['REQUEST_URI']
            ],
            'remote_ip'    => $_SERVER['REMOTE_ADDR'],
            'params'       => $_GET,
            'body'         => $_POST,
            'json'         => $this->getJsonBody(),
        ];
    }

    /**
     * Send log to micro service
     */
    public function send()
    {
        try {

            if (in_array($this->message, $this->skip_report))
            {
                return null;
            }

            $this->validate();

            $request_data = [
                'verify'          => false,
                'allow_redirects' => false,
                'body'           => $this->getRequestData(),
                'headers'         => [
                    'Content-Type'     => 'application/json',
                ]
            ];

            $response = $this->getClient()->post($this->uri, $request_data);
            $data = (array)json_decode($response->getBody()->getContents());
            return $data;

        } catch(Exception $e) {
            // empty to avoid errors
        }
    }

    /**
     * Sends an exception report
     *
     * @param \Exception $e
     * @param array $data
     * @param array $tags
     * @param bool  $send_localhost default FALSE
     */
    public static function reportException(Throwable $e, $data = [], $tags = [], $send_localhost = false)
    {

        /**
         * Avoiding logs in localhost unless the user sets it manually.
         */
        if (!$send_localhost && config('app.env') == 'local')
        {
            return;
        }

        $logger = new self();

        $payload = array_merge($logger->getPayload(), ['file' => "{$e->getFile()}:{$e->getLine()}"], ['stackTrace' => $e->getTrace()], $data);

        $logger->error($e->getMessage())
            ->payload($payload)
            ->tags(...$tags)
            ->send();
    }

    /**
     * Sends an error report
     *
     * @param string $message
     * @param array  $data
     * @param array  $tags
     * @param bool   $send_localhost default FALSE
     */
    public static function reportError($message, $data = [], $tags = [], $send_localhost = false)
    {
        /**
         * Avoiding logs in localhost unless the user sets it manually.
         */
        if (!$send_localhost && config('app.env') == 'local')
        {
            return;
        }

        $logger = new self();

        $payload = array_merge($logger->getPayload(), $data);

        $logger->error($message)
            ->payload($payload)
            ->tags(...$tags)
            ->send();
    }

    /**
     * Sends a warning report
     *
     * @param string $message
     * @param array  $data
     * @param array  $tags
     * @param bool   $send_localhost default FALSE
     */
    public static function reportWarning($message, $data = [], $tags = [], $send_localhost = false)
    {
        /**
         * Avoiding logs in localhost unless the user sets it manually.
         */

         if (!$send_localhost && config('app.env') == 'local')
        {
            return;
        }

        $logger = new self();

        $payload = array_merge($logger->getPayload(), $data);

        $logger->warning($message)
            ->payload($payload)
            ->tags(...$tags)
            ->send();
    }

    /**
     * Sends an info report
     *
     * @param string $message
     * @param array  $data
     * @param array  $tags
     * @param bool   $send_localhost default FALSE
     */
    public static function reportInfo($message, $data = [], $tags = [], $send_localhost = false)
    {

        /**
         * Avoiding logs in localhost unless the user sets it manually.
         */
        if (!$send_localhost && config('app.env') == 'local')
        {
            return;
        }

        $logger = new self();

        $payload = array_merge($logger->getPayload(), $data);

        $logger->info($message)
            ->payload($payload)
            ->tags(...$tags)
            ->send();
    }

    /**
     * Sends a debug report
     *
     * @param string $message
     * @param array  $data
     * @param array  $tags
     * @param bool   $send_localhost default FALSE
     */
    public static function reportDebug($message, $data = [], $tags = [], $send_localhost = false)
    {
        /**
         * Avoiding logs in localhost unless the user sets it manually.
         */
        if (!$send_localhost && config('app.env') == 'local')
        {
            return;
        }

        $logger = new self();

        $payload = array_merge($logger->getPayload(), $data);

        $logger->debug($message)
            ->payload($payload)
            ->tags(...$tags)
            ->send();
    }
}
