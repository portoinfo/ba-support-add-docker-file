<?php

namespace App;

use App\Events\MessageSent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class ChatHistory extends Model
{
    protected $table = 'chat_history';

    protected $fillable = [
        'chat_id', 'company_user_company_department_id', 'type', 'content', 'content_translated','hidden_for_client',  'created_at', 'created_by'
    ];
    protected $hidden = [
        'updated_at', 'deleted_at', 'created_by', 'updated_by', 'deleted_at'
    ];

    public function chat(){
        return $this->belongsTo(Chat::class);
    }

    public static function createEvent($chatId, $content, $cucd_id, $user_auth_id) {

        $create = ChatHistory::create([
            'chat_id'                               => $chatId,
            'type'                                  => 'EVENT',
            'content'                               => $content,
            'company_user_company_department_id'    => $cucd_id,
            'created_by'                            => $user_auth_id,
        ]);

        if ($create) {
            $user = json_decode(json_encode(Auth::user()), true);
            $msg = json_decode(json_encode($create), true);
            $result =  array_merge($msg, $user);
			broadcast(new MessageSent($result));

            return $create;
        } else {
            return false;
        }
    }

}
