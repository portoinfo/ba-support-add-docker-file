<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LogBuilderallStatus extends Model
{
    protected $table = 'log_builderall_status';
    
    public $timestamps = false;

    protected $fillable = [
        'user_auth_id',
        'old_status',
        'new_status',
        'origin'
    ];
}
