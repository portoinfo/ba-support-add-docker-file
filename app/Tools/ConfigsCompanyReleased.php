<?php

namespace App\Tools;

/**
 * @return $company_id
 */
class ConfigsCompanyReleased {

    const BUILDERALL_BRASIL = 1;
    const BUILDERALL_USA = 15;

    public static function get(){
        //COMPANHIAS DE PRODUÇÃO
        $array = [];

        if(!config('app')['is_helpdesk']){
            array_push( $array , Crypt::encrypt(self::BUILDERALL_BRASIL));
            array_push( $array , Crypt::encrypt(self::BUILDERALL_USA));
        }

        //COMPANHIAS DE TESTE
        if(env('APP_ENV') == 'local'){
            array_push( $array , Crypt::encrypt(11));  // Coca Cola ORIGINAL (TESTES)
        }

        return $array;
    }

    /**
     * @return Boolean is helpdesk
     */
    public static function is_helpdesk(){
        if(config('app')['is_helpdesk']){
            return 'helpdesk';
        }else{
            return 'builderall';
        }
    }



}
