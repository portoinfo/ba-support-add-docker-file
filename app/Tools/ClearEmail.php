<?php

namespace App\Tools;

class ClearEmail{

    /**
     * Clear email client
     */
    public static function clear(string $email)
    {
        // try{


            $concat = "";
            $pieces = explode("_", $email);

            if($pieces[0] == 'comp'){
                $pieces = array_splice($pieces, 2);
                for ($i = 0; $i < count($pieces); $i++) {
                    $concat .= $pieces[$i] . '_';
                }
            }else{
                return $email;
            }

            if(substr($concat, 0, -1) == null){
                return $email;
            }else{
                return $email = substr($concat, 0, -1);
            }


        // } catch (\Exception $e) {
        //     echo $e; 
		// 	return $email;
		// }
        
    }

    public function clearArray()
    {
        // foreach ($query as $key) {
        //     //$key->id = Crypt::encrypt($key->id);

        //     $concat = "";
        //     $pieces = explode("_", $key->email);
        //     $pieces = array_splice($pieces, 2);

        //     for ($i = 0; $i < count($pieces); $i++) {
        //         $concat .= $pieces[$i] . '_';
        //     }
        //     $key->email = substr($concat, 0, -1);

        //     if ($key->email == request('email')) {
        //         $tickets['value'] = $key;
        //         $tickets['success'] = true;
        //     }
        // }
        return 'eae';
    }
}