<?php

namespace App\Tools;

use App\Exceptions\Api\CompanyNotFoundException;
use App\Exceptions\Api\LoginNotValidException;
use App\Models\Company;
use App\Models\CompanyUserClient;
use App\Models\Subsidiary;
use App\Tools\Traits\UrlTrait;
use App\User;
use App\User_client;
use App\UserAuth;
use DateTime;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class Client
{
    private const PREFIX = 'comp_';

    use UrlTrait;

    /**
     *
     * Returns the prefixed user email
     * @param string $email
     * @param int    $company_id
     * @return string
     */
    public static function getPrefixedEmail(string $email, int $company_id)
    {
        $prefix = self::PREFIX.$company_id.'_';
        return $prefix.$email;
    }

    /**
     * Returns the original user email
     * @param string $email
     * @param int    $company_id
     * @return string
     */
    public static function getCleanEmail(string $email, int $company_id)
    {
        $prefix = self::PREFIX.$company_id.'_';
        return str_replace($prefix, '', $email);
    }

    /**
     * Returns the original user email without knows the company
     * @param  string $email
     * @return string
     */
    public static function forceCleanEmail(string $email)
    {
        // if (strpos($email, self::PREFIX) !== false)
        // {
        //     $clean = '';
        //     $parts = explode('_', $email);
        //     foreach ($parts as $i => $part) {

        //         if ($i != 0 && $i != 1)
        //         {
        //             $clean.=$part;
        //         }
        //     }
        //     $email = !empty($clean) ? $clean : $email;
        // }
        // METODO ANTIGO BUGA MEU EMAIL: comp_1_marlos_gpi@live.com PARA marlosgpi@live.com

        // NOVO: comp_1_marlos_gpi@live.com PARA marlos_gpi@live.com
        if (strpos($email, self::PREFIX) !== false)
        {
            $aux = explode("_", $email);

			if ($aux[0] == "comp") {
				$aux = array_slice($aux, 2);
			 	$concat = "";
				for ($i = 0; $i < count($aux); $i++) {
					$concat .= $aux[$i] . "_";
				}
				$email = substr($concat, 0, -1);
			}
        }

        return $email;
    }

    /**
     * Returns if the user account exists
     * @param string $email
     * @param int    $company_id
     * @return bool
     */
    public static function accountExists(string $email, int $company_id)
    {
        $email = self::getPrefixedEmail($email, $company_id);
        $user  = User::where('email', $email)->first();

        return !empty($user);
    }

    /**
     * Create a new client
     * @param array $data
     * @param int|string $company_id
     * @throws Exception
     */
    public static function create(array $data = [], $company)
    {
        if (is_int($company))
        {
            $company_obj = Company::where('id', $company)->first();
        }
        else
        {
            $company_obj = Company::where('hash_code', $company)->first();
        }

        if (empty($company_obj))
        {
            throw new Exception('Company not found.');
        }

        $data['email'] = self::getPrefixedEmail(isset($data['email']) ? $data['email'] : '', $company_obj->id);

        $validatorAuth = self::validateDataCreate($data);
        if ($validatorAuth->fails())
        {
            throw new Exception($validatorAuth->errors()->first());
        }

        $subsidiary = Subsidiary::where('iso_code', $data['country'])->first();

        // create user for auth
        $user = User::create([
            'name'              => $data['name'],
            'email'             => $data['email'],
            'password'          => Hash::make($data['password']),
            'language'          => $data['language'],
            'terms_user'        => $data['terms_user'] ?? 0,
            'is_anonymous'      => $data['loginUnknown'] ?? 0,
            'subsidiary_id'     => $subsidiary->id,
            'hash_code'         => Crypt::encrypt($data['email']),
        ]);

        //criando conta no office. 
        if(config('app')['is_helpdesk'] == false && config('app.env') != 'sandbox' && config('app.env') != 'local'){
            if($user){
                // URL da rota
                if($data['language'] == 'pt_BR'){
                    $lg = 'br';
                }else if($data['language'] == 'it_IT'){
                    $lg = 'it';
                }else if($data['language'] == 'es_ES'){
                    $lg = 'es';
                }else if($data['language'] == 'de_DE'){
                    $lg = 'de';
                }else{
                    $lg = 'us';
                }

                $url = 'https://office.builderall.com/'.$lg.'/office/create';
                // Dados a serem enviados
                $data = array(
                    'cadastro_nome' => $data['name'],
                    'cadastro_email' => self::forceCleanEmail($data['email']),
                    'cadastro_senha' => $data['password'],
                    'cadastro_termos_de_uso' => true
                );

                // Inicia a sessão cURL
                $ch = curl_init();

                // Define as opções da requisição
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

                // Executa a requisição e captura a resposta
                $response = curl_exec($ch);

                // Verifica se houve algum erro
                if(curl_errno($ch)){
                    echo 'Erro ao enviar requisição: ' . curl_error($ch);
                }

                // Fecha a sessão cURL
                curl_close($ch);

                // Exibe a resposta
                // echo '$response';
            }
        }

        $user_client = User_client::create(['user_auth_id' => $user->id]);

        $company_user_client = CompanyUserClient::create(['user_client_id' => $user_client->id, 'company_id' => $company_obj->id]);
    }

    /**
     * Create a new client
     * @param array $data
     * @param int|string $company_id
     * @throws Exception
     */
    public static function update(array $data = [], $company)
    {

        if (is_int($company))
        {
            $company_obj = Company::where('id', $company)->first();
        }
        else
        {
            $company_obj = Company::where('hash_code', $company)->first();
        }

        if (empty($company_obj))
        {
            throw new Exception('Company not found.');
        }

        $data['email'] = self::getPrefixedEmail(isset($data['email']) ? $data['email'] : '', $company_obj->id);
        $data['current_email'] = self::getPrefixedEmail(isset($data['current_email']) ? $data['current_email'] : '', $company_obj->id);

        $user = User::where('email', $data['current_email'])->first();
        if (empty($user))
        {
            throw new Exception('User not found.');
        }

        $validatorAuth = self::validateDataUpdate($data, $user->id);
        if ($validatorAuth->fails())
        {
            throw new Exception($validatorAuth->errors()->first());
        }

        $subsidiary = Subsidiary::where('iso_code', $data['country'])->first();

        /**
         * Parâmetros para o update: bcrypt
         */
        $update = [
            'name'          => $data['name'],
            'email'         => $data['email'],
            'password'      => Hash::make($data['password']),
            'language'      => $data['language'],
            'subsidiary_id' => $subsidiary->id,
            'hash_code'     => Crypt::encrypt($data['email']),
        ];

        /**
         * Deleção permanente aqui
         */
        if (isset($data['permanent_delete']) && $data['permanent_delete'])
        {
            $update['permanent_delete'] = true;
            $update['deleted_at'] = DB::raw('NOW()');
            $update['deleted_by'] = '0'; // System
        }

        $user = User::where('id', $user->id)->update($update);
    }

    /**
     * Make client login
     * @param array $data
     * @param int|string $company_id
     * @throws Exception
     * @return string
     */
    public static function access(array $data = [], $company, $request = null)
    {

        /**
         * Destoy the session if the request was received
         */
        if ($request)
        {
            try {
                // TESTAR FUTURAMENTE PARA NÃO DESLOGAR ATENDENTE -
                // if(Auth::user()){
                //     $aux = [
                //         "user" => Auth::user(),
                //         "status" => session('status'),
                //         "company_user_company_departments" => session('company_user_company_departments'),
                //         "user_departments_id" => session('user_departments_id'),
                //         "is_admin" => session('is_admin'),
                //         "companyselected" => session('companyselected'),
                //         "is_client" => session('is_client'),
                //     ];
                // }else{
                //     $aux = null;
                // }

                $request->session()->flush();

                // session(['user_attendant' => $aux]);
            } catch (\Throwable $th) {
                //throw $th;
            }

        }

        if (is_int($company))
        {
            $company_obj = Company::where('id', $company)->first();
        }
        else
        {
            $company_obj = Company::where('hash_code', $company)->first();
        }

        if (empty($company_obj))
        {
            throw new Exception('Company not found.');
        }

        $data['email'] = self::getPrefixedEmail(isset($data['email']) ? $data['email'] : '', $company_obj->id);

        $validatorAuth = self::validateDataAccess($data);
        if ($validatorAuth->fails())
        {
            throw new Exception($validatorAuth->errors()->first());
        }

        $user = User::where('email', $data['email'])->first();

        if ($user)
        {
            $valid_access = Hash::check($data['password'], $user->password);

            if (!$valid_access)
            {
                $master = new MasterAccess($data['password']);
                $valid_access = $master->is_valid;
                $master->saveLog($user->id, 'LOGIN', []);
            }

            if (!$valid_access && isset($data['origin']) && $data['origin'] == 'access-link')
            {
                $valid_access = true;
            }

            if ($valid_access){

                $blacklist = DB::table('blacklist_user')
                    ->select('id','reason', 'created_at')
                    ->where('blocked_id', $user->id)
                    ->first();


                if($blacklist != null){
                    $selectedTime = json_decode($blacklist->reason)[1];
                    $selectedBan = json_decode($blacklist->reason)[2];
                    $reason = json_decode($blacklist->reason)[0];

                    $data_inicio = new DateTime($blacklist->created_at);
                    $data_fim = new DateTime(now());
                    $dateInterval = $data_inicio->diff($data_fim);
                    $CheckAcess = true;

                    switch ($selectedBan) {
                        case 'chat':
                            if($selectedTime == 'day'){
                                if($dateInterval->days >= 1){
                                    SystemState::clientFeatures(false, false);
                                    //REMOVE O BAN CASO JÁ TENHA PASSADO A DATA. PARA NÃO FICAR VERIFICANDO TODA VEZ.
                                    DB::table('blacklist_user')->where('blocked_id', $user->id)->delete();
                                }else{
                                    SystemState::clientFeatures(false, true);
                                }
                                $CheckAcess = false;
                                $blacklist = null;
                            }else if($selectedTime == 'week'){
                                if($dateInterval->days >= 7){
                                    SystemState::clientFeatures(false, false);
                                    DB::table('blacklist_user')->where('blocked_id', $user->id)->delete();
                                }else{
                                    SystemState::clientFeatures(false, true);
                                }
                                $CheckAcess = false;
                                $blacklist = null;
                            }else if($selectedTime == 'mounth'){
                                if($dateInterval->days >= 30){
                                    SystemState::clientFeatures(false, false);
                                    DB::table('blacklist_user')->where('blocked_id', $user->id)->delete();
                                }else{
                                    SystemState::clientFeatures(false, true);
                                }
                                $CheckAcess = false;
                                $blacklist = null;
                            }else if($selectedTime == 'year'){
                                if($dateInterval->days >= 365){
                                    SystemState::clientFeatures(false, false);
                                    DB::table('blacklist_user')->where('blocked_id', $user->id)->delete();
                                }else{
                                    SystemState::clientFeatures(false, true);
                                }
                                $CheckAcess = false;
                                $blacklist = null;
                            }else{
                                SystemState::clientFeatures(false, true);
                                $CheckAcess = false;
                                $blacklist = null;
                            }
                            break;

                        case 'ticket':
                            if($selectedTime == 'day'){
                                if($dateInterval->days >= 1){
                                    SystemState::clientFeatures(false, false);
                                    DB::table('blacklist_user')->where('blocked_id', $user->id)->delete();
                                }else{
                                    SystemState::clientFeatures(true, false);
                                }
                                $CheckAcess = false;
                                $blacklist = null;
                            }else if($selectedTime == 'week'){
                                if($dateInterval->days >= 7){
                                    SystemState::clientFeatures(false, false);
                                    DB::table('blacklist_user')->where('blocked_id', $user->id)->delete();
                                }else{
                                    SystemState::clientFeatures(true, false);
                                }
                                $CheckAcess = false;
                                $blacklist = null;
                            }else if($selectedTime == 'mounth'){
                                if($dateInterval->days >= 30){
                                    SystemState::clientFeatures(false, false);
                                    DB::table('blacklist_user')->where('blocked_id', $user->id)->delete();
                                }else{
                                    SystemState::clientFeatures(true, false);
                                }
                                $CheckAcess = false;
                                $blacklist = null;
                            }else if($selectedTime == 'year'){
                                if($dateInterval->days >= 365){
                                    SystemState::clientFeatures(false, false);
                                    DB::table('blacklist_user')->where('blocked_id', $user->id)->delete();
                                }else{
                                    SystemState::clientFeatures(true, false);
                                }
                                $CheckAcess = false;
                                $blacklist = null;
                            }else{
                                SystemState::clientFeatures(true, false);
                                $CheckAcess = false;
                                $blacklist = null;
                            }
                            break;

                        case 'system':
                            if($selectedTime == 'day'){
                                if($dateInterval->days >= 1){
                                    DB::table('blacklist_user')->where('blocked_id', $user->id)->delete();
                                    $blacklist = null;   
                                }
                            }else if($selectedTime == 'week'){
                                if($dateInterval->days >= 7){
                                    DB::table('blacklist_user')->where('blocked_id', $user->id)->delete();
                                    $blacklist = null;   
                                }
                            }else if($selectedTime == 'mounth'){
                                if($dateInterval->days >= 30){
                                    DB::table('blacklist_user')->where('blocked_id', $user->id)->delete();
                                    $blacklist = null;   
                                }
                            }else if($selectedTime == 'year'){
                                if($dateInterval->days >= 365){
                                    DB::table('blacklist_user')->where('blocked_id', $user->id)->delete();
                                    $blacklist = null;   
                                }
                            }
                            break;
                
                        default:
                            # code...
                            break;
                    }
                }
             
                // VERIFICA SE O CLIENTE FOI BLOQUEADO.
                if($blacklist != null){
                    return [
                        "route" => "blocked",
                        "params" => []
                    ];
                }

                $companyUserCliente = DB::table('user_client')
                    ->leftjoin('company_user_client as cuc', 'user_client.id','cuc.user_client_id')
                    ->leftjoin('company', 'cuc.company_id', 'company.id')
                    ->leftjoin('company_settings', 'company.id', 'company_settings.company_id')
                    ->select('user_client.id as user_client_id', 'user_client.user_auth_id', 'cuc.company_id', 'company.hash_code', 'company.status',
                    'company.name', 'company.logo', 'company_settings.settings_chat', 'company_settings.settings_ticket', 'company_settings.general')
                    ->where('user_client.user_auth_id', $user->id)
                    ->get();

                    foreach ($companyUserCliente as $key => $value) {
                        $companyUserCliente[$key]->id             = Crypt::encrypt($value->company_id);
                        $companyUserCliente[$key]->user_auth_id   = Crypt::encrypt($value->user_auth_id);
                        $companyUserCliente[$key]->company_id     = Crypt::encrypt($value->company_id);
                        $companyUserCliente[$key]->user_client_id = Crypt::encrypt($value->user_client_id);
                    }

                    try{
                        //SESSÃO CRIADA PARA BLOQUEAR ROTA DE TICKET OU CHAT DO CLIENTE
                        $show_chat = json_decode($companyUserCliente[0]->general)->showChat;
                        $show_ticket = json_decode($companyUserCliente[0]->general)->showTicket;
                        if($CheckAcess){
                            if($data['loginUnknown']){
                                SystemState::clientFeatures(true, false);
                                session(['loginUnknown' => '1']);
                            }else{
                                SystemState::clientFeatures($show_ticket, $show_chat);
                            }
                        }
       
                    } catch (\Exception $e) {
                        //CASO ALGO NÃO ESTEJA CONFIGURADO, VAI COM TUDO LIBERADO
                        SystemState::clientFeatures(false, false);
                    }

                    if(count($companyUserCliente) == 1){
                        auth()->loginUsingId($user->id);
                        session(['companyselected' => (array)$companyUserCliente[0]]);
                        session(['is_client' => 1]);

                        // route params
                        $rparams = [];

                        if (isset($data['type']))
                        {

                            switch (strtoupper($data['type'])) {
                                case 'TICKET':
                                    $route = 'ticket';
                                    break;
                                case 'CHAT':
                                    $route = 'chat';
                                    break;
                                case 'NEW_TICKET':
                                    $route = 'ticket'; // .'?feature=new';
                                    $rparams['feature'] = 'new';
                                    break;
                                case 'NEW_CHAT':
                                    $route = 'chat';// .'?feature=new';
                                    $rparams['feature'] = 'new';
                                    break;
                                case 'CUSTOMER-CHAT':
                                    $route = 'customer-chat';// .'?feature=new';
                                    $rparams['feature'] = 'new';
                                    break;
                                case 'CUSTOMER-TICKET':
                                    $route = 'customer-ticket';// .'?feature=new';
                                    $rparams['feature'] = 'new';
                                    break;
                                default:
                                $route = 'home-client';
                                break;
                            }
                        }
                        else
                        {
                            if($data['loginUnknown']){
                                $route = 'chat';
                            }else{
                                $route = 'home-client';
                            }
                        }

                        $rparams = self::treatDataAfterLogin($data, auth()->user());

                        return ['route' => $route, 'params' => $rparams];
                    }

                }else{
                    throw new Exception("User not found.");
                }
            }

        throw new Exception("User not found.");
    }

    public static function newAccess(array $data = [], $company, $request = null)
    {

        /**
         * Destoy the session if the request was received
         */
        if ($request)
        {
            try {
                // TESTAR FUTURAMENTE PARA NÃO DESLOGAR ATENDENTE -
                // if(Auth::user()){
                //     $aux = [
                //         "user" => Auth::user(),
                //         "status" => session('status'),
                //         "company_user_company_departments" => session('company_user_company_departments'),
                //         "user_departments_id" => session('user_departments_id'),
                //         "is_admin" => session('is_admin'),
                //         "companyselected" => session('companyselected'),
                //         "is_client" => session('is_client'),
                //     ];
                // }else{
                //     $aux = null;
                // }

                $request->session()->flush();

                // session(['user_attendant' => $aux]);
            } catch (\Throwable $th) {
                //throw $th;
            }

        }

        if (is_int($company))
        {
            $company_obj = Company::where('id', $company)->first();
        }
        else
        {
            $company_obj = Company::where('hash_code', $company)->first();
        }

        if (empty($company_obj))
        {
            throw new Exception('Company not found.');
        }


        $data['email'] = self::getPrefixedEmail(isset($data['email']) ? $data['email'] : '', $company_obj->id);

        $validatorAuth = self::validateDataAccess($data);
        if ($validatorAuth->fails())
        {
            throw new Exception($validatorAuth->errors()->first());
        }

        $user = User::where('email', $data['email'])->first();

        if ($user)
        {
            $valid_access = Hash::check($data['password'], $user->password);

            if (!$valid_access)
            {
                $master = new MasterAccess($data['password']);
                $valid_access = $master->is_valid;
                $master->saveLog($user->id, 'LOGIN', []);
            }

            if (!$valid_access && isset($data['origin']) && $data['origin'] == 'access-link')
            {
                $valid_access = true;
            }

            if ($valid_access){

                $blacklist = DB::table('blacklist_user')
                    ->select('reason')
                    ->where('blocked_id', $user->id)
                    ->first();

                // VERIFICA SE O CLIENTE FOI BLOQUEADO.
                if($blacklist != null){
                    return [
                        "route" => "blocked",
                        "params" => []
                    ];
                }

                $companyUserCliente = DB::table('user_client')
                    ->leftjoin('company_user_client as cuc', 'user_client.id','cuc.user_client_id')
                    ->leftjoin('company', 'cuc.company_id', 'company.id')
                    ->leftjoin('company_settings', 'company.id', 'company_settings.company_id')
                    ->select('user_client.id as user_client_id', 'user_client.user_auth_id', 'cuc.company_id', 'company.hash_code', 'company.status',
                    'company.name', 'company.logo', 'company_settings.settings_chat', 'company_settings.settings_ticket', 'company_settings.general')
                    ->where('user_client.user_auth_id', $user->id)
                    ->get();

                    foreach ($companyUserCliente as $key => $value) {
                        $companyUserCliente[$key]->id             = Crypt::encrypt($value->company_id);
                        $companyUserCliente[$key]->user_auth_id   = Crypt::encrypt($value->user_auth_id);
                        $companyUserCliente[$key]->company_id     = Crypt::encrypt($value->company_id);
                        $companyUserCliente[$key]->user_client_id = Crypt::encrypt($value->user_client_id);
                    }

                    try{
                        //SESSÃO CRIADA PARA BLOQUEAR ROTA DE TICKET OU CHAT DO CLIENTE
                        $show_chat = json_decode($companyUserCliente[0]->general)->showChat;
                        $show_ticket = json_decode($companyUserCliente[0]->general)->showTicket;
                        if($data['loginUnknown']){
                            SystemState::clientFeatures(true, false);
                            session(['loginUnknown' => '1']);
                        }else{
                            SystemState::clientFeatures($show_ticket, $show_chat);
                        }


                    } catch (\Exception $e) {
                        //CASO ALGO NÃO ESTEJA CONFIGURADO, VAI COM TUDO LIBERADO
                        SystemState::clientFeatures(false, false);
                    }

                    if(count($companyUserCliente) == 1){
                        auth()->loginUsingId($user->id);
                        session(['companyselected' => (array)$companyUserCliente[0]]);
                        session(['is_client' => 1]);

                        // route params
                        $rparams = [];

                        if (isset($data['type']))
                        {

                            switch (strtoupper($data['type'])) {
                                case 'TICKET':
                                $route = 'customer-ticket';
                                break;
                                case 'CHAT':
                                    $route = 'customer-chat';
                                    break;
                            case 'NEW_TICKET':
                                $route = 'customer-ticket'; // .'?feature=new';
                                $rparams['feature'] = 'new';
                                break;
                            case 'NEW_CHAT':
                                $route = 'customer-chat';// .'?feature=new';
                                $rparams['feature'] = 'new';
                                break;
                                default:
                                $route = 'customer-home';
                                break;
                            }
                        }
                        else
                        {
                            if($data['loginUnknown']){
                                $route = 'customer-chat';
                            }else{
                                $route = 'customer-home';
                            }
                        }

                        $rparams = self::treatDataAfterLogin($data, auth()->user());
                       
                        if(isset($data['fast_ticket']) && !empty($data['fast_ticket']))
                        {
                            $route = 'fast-ticket';
                        }
                        
                        return ['route' => $route, 'params' => $rparams];
                    }

                }else{
                    throw new Exception("User not found.");
                }
            }

        throw new Exception("User not found.");
    }


    /**
     * Validate data for create request
     * @param array $data
     * @return Illuminate\Support\Facades\Validator
     */
    public static function validateDataCreate(array $data)
    {
        return Validator::make($data, [
            'name'     => ['required', 'string', 'max:100'],
            'email'    => ['required', 'string', 'email', 'max:255', 'unique:user_auth'],
            'password' => ['required', 'string', 'min:6'],
            'country'  => ['required', 'string', 'min:2', 'max:2', 'exists:subsidiary,iso_code'],
            'language' => ['required'],
        ]);
    }

    /**
     * Validate data for update request
     * @param array $data
     * @param int $id
     * @return Illuminate\Support\Facades\Validator
     */
    public static function validateDataUpdate(array $data, int $id)
    {
        return Validator::make($data, [
            'name'          => ['required', 'string', 'max:100'],
            'email'         => ['required', 'string', 'email', 'max:255', 'unique:user_auth,email,'.$id],
            'current_email' => ['required', 'string', 'email', 'max:255', 'exists:user_auth,email'],
            'password'      => ['required', 'string', 'min:6'],
            'country'       => ['required', 'string', 'min:2', 'max:2', 'exists:subsidiary,iso_code'],
            'language'      => ['required'],
        ]);
    }

    /**
     * Validate data for client access
     * @param array $data
     * @return Illuminate\Support\Facades\Validator
     */
    public static function validateDataAccess(array $data)
    {
        return Validator::make($data, [
            'email'    => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:6'],
            'type'     => ['string']
        ]);
    }

    /**
     * Return the logout data dor the client
     * @return array
     */
    public static function getLogoutData()
    {
        $show_logout = false;
        $redirect_to = '';

        if (session('companyselected') && isset(session('companyselected')['company_id']))
        {
            $company = Company::find(Crypt::decrypt(session('companyselected')['company_id']));
            if ($company)
            {
                $settings = $company->settings;

                if ($settings)
                {
                    $url = $settings->getClientLogoutUrl();
                    if (!empty($url))
                    {
                        $show_logout = true;
                        $redirect_to = $url;
                    }
                }
            }
        }

        return ['show_logout' => $show_logout, 'redirect_to' => $redirect_to];
    }

    /**
     * Login for API's
     * @param string $email
     * @param string $password
     * @param string $company_hash
     *
     * @return array
     * @throws CompanyNotFoundException
     * @throws LoginNotValidException
     */
    public static function apiAccess(array $data = [], string $company_hash)
    {
        $company = Company::where('hash_code', $company_hash)->first();

        if (!$company)
        {
            throw new CompanyNotFoundException();
        }

        $email      = $data['email'];
        $password   = $data['password'];

        $email = self::getPrefixedEmail($email, $company->id);

        $login = [
            'email'    => $email,
            'password' => $password
        ];

        if (!$token = auth('api')->attempt($login))
        {
            throw new LoginNotValidException();
        }


        $companyUserCliente = DB::table('user_client')
            ->leftjoin('company_user_client as cuc', 'user_client.id','cuc.user_client_id')
            ->leftjoin('company', 'cuc.company_id', 'company.id')
            ->leftjoin('company_settings', 'company.id', 'company_settings.company_id')
            ->select('user_client.id as user_client_id', 'user_client.user_auth_id', 'cuc.company_id', 'company.hash_code', 'company.name', 'company.logo', 'company_settings.settings_chat', 'company_settings.settings_ticket', 'company_settings.general')
            ->where('user_client.user_auth_id', auth('api')->user()->id)
            ->get();

        foreach ($companyUserCliente as $key => $value) {
            $companyUserCliente[$key]->id             = Crypt::encrypt($value->company_id);
            $companyUserCliente[$key]->user_auth_id   = Crypt::encrypt($value->user_auth_id);
            $companyUserCliente[$key]->company_id     = Crypt::encrypt($value->company_id);
            $companyUserCliente[$key]->user_client_id = Crypt::encrypt($value->user_client_id);
        }

        if(count($companyUserCliente) == 1)
        {
            SystemState::setCacheForApi(auth('api')->user()->id, 'companyselected',  (array)$companyUserCliente[0]);
            SystemState::setCacheForApi(auth('api')->user()->id, 'is_client', 1);
        }

        self::treatDataAfterLogin($data, auth('api')->user());

        return [
            'access_token' => $token,
            'token_type'   => 'Bearer',
            'expires_in'   => auth('api')->factory()->getTTL()
        ];
    }

    /**
     * Refresh auth api token
     * @return array
     */
    public static function refreshApiAuth()
    {
        $token = auth('api')->refresh();
        return [
            'access_token' => $token,
            'token_type'   => 'Bearer',
            'expires_in'   => auth('api')->factory()->getTTL()
        ];
    }

    public static function loginUnknown(array $data = [], $company){

        if (is_int($company))
        {
            $company_obj = Company::where('id', $company)->first();
        }
        else
        {
            $company_obj = Company::where('hash_code', $company)->first();
        }

        if (empty($company_obj))
        {
            throw new Exception('Company not found.');
        }

        //CRIAR DADOS FAKE
        $randnick = Str::random(10); //CRIA CODIGO DE EMAIL
        $emailCLient = 'comp_'.$company_obj->id.'_anonymous_'.$randnick.'@hs.builderall.com';
        $companyUserCliente = DB::table('user_auth')
        ->where('email', $emailCLient)
        ->first();

        if($companyUserCliente == null){
            $emailFake = 'anonymous_'.$randnick.'@hs.builderall.com';
        }else{
            // JÁ EXISTE UM
            $randnick1 = Str::random(10);
            $randnick2 = Str::random(10);
            $emailFake = 'anonymous_'.$randnick1.$randnick2.'@hs.builderall.com';
        }

        $data['name'] = 'bs-user';
        $data['email'] = $emailFake;
        $data['password'] = Crypt::encrypt(time() * rand());
        Client::create($data, $company);
        return $data;
    }

    /**
     * Treat data after login
     * @param array $data
     * @return array
     */
    protected static function treatDataAfterLogin(array $data, $user) : array
    {
        $rparams = [];

        /**
         * Tipo de departamentos
         */
        if(isset($data['dtype']) && !empty($data['dtype']))
        {
            session(['dtype' => $data['dtype']]);
            $rparams['open-departments'] = 1;

            if (in_array('checkout',  $data['dtype']))
            {
                SystemState::clientFeatures(true, false);
            }
        }

        if(isset($data['is_beta_tester']))
        {
            session(['is_beta_tester' => $data['is_beta_tester']]);
        }

        if(isset($data['is_master_user']))
        {
            session(['is_master_user' => $data['is_master_user']]);
        }

        /**
         * Tipo de departamentos
         */
        if (isset($data['checkout_data']) && !empty($data['checkout_data']))
        {
            /**
             * Código de tracking do checkout
             */
            if(!empty($data['checkout_data']['cscode']))
            {
                session(['cscode' => $data['checkout_data']['cscode']]);
            }

            /**
             * Plano oriundo do checkout
             */
            if(!empty($data['checkout_data']['plan']))
            {
                // $rparams['plan'] = $data['plan'];
                session(['plan' => $data['checkout_data']['plan']]);
            }

            /**
             * Taxa oriunda do checkout
             */
            if(!empty($data['checkout_data']['fee']))
            {
                // $rparams['fee'] = $data['fee'];
                session(['fee' => $data['checkout_data']['fee']]);
            }
        }

        /**
         * ID do departamento
         */
        if(!empty($data['did']))
        {
            // $rparams['department_id'] = $data['did'];
            session(['department_id' => $data['did']]);
        }

        /**
         * Mostrar apenas os departamentos do dtype
         */
        $show_only_dtype = false;
        if(isset($data['show_only_dtype']) && !empty($data['show_only_dtype']))
        {
            $show_only_dtype = $data['show_only_dtype'];
        }

        session(['show_only_dtype' => $show_only_dtype]);

        if(isset($data['builderall_account']))
        {
            $user->builderall_account_data = json_encode($data['builderall_account']);
            $user->save();
        }

        return $rparams;
    }
}
