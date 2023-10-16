<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TicketChatAnswer extends Model
{
    protected $table = 'ticket_chat_answer';

    protected $fillable = [
        'company_depart_settings_question_id',	'ticket_id', 'chat_id', 'answer'
    ];
    protected $hidden = [
        'created_at', 'updated_at', 'deleted_at', 'created_by', 'updated_by', 'deleted_by'
    ];
}
