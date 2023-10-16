<?php

namespace App\Tools;

use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
/**
 * Class created to handle system states
 */
class SystemState {


    /**
     * Handle features available to client
     * @param bool $ticket Hide tickets
     * @param bool $chat Hide chat
     *
     * @return void
     */
    public static function clientFeatures ($ticket = false, $chat = false) : void
    {
        session([
            'restriction_client' => [
                0 => (object) [
                    "ticket_hide" => $ticket,
                    "chat_hide"   => $chat,
                ],
            ]
        ]);
    }

    /**
     * Set cache values to api user
     * @param int $user_id
     * @param string $key
     * @param string $value
     *
     * @return void
     */
    public static function setCacheForApi ($user_id, $key, $value)
    {
        $cache_key = $user_id."-".$key;
        Cache::put($cache_key, $value, now()->addMinutes(1440));
    }

    /**
     * Get cached values of api user
     * @param int $user_id
     * @param string $key
     * @param mixed $default
     *
     * @return mixed
     */
    public static function getCacheForApi ($user_id, $key, $default = null)
    {
        $cache_key = $user_id."-".$key;
        return Cache::get($cache_key, $default);
    }

}

