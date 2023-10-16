<?php

namespace App\Mail;

use App\Tools\ClearEmail;
use App\Tools\Crypt;
use App\Tools\Crypt\RC4;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use stdClass;

class sendEmailCustom extends Mailable
{
    use Queueable, SerializesModels;

    private $ticket_id;
    private $user;
    private $user_email;
    private $title;
    private $textHtml;
    private $name_sender;
    private $email_sender;
    private $hash_company;
    
    /**
     * Create a new message instance.
     *
     * @param $user : The default logged-in user.
     * @param $user_email : The email address that will receive this email.
     * @param $title : The title of the email.
     * @param $textHtml : The body of the email.
     * @param $name_sender: sender name
     * @param $name_email: sender's email
     * @param $email_to_send: email where it will be sent
     * @param $hash_company: company hash_code
     * @return void
     */
    public function __construct($ticket_id, $user, $user_email, $title, $textHtml, $name_sender, $email_sender, $hash_company)
    {
        $this->ticket_id = $ticket_id;
        $this->hash_company = $hash_company;
        $this->user = $user;
        $this->user_email = $user_email;
        $this->title = $title;
        $this->textHtml = $this->filter($textHtml);
        $this->name_sender = $name_sender;
        $this->email_sender = $email_sender;
    }

    public function filter($textHtml, $company_name = null, $type = null, $departments_name = null)
    {
        $nome = $this->user->name;
		$empresa = session('companyselected') == null ? $company_name : session('companyselected')['name'];
		$id = $this->ticket_id;

        $company = DB::table('company')
        ->where('hash_code', $this->hash_company)
        ->select('id')
        ->first();

        if($id == null){
            $src = '#';
        }else{
            $result = DB::table('user_auth')
            ->where('email', 'comp_'.$company->id.'_'.$this->user_email)
            ->select('hash_code')
            ->first();
            
            if($result == null){
                $src = '#';
            }else{
                $src = 'client/'.$result->hash_code.'/'.session('companyselected')['hash_code'].'/customer-ticket';
            }
        }
        
        if(config('app.env') == 'sandbox'){ // IO
            $url = 'ba-support.builderall.io/'.$src;
        }else if(config('app.is_helpdesk')){ //.HS
            $url = 'hs.builderall.com/'.$src;
        }else{ //.COM
            $url = 'ba-support.builderall.com/'.$src;
            // $url = 'localhost:8000/'.$src;
        }

        // /{user}/{company}/{type}
		// $data = RC4::decryptClientAccess(config('app.rc4_key'), 'WU1EkXsI53Acbck8GTP%2Fqr74spBna%2BFYsOAizlxYrZ1%2Fl6WEqU%2BWETjdF8192FmJFF1ghvIy41f2SEg7zHjx27VD3ixBkvb8jRV9xE%2Fmf%2FApPL2LMYk40sGI7tBplkfu%2B5PpGB50mX5QjSqUA6vgSgMjfKUlk1%2Fo25guqpmx%2BNHR4HMN34gj%2FHhGt0A4aQHQ%2B1frrVyK%2BTqQvp0XO0d0u3wcrEjvKDU9Ke9KfUpifzya2l01V0%2FoB8K7mcv1vt1Ch2hGD0qhieTm769dh9Vs5ynKlVmg%2BTRPlSEsxbAHgjNYkNSO0fk9NEG3gMMyuWWanFBmRZV9WF58YLR3Pu9sM0iWetzaoWvJ1F7Ek959x0V3Un36qLacPDUkG0o5er1YJ505hqRNb6k5uHEaduqra3uPu4Xb');

        $textHtml = str_replace('{name}', $nome, $textHtml);
		$textHtml = str_replace('{company}', $empresa, $textHtml);
		$textHtml = str_replace('{ticket_id}', $id, $textHtml);
		$textHtml = str_replace('{link_login}', $url, $textHtml);
        $textHtml = str_replace('{department}', $departments_name, $textHtml);
        $textHtml = str_replace('{type}', $type, $textHtml);
        return $textHtml;
    }

