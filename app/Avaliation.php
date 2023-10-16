<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Avaliation extends Model
{
    protected $table = 'avaliation';

    protected $fillable = [
        'ticket_id', 'chat_id', 'stars_atendent', 'stars_service', 'comment', 'created_by',
    ];

    protected $hidden = [
        'created_at', 'updated_at', 'deleted_at', 'updated_by', 'deleted_at'
    ];
}
