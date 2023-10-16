<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TicketComments extends Model
{

    public $timestamps = false;

    protected $table = 'ticket_comments';

    protected $guarded = [];
}