    /**
     * Send ticket received message : Recebido
     */
    public function ticketReceived()
    {
        $result = DB::table('emails')
        ->where('company_id', Crypt::decrypt(session('companyselected')['id']))
        ->where('language', $this->user->language)
        ->where('type','opened')
        ->select('title', 'email', 'name_sender','email_sender')
        ->first();

        // SE NÃO TIVER, PEGAR O EMAIL EM INGLÊS 
        if($result == null){
            $result = DB::table('emails')
            ->where('company_id', Crypt::decrypt(session('companyselected')['id']))
            ->where('language', 'en_US')
            ->where('type','opened')
            ->select('title', 'email', 'name_sender','email_sender')
            ->first();
        }

        //SE NÃO TIVER, PEGAR MODELO DEFAULT
        if($result == null){
            $result = DB::table('emails')
            ->where('company_id', null)
            ->where('language', $this->user->language == 'pt_BR'? 'pt_BR':'en_US')
            ->where('type','opened')
            ->select('title', 'email', 'name_sender','email_sender')
            ->first();
        }

        if($result !=null){
            $company = DB::table('company')
            ->where('id', Crypt::decrypt(session('companyselected')['id']))
            ->select('name')
            ->first();

            if($company == null){
                $name_sender = "Support";
                $email_sender = "noreply.ba-support@builderall.com";
            }else{
                $name_sender = $result->name_sender;
                $email_sender = $result->email_sender;
            }

            if($result != null){
                $mail = new sendEmailCustom(
                    $this->ticket_id, 
                    $this->user, 
                    $this->user_email, 
                    $this->filter($result->title), 
                    $this->filter($result->email),
                    $name_sender,
                    $email_sender,
                    $this->hash_company
                );
                Mail::send($mail);
            }
        }
    }

    /**
     * Send ticket repplied message : Respondido
     */
    public function ticketReplied()
    {
        $result = DB::table('emails')
        ->where('company_id', Crypt::decrypt(session('companyselected')['id']))
        ->where('language', $this->user->language)
        ->where('type','replied')
        ->select('title', 'email', 'name_sender','email_sender')
        ->first();

        // SE NÃO TIVER, PEGAR O EMAIL EM INGLÊS 
        if($result == null){
            $result = DB::table('emails')
            ->where('company_id', Crypt::decrypt(session('companyselected')['id']))
            ->where('language', 'en_US')
            ->where('type','replied')
            ->select('title', 'email', 'name_sender','email_sender')
            ->first();
        }

        //SE NÃO TIVER, PEGAR MODELO DEFAULT
        if($result == null){
            $result = DB::table('emails')
            ->where('company_id', null)
            ->where('language', $this->user->language == 'pt_BR'? 'pt_BR':'en_US')
            ->where('type','replied')
            ->select('title', 'email', 'name_sender','email_sender')
            ->first();
        }

        if($result !=null){
            $company = DB::table('company')
            ->where('id', Crypt::decrypt(session('companyselected')['id']))
            ->select('name')
            ->first();

            if($company == null){
                $name_sender = "Support";
                $email_sender = "noreply.ba-support@builderall.com";
            }else{
                $name_sender = $result->name_sender;
                $email_sender = $result->email_sender;
            }

            if($result != null){
                $mail = new sendEmailCustom(
                    $this->ticket_id, 
                    $this->user, 
                    $this->user_email, 
                    $this->filter($result->title), 
                    $this->filter($result->email),
                    $name_sender,
                    $email_sender,
                    $this->hash_company
                );
                Mail::send($mail);
            }
        }
    }

    /**
     * Send ticket repplied message : Fechado
     */
    public function ticketClosed()
    {
        $result = DB::table('emails')
        ->where('company_id', Crypt::decrypt(session('companyselected')['id']))
        ->where('language', $this->user->language)
        ->where('type','closed')
        ->select('title', 'email', 'name_sender','email_sender')
        ->first();

        // SE NÃO TIVER, PEGAR O EMAIL EM INGLÊS 
        if($result == null){
            $result = DB::table('emails')
            ->where('company_id', Crypt::decrypt(session('companyselected')['id']))
            ->where('language', 'en_US')
            ->where('type','closed')
            ->select('title', 'email', 'name_sender','email_sender')
            ->first();
        }

        //SE NÃO TIVER, PEGAR MODELO DEFAULT
        if($result == null){
            $result = DB::table('emails')
            ->where('company_id', null)
            ->where('language', $this->user->language == 'pt_BR'? 'pt_BR':'en_US')
            ->where('type','closed')
            ->select('title', 'email', 'name_sender','email_sender')
            ->first();
        }

        if($result !=null){
            $company = DB::table('company')
            ->where('id', Crypt::decrypt(session('companyselected')['id']))
            ->select('name')
            ->first();
            
            if($company == null){
                $name_sender = "Support";
                $email_sender = "noreply.ba-support@builderall.com";
            }else{
                $name_sender = $result->name_sender;
                $email_sender = $result->email_sender;
            }
    
            if($result != null){
                $mail = new sendEmailCustom(
                    $this->ticket_id, 
                    $this->user, 
                    $this->user_email, 
                    $this->filter($result->title), 
                    $this->filter($result->email),
                    $name_sender,
                    $email_sender,
                    $this->hash_company
                );
                Mail::send($mail);
            }
        }
    }

