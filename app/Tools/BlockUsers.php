<?php

namespace App\Tools;

use App\Ticket;
use Illuminate\Support\Facades\DB;
use App\Tools\Crypt;
use Exception;

/**
 * Bloquear Clientes
 */
class BlockUsers{
    
    /**
	 * @param id $user_auth_id
	 * @param string $reason
	 */
    public static function blockClient($user_auth_id, $reason)
    {
        if(is_null($user_auth_id)){
            throw new Exception("User not found.");
        }
        
        $id = Crypt::decrypt($user_auth_id);
        
        if(!$id){
            $id = $user_auth_id;
        }

        return DB::table('blacklist_user')->insert([
            'blocked_id' => $id,
            'type' => 'CLIENT',
            'reason' => $reason,
            'created_by' => auth()->user()->id,
        ]);
    }
    
    public static function unlockClient($user_auth_id)
    {
        if(is_null($user_auth_id)){
            throw new Exception("User not found.");
        }
           
        $id = Crypt::decrypt($user_auth_id);

        if(!$id){
            $id = $user_auth_id;
        }

        return DB::table('blacklist_user')
        ->where('blocked_id', $id)
        ->delete();
    }

}
