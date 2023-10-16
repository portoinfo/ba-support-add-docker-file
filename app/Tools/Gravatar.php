<?php

namespace App\Tools;

use Exception;

class Gravatar
{

    public function __construct(){ }

    /**
     * Get user gravatar
     * @param  string $email
     * @return string
     */
    public static function getGravatar($email, $size = 60){

        $email = Client::forceCleanEmail($email);

        $hash = md5($email);

        $params = http_build_query([
            's' => $size,
            'd' => 404,
            'r' => 'g'
        ]);

        $image = '//secure.gravatar.com/avatar/'.$hash.'?'.$params;

        return $image;
    }

}