    /**
     * 
     */
    public function changePassword($link)
    {
        $result = DB::table('emails')
        ->where('language', $this->user->language)
        ->where('type','password')
        ->select('title', 'email', 'name_sender','email_sender')
        ->first();

        // SE NÃO TIVER, PEGAR O EMAIL EM INGLÊS 
        if($result == null){
            $result = DB::table('emails')
            ->where('company_id', Crypt::decrypt(session('companyselected')['id']))
            ->where('language', 'en_US')
            ->where('type','password')
            ->select('title', 'email', 'name_sender','email_sender')
            ->first();
        }

        //SE NÃO TIVER, PEGAR MODELO DEFAULT
        if($result == null){
            $result = DB::table('emails')
            ->where('company_id', null)
            ->where('language', $this->user->language == 'pt_BR'? 'pt_BR':'en_US')
            ->where('type','password')
            ->select('title', 'email', 'name_sender','email_sender')
            ->first();
        }

        if($result != null){
            if($this->user->language == 'pt_BR'){
                $name_sender = "Support";
                $email_sender = "noreply.ba-support@builderall.com";
            }else{
                $name_sender = "Support";
                $email_sender = "noreply.ba-support@builderall.com";
            }
    
            // SE NÃO TIVER, PEGAR O EMAIL EM INGLÊS 
            if($result == null){
                $result = DB::table('emails')
                ->where('language', 'en_US')
                ->where('type','password')
                ->select('title', 'email')
                ->first();
            }
    
            if($result != null){
                $textHtml = str_replace('{redirect_url}', $link, $result->email);
                $mail = new sendEmailCustom(
                    $this->ticket_id, 
                    $this->user, 
                    $this->user_email, 
                    $this->filter($result->title), 
                    $this->filter($textHtml),
                    $name_sender,
                    $email_sender,
                    $this->hash_company
                );
                Mail::send($mail);
            }
        }
    }
    /**
     * 
     */
    public function changePasswordClient($link)
    {
        $company = DB::table('company')
        ->where('hash_code', $this->hash_company)
        ->select('id','name')
        ->first();

        if($company == null){
            $name_sender = "Support";
            $email_sender = "noreply.ba-support@builderall.com";
        }else{
            $result = DB::table('emails')
            ->where('company_id', $company->id)
            ->where('language', $this->user->language)
            ->where('type','password')
            ->select('title', 'email', 'name_sender','email_sender')
            ->first();

            //SE NÃO TIVER, PEGAR MODELO DEFAULT
            if($result == null){
                $result = DB::table('emails')
                ->where('company_id', null)
                ->where('language', $this->user->language == 'pt_BR'? 'pt_BR':'en_US')
                ->where('type','password')
                ->select('title', 'email', 'name_sender','email_sender')
                ->first();
            }

            if($company != null){
                $name_sender = $result->name_sender;
                $email_sender = $result->email_sender;
            }

            if($result != null){
                $textHtml = str_replace('{redirect_url}', $link, $result->email);
                $mail = new sendEmailCustom(
                    $this->ticket_id, 
                    $this->user, 
                    $this->user_email, 
                    $this->filter($result->title, $company->name), 
                    $this->filter($textHtml, $company->name),
                    $name_sender,
                    $email_sender,
                    $this->hash_company
                );
                Mail::send($mail);
            }
        }
    }
    /**
     * Send ticket/chat opened message : Aberto
     */
    public function ticketchatOpened($type,$onlines,$departments)
    {

        $departments_id = Crypt::decrypt($departments['id']) == false ? $departments['id'] : Crypt::decrypt($departments['id']);
        $departments_name = $departments['name'];

        $company = DB::table('company')
        ->where('hash_code', $this->hash_company)
        ->select('id', 'name')
        ->first();
        if($company == null){
            return;
        }
        
        $result = DB::table('emails')
        ->where('company_id', Crypt::decrypt(session('companyselected')['id']))
        ->where('language', $this->user->language)
        ->where('type','attendant')
        ->select('title', 'email', 'name_sender','email_sender')
        ->first();

        //SE NÃO TIVER, PEGAR MODELO DEFAULT
        if($result == null){
            $result = DB::table('emails')
            ->where('company_id', null)
            ->where('language', $this->user->language == 'pt_BR'? 'pt_BR':'en_US')
            ->where('type','attendant')
            ->select('title', 'email', 'name_sender','email_sender')
            ->first();
        }
        
        // SE NÃO TIVER, PEGAR O EMAIL EM INGLÊS 
        if($result == null){
            $result = DB::table('emails')
            ->where('language', 'en_US')
            ->where('type','attendant')
            ->select('title', 'email', 'name_sender','email_sender')
            ->first();
            if($result == null){
                return;
            }
        }
        
        $users = DB::table('company_user')
        ->join('user_auth', 'company_user.user_auth_id', 'user_auth.id')
        ->where('company_user.company_id', $company->id)
        ->where('company_user.config', '!=', '0')
        ->select('user_auth.id', 'user_auth.name', 'user_auth.email','company_user.user_auth_id', 'company_user.config', 'user_auth.language')
        ->get();

        foreach ($users  as $key) {

            // $key->email = 'marlos_gpi@live.com'; // GAMBIARRRA PRA EU RECEBER EMAIL
            
            if(!isset(json_decode($key->config)[3]) || !isset(json_decode($key->config)[2]) || !isset(json_decode($key->config)[1]) || json_decode($key->config) == 'new_ticket'){
                
            }else{
                $type_c_t = json_decode($key->config)[0];
                $type_f_o = json_decode($key->config)[1];
                $type_departments_id = json_decode($key->config)[2];
                $type_notify = json_decode($key->config)[3];
            
                foreach ($type_c_t  as $c_t) { 
                    if($c_t == 'new_ticket' && $type == 'TICKET' || $c_t == 'new_chat' && $type == 'CHAT'){

                    if($type_f_o == 'forever'){ // VERIFICAR SE ELE MARCOU SEMPRE RECEBER 
                        
                            foreach ($type_departments_id as $dep_id) {  // VERIFICAR SE ELE MARCOU O DEPARTAMENTO 
                                if(Crypt::decrypt($dep_id) == $departments_id){

                                    foreach ($type_notify as $tn) { // VERIFICAR SE ELE MARCOU RECEBER EMAIL
                                    if($tn == 'noty_email'){
                                            $content = [
                                                'name'    => $key->name,
                                                'company' => $company->name,
                                                'department' => $departments_name,
                                                'type' => $type,
                                            ];
                                        
                                            // MailCentral::sendMessage(self::TICKET_CHAT_OPENED, $key->email, $key->language, $content, 0, $this->company->name);
                                            $mail = new sendEmailCustom(
                                                $this->ticket_id, 
                                                $this->user, 
                                                $key->email, 
                                                $this->filter($result->title, $company->name, $type, $departments_name), 
                                                $this->filter($result->email, $company->name, $type, $departments_name,),
                                                $result->name_sender,
                                                $result->email_sender,
                                                $this->hash_company
                                            );
                                            Mail::send($mail);
                                    }
                                    }
                                }
                            }
                        }else if($type_f_o == 'online'){ // VERIFICAR SE ELE MARCOU SÓ RECEBER ONLINE
                            
                            foreach ($onlines  as $online) { 
                                if($online['id'] == $key->id){
                                
                                    foreach ($type_departments_id as $dep_id) {  // VERIFICAR SE ELE MARCOU O DEPARTAMENTO 
                                        if(Crypt::decrypt($dep_id) == $departments_id){

                                            foreach ($type_notify as $tn) { // VERIFICAR SE ELE MARCOU RECEBER EMAIL
                                            if($tn == 'noty_email'){
                                                    $content = [
                                                        'name'    => $key->name,
                                                        'company' => $company->name,
                                                        'department' => $departments_name,
                                                        'type' => $type,
                                                    ];
                                            
                                                    // MailCentral::sendMessage(self::TICKET_CHAT_OPENED, $key->email, $key->language, $content, 0, $this->company->name);
                                                    $mail = new sendEmailCustom(
                                                        $this->ticket_id, 
                                                        $this->user, 
                                                        $key->email, 
                                                        $this->filter($result->title, $company->name, $type, $departments_name), 
                                                        $this->filter($result->email, $company->name, $type, $departments_name,),
                                                        $result->name_sender,
                                                        $result->email_sender,
                                                        $this->hash_company
                                                    );
                                                    Mail::send($mail);
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

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {   
        $this->subject($this->title);
        $this->to($this->user_email);
        $this->from($this->email_sender, $this->name_sender)->html($this->textHtml);
    }
}
