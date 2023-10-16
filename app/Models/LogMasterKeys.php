<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogMasterKeys extends Model
{
    public $timestamps = false;
    
    protected $fillable = ['master_keys_id', 'user_auth_id', 'action', 'action_data', 'created_at'];
}
