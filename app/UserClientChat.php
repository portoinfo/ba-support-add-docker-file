<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserClientChat extends Model
{
    protected $table = 'user_client_chat';

    protected $fillable = [
        'user_client_id', 'chat_id', 'created_at'
    ];
    protected $hidden = [
        'updated_at', 'deleted_at', 'created_by', 'updated_by', 'deleted_at'
    ];

    /*
    public function chat()
    {
        return $this->belongsTo(Chat::class);
    }
    */
}
