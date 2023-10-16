<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserAuth extends Model
{
    use SoftDeletes;

    protected $table = 'user_auth';

    protected $fillable = [
        'name', 'email', 'subsidiary_id', 'password', 'updated_at', 'deleted_at', 'cookies_accepted',
    ];

    public static function getDefaultConfig() {
        return (object)[
            'fontSize'      => '16px',
            'signature'     => '',
            'notification'  => (object)[
                'email'     => 1,
                'system'    => 1,
                'telegram'  => 0,
            ],
        ];
    }

    /*
    protected $hidden = [
       'subsidiary_id', 'name', 'email', 'email_verified_at', 'phone', 'language', 'created_at', 'updated_at', 'deleted_at', 'created_by', 'updated_by', 'deleted_at'
    ];
    */
}
