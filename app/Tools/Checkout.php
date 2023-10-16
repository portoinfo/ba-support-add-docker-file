<?php

namespace App\Tools;

use App\Chat;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class Checkout
{


    /**
     * @param string $chat_id
     */
    public static function chat_extra_data(int $chat_id)
    {
        // VARIAVEIS
        // 'dtype'
        // 'cscode'
        // 'plan'
        // 'fee'

        //PEGAR VALORES
        $array = [
            "dtype" => session('dtype'),
            "cscode" => session('cscode'),
            "plan" => session('plan'),
            "fee" => session('fee'),
        ];

        //TRANFORMA EM JSON E SALVA
        $chat = Chat::where('id', $chat_id)->update([
            'extra_data'          => json_encode($array),
        ]);
    }

    /**
     * destroy sessions
     * return void
    */
    public static function chat_extra_data_finished()
    {
        session()->forget('dtype');
        session()->forget('cscode');
        session()->forget('plan');
        session()->forget('fee');
    }

    /**
     * get cscode
    */
    public static function parse_cscode($cscode)
    {

        $arrayName = array(
            'cscode' => strval($cscode),
        );

        $query ="SELECT * FROM (
                    SELECT chat.id AS chat_id, chat.status, company.id AS company_id, company.name AS company_name, user_auth.id AS user_auth_id, user_auth.name AS name_attendants, user_auth.email AS email_attendants, chat.created_at,
                    JSON_UNQUOTE(JSON_EXTRACT(extra_data, '$.cscode')) AS cscode, extra_data
                    FROM chat
                    LEFT JOIN company_user_company_department cucd ON chat.comp_user_comp_depart_id_current = cucd.id
                    LEFT JOIN company_user ON cucd.company_user_id = company_user.id
                    LEFT JOIN company ON company_user.company_id = company.id
                    LEFT JOIN user_auth ON company_user.user_auth_id = user_auth.id
                    WHERE chat.deleted_at IS NULL
                ) sub WHERE sub.cscode = :cscode";

        $result = DB::select($query, $arrayName);

        return $result;
    }
}
