<?php

namespace App\Tools;

use App\Ticket;
use Illuminate\Support\Facades\DB;
use App\Tools\Crypt;
use Exception;

/**
 * Bloquear Clientes
 */
class ChatWorkingTimes{

    public static function Insert($chat_id, $cucd_id, $chat_history_id, $ticket_id = null)
    {
        $is_check_register = DB::table('chat_working_times')
        ->where('chat_id', $chat_id)
        ->whereNull('final_date')->first();

        if($is_check_register == null){
            return DB::table('chat_working_times')->insertGetId([
                'chat_id' => $chat_id,
                'ticket_id' => $ticket_id,
                'company_user_company_department_id' => $cucd_id,
                'initial_date' => \Carbon\Carbon::now()->toDateTimeString(),
                'chat_history_id' => $chat_history_id,
            ]);
        }
    }

    public static function Update($chat_id)
    {
        return DB::table('chat_working_times')
            ->where('chat_id', $chat_id)
            ->whereNull('final_date')
            ->update([
                'final_date' => \Carbon\Carbon::now()->toDateTimeString(),
        ]);
    }

}
