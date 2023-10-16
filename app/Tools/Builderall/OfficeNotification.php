<?php

namespace App\Tools\Builderall;

use Exception;
use Nats\Connection;
use Nats\ConnectionOptions;

class OfficeNotification
{ 
    private static $instance;
    
    /**
     * Get instace for singleton
     */
    private static function getInstance()
    {
        if (empty(self::$instance))
        {
            $options = new ConnectionOptions(self::getConnectionOptions());
            self::$instance = new Connection($options);
            self::$instance->connect();
        }

        return self::$instance;
    }

    /**
     * Get onnection options
     */
    private static function getConnectionOptions()
    {
        $configs = config('nats.builderall');
        return $configs[config('app.env')];
    }

    /**
     * Send a notification to office user
     * @param string $to
     * @param string $message
     * @param string $description
     * @param string $action
     * @param string $image
     * @param string $category
     */
    public static function send(string $to, string $message, string $description=null, string $action=null, string $image=null, string $category=null)
    {
        try {

            if(strpos($to, '@') !== false) {
                $to = md5($to); 
            }

            $notification = array_filter([
                'owner'       => $to,
                'message'     => $message,
                'description' => $description,
                'action'      => $action,
                'image'       => $image,
                'category'    => $category,
                'from'        => 'support_system'
            ]);

            self::getInstance()->publish('notification.add', json_encode($notification));
        
        } catch (Exception $e) {
            Logger::reportException($e, [], ['office-notification', 'integration'], false);
        }
    }

    /**
     * List all notification fot a channel
     * @param string|array $channels
     * @return array 
     */
    public static function listAll($channels) {
    
        try{
            $list = null;

            self::getInstance()->request('notification.list', json_encode($channels), function ($response) use(&$list) {
                if (empty($response) || empty($response->body))
                {
                    throw new Exception('could not list notification');
                } 

                $data = json_decode($response->body, true);
                
                $list = empty($data) ? [] : $data;
            });

            return $list;

        }catch(Exception $e){
            Logger::reportException($e, [], ['office-notification', 'integration'], false);
            return [];
        }
    }
}