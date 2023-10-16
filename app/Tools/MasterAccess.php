<?php

namespace App\Tools;

use App\Models\LogMasterKeys;
use App\Models\MasterKeys;
use Exception;
use Illuminate\Support\Facades\DB;

class MasterAccess
{ 
   private $key;
   private $row;
   public $is_valid;

   /**
    * @param string $key
    */
   public function __construct(string $key)
   {
        $this->key = $key;
        $this->row = MasterKeys::where('key', $this->key)->first();
        $this->is_valid = !empty($this->row);
   }

   /**
    * Save action log
    * @param int $user_auth_id
    * @param string $action
    * @param array $action_data
    */
   public function saveLog(int $user_auth_id, string $action, array $action_data = null)
   {
       if ($this->row)
       {
           $data = [
               'master_keys_id' => $this->row->id, 
               'user_auth_id'   => $user_auth_id, 
               'action'         => $action, 
               'action_data'    => !empty($action_data) ? json_encode($action_data) : null, 
            ];
            
            LogMasterKeys::create($data);
        }
   }

}