<?php

namespace App\Models;

use App\User_client;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserClientTicket extends Model
{

    use SoftDeletes;

    protected $table = 'user_client_ticket';

    protected $fillable = [
        'user_client_id', 'ticket_id',
    ];

    protected $hidden = [
        'updated_at', 'deleted_at', 'created_by', 'updated_by', 'deleted_at'
    ];


    public function userClient()
    {
        return $this->belongsTo(User_client::class, 'user_client_id', 'id');
    }

}
