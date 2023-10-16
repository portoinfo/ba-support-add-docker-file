<?php

namespace App\Tools\telegram;

use App\Models\Company_user;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Notification;
use App\Models\telegramUser;
use App\User;
use Illuminate\Support\Facades\DB;
use App\telegramMessage;
use Illuminate\Support\Facades\Http;
use App\Notifications\notifyTelegramBot;
use App\Notifications\TelegramNotification;
use App\Tools\Builderall\Logger;
use App\Tools\Crypt;
use Illuminate\Support\Facades\Lang;

class TelegramBot 
{
    protected $texto = [
        '0' => 'Bem vindo a BuilderAllHelpDesk',
        '1' => 'Se Deseja Cadastrar para receber notificações do sistema envie seu email vinculado ao seu usuário no sistema',
        '2' => 'Seu Telegram já está cadastrado em nosso sistema, se deseja trocar o email  digite "trocar" sem as aspas duplas. 
                Obrigado Pelo Contato!',
        '3' => 'Cadastro Realizado Com Sucesso!',
        '4' => 'O Sistema não possui cadastro com Email Informado',
        '5' => 'Informe o Novo Email:'
    ];

    public function getMensagensTelegram()
    {
        // Notification::route('telegram',2091826433) // João
        //     ->notify(new NotifyTelegramBot('DORIME DORIME DORIME '));

        // Notification::route('telegram', 2068744014) // Marlos
        // ->notify(new NotifyTelegramBot('PING: ' .date('H:i:s')));

        $mensagens = telegramMessage::get();
    
        if($mensagens == '[]'){
            $position = 0;
        }else{
            $position = $mensagens[0]->position_message;
        }
        
        if(config('app.env') == 'sandbox'){
            $response = Http::get('https://api.telegram.org/bot2024508259:AAGw0W_MiLSjYmBZZA26VBQD09HsRr91TrY/getUpdates?offset='.$position); // IO
        }else if(config('app.is_helpdesk')){
            $response = Http::get('https://api.telegram.org/bot2102893791:AAGM96ewnrQ1qm6iCQLnkQGqyxL8WdV2f18/getUpdates?offset='.$position); //.HS 
        }else{
            $response = Http::get('https://api.telegram.org/bot2056592771:AAFblkt_S4KeM79b2U8og_XC5TNvUJwS4kY/getUpdates?offset='.$position); //.COM
        }

        $retornos = (string) $response->getBody();
        $retornos = json_decode($retornos)->result;

        if($retornos == []){
        }else{
            foreach($retornos as $retorno){

                //RESPONDO COM OS COMANDOS ---------------------------------------------------------------------------------------------
                $this->commands($retorno);
                //RESPONDO COM OS COMANDOS ---------------------------------------------------------------------------------------------

                // Deletando Mensagem Anteriores
                DB::table('telegram_message')
                    ->where('position_message' , '<=' , $retorno->update_id )->delete();

                $ultime_mensagem_respondida = $retorno->update_id + 1;
            }

            telegramMessage::create([
                'position_message' => $ultime_mensagem_respondida,
            ]);
        }
        
    }

    public function commands($msgs)
    {
        try{
          
            if($msgs->message->text == '/start' || $msgs->message->text == '/check'){
                $company_user = Company_user::join('user_auth', 'company_user.user_auth_id', 'user_auth.id')
                ->where('company_user.telegram_chat_id', $msgs->message->chat->id)
                ->select('user_auth.email', 'company_user.id as company_user_id', 'user_auth.language')
                ->first();

                if($company_user == null){
                    Notification::route('telegram', $msgs->message->chat->id)
                    ->notify(new NotifyTelegramBot(Lang::get("app.bs-welcome-to", [], 'pt_BR') . ' Builderall HelpDesk'));
                    Notification::route('telegram', $msgs->message->chat->id)
                    ->notify(new NotifyTelegramBot(Lang::get("app.bs-if-you-want-to-register-to-receive-notific", [], 'pt_BR')));
                }else{
                    Notification::route('telegram', $msgs->message->chat->id)
                    ->notify(new NotifyTelegramBot(Lang::get("app.bs-email-already-linked-wait-for-notification", [], $company_user->language)));
                }
            }else{
                // VERIFICAR SE O EMAIL E SE JÁ ESTA VINCULADO
                if (filter_var($msgs->message->text, FILTER_VALIDATE_EMAIL)) {

                    $company_user = Company_user::join('user_auth', 'company_user.user_auth_id', 'user_auth.id')
                    ->where('company_user.telegram_chat_id', $msgs->message->chat->id)
                    ->select('user_auth.email', 'company_user.id as company_user_id')
                    ->first();
                
                    if($company_user != null){
                        return Notification::route('telegram', $msgs->message->chat->id)
                        ->notify(new NotifyTelegramBot(Lang::get("app.bs-email-already-linked-wait-for-notification", [], 'pt_BR')));
                    }else{
                        //REDIRECIONAR ELE PARA A ROTA DE LOGIN COM OS PARAMETROS - EMAIL/CHAT_ID
                        Notification::route('telegram', $msgs->message)
                        ->notify(new TelegramNotification());
                    }
                }else{
                    $company_user = Company_user::join('user_auth', 'company_user.user_auth_id', 'user_auth.id')
                    ->where('company_user.telegram_chat_id', $msgs->message->chat->id)
                    ->select('user_auth.email', 'company_user.id as company_user_id', 'language')
                    ->first();

                    if($company_user != null){
                        Notification::route('telegram', $msgs->message->chat->id)
                        ->notify(new NotifyTelegramBot(Lang::get("app.bs-email-already-linked-wait-for-notification", [], $company_user->language)));
                    }
                }
            }
        } catch (\Exception $e) {
            echo $e->getMessage();
            Logger::reportException($e, [], ['Homercontroller', 'commands'], false);
            $change['success'] = false;
        }
    }

     /**
     * Notification All users
     *
     * @param string $session
     * @param string $chat_or_ticket
     * @param string $create_or_message
     * @param string $id
     * @param string $description
     * @return void
     */
    public function notificationAllUsers($session, $ct, $type, $id, $department, $description, $onlineUsers = null)
    {
        try {
            $company_department = DB::table('company_department')
            ->where('id', Crypt::decrypt($department))
            ->select('name')
            ->first();

            $company_user = DB::table('company_user')
            ->join('user_auth', 'company_user.user_auth_id', 'user_auth.id')
            ->where('company_id', Crypt::decrypt($session['company_id']))
            ->where('telegram_chat_id', '!=', null)
            ->select('user_auth.id', 'telegram_chat_id', 'config')
            ->get();
            
            foreach($company_user as $cu){
                
                if($cu->config == null 
                || $cu->config == '0' 
                || $cu->config == '"new_ticket"' 
                || $cu->config == '"new_chat"' 
                || $cu->config == '"[[],null,[]]"' 
                || $cu->config == '"new_chat"' 
                || $cu->config == '[null,null,null]'){
                    $cu->config = [];
                    $departmentSelect = [];
                }else{
              
                    $config = json_decode($cu->config)[0];
                    $notytype = json_decode($cu->config)[1];
                    if(isset(json_decode($cu->config)[2]) == true){
                        $departmentSelect = json_decode($cu->config)[2];
                    }else{
                        $departmentSelect = [];
                    }

                    if(isset(json_decode($cu->config)[3][1])){
                        if(json_decode($cu->config)[3][0] == 'noty_telegram' || json_decode($cu->config)[3][1] == 'noty_telegram'){
                            $noty = 'noty_telegram';
                        }else{
                            $noty = '';
                        }
                    }else{
                        if(json_decode($cu->config)[3][0] == 'noty_telegram'){
                            $noty = 'noty_telegram';
                        }else{
                            $noty = '';
                        }
                    }
                    
                }
                
                foreach($departmentSelect as $depart){
                    if($depart == $department){
                        
                        foreach($config as $one){
                            if($notytype == 'forever'){
                                if($ct == 'ticket' && $one == 'new_ticket' && $type == 'create' && $noty == 'noty_telegram'){
                                    return Notification::route('telegram', $cu->telegram_chat_id)
                                    ->notify(new NotifyTelegramBot(
                                        Lang::get("app.bs-department", [], auth()->user()->language).': '.
                                        Lang::get($company_department->name, [], auth()->user()->language)." \n".
                                        Lang::get("app.bs-ticket", [], auth()->user()->language).': '.$id. "\n".
                                        Lang::get("app.bs-ticket-created-by", [], auth()->user()->language).': '.auth()->user()->name." \n".
                                        Lang::get("app.bs-description", [], auth()->user()->language).': '.strip_tags($description)
                                    ));
                                }
            
                                if($ct == 'chat' && $one == 'new_chat' && $type == 'create' && $noty == 'noty_telegram'){
                                    return Notification::route('telegram', $cu->telegram_chat_id)
                                    ->notify(new NotifyTelegramBot(
                                        Lang::get("app.bs-department", [], auth()->user()->language).': '.
                                        Lang::get($company_department->name, [], auth()->user()->language)." \n".
                                        Lang::get("app.bs-chat", [], auth()->user()->language).': '.$id. "\n".
                                        Lang::get("app.bs-chat-created-by", [], auth()->user()->language).': '.auth()->user()->name." \n".
                                        Lang::get("app.bs-description", [], auth()->user()->language).': '.strip_tags($description)
                                    ));
                                }
                            }
                        }
                    }
                }
            }

            // AQUI REMOVE TODOS OS CARAS ONLINE DA LISTA.
            $count = 0;
            foreach($company_user as $cu){
                foreach($onlineUsers as $key){
                    if($cu->id == $key['id']){
                        unset($company_user[$count]);
                    }
                }
                $count++;
            }

            foreach($company_user as $cu){
                
                if($cu->config == null || $cu->config == '0'){
                    $cu->config = '[]';
                }else{
                    $config = json_decode($cu->config)[0];
                    $notytype = json_decode($cu->config)[1];
                    if(isset(json_decode($cu->config)[2]) == true){
                        $departmentSelect = json_decode($cu->config)[2];
                    }else{
                        $departmentSelect = [];
                    }
                }

                foreach($departmentSelect as $depart){
                    if($depart == $department){
                        foreach($config as $one){
                            if($notytype == 'online'){
                                if($ct == 'ticket' && $one == 'new_ticket' && $type == 'create' && $noty == 'noty_telegram'){
                                    return Notification::route('telegram', $cu->telegram_chat_id)
                                    ->notify(new NotifyTelegramBot(
                                        Lang::get("app.bs-department", [], auth()->user()->language).': '.
                                        Lang::get("app.".$company_department->name, [], auth()->user()->language)." \n".
                                        Lang::get("app.bs-ticket", [], auth()->user()->language).': '.$id. "\n".
                                        Lang::get("app.bs-ticket-created-by", [], auth()->user()->language).': '.auth()->user()->name." \n".
                                        Lang::get("app.bs-description", [], auth()->user()->language).': '.strip_tags($description)
                                    ));
                                }
            
                                if($ct == 'chat' && $one == 'new_chat' && $type == 'create' && $noty == 'noty_telegram'){
                                    return Notification::route('telegram', $cu->telegram_chat_id)
                                    ->notify(new NotifyTelegramBot(
                                        Lang::get("app.bs-department", [], auth()->user()->language).': '.
                                        Lang::get("app.".$company_department->name, [], auth()->user()->language)." \n".
                                        Lang::get("app.bs-chat", [], auth()->user()->language).': '.$id. "\n".
                                        Lang::get("app.bs-chat-created-by", [], auth()->user()->language).': '.auth()->user()->name." \n".
                                        Lang::get("app.bs-description", [], auth()->user()->language).': '.strip_tags($description)
                                    ));
                                }
                            }
                        }
                    }
                }
            }     

            // if($ct == 'ticket' && $one == 'ticket_answered' && $type == 'message'){
            //     return Notification::route('telegram', $cu->telegram_chat_id)
            //     ->notify(new NotifyTelegramBot(Lang::get("Ticket $id Respondido: \n $description", [], 'pt_BR')));
            // }

            // if($ct == 'chat' && $one == 'chat_answered' && $type == 'message'){
            //     return Notification::route('telegram', $cu->telegram_chat_id)
            //     ->notify(new NotifyTelegramBot(Lang::get("Chat $id Respondido: \n $description", [], 'pt_BR')));
            // }

        } catch (\Exception $e) {
            // echo $e;
            Logger::reportException($e, [], ['TelegramBot', 'notificationAllUsers'], false);
            $change['success'] = false;
        }
    }







}