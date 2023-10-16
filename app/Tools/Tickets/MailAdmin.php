<?php

namespace App\Tools\Tickets;

use App\Models\Company;
use App\Models\Company_department;
use App\Models\Company_user;
use App\Tools\Builderall\MailCentral;
use App\Tools\Client;
use App\Tools\Crypt;
use App\User;
use Illuminate\Support\Facades\DB;

class MailAdmin{

    private const TICKET_RECEIVED       = 'support-ticket-received';
    private const TICKET_REPLIED        = 'support-ticket-replied';
    private const TICKET_CLOSED         = 'support-ticket-closed';
    private const TICKET_CHAT_OPENED    = 'support-chat-ticket-opened';

    private $client;
    private $company;
    private $department;
    private $mail_to;
    private $allowed_to_send;

    public function __construct(int $company_id, int $client_id, int $department_id)
    {
        $this->client      = User::find($client_id);
        $this->company     = Company::find($company_id);
        $this->department  = Company_department::find($department_id);
        $this->mail_to     = Client::getCleanEmail($this->client->email, $company_id);

        if ($this->department)
        {
            $this->allowed_to_send = config('app.env') != 'local' && $this->department->ticketNotificationsSettings('email');
        }
    }

    /**
     * Send ticket received message : Recebido
     */
    public function ticketReceived()
    {
        if (!$this->allowed_to_send)
        {
            return;
        }

        $content = [
            'name'    => $this->client->name,
            'company' => $this->company->name,
        ];

        MailCentral::sendMessage(self::TICKET_RECEIVED, $this->mail_to, $this->client->language, $content, 0, $this->company->name);
    }

    /**
     * Send ticket repplied message : Respondido
     */
    public function ticketReplied()
    {

        if (!$this->allowed_to_send)
        {
            return;
        }

        $content = [
            'name'    => $this->client->name,
            'company' => $this->company->name,
            'url'     => route('client-access-link', ['company' => $this->company->hash_code, 'user' => $this->client->hash_code, 'type' => 'ticket']),
        ];

        MailCentral::sendMessage(self::TICKET_REPLIED, $this->mail_to, $this->client->language, $content, 0, $this->company->name);
    }
    
    /**
     * Send ticket repplied message : Fechado
     */
    public function ticketClosed()
    {
        if (!$this->allowed_to_send)
        {
            return;
        }

        $content = [
            'name'    => $this->client->name,
            'company' => $this->company->name,
        ];

        MailCentral::sendMessage(self::TICKET_CLOSED, $this->mail_to, $this->client->language, $content, 0, $this->company->name);
    }

    /**
     * Send ticket opened message : Aberto
     */
    public function ticketchatOpened($type,$onlines)
    {
        $users = DB::table('company_user')
        ->join('user_auth', 'company_user.user_auth_id', 'user_auth.id')
        ->where('company_user.company_id', $this->company->id)
        ->where('company_user.config', '!=', '0')
        ->select('user_auth.id', 'user_auth.name', 'user_auth.email','company_user.user_auth_id', 'company_user.config', 'user_auth.language')
        ->get();

        foreach ($users  as $key) {

            // $key->email = 'marlos_gpi@live.com'; // GAMBIARRRA PRA EU RECEBER EMAIL

            if(!isset(json_decode($key->config)[3]) || !isset(json_decode($key->config)[2]) || !isset(json_decode($key->config)[1]) || json_decode($key->config) == 'new_ticket'){
                return;
            }
            $type_c_t = json_decode($key->config)[0];
            $type_f_o = json_decode($key->config)[1];
            $type_departments_id = json_decode($key->config)[2];
            $type_notify = json_decode($key->config)[3];

            foreach ($type_c_t  as $c_t) { 
                if($c_t == 'new_ticket' && $type == 'TICKET' || $c_t == 'new_chat' && $type == 'CHAT'){

                   if($type_f_o == 'forever'){ // VERIFICAR SE ELE MARCOU SEMPRE RECEBER 

                        foreach ($type_departments_id as $dep_id) {  // VERIFICAR SE ELE MARCOU O DEPARTAMENTO 
                            if(Crypt::decrypt($dep_id) == $this->department->id){

                                foreach ($type_notify as $tn) { // VERIFICAR SE ELE MARCOU RECEBER EMAIL
                                   if($tn == 'noty_email'){
                                        $content = [
                                            'name'    => $key->name,
                                            'company' => $this->company->name,
                                            'department' => $this->department->name,
                                            'type' => $type,
                                        ];
                                
                                        MailCentral::sendMessage(self::TICKET_CHAT_OPENED, $key->email, $key->language, $content, 0, $this->company->name);
                                   }
                                }
                            }
                        }
                    }else if($type_f_o == 'online'){ // VERIFICAR SE ELE MARCOU SÓ RECEBER ONLINE
                        
                        foreach ($onlines  as $online) { 
                            if($online['id'] == $key->id){
                            
                                foreach ($type_departments_id as $dep_id) {  // VERIFICAR SE ELE MARCOU O DEPARTAMENTO 
                                    if(Crypt::decrypt($dep_id) == $this->department->id){
        
                                        foreach ($type_notify as $tn) { // VERIFICAR SE ELE MARCOU RECEBER EMAIL
                                        if($tn == 'noty_email'){
                                                $content = [
                                                    'name'    => $key->name,
                                                    'company' => $this->company->name,
                                                    'department' => $this->department->name,
                                                    'type' => $type,
                                                ];
                                        
                                                MailCentral::sendMessage(self::TICKET_CHAT_OPENED, $key->email, $key->language, $content, 0, $this->company->name);
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }

}