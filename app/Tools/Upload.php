<?php

namespace App\Tools;

class Upload {
    static function replaceImagesInContent($content, $images, $company_id, $chat_id) {
        foreach ($images as $row) {
            // Define the Base64 value you need to save as an image
            $b64 = explode(',', $row)[1];
            
            $image_name = Crypt::encrypt(uniqid(md5(uniqid() . microtime())));
            $data = base64_decode($b64);
            $dir = '..' . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'company' . DIRECTORY_SEPARATOR . $company_id . DIRECTORY_SEPARATOR . 'chat' . DIRECTORY_SEPARATOR . $chat_id . DIRECTORY_SEPARATOR;
            $filename = $image_name . '.png';

            // Caso já exista o diretório, apenas movo o arquivo pra dentro dele, se não, crio a pasta e movo o arquivo em seguida...
            if (is_dir($dir)) {
                $success = file_put_contents($dir.$filename, $data);
            } else {
                mkdir($dir, 0755, true);
                $success = file_put_contents($dir.$filename, $data);
            }

            if ($success) {
                $content = str_replace($row, 'chat/files/'. Crypt::encrypt($chat_id) .'/'.$filename, $content);
            }

        }

        return $content;
    }   

    static function ticketFiles($content, $files, $company_id, $chat_id) {
        $array_files = [];
        foreach ($files["files"]["name"] as $i => $file) {
            // Quebro o nome completo do arquivo em várias posições e crio duas váriaveis: uma armazena o nome original e outra a extensão do arquivo...
            $explode = explode('.', $files['files']['name'][$i]);
            $original_name = str_replace('.' . end($explode), "", $files['files']['name'][$i]);
            $extension = '.' . strtolower(end($explode));
            // Indico o diretorio para onde o arquivo deve ser enviado...
            $dir = '..' . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'company' . DIRECTORY_SEPARATOR . $company_id . DIRECTORY_SEPARATOR . 'chat' . DIRECTORY_SEPARATOR . $chat_id . DIRECTORY_SEPARATOR;
            // Gero um nome único para o arquivo antes de move-lo para a pasta...
            $unique_name = Crypt::encrypt(uniqid(md5($original_name . microtime()))) . $extension;
            $new_name = $dir . $unique_name;

            // Caso já exista o diretório, apenas movo o arquivo pra dentro dele, se não, crio a pasta e movo o arquivo em seguida...
            if (is_dir($dir)) {
                move_uploaded_file($files['files']['tmp_name'][$i], $new_name);
            } else {
                mkdir($dir, 0755, true);
                move_uploaded_file($files['files']['tmp_name'][$i], $new_name);
            }

            $array_files[$i] = [
                'unique_name' => $unique_name,
                'original_name' => $original_name . $extension,
            ];
        }

        return json_encode([
            'message' => $content,
            'files' => $array_files,
        ]);
    }
}