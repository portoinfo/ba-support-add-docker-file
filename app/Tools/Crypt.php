<?php

namespace App\Tools;

/**
 * método para encriptar e descriptar strings
 * O initialization vector(IV) tem que ser o mesmo quando encripta e quando decripta
 * 
 * @param string $string: string para encriptar e descriptar
 *
 * @return string
 */
class Crypt {
    static function encrypt($string){
        $output = false;
        $encrypt_method = "AES-256-CBC";

        $secret_key = config('app.env') != 'production' ? 'Minha key secreta aqui' : 'da6187a82864829dafa318d1f9fc41d0016d079a638203bbd5f00630741b856e';
        $secret_iv  = config('app.env') != 'production' ? 'Minha key secreta aqui' : '6a743687bd430ac18f7d57936f1bb2bf310919fcee55ad8e2e9e0b43eeed6ff1';

        // hash
        $key = hash('sha256', $secret_key);

        // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
        $iv = substr(hash('sha256', $secret_iv), 0, 16);
        $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
        $output = base64_encode($output);
        return $output;
  
    } 

    static function decrypt($string){
        $output = false;
        $encrypt_method = "AES-256-CBC";

        $secret_key = config('app.env') != 'production' ? 'Minha key secreta aqui' : 'da6187a82864829dafa318d1f9fc41d0016d079a638203bbd5f00630741b856e';
        $secret_iv  = config('app.env') != 'production' ? 'Minha key secreta aqui' : '6a743687bd430ac18f7d57936f1bb2bf310919fcee55ad8e2e9e0b43eeed6ff1';
        // hash

        $key = hash('sha256', $secret_key);
        // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
        $iv = substr(hash('sha256', $secret_iv), 0, 16);
        $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
        return $output;
    }
}