<?php

namespace App\Http\Controllers;

use App\Tools\Builderall\Logger;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\User;
use App\CompanyUserCompanyDepartment;
use App\Mail\sendEmailCustom;
use App\Models\Company;
use App\Models\Company_user;
use App\Notifications\notifyTelegramBot;
use App\telegramMessage;
use App\Tools\Client;
use App\User_client;
use Illuminate\Support\Facades\Auth;
use App\Tools\Crypt;
use App\Tools\MasterAccess;
use App\Tools\Checkout;
use Exception;
use Builderall\Authenticator\BuilderallAuth;
use Illuminate\Support\Str;
use App\Tools\Builderall\MailCentral;
use NotificationChannels\Telegram\TelegramChannel;
use illuminate\support\Facades\Config;
use App\Notifications\TelegramNotification;
use App\Tools\Crypt\RC4;
use App\Tools\telegram\TelegramBot;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Lang;
use illuminate\support\Facades\Notification;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Route;

class HomeController extends Controller
{

    public function index(Request $request)
    {
        // VERIFICAÇÃO QUANDO HÁ ROTAS EM COMUM.
        if ($request->session()->exists('user')) {
            return redirect('/client');
        }
        // VERIFICAÇÃO QUANDO HÁ ROTAS EM COMUM.
        if (!Auth::user()) {
            return redirect('/login');
        }

        return view('layouts2.start-home', [ 'attendant_id' => Crypt::encrypt(Auth::user()->id) ]);
    }

    public function indexTeste(Request $request)
    {
        // VERIFICAÇÃO QUANDO HÁ ROTAS EM COMUM.
        if ($request->session()->exists('user')) {
            return redirect('/client');
        }
        // VERIFICAÇÃO QUANDO HÁ ROTAS EM COMUM.
        if (!Auth::user()) {
            return redirect('/login');
        }

        return view('layouts2.start-home-new', [ 'attendant_id' => Crypt::encrypt(Auth::user()->id)]);
    }

    public function indexTeste2(Request $request)
    {
        return view('layouts2.start-home-new-client', [ 'attendant_id' => Crypt::encrypt(Auth::user()->id)]);
    }

    public function loginClientOld(Request $request, $company)
    {
        if ($request->isMethod('post'))
        {

            try {
                if($request->loginUnknown){
                    $requestFake = Client::loginUnknown($request->all(), $company);
                    $response = Client::access($requestFake, $company, $request);
                }else{
                    $response = Client::access($request->all(), $company, $request);
                }

                // $dtype = ['checkout'];
                // $dtype = ['builderall-mentor'];
                // session(['dtype' => $dtype]);
                // session(['show_only_dtype' => false]);

                $login = [
                    'success' => true,
                    'redir'   => route($response['route'], $response['params'], true),
                ];
            } catch (\Exception $e) {
                echo $e;
                Logger::reportException($e, [], ['home-controller', 'login-client'], false);
                $login = [
                    'success' => false,
                    'value'   => 'not_email',
                ];
            }

            return $login;
        } else {
            $company = Company::where('hash_code', $company)->first();

            if($company == null){
                return response()->view('errors.404', [], 404);
            }

            $company_id = $company['id'];
            $company['id'] = Crypt::encrypt($company['id']);

            $company_settings = DB::table('company_settings')
			->select('general')
			->where('company_id', $company_id)
			->get();

            if($company_settings == '[]'){
                $acesso_anonymous = false;
            }else{
                if(isset(json_decode($company_settings[0]->general)->acesso_anonymous)){
                    $acesso_anonymous = json_decode($company_settings[0]->general)->acesso_anonymous;
                }else{
                    $acesso_anonymous = false;
                }
            }

            if (!$company) {
                return response()->view('errors.404', [], 404);
            }
            return view('layouts2.loginClientOld', ['company' => $company->toArray(), 'acesso_anonymous' => $acesso_anonymous, 'settings' => $company_settings]);
            // return view('client.auth.login', ['company' => $company->toArray(), 'acesso_anonymous' => $acesso_anonymous, 'settings' => $company_settings]);
        }
    }

    public function loginClient(Request $request, $company)
    {
        if ($request->isMethod('post'))
        {
            try {
                if ($request->isOfficePopup) {

                    $data = RC4::decryptClientAccess(config('app.rc4_key'), $request->officePopupClientHash);
                    $company = Company::where('hash_code', $company)->first();
                    $account_exists = Client::accountExists($data['email'], $company->id);

                    if ($account_exists) {
                        Client::update($data, $company->id);
                    } else {
                        Client::create($data, $company->id);
                    }

                    $user_data = [
                        "name" => $data['name'],
                        "email" => $data['current_email'],
                        "password" => $data['password'],
                        "loginUnknown" => false
                    ];

                    $response = Client::newAccess($user_data, $company->hash_code, $request);
                    $email = $user_data['email'];
                    $pass = $user_data['password'];

                } else if ($request->loginUnknown){
                    $requestFake = Client::loginUnknown($request->all(), $company);
                    $response = Client::newAccess($requestFake, $company, $request);
                    $email = $requestFake['email'];
                    $pass = $requestFake['password'];
                } else{
                    $response = Client::newAccess($request->all(), $company, $request);
                    $email = $request->email;
                    $pass = $request->password;
                }
                //SESSÃO INDICANDO QUE É POPUP
                session(['isPopup' => request('isPopup')]);

                // LINK DO CHECKOUT - https://checkout.builderall.com/payment/wp-funnel-club-wl-brl
                //https://ba-support.builderall.com/customer-chat?open-departments=1
                // $dtype = ['checkout'];
                // $dtype = ['builderall-mentor'];
                // session(['dtype' => $dtype]);
                // session(['department_id' => 'NndsbjNNZlVqT241ZzhNQUZsWE1iUT09']);
                // session(['show_only_dtype' => true]);
                // {"is_vip":true,"id":"1634790","uuid":"1f25f051-7bd1-4fb3-af57-39e72aa798c0"}
                if(isset(auth()->user()->builderall_account_data)){
                    if(json_decode(auth()->user()->builderall_account_data)->is_vip){
                        $dtype = ['builderall-mentor'];
                        session(['show_only_dtype' => true]);
                        session(['dtype' => $dtype]);
                    }
                }

                $login = [
                    'success' => true,
                    'redir'   => route($response['route'], $response['params'], true),
                    'email'   =>  $email,
                    'password'=> $pass,
                ];

            } catch (\Exception $e) {
                Logger::reportException($e, [], ['home-controller', 'login-client-new'], false);
                $login = [
                    'success' => false,
                    'value'   => 'not_email',
                ];
            }

            return $login;
        } else {
            $company = Company::where('hash_code', $company)->first();

            if($company == null){
                return response()->view('errors.404', [], 404);
            }
            $company_id = $company['id'];
            $company['id'] = Crypt::encrypt($company['id']);

            $company_settings = DB::table('company_settings')
			->select('general')
			->where('company_id', $company_id)
			->get();

            if($company_settings == '[]'){
                $acesso_anonymous = false;
            }else{
                if(isset(json_decode($company_settings[0]->general)->acesso_anonymous)){
                    $acesso_anonymous = json_decode($company_settings[0]->general)->acesso_anonymous;
                }else{
                    $acesso_anonymous = false;
                }
            }

            if (!$company || ($company_id == 1 && !session('isPopup'))) {
                return response()->view('errors.404', [], 404);
            }
            return view('client.auth.login', ['company' => $company->toArray(), 'acesso_anonymous' => $acesso_anonymous, 'settings' => $company_settings]);
        }
    }

    public function clientAccessLink(Request $request, $user, $company, $type)
    {
        try {
            $user_obj = User::where('hash_code', $user)->first();

            if (!$user_obj) {
                throw new Exception("User not found.");
            }

            $data = [
                'email'    => Client::forceCleanEmail($user_obj->email),
                'password' => $user_obj->password,
                'type'     => $type,
                'origin'   => 'access-link'
            ];

            $response = Client::access($data, $company, $request);
            $redir    = route($response['route'], $response['params'], true);
            return redirect($redir);
        } catch (Exception $e) {
            return response()->view('errors.404', [], 404);
        }
    }

    public function registerClientOld(Request $request, $company)
    {
        if ($request->isMethod('post')) {
            try {

                if($request->loginUnknown){
                    $requestFake = Client::loginUnknown($request->all(), $company);
                    $response = Client::access($requestFake, $company, $request);
                }else{
                    Client::create($request->all(), $company);
                    $response = Client::access($request->all(), $company, $request);
                }

                $redir    = route($response['route'], $response['params'], true);
                $register = [
                    'success' => true,
                    'redir'   => $redir,
                ];

            } catch (\Exception $e) {
                Logger::reportException($e, [], ['home-controller', 'register-client'], false);
                $register = [
                    'success' => false,
                    'value'   => $e->getMessage(),
                ];
            }

            return $register;
        } else {

            $company = Company::where('hash_code', $company)->first();

            $company_settings = DB::table('company_settings')
			->select('general')
			->where('company_id', $company->id)
			->get();

            if($company_settings == '[]'){
                $acesso_anonymous = false;
            }else{
                if(isset(json_decode($company_settings[0]->general)->acesso_anonymous)){
                    $acesso_anonymous = json_decode($company_settings[0]->general)->acesso_anonymous;
                }else{
                    $acesso_anonymous = false;
                }
            }

            if (!$company) {
                return response()->view('errors.404', [], 404);
            }
            return view('layouts2.registerClient', ['company' => $company->toArray(), 'acesso_anonymous' => $acesso_anonymous]);
            // return view('client.auth.register', ['company' => $company->toArray(), 'acesso_anonymous' => $acesso_anonymous]);
        }
    }

    public function registerClient(Request $request, $company)
    {
        if ($request->isMethod('post')) {
            try {

                if($request->loginUnknown){
                    $requestFake = Client::loginUnknown($request->all(), $company);
                    $response = Client::newAccess($requestFake, $company, $request);
                }else{
                    Client::create($request->all(), $company);
                    $response = Client::newAccess($request->all(), $company, $request);
                }

                $redir    = route($response['route'], $response['params'], true);
                $register = [
                    'success' => true,
                    'redir'   => $redir,
                ];

            } catch (\Exception $e) {
                Logger::reportException($e, [], ['home-controller', 'register-client-new'], false);
                $register = [
                    'success' => false,
                    'value'   => $e->getMessage(),
                ];
            }

            return $register;
        } else {

            $company = Company::where('hash_code', $company)->first();

            $company_settings = DB::table('company_settings')
			->select('general')
			->where('company_id', $company->id)
			->get();

            if($company_settings == '[]'){
                $acesso_anonymous = false;
            }else{
                if(isset(json_decode($company_settings[0]->general)->acesso_anonymous)){
                    $acesso_anonymous = json_decode($company_settings[0]->general)->acesso_anonymous;
                }else{
                    $acesso_anonymous = false;
                }
            }

            if (!$company) {
                return response()->view('errors.404', [], 404);
            }
            return view('client.auth.register', ['company' => $company->toArray(), 'acesso_anonymous' => $acesso_anonymous]);
        }
    }

    public function login(Request $request)
    {
        if ($request->isMethod('post')) {

            $request->session()->flush();

            $login['success'] = false;

            // validação para atendente desativado ou deletado não possa entrar na company
            $user = User::join('company_user', 'company_user.user_auth_id', 'user_auth.id')
                ->where('email', request('email'))
                ->where('company_user.is_active', 1) // ESTÁ ATIVADO
                ->where('company_user.deleted_at', null) // NÃO FOI DELETADO
                ->first();

            if($user == null){

                $user = User::where('email', request('email'))
                ->first();

                if($user == null){
                    $login['success']  = false;
                    return $login;
                }

                if($user->can_create_company == 0){
                    $login['success']  = false;
                    return $login;
                }
            }
            // validação em cima da hora para atendente desativado ou deletado não possa entrar na company

            if (config('app.env') != 'local' && config('app')['is_helpdesk'])
            {
                $token = BuilderallAuth::generateToken(request('email'), request('password'));
                if ($token)
                {
                    $login['redirect'] = '/helpdesk/login-by-token' . '?' . http_build_query(['token' => $token]);
                    $login['success']  = true;
                }
            }

            if(!$login['success'])
            {
                try {

                    $user = User::where('email', request('email'))
                        ->first();

                    if ($user) {

                        $valid_access = Hash::check(request('password'), $user->password);

                        if (!$valid_access) {
                            $master = new MasterAccess(request('password'));
                            $valid_access = $master->is_valid;
                            $master->saveLog($user->id, 'LOGIN', []);
                        }

                        if ($valid_access) {
                            //SALVA INFO DE QUANDO FICOU ONLINE
                            DB::table('user_auth_status')->insertGetId([
                                'user_auth_id' => $user->id,
                                'status' => 'online',
                                'created_at' => \Carbon\Carbon::now()->toDateTimeString(),
                            ]);

                            //alteração de rota caso seja admin ou atendente logando
                            setcookie("client_logout_url", '/login', time() + 86400);

                            //SESSÃO CRIADA CASO NÃO TENHA COMPANY SELECIONADA
                            session([
                                'restriction' => $array = [
                                    0 => (object) [
                                        "ticket_alllist" => 0,
                                        "ticket_admin" => 0,
                                        "ticket_create" => 0,
                                        "ticket_open" => 0,
                                        "ticket_resolved" => 0,
                                        "ticket_close" => 0,
                                        "ticket_moved" => 0,
                                        "ticket_blocked" => 0,
                                        "ticket_delete" => 0,
                                        "ticket_returnQueue" => 0,
                                        "ticket_reopenTicket" => 0,
                                        "chat_alllist" => 0,
                                        "chat_queue_full_control" => 0,
                                        "chat_admin" => 0,
                                        "chat_transform" => 0,
                                        "chat_open" => 0,
                                        "chat_resolved" => 0,
                                        "chat_close" => 0,
                                        "chat_moved" => 0,
                                        "chat_blocked" => 0,
                                        "chat_delete" => 0,
                                        "company" => 0,
                                        "department" => 0,
                                        "group" => 0,
                                        "agents" => 0,
                                        "integration" => 0,
                                        "client" => 0,
                                        "monitoring" => 0,
                                        "analyze" => 0,
                                    ],
                                ]
                            ]);

                            //----------------------------MODELO 1
                            $companyUser = DB::table('company_user')
                                ->leftjoin('company', 'company_user.company_id', 'company.id')
                                ->select('company.id', 'company_user.is_admin', 'company.name',
                                'company.logo', 'company_user.id as company_user_id', 'company.status', 'company.hash_code', 'company_user.telegram_chat_id')
                                ->where('company_user.user_auth_id', $user->id)
                                ->where('company_user.deleted_at', null)
                                ->where('company.deleted_at', null)
                                ->get();


                            //caso ele deslogue sem criar a primeira company
                            if ($companyUser == []) {
                                session(['is_admin' => '1']);
                                auth()->loginUsingId($user->id);
                                $login['success'] = true;
                                $login['value'] = 'company';
                                echo json_encode($login);
                                return;
                            }

                            foreach ($companyUser as $key) {
                                $key->id = Crypt::encrypt($key->id);

                                //CODIGO PARA ATT LOGIN
                                DB::table('company_user')
                                ->where('id', $key->company_user_id )
                                ->update([
                                    'last_login' => \Carbon\Carbon::now()->toDateTimeString(),
                                ]);

                                $key->company_user_id = Crypt::encrypt($key->company_user_id);
                                if($key->telegram_chat_id != null){
                                    $key->telegram_chat_id = Crypt::encrypt($key->telegram_chat_id);
                                }
                            }


                            //CASO ELE TENHA APENAS UMA COMPANHIA
                            if (count($companyUser) == 1) {
                                if ($companyUser[0]->is_admin) {
                                    //admin master
                                    session(['is_admin' => '1']);
                                    session(['companyselected' => (array)$companyUser[0]]);
                                    auth()->loginUsingId($user->id);
                                    $login['success'] = true;
                                    $login['value'] = 'home';
                                } else {
                                    //atendente
                                    session(['is_admin' => '0']);
                                    session(['companyselected' => (array)$companyUser[0]]);
                                    auth()->loginUsingId($user->id);
                                    $login['success'] = true;
                                    $login['value'] = 'home';
                                }

                                //CRIAR SESSÃO DE DE PERMISSÕES DE GRUPOS
                                $this->getRestrictionsGroupUser(intval(auth()->user()->id), intval(Crypt::decrypt(session('companyselected')['id'])));


                                //CODIGO PARA PEGAR DEPARTAMENTOS NO QUAL O USER PERTENCE!
                                $company_user_company_department_id = CompanyUserCompanyDepartment::join('company_department', 'company_user_company_department.company_department_id', 'company_department.id')
                                    ->join('company_user', 'company_user_company_department.company_user_id', 'company_user.id')
                                    ->select('company_department.id')
                                    ->where('company_user.user_auth_id', Auth::id())
                                    ->where('company_user.company_id', Crypt::decrypt($companyUser[0]->id))
                                    ->where('company_user_company_department.is_active', 1)
                                    ->get();
                                $department = [];

                                foreach ($company_user_company_department_id as $key) {
                                    array_push($department, Crypt::encrypt($key->id));
                                }
                                session(['user_departments_id' => $department]);

                                //CODIGO PARA PEGAR DEPARTAMENTOS NO QUAL O USER PERTENCE!

                                //CODIGO PARA PEGAR DEPARTAMENTOS OS COMPANY_USER_COMPANY_DEPARTMENT DO USER!
                                $company_user_company_department = CompanyUserCompanyDepartment::join('company_department', 'company_user_company_department.company_department_id', 'company_department.id')
                                    ->join('company_user', 'company_user_company_department.company_user_id', 'company_user.id')
                                    ->select('company_department.id as company_department_id', 'company_user_company_department.id as company_user_company_department_id')
                                    ->where('company_user.user_auth_id', Auth::id())
                                    ->where('company_user.company_id', Crypt::decrypt($companyUser[0]->id))
                                    ->get();

                                $cucd_id = [];

                                $i = 0;
                                foreach ($company_user_company_department as $row) {
                                    $cucd_id[$i] = (object) [
                                        'company_department_id' => Crypt::encrypt($row->company_department_id),
                                        'company_user_company_department_id' => Crypt::encrypt($row->company_user_company_department_id)
                                    ];

                                    $i++;
                                }

                                session(['company_user_company_departments' => $cucd_id]);
                                //CODIGO PARA PEGAR DEPARTAMENTOS OS COMPANY_USER_COMPANY_DEPARTMENT DO USER!

                            //CASO ELE NÃO TENHA COMPANHIA
                            } else if (count($companyUser) == 0) {
                                session(['is_admin' => '1']);
                                auth()->loginUsingId($user->id);
                                $login['success'] = true;
                                $login['value'] = 'company';
                            } else { // CASO ELE TENHA MAIS DE UMA
                                if ($companyUser[0]->is_admin) {
                                    session(['is_admin' => '1']);
                                    auth()->loginUsingId($user->id);
                                    $login['success'] = true;
                                    $login['value'] = 'selectCompany';
                                } else {
                                    session(['is_admin' => '0']);
                                    auth()->loginUsingId($user->id);
                                    $login['success'] = true;
                                    $login['value'] = 'selectCompany';
                                }
                            }
                        } else {
                            echo redirect('/login')->withErrors([
                                'message' => 'Email ou senha incorreta!'
                            ]);
                        }
                    } else {
                        $login['success'] = false;
                        $login['value'] = 'not_email';
                    }
                } catch (\Exception $e) {
                    echo $e;
                    Logger::reportException($e, [], ['home-controller', 'login'], false);
                    $login['success'] = false;
                }
            }
            /**
             * Caso necessário, redireciona o usuário
             * para o sistema diretamente do backend
             */
            if (request('access', false) && $login['success']){
                if (!$request->session()->exists('status')) {
                    session(['status' => 'online']);
                }
                return redirect('/select-company');
            }

            echo json_encode($login);
        } else {
            if (Auth::user()) {
                echo redirect('/home');
            }
            
            // else{
            //     if(request('hs') == 1){

            //     }else if (config('app.is_helpdesk')) {
            //         return redirect('https://office.builderall.com/us/office');
            //     }
            // }

            echo view('layouts2.login');
        }
    }

    public function selectCompany(Request $request)
    {
        if ($request->isMethod('post')) {
            try {
                if(request('companyselected')['status'] == 'ACTIVE'){

                    session(['companyselected' => request('companyselected')]);
                    session(['is_admin' => request('companyselected')['is_admin']]);

                    //CODIGO PARA ATT LOGIN
                    DB::table('company_user')
                    ->where('id', Crypt::decrypt(request('companyselected')['company_user_id']))
                    ->update([
                        'last_login' => \Carbon\Carbon::now()->toDateTimeString(),
                        'updated_by' => auth()->user()->id,
                    ]);

                    //CRIAR SESSÃO DE DE PERMISSÕES DE GRUPOS
                    $this->getRestrictionsGroupUser(intval(auth()->user()->id), intval(Crypt::decrypt(session('companyselected')['id'])));

                    //CODIGO PARA PEGAR DEPARTAMENTOS NO QUAL O USER PERTENCE!
                    $company_user_company_department_id = CompanyUserCompanyDepartment::join('company_department', 'company_user_company_department.company_department_id', 'company_department.id')
                        ->join('company_user', 'company_user_company_department.company_user_id', 'company_user.id')
                        ->select('company_department.id')
                        ->where('company_user.user_auth_id', Auth::id())
                        ->where('company_user.company_id', Crypt::decrypt(request('companyselected')['id']))
                        ->where('company_user_company_department.is_active', 1)
                        ->get();
                    $department = [];

                    foreach ($company_user_company_department_id as $key) {
                        array_push($department, Crypt::encrypt($key->id));
                    }
                    session(['user_departments_id' => $department]);
                    //CODIGO PARA PEGAR DEPARTAMENTOS NO QUAL O USER PERTENCE!

                    //CODIGO PARA PEGAR DEPARTAMENTOS OS COMPANY_USER_COMPANY_DEPARTMENT DO USER!
                    $company_user_company_department = CompanyUserCompanyDepartment::join('company_department', 'company_user_company_department.company_department_id', 'company_department.id')
                        ->join('company_user', 'company_user_company_department.company_user_id', 'company_user.id')
                        ->select('company_department.id as company_department_id', 'company_user_company_department.id as company_user_company_department_id')
                        ->where('company_user.user_auth_id', Auth::id())
                        ->where('company_user.company_id', Crypt::decrypt(request('companyselected')['id']))
                        ->get();

                    $cucd_id = [];

                    $i = 0;
                    foreach ($company_user_company_department as $row) {
                        $cucd_id[$i] = (object) [
                            'company_department_id' => Crypt::encrypt($row->company_department_id),
                            'company_user_company_department_id' => Crypt::encrypt($row->company_user_company_department_id)
                        ];

                        $i++;
                    }
                    session(['company_user_company_departments' => $cucd_id]);
                    //CODIGO PARA PEGAR DEPARTAMENTOS OS COMPANY_USER_COMPANY_DEPARTMENT DO USER!


                    $company['value'] = auth()->user()->company;
                    $company['success'] = true;

                }else if(request('companyselected')['status'] == 'INACTIVE'){
                    $company['value'] = 'blocked';
                    $company['success'] = true;
                }
            } catch (Exception $e) {
                // echo $e;
                Logger::reportException($e, [], ['home-controller', 'select-company'], false);
                $company['success'] = false;
            }
            echo json_encode($company);
        } else {
            return view('functions.admin.company.selectCompany');
        }
    }

    public function statusUser(Request $request){
        echo 'id:' . request('id');
        echo '<br>data:' . request('data');

        //?id=21312321

    }

    public function register(Request $request)
    {
        if ($request->isMethod('post')) {

            $request->session()->flush();

            $register['success'] = false;

            try {

                $user = User::where('email', request('email'))->first();

                if ($user == null) {
                    $id = DB::table('user_auth')->insertGetId([
                        'subsidiary_id' => 2,
                        'name' => request('name'),
                        'email' => request('email'),
                        'password' => bcrypt(request('password')),
                        'language' => request('userLang'),
                        'can_create_company' => 1,
                        'hash_code' => Crypt::encrypt(request('email')),
                    ]);

                    session(['is_admin' => 1]);

                    auth()->loginUsingId($id);

                    $register['success'] = true;
                    $register['id'] = Crypt::encrypt($id);
                    $register['created'] = \Carbon\Carbon::now()->toDateTimeString();
                } else {
                    $register['success'] = false;
                    $register['value'] = 'not_email';
                }
            } catch (\Exception $e) {
                //echo $e;
                Logger::reportException($e, [], ['home-controller', 'register'], false);
                $register['success'] = false;
            }

            return json_encode($register);
        } else {
            return view('layouts2.register');
        }
    }

    public function logout(Request $request)
    {
        if(session('is_client') == 1){
            return redirect(route('logout-client'));
        }

        auth()->logout();
        $request->session()->flush();
        return redirect(route('login'));
    }

    public function logoutClient(Request $request)
    {
        if (isset($_SERVER['HTTP_SEC_FETCH_DEST']) && $_SERVER['HTTP_SEC_FETCH_DEST'] == 'iframe') {
            // se tiver em um iframe (popup);
            $route = route('login-client-new', ['company' => session('companyselected')['hash_code']]);
        } else {
            $route = isset($_COOKIE['client_logout_url']) ? $_COOKIE['client_logout_url'] : '';
        }

        auth()->logout();
        $request->session()->flush();

        if ($route) {
            return redirect($route);
        }

        return response()->view('errors.404', [], 404);
    }


    public function checkLoginUser($hash)
    {

        echo $hash;
        ///login/company?email="marlos_gpi@live.com"&force_login=1&fun
    }

    public function getSummaryCardsHomeDashboard()
    {
        try {
            $sql = "
                SELECT
                    'Ticket' as `type`,
                    IFNULL(SUM(IF(ut.id IS NULL AND t.status = 'OPENED', 1, 0)), 0) as qtd_opened,
                    IFNULL(SUM(IF(ut.id IS NOT NULL AND t.status = 'IN_PROGRESS', 1, 0)), 0) as qtd_in_progress
                FROM
                    ticket AS t
                    LEFT JOIN user_ticket as ut
                        ON t.id = ut.ticket_id
                WHERE
                    t.company_id = ?
                    AND t.`status` IN ('OPENED', 'IN_PROGRESS')
                    AND t.deleted_at IS NULL
                UNION ALL
                SELECT
                    'Chat' as `type`,
                    IFNULL(SUM(IF(comp_user_comp_depart_id_current IS NULL AND status = 'OPENED', 1, 0)), 0) as qtd_opened,
                    IFNULL(SUM(IF(comp_user_comp_depart_id_current IS NOT NULL AND status = 'IN_PROGRESS', 1, 0)), 0) as qtd_in_progress
                FROM
                    chat
                WHERE
                    company_id = ?
                    AND ticket_id IS NULL
                    AND deleted_at IS NULL
                    AND (status = 'OPENED' OR status = 'IN_PROGRESS');
            ";
            $res = DB::select($sql, [
                Crypt::decrypt(request('company_id')),
                Crypt::decrypt(request('company_id'))
            ]);

            $aux = [];
            if(!empty($res) && isset($res[0]) && isset($res[1]) ) {
                for ($i = 0; $i < count($res); $i++) {
                    $res[$i]->qtd_opened = intval($res[$i]->qtd_opened);
                    $res[$i]->qtd_in_progress = intval($res[$i]->qtd_in_progress);
                }
                $aux['ticket'] = (array) $res[0];
                $aux['chat'] = (array) $res[1];
            } else {
                $aux['ticket'] = [
                    "type" => "Ticket",
                    "qtd_opened" => 0,
                    "qtd_in_progress" => 0
                ];
                $aux['chat'] = [
                    "type" => "Chat",
                    "qtd_opened" => 0,
                    "qtd_in_progress" => 0
                ];
            }
            $res = $aux;

            $res['success'] = true;

            return json_encode($res);
        } catch (\Exception $e) {
            echo $e;
            Logger::reportException($e, [], ['home-controller', 'getSummaryCardsHomeDashboard'], false);
            $res['success'] = false;
        }
    }

    public function getProgressCardsHomeDashboard()
    {
        try {
            $sql = "
                SELECT
                    'Chat' as type,
                    SUM(if(`status` IN ('CLOSED','RESOLVED') AND date(update_status_closed_resolved) = DATE_SUB(curdate(), INTERVAL 1 DAY), 1, 0)) as fechado_ontem,
                    SUM(if(`status` IN ('CLOSED','RESOLVED') AND date(update_status_closed_resolved) = curdate(), 1, 0)) as fechado_hoje,
                    SUM(if(`status` != 'CANCELED' AND  DATE(created_at) < curdate(), 1, 0)) - SUM(if(`status` IN ('CLOSED', 'RESOLVED') AND DATE(created_at) < curdate() AND DATE(update_status_closed_resolved) < DATE_SUB(curdate(), INTERVAL 1 DAY), 1, 0)) as total_ontem,
                    SUM(if(`status` != 'CANCELED' AND  DATE(created_at) <= curdate(), 1, 0)) - SUM(if(`status` IN ('CLOSED', 'RESOLVED') AND DATE(created_at) <= curdate() AND DATE(update_status_closed_resolved) <= DATE_SUB(curdate(), INTERVAL 1 DAY), 1, 0)) as total_hoje
                FROM
                    chat
                WHERE
                    company_id = ?
                    AND deleted_at IS NULL
                UNION ALL
                SELECT
                    'Ticket' as type,
                    SUM(if(`status` IN ('CLOSED','RESOLVED') AND date(update_status_closed_resolved) = DATE_SUB(curdate(), INTERVAL 1 DAY), 1, 0)) as fechado_ontem,
                    SUM(if(`status` IN ('CLOSED','RESOLVED') AND date(update_status_closed_resolved) = curdate(), 1, 0)) as fechado_hoje,
                    SUM(if(`status` != 'CANCELED' AND  DATE(created_at) < curdate(), 1, 0)) - SUM(if(`status` IN ('CLOSED', 'RESOLVED') AND DATE(created_at) < curdate() AND DATE(update_status_closed_resolved) < DATE_SUB(curdate(), INTERVAL 1 DAY), 1, 0)) as total_ontem,
                    SUM(if(`status` != 'CANCELED' AND  DATE(created_at) <= curdate(), 1, 0)) - SUM(if(`status` IN ('CLOSED', 'RESOLVED') AND DATE(created_at) <= curdate() AND DATE(update_status_closed_resolved) <= DATE_SUB(curdate(), INTERVAL 1 DAY), 1, 0)) as total_hoje
                FROM
                    ticket
                WHERE
                    company_id = ?
                    AND deleted_at IS NULL;
            ";
            $res = DB::select($sql, [
                Crypt::decrypt(request('company_id')),
                Crypt::decrypt(request('company_id'))
            ]);

            $aux = [];
            if(!empty($res) && isset($res[0]) && isset($res[1]) ) {
                for ($i = 0; $i < count($res); $i++) {
                    $res[$i]->fechado_ontem = intval($res[$i]->fechado_ontem);
                    $res[$i]->fechado_hoje = intval($res[$i]->fechado_hoje);

                    $res[$i]->total_ontem = intval($res[$i]->total_ontem);
                    $res[$i]->total_hoje = intval($res[$i]->total_hoje);
                }
                $aux['ticket'] = (array) $res[1];
                $aux['chat'] = (array) $res[0];
            } else {
                $aux['ticket'] = [
                    'fechado_hoje' => 0,
                    'fechado_ontem' => 0,
                    'total_hoje' => 0,
                    'total_ontem' => 0,
                    'type' => "Ticket"
                ];
                $aux['chat'] = [
                    'fechado_hoje' => 0,
                    'fechado_ontem' => 0,
                    'total_hoje' => 0,
                    'total_ontem' => 0,
                    'type' => "Chat"
                ];
            }
            $res = $aux;

            $res['success'] = true;

            return json_encode($res);
        } catch (\Exception $e) {
            //echo $e;
            Logger::reportException($e, [], ['home-controller', 'getProgressCardsHomeDashboard'], false);
            $res['success'] = false;
        }
    }

    public function getBarChartHomeDashboard()
    {
        try {
            if (request('period') == 'week') {
                $sql = "
                    SELECT
                        'chat' as tipo,
                        DATE_FORMAT(created_at, '%d-%m') as dia,
                        DATE_FORMAT(created_at, '%d') as `dia`,
                        DATE_FORMAT(created_at, '%m') as `mes`,
                        DATE_FORMAT(created_at, '%Y') as `ano`,
                        COUNT(id) as qtd
                    FROM
                        chat
                    WHERE
                        company_id = ?
                        AND deleted_at IS NULL
                        AND created_at between DATE_SUB(curdate(), INTERVAL DAYOFWEEK(curdate())-1 DAY) AND curdate()
                    group by 1, 2
                    UNION ALL
                    SELECT
                        'ticket' as tipo,
                        DATE_FORMAT(created_at, '%d-%m') as dia,
                        DATE_FORMAT(created_at, '%d') as `dia`,
                        DATE_FORMAT(created_at, '%m') as `mes`,
                        DATE_FORMAT(created_at, '%Y') as `ano`,
                        COUNT(id) as qtd
                    FROM
                        ticket
                    WHERE
                        company_id = ?
                        AND deleted_at IS NULL
                        AND created_at between DATE_SUB(curdate(), INTERVAL DAYOFWEEK(curdate())-1 DAY) AND curdate()
                    group by 1, 2;
                ";

                $res = DB::select($sql, [
                    Crypt::decrypt(request('company_id')),
                    Crypt::decrypt(request('company_id'))
                ]);

                $aux = [
                    'labels' => [],
                    'chats' => [],
                    'tickets' => [],
                    'success' => false
                ];

                $now = date_create();
                $first_day_of_week = date_sub(date_create(), date_interval_create_from_date_string(date('w', date_timestamp_get(date_create())) . " days"));
                $interations = intval(date('w', date_timestamp_get(date_create())));
                for ($i = 0; $i <= $interations; $i++) {
                    $aux['labels'][date_format($first_day_of_week, 'Ymd')] = [
                        'dia' => date_format($first_day_of_week, 'd'),
                        'mes' => date_format($first_day_of_week, 'm'),
                        'ano' => date_format($first_day_of_week, 'Y'),
                    ];
                    $aux['chats'][date_format($first_day_of_week, 'Ymd')] = 0;
                    $aux['tickets'][date_format($first_day_of_week, 'Ymd')] = 0;

                    $first_day_of_week = date_add($first_day_of_week, date_interval_create_from_date_string("1 days"));
                }

                if(!empty($res)) {
                    for ($i = 0; $i < count($res); $i++) {
                        if ($res[$i]->tipo == 'chat') {
                            $dt = $res[$i]->ano . $res[$i]->mes . $res[$i]->dia;
                            $aux['chats'][$dt] = intval($res[$i]->qtd);
                        } else {
                            $dt = $res[$i]->ano . $res[$i]->mes . $res[$i]->dia;
                            $aux['tickets'][$dt] = intval($res[$i]->qtd);
                        }
                    }
                }

                $aux['labels'] = array_values($aux['labels']);
                $aux['chats'] = array_values($aux['chats']);
                $aux['tickets'] = array_values($aux['tickets']);

                $res = $aux;
            } else if (request('period') == 'month') {

                $sql = "
                    SELECT
                        `type`,
                        semana,
                        count(*) as qtd
                    FROM (
                        SELECT
                            'chat' as `type`,
                            (CASE
                                WHEN (DATEDIFF(CURDATE(), created_at) + 1) BETWEEN 1 AND 7 THEN 4
                                WHEN (DATEDIFF(CURDATE(), created_at) + 1) BETWEEN 8 AND 14 THEN 3
                                WHEN (DATEDIFF(CURDATE(), created_at) + 1) BETWEEN 15 AND 21 THEN 2
                                WHEN (DATEDIFF(CURDATE(), created_at) + 1) BETWEEN 22 AND 28 THEN 1
                            END) AS semana
                        FROM
                            chat
                        WHERE
                            company_id = ?
                            AND `status` != 'CANCELED'
                            AND created_at >= DATE_SUB(curdate(), INTERVAL 27 DAY)
                            AND deleted_at IS NULL
                        UNION ALL
                        SELECT
                            'ticket' as `type`,
                            (CASE
                                WHEN (DATEDIFF(CURDATE(), created_at) + 1) BETWEEN 1 AND 7 THEN 4
                                WHEN (DATEDIFF(CURDATE(), created_at) + 1) BETWEEN 8 AND 14 THEN 3
                                WHEN (DATEDIFF(CURDATE(), created_at) + 1) BETWEEN 15 AND 21 THEN 2
                                WHEN (DATEDIFF(CURDATE(), created_at) + 1) BETWEEN 22 AND 28 THEN 1
                            END) AS semana
                        FROM
                            ticket
                        WHERE
                            company_id = ?
                            AND `status` != 'CANCELED'
                            AND created_at >= DATE_SUB(curdate(), INTERVAL 27 DAY)
                            AND deleted_at IS NULL
                    ) sub
                    GROUP BY `type`, semana
                    ORDER BY semana, `type`;
                ";
                $res = DB::select($sql, [
                    Crypt::decrypt(request('company_id')),
                    Crypt::decrypt(request('company_id'))
                ]);

                $aux = [
                    'labels' => [1, 2, 3, 4],
                    'chats' => [0, 0, 0, 0],
                    'tickets' => [0, 0, 0, 0],
                    'success' => false
                ];

                if(!empty($res)) {
                    for ($i = 0; $i < count($res); $i++) {
                        $index = intval($res[$i]->semana) - 1;
                        if ($res[$i]->type == 'chat') {
                            $aux['chats'][$index] = intval($res[$i]->qtd);
                        } else {
                            $aux['tickets'][$index] = intval($res[$i]->qtd);
                        }
                    }
                }
                $res = $aux;
            } else { // ano
                $sql = "
                    SELECT
                        'chat' as tipo,
                        DATE_FORMAT(created_at, '%m-%Y') AS `mes_ano`,
                        DATE_FORMAT(created_at, '%m') as `mes`,
                        DATE_FORMAT(created_at, '%Y') as `ano`,
                        COUNT(id) as qtd
                    FROM
                        chat
                    WHERE
                        company_id = ?
                        AND deleted_at IS NULL
                        AND extract(YEAR FROM created_at) = extract(YEAR FROM curdate())
                    group by 1, 2
                    UNION ALL
                    SELECT
                        'ticket' as tipo,
                        DATE_FORMAT(created_at, '%m-%Y') AS `mes_ano`,
                        DATE_FORMAT(created_at, '%m') as `mes`,
                        DATE_FORMAT(created_at, '%Y') as `ano`,
                        COUNT(id) as qtd
                    FROM
                        ticket
                    WHERE
                        company_id = ?
                        AND deleted_at IS NULL
                        AND extract(YEAR FROM created_at) = extract(YEAR FROM curdate())
                    group by 1, 2;
                ";
                $res = DB::select($sql, [
                    Crypt::decrypt(request('company_id')),
                    Crypt::decrypt(request('company_id'))
                ]);

                $aux = [
                    'labels' => [],
                    'chats' => [],
                    'tickets' => [],
                    'success' => false
                ];

                $current_month = intval(date_format(date_create(), 'n'));
                $current_year = date_format(date_create(), 'Y');
                for ($i = 1; $i <= $current_month; $i++) {
                    $mes = $i < 10 ? '0' . $i : $i;
                    $aux['labels'][$current_year . $mes] = [
                        'mes' => $mes,
                        'ano' => $current_year,
                    ];
                    $aux['chats'][$current_year . $mes] = 0;
                    $aux['tickets'][$current_year . $mes] = 0;
                }

                if(!empty($res)) {
                    for ($i = 0; $i < count($res); $i++) {
                        if ($res[$i]->tipo == 'chat') {
                            $dt = $res[$i]->ano . $res[$i]->mes;
                            $aux['chats'][$dt] = intval($res[$i]->qtd);
                        } else {
                            $dt = $res[$i]->ano . $res[$i]->mes;
                            $aux['tickets'][$dt] = intval($res[$i]->qtd);
                        }
                    }
                }

                $aux['labels'] = array_values($aux['labels']);
                $aux['chats'] = array_values($aux['chats']);
                $aux['tickets'] = array_values($aux['tickets']);

                $res = $aux;
            }

            $res['success'] = true;

            return json_encode($res);
        } catch (\Exception $e) {
            //echo $e;
            Logger::reportException($e, [], ['home-controller', 'getProgressCardsHomeDashboard'], false);
            $res['success'] = false;
        }
    }

    public function getSummaryCardsAgentHomeDashboard(Request $request) {
		try {
            // https://prnt.sc/X3q_lJRpRcdF
            $sql = "CALL pro_dashboard_details_numbers_attendant(?, ?, ?, ?, ?, ?);";

			$res = DB::select($sql, [
				Crypt::decrypt(request('company_id')),
				Crypt::decrypt(request('attendant_id')),
				request('department_id') === 0 ? NULL : Crypt::decrypt(request('department_id')),
				request('initial_date') == NULL ? NULL : request('initial_date'),
				request('final_date') == NULL ? NULL : request('final_date'),
                0
			]);

			$aux = [
				[
					'type' => "chat",
					'avg_queue_time' => 0,
					'avg_service_time' => 0,
					'changed_to_ticket' => 0,
					'closed' => 0,
					'count' => 0,
					'in_progress' => 0,
					'resolved' => 0
				],
				[
					'type' => "ticket",
					'avg_queue_time' => 0,
					'avg_service_time' => 0,
					'closed' => 0,
					'count' => 0,
					'in_progress' => 0,
					'resolved' => 0
				]
			];

			if(!empty($res)) {
				foreach($aux as $i => &$item) {
					foreach($item as $j => &$value){
						if( $j == 'type' ) {
							$value = strtolower(( (array) $res[$i] )[$j]);
						} else {
							$value = intval(( (array) $res[$i] )[$j]);
						}
					}
				}
			}
			$res = $aux;

			$res['success'] = true;

            return json_encode($res);
		} catch (\Exception $e) {
            echo $e;
            Logger::reportException($e, [], ['home-controller', 'getSummaryCardsAgentHomeDashboard'], false);
            $res['success'] = false;
        }
    }

    public function getQualitativeCardsAgentHomeDashboard(Request $request) {
		try {
			$sql = "CALL pro_dashboard_details_average_attendants_attendances(?, ?, ?, ?, ?, ?);";

			$res = DB::select($sql, [
				Crypt::decrypt(request('company_id')),
				Crypt::decrypt(request('attendant_id')),
				request('department_id') === 0 ? 0 : Crypt::decrypt(request('department_id')),
				request('initial_date') == null ? 0 : request('initial_date'),
				request('final_date') == null ? 0 : request('final_date'),
                0
			]);

			$res['success'] = true;

            return json_encode($res);
		} catch (\Exception $e) {
            echo $e;
            Logger::reportException($e, [], ['home-controller', 'getQualitativeCardsAgentHomeDashboard'], false);
            $res['success'] = false;
        }
    }

    public function getBarChartAgentHomeDashboard(Request $request) {
        try {

			$sql = "CALL pro_dashboard_attendant_tickets_chats_opened_x_closed(?, ?, ?, ?, ?)";

			$res = DB::select($sql, [
				Crypt::decrypt(request('company_id')),
				request('department_id') === 0 ? 0 : Crypt::decrypt(request('department_id')),
				request('attendant_id') === 0 ? 0 : Crypt::decrypt(request('attendant_id')),
				strtoupper(request('period')),
                0
			]);

			if(request('period') == 'week') {
				$aux = [
                    'labels' => [],
					'chats_opened' => [],
					'chats_closed' => [],
					'tickets_opened' => [],
					'tickets_closed' => [],
                    'success' => false
				];

				$first_day_of_week = date_sub(date_create(), date_interval_create_from_date_string( date('w', date_timestamp_get(date_create()))." days") );
                $interations = intval(date('w', date_timestamp_get(date_create())));
                for($i = 0; $i <= $interations; $i++) {
                    $aux['labels'][date_format($first_day_of_week, 'Ymd')] = [
                        'dia' => date_format($first_day_of_week, 'd'),
                        'mes' => date_format($first_day_of_week, 'm'),
                        'ano' => date_format($first_day_of_week, 'Y'),
                    ];
					$aux['chats_opened'][date_format($first_day_of_week, 'Ymd')] = 0;
					$aux['chats_closed'][date_format($first_day_of_week, 'Ymd')] = 0;
					$aux['tickets_opened'][date_format($first_day_of_week, 'Ymd')] = 0;
					$aux['tickets_closed'][date_format($first_day_of_week, 'Ymd')] = 0;

                    $first_day_of_week = date_add($first_day_of_week, date_interval_create_from_date_string( "1 days") );
				}

				if(!empty($res)) {
					for($i = 0; $i < count($res); $i++) {
						$dt = $res[$i]->year.($res[$i]->month < 10 ? '0'.$res[$i]->month :  $res[$i]->month).($res[$i]->day < 10 ? '0'.$res[$i]->day : $res[$i]->day );
						switch($res[$i]->type) {
							case 'Chats_Opened':
								$aux['chats_opened'][$dt] = intval($res[$i]->count);
								break;
							case 'Chats_Closed':
								$aux['chats_closed'][$dt] = intval($res[$i]->count);
								break;
							case 'Tickets_Opened':
								$aux['tickets_opened'][$dt] = intval($res[$i]->count);
								break;
							case 'Tickets_Closed':
								$aux['tickets_closed'][$dt] = intval($res[$i]->count);
								break;
						}
					}
				}

                $aux['labels'] = array_values($aux['labels']);
				$aux['chats_opened'] = array_values($aux['chats_opened']);
				$aux['chats_closed'] = array_values($aux['chats_closed']);
				$aux['tickets_opened'] = array_values($aux['tickets_opened']);
				$aux['tickets_closed'] = array_values($aux['tickets_closed']);

				$res = $aux;
			} else if(request('period') == 'month') {
				$aux = [
                    'labels' => [1,2,3,4],
					'chats_opened' => [0,0,0,0],
					'chats_closed' => [0,0,0,0],
					'tickets_opened' => [0,0,0,0],
					'tickets_closed' => [0,0,0,0],
                    'success'=> false
				];

				if(!empty($res)) {
					for($i = 0; $i < count($res); $i++){
						$index = intval($res[$i]->week)-1;
						switch($res[$i]->type ){
							case 'Chats_Opened':
								$aux['chats_opened'][$index] = intval($res[$i]->count);
								break;
							case 'Chats_Closed':
								$aux['chats_closed'][$index] = intval($res[$i]->count);
								break;
							case 'Tickets_Opened':
								$aux['tickets_opened'][$index] = intval($res[$i]->count);
								break;
							case 'Tickets_Closed':
								$aux['tickets_closed'][$index] = intval($res[$i]->count);
								break;
						}
					}
				}

				$res = $aux;
			} else {
				$aux = [
                    'labels' => [],
					'chats_opened' => [],
					'chats_closed' => [],
					'tickets_opened' => [],
					'tickets_closed' => [],
                    'success' => false
                ];

                $current_month = intval(date_format(date_create(), 'n'));
                $current_year = date_format(date_create(), 'Y');
                for($i = 1; $i <= $current_month; $i++) {
                    $mes = $i < 10 ? '0'.$i : $i;
                    $aux['labels'][$current_year.$mes] = [
                        'mes' => $mes,
                        'ano' => $current_year,
                    ];
					$aux['chats_opened'][$current_year.$mes] = 0;
					$aux['chats_closed'][$current_year.$mes] = 0;
					$aux['tickets_opened'][$current_year.$mes] = 0;
					$aux['tickets_closed'][$current_year.$mes] = 0;
                }

				if(!empty($res)) {
					for($i = 0; $i < count($res); $i++) {
						$dt = $res[$i]->year.($res[$i]->month < 10 ? '0'.$res[$i]->month : $res[$i]->month);
						switch($res[$i]->type){
							case 'Chats_Opened':
								$aux['chats_opened'][$dt] = intval($res[$i]->count);
								break;
							case 'Chats_Closed':
								$aux['chats_closed'][$dt] = intval($res[$i]->count);
								break;
							case 'Tickets_Opened':
								$aux['tickets_opened'][$dt] = intval($res[$i]->count);
								break;
							case 'Tickets_Closed':
								$aux['tickets_closed'][$dt] = intval($res[$i]->count);
								break;
						}
					}
				}

                $aux['labels'] = array_values($aux['labels']);
				$aux['chats_opened'] = array_values($aux['chats_opened']);
				$aux['chats_closed'] = array_values($aux['chats_closed']);
				$aux['tickets_opened'] = array_values($aux['tickets_opened']);
				$aux['tickets_closed'] = array_values($aux['tickets_closed']);

				$res = $aux;
			}

			$res['success'] = true;

            return json_encode($res);
		} catch (\Exception $e) {
            //echo $e;
            Logger::reportException($e, [], ['home-controller', 'getBarChartAgentHomeDashboard'], false);
            $res['success'] = false;
        }
    }

    public function getLineChartAgentHomeDashboard(Request $request) {
        try {

			$sql = "CALL pro_dashboard_attendant_tickets_chats_resolved(?, ?, ?, ?, ?)";

			$res = DB::select($sql, [
				intval( Crypt::decrypt(request('company_id')) ),
				request('department_id') === 0 ? 0 : intval( Crypt::decrypt(request('department_id')) ),
				request('attendant_id') === 0 ? 0 : Crypt::decrypt(request('attendant_id')),
				strtoupper(request('period')),
                0
			]);

			if(request('period') == 'week') {
				$aux = [
                    'labels' => [],
					'attendants' => [],
                    'success' => false
				];

				$first_day_of_week = date_sub(date_create(), date_interval_create_from_date_string( date('w', date_timestamp_get(date_create()))." days") );
                $interations = intval(date('w', date_timestamp_get(date_create())));

				for($i = 0; $i <= $interations; $i++) {
					$aux['labels'][date_format($first_day_of_week, 'Ymd')] = [
						'dia' => date_format($first_day_of_week, 'd'),
						'mes' => date_format($first_day_of_week, 'm'),
						'ano' => date_format($first_day_of_week, 'Y'),
					];


					$aux['attendants'][ request('attendant_id') ][ date_format($first_day_of_week, 'Ymd') ] = 0;

                    $first_day_of_week = date_add($first_day_of_week, date_interval_create_from_date_string( "1 days") );
				}

				if(!empty($res)) {
					for($i = 0; $i < count($res); $i++) {
						$dt = $res[$i]->year.($res[$i]->month < 10 ? '0'.$res[$i]->month :  $res[$i]->month).($res[$i]->day < 10 ? '0'.$res[$i]->day : $res[$i]->day );

						$aux['attendants'][request('attendant_id')][$dt] = intval($res[$i]->count);
					}
				}

				$aux['labels'] = array_values($aux['labels']);
				$aux['attendants'][ request('attendant_id') ] = array_values($aux['attendants'][ request('attendant_id') ]);

				$res = $aux;
			} else if(request('period') == 'month') {
				$aux = [
                    'labels' => [1,2,3,4],
					'attendants' => [],
                    'success'=> false
				];

				$aux['attendants'][ request('attendant_id') ] = [0, 0, 0, 0];

				if(!empty($res)) {
					for($i = 0; $i < count($res); $i++){
						$index = intval($res[$i]->week)-1;
						$aux['attendants'][ request('attendant_id') ][$index] = intval($res[$i]->count);
					}
				}

                $res = $aux;
			} else {
				$aux = [
                    'labels' => [],
					'attendants' => [],
                    'success' => false
                ];

                $current_month = intval(date_format(date_create(), 'n'));
                $current_year = date_format(date_create(), 'Y');
                for($i = 1; $i <= $current_month; $i++) {
                    $mes = $i < 10 ? '0'.$i : $i;
                    $aux['labels'][$current_year.$mes] = [
                        'mes' => $mes,
                        'ano' => $current_year,
                    ];

					$aux['attendants'][ request('attendant_id') ][ $current_year.$mes ] = 0;
                }

				if(!empty($res)) {
					for($i = 0; $i < count($res); $i++) {
						$dt = $res[$i]->year.($res[$i]->month < 10 ? '0'.$res[$i]->month : $res[$i]->month);
						$aux['attendants'][ request('attendant_id') ][$dt] = intval($res[$i]->count);
					}
				}

                $aux['labels'] = array_values($aux['labels']);
				$aux['attendants'][ request('attendant_id') ] = array_values($aux['attendants'][ request('attendant_id') ]);

                $res = $aux;
			}

			$res['success'] = true;

            return json_encode($res);
		} catch (\Exception $e) {
            echo $e;
            Logger::reportException($e, [], ['home-controller', 'getLineChartAgentHomeDashboard'], false);
            $res['success'] = false;
        }
    }

    public function getRestrictionsGroupUser($user_id, $company_id)
    {
        //CODIGO PARA PEGAR GRUPO NO QUAL O USER PERTENCE!
        $arrayName = array(
            'user' => $user_id,
            'company' => $company_id,
        );

        $query = "SELECT
                    MAX(`ticket_alllist`) AS `ticket_alllist`,
                    MAX(`ticket_admin`) AS `ticket_admin`,
                    MAX(`ticket_create`) AS `ticket_create`,
                    MAX(`ticket_open`) AS `ticket_open`,
                    MAX(`ticket_resolved`) AS `ticket_resolved`,
                    MAX(`ticket_close`) AS `ticket_close`,
                    MAX(`ticket_moved`) AS `ticket_moved`,
                    MAX(`ticket_blocked`) AS `ticket_blocked`,
                    MAX(`ticket_delete`) AS `ticket_delete`,
                    MAX(`ticket_returnQueue`) AS `ticket_returnQueue`,
                    MAX(`ticket_reopenTicket`) AS `ticket_reopenTicket`,
                    MAX(`chat_admin`) AS `chat_admin`,
                    MAX(`chat_alllist`) AS `chat_alllist`,
                    MAX(`chat_queue_full_control`) AS `chat_queue_full_control`,
                    MAX(`chat_transform`) AS `chat_transform`,
                    MAX(`chat_open`) AS `chat_open`,
                    MAX(`chat_resolved`) AS `chat_resolved`,
                    MAX(`chat_close`) AS `chat_close`,
                    MAX(`chat_moved`) AS `chat_moved`,
                    MAX(`chat_blocked`) AS `chat_blocked`,
                    MAX(`chat_delete`) AS `chat_delete`,
                    MAX(`company`) AS `company`,
                    MAX(`department`) AS `department`,
                    MAX(`group`) AS `group`,
                    MAX(`agents`) AS `agents`,
                    MAX(`integration`) AS `integration`,
                    MAX(`client`) AS `client`,
                    MAX(`monitoring`) AS `monitoring`,
                    MAX(`analyze`) AS `analyze`
                FROM (
                    SELECT company_user.company_id, user_group.settings,
                        IF(JSON_EXTRACT(settings, '$.permissions.ticket.admin') = 'true', 1, 0) AS `ticket_admin`,
                        IF(JSON_EXTRACT(settings, '$.permissions.ticket.alllist') = 'true', 1, 0) AS `ticket_alllist`,
                        IF(JSON_EXTRACT(settings, '$.permissions.ticket.create') = 'true', 1, 0) AS `ticket_create`,
                        IF(JSON_EXTRACT(settings, '$.permissions.ticket.open') = 'true', 1, 0) AS `ticket_open`,
                        IF(JSON_EXTRACT(settings, '$.permissions.ticket.resolved') = 'true', 1, 0) AS `ticket_resolved`,
                        IF(JSON_EXTRACT(settings, '$.permissions.ticket.close') = 'true', 1, 0) AS `ticket_close`,
                        IF(JSON_EXTRACT(settings, '$.permissions.ticket.moved') = 'true', 1, 0) AS `ticket_moved`,
                        IF(JSON_EXTRACT(settings, '$.permissions.ticket.blocked') = 'true', 1, 0) AS `ticket_blocked`,
                        IF(JSON_EXTRACT(settings, '$.permissions.ticket.delete') = 'true', 1, 0) AS `ticket_delete`,
                        IF(JSON_EXTRACT(settings, '$.permissions.ticket.returnQueue') = 'true', 1, 0) AS `ticket_returnQueue`,
                        IF(JSON_EXTRACT(settings, '$.permissions.ticket.reopenTicket') = 'true', 1, 0) AS `ticket_reopenTicket`,
                        IF(JSON_EXTRACT(settings, '$.permissions.chat.admin') = 'true', 1, 0) AS `chat_admin`,
                        IF(JSON_EXTRACT(settings, '$.permissions.chat.alllist') = 'true', 1, 0) AS `chat_alllist`,
                        IF(JSON_EXTRACT(settings, '$.permissions.chat.queue_full_control') = 'true', 1, 0) AS `chat_queue_full_control`,
                        IF(JSON_EXTRACT(settings, '$.permissions.chat.transform') = 'true', 1, 0) AS `chat_transform`,
                        IF(JSON_EXTRACT(settings, '$.permissions.chat.open') = 'true', 1, 0) AS `chat_open`,
                        IF(JSON_EXTRACT(settings, '$.permissions.chat.resolved') = 'true', 1, 0) AS `chat_resolved`,
                        IF(JSON_EXTRACT(settings, '$.permissions.chat.close') = 'true', 1, 0) AS `chat_close`,
                        IF(JSON_EXTRACT(settings, '$.permissions.chat.moved') = 'true', 1, 0) AS `chat_moved`,
                        IF(JSON_EXTRACT(settings, '$.permissions.chat.blocked') = 'true', 1, 0) AS `chat_blocked`,
                        IF(JSON_EXTRACT(settings, '$.permissions.chat.delete') = 'true', 1, 0) AS `chat_delete`,
                        IF(JSON_EXTRACT(settings, '$.permissions.company.view') = 'true', 1, 0) AS `company`,
                        IF(JSON_EXTRACT(settings, '$.permissions.department.view') = 'true', 1, 0) AS `department`,
                        IF(JSON_EXTRACT(settings, '$.permissions.group.view') = 'true', 1, 0) AS `group`,
                        IF(JSON_EXTRACT(settings, '$.permissions.agents.view') = 'true', 1, 0) AS `agents`,
                        IF(JSON_EXTRACT(settings, '$.permissions.integration.view') = 'true', 1, 0) AS `integration`,
                        IF(JSON_EXTRACT(settings, '$.permissions.client.view') = 'true', 1, 0) AS `client`,
                        IF(JSON_EXTRACT(settings, '$.permissions.monitoring.view') = 'true', 1, 0) AS `monitoring`,
                        IF(JSON_EXTRACT(settings, '$.permissions.analyze.view') = 'true', 1, 0) AS `analyze`
                            FROM user_group
                            JOIN company_user_user_group ON user_group.id = company_user_user_group.user_group_id
                            JOIN company_user ON company_user_user_group.company_user_id = company_user.id
                            WHERE company_user.company_id = :company
                            AND company_user.user_auth_id = :user
                        ) sub";

        $settings = DB::select($query, $arrayName);

        if(session('is_admin') == 1){
            session([
                'restriction' => $array = [
                    0 => (object) [
                        "ticket_alllist" => 1,
                        "ticket_admin" => 1,
                        "ticket_create" => 1,
                        "ticket_open" => 1,
                        "ticket_resolved" => 1,
                        "ticket_close" => 1,
                        "ticket_moved" => 1,
                        "ticket_blocked" => 1,
                        "ticket_delete" => 1,
                        "ticket_returnQueue" => 1,
                        "ticket_reopenTicket" => 1,
                        "chat_alllist" => 1,
                        "chat_queue_full_control" => 1,
                        "chat_admin" => 1,
                        "chat_transform" => 1,
                        "chat_open" => 1,
                        "chat_resolved" => 1,
                        "chat_close" => 1,
                        "chat_moved" => 1,
                        "chat_blocked" => 1,
                        "chat_delete" => 1,
                        "company" => 1,
                        "department" => 1,
                        "group" => 1,
                        "agents" => 1,
                        "integration" => 1,
                        "client" => 1,
                        "monitoring" => 1,
                        "analyze" => 1,
                    ],
                ]
            ]);
        }else if($settings[0]->ticket_admin || $settings[0]->chat_admin){
            session([
                'restriction' => $array = [
                    0 =>  (object) [
                        "ticket_alllist" => $settings[0]->ticket_admin,
                        "ticket_admin" => $settings[0]->ticket_admin,
                        "ticket_create" => $settings[0]->ticket_admin,
                        "ticket_open" => $settings[0]->ticket_admin,
                        "ticket_resolved" => $settings[0]->ticket_admin,
                        "ticket_close" => $settings[0]->ticket_admin,
                        "ticket_moved" => $settings[0]->ticket_admin,
                        "ticket_blocked" => $settings[0]->ticket_admin,
                        "ticket_delete" => $settings[0]->ticket_admin,
                        "ticket_returnQueue" => $settings[0]->ticket_admin,
                        "ticket_reopenTicket" => $settings[0]->ticket_admin,
                        "chat_alllist" => $settings[0]->chat_admin,
                        "chat_queue_full_control" => $settings[0]->chat_admin,
                        "chat_admin" => $settings[0]->chat_admin,
                        "chat_transform" => $settings[0]->chat_admin,
                        "chat_open" => $settings[0]->chat_admin,
                        "chat_resolved" => $settings[0]->chat_admin,
                        "chat_close" => $settings[0]->chat_admin,
                        "chat_moved" => $settings[0]->chat_admin,
                        "chat_blocked" => $settings[0]->chat_admin,
                        "chat_delete" => $settings[0]->chat_admin,
                        "company" => $settings[0]->company,
                        "department" => $settings[0]->department,
                        "group" => $settings[0]->group,
                        "agents" => $settings[0]->agents,
                        "integration" => $settings[0]->integration,
                        "client" => $settings[0]->client,
                        "monitoring" => $settings[0]->monitoring,
                        "analyze" => $settings[0]->analyze,
                    ],
                ]
            ]);
        }else{
            if ($settings != '[]') {
                session(['restriction' => $settings]);
            }
        }
        //CODIGO PARA PEGAR GRUPO NO QUAL O USER PERTENCE!
    }

    public function getInfomationCheckout($cscode){
        $result = Checkout::parse_cscode($cscode);
        echo json_encode($result);
    }

    public function AnyConfigTerms(Request $request){

        if ($request->isMethod('get')) {

            try {
                $term['success'] = false;

                $term['result'] = DB::table('user_auth')
                    ->where('id', auth()->user()->id)
                    ->select('id', 'terms_user')
                    ->first();

                $term['success'] = true;
            } catch (\Exception $e) {
                //echo $e;
                Logger::reportException($e, [], ['any-Config-Terms', 'register'], false);
                $term['success'] = false;
            }
            return $term;
        }else{
            try {
                $term['success'] = false;
                DB::table('user_auth')
                ->where('id', auth()->user()->id)
                ->update([
                    'terms_user' => 1,
                    'updated_by' => auth()->user()->id,
                ]);

                $term['success'] = true;
            } catch (\Exception $e) {
                //echo $e;
                Logger::reportException($e, [], ['any-Config-Terms', 'register'], false);
                $term['success'] = false;
            }
            return $term;
        }
    }

    public function forgotPassword(){

        $change['success'] = false;

        try {
            $user = User::where('email', request('email'))->first();
            $date = \Carbon\Carbon::now()->toDateTimeString();
            $expired = \Carbon\Carbon::parse($date);
            $expired->addHours(3);

            if($user == null){
                $change['success'] = false;
                $change['error'] = 'not_found';
                return $change;
            }else{
                // SE JÁ EXISTE, DELETO
                DB::table('change_password_token')
                ->where('user_auth_id', $user->id)
                ->where('used_at', null)
                ->select('id')
                ->delete();

                $hash = Str::random(30);

                // E CRIO OUTRO
                $result = DB::table('change_password_token')->insert([
                    'token' => $hash,
                    'user_auth_id' => $user->id,
                    'created_at' => \Carbon\Carbon::now()->toDateTimeString(),
                    'used_at' => null,
                    'expired_at' => $expired,
                ]);

                 //ENVIAR EMAIL COM O LINK
                 if(config('app')['is_helpdesk']){
                    // INFORMAR HELPDESK
                    $link = 'hs.builderall.com/change-password/'.$hash;
                    $content = [
                        'name'         => $user->name,
                        'company'      => 'HelpDesk',
                        'redirect_url' => $link,
                    ];
                    // $response = MailCentral::sendMessage('support-change-password', request('email'), request('language'), $content, 0, 'HelpDesk');
                    $response = new sendEmailCustom('', $user, request('email'), '','','','','');
                    $response->changePassword($link);
                }else{
                    // INFORMAR BUILDERALL
                    $link = 'ba-support.builderall.com/change-password/'.$hash;
                    $content = [
                        'name'         => $user->name,
                        'company'      => 'HelpDesk',
                        'redirect_url' => $link,
                    ];
                    // $response = MailCentral::sendMessage('support-change-password', request('email'), request('language'), $content, 0, 'HelpDesk');
                    $response = new sendEmailCustom('', $user, request('email'), '','','','','');
                    $response->changePassword($link);
                    // NOTIFICAÇÃO NO OFFICE ?
                }

                $change['result'] = $result;
                $change['success'] = true;
            }

        } catch (\Exception $e) {
            echo $e;
            Logger::reportException($e, [], ['Homercontroller', 'forgotPassword'], false);
            $change['success'] = false;
        }

        return $change;
    }

    public function forgotPasswordClient(){

        $change['success'] = false;
        try {

            $query = DB::table('company')
            ->select('id', 'name','hash_code')
            ->where('id', Crypt::decrypt(request('company')))
            ->first();

            if(!$query == null){
                $email = 'comp_'.$query->id.'_'.request('email');
                $company = $query->name;
            }

            $user = User::where('email', $email)->where('user_uuid', null)->first();

            $date = \Carbon\Carbon::now()->toDateTimeString();
            $expired = \Carbon\Carbon::parse($date);
            $expired->addHours(3);

            if($user == null){
                $change['success'] = false;
                $change['error'] = 'not_found';
                return $change;
            }else{
                // SE JÁ EXISTE, DELETO
                DB::table('change_password_token')
                ->where('user_auth_id', $user->id)
                ->where('used_at', null)
                ->select('id')
                ->delete();

                $hash = Str::random(30);

                // E CRIO OUTRO
                $result = DB::table('change_password_token')->insert([
                    'token' => $hash,
                    'user_auth_id' => $user->id,
                    'created_at' => \Carbon\Carbon::now()->toDateTimeString(),
                    'used_at' => null,
                    'expired_at' => $expired,
                ]);

                //ENVIAR EMAIL COM O LINK
                if(config('app')['is_helpdesk']){
                    // INFORMAR HELPDESK
                    $link = 'hs.builderall.com/change-password/'.$hash;
                    $content = [
                        'name'         => $user->name,
                        'company'      => $company,
                        'redirect_url' => $link,
                    ];
                    // $response = MailCentral::sendMessage('support-change-password', request('email'), request('language'), $content, 0, $company);
                    $response = new sendEmailCustom('', $user, request('email'), '','','','',$query->hash_code);
                    $response->changePasswordClient($link);
                }else{
                    // INFORMAR BUILDERALL
                    $link = 'ba-support.builderall.com/change-password/'.$hash;
                    $content = [
                        'name'         => $user->name,
                        'company'      => $company,
                        'redirect_url' => $link,
                    ];
                    // $response = MailCentral::sendMessage('support-change-password', request('email'), request('language'), $content, 0, $company);
                    $response = new sendEmailCustom('', $user, request('email'), '','','','',$query->hash_code);
                    $response->changePasswordClient($link);
                    // NOTIFICAÇÃO NO OFFICE ?
                }

                $change['result'] = $result;
                $change['success'] = true;
            }

        } catch (\Exception $e) {
            echo $e;
            Logger::reportException($e, [], ['Homercontroller', 'forgotPassword'], false);
            $change['success'] = false;
        }

        return $change;
    }

    public function changePassword($hash){

        $result = DB::table('change_password_token')
        ->where('token', $hash)
        ->where('used_at', null)
        ->select('created_at', 'expired_at', 'token')
        ->first();

        if (!$result) {
            return response()->view('errors.404', [], 404);
        }

        $now = \Carbon\Carbon::now()->toDateTimeString();
        $expired = $result->expired_at;

        if(strtotime($now) < strtotime($expired)){
            return view('layouts2.changePassword', ['result' => $result->token]);
        }else{
            return response()->view('errors.404', [], 404);
        }
    }

    public function saveChangePassword(){

        $change['success'] = false;

        try {

            $result = DB::table('change_password_token')
            ->where('token', request('token'))
            ->where('used_at', null)
            ->select('user_auth_id as id')
            ->first();

            $getCompany = DB::table('user_auth')
            ->where('id', $result->id)
            ->select('email')
            ->first(); 

            $email = $getCompany->email;
            $pos = strpos($email, "comp_");
            if ($pos !== false) {
                $pos += 5; // Adiciona o comprimento da substring "comp_"
                $number = "";
                while (is_numeric($email[$pos])) {
                    $number .= $email[$pos];
                    $pos++;
                }
            
                $hash_company = DB::table('company')
                ->where('id', $number)
                ->select('hash_code')
                ->first();
            } else {
                $hash_company = null;
            }

            $user = DB::table('user_auth')
            ->where('id', $result->id)
            ->update([
                'password' => bcrypt(request('password')),
                'updated_by' => \Carbon\Carbon::now()->toDateTimeString(),
            ]);

            if($user){
                $result =  DB::table('change_password_token')
                ->where('token', request('token'))
                ->where('used_at', null)
                ->update([
                    'used_at' => \Carbon\Carbon::now()->toDateTimeString(),
                    'expired_at' => \Carbon\Carbon::now()->toDateTimeString(),
                ]);
            }

            $change['redirect'] = $hash_company == null ? '/login': '/client/'.$hash_company->hash_code.'/login';
            $change['success'] = true;

        } catch (\Exception $e) {
            echo $e;
            Logger::reportException($e, [], ['Homercontroller', 'saveChangePassword'], false);
            $change['success'] = false;
        }
        return $change;
    }

    public function telegram(){
        try {
            // Notification::route('telegram', 2068744014)
            // ->notify(new notifyTelegramBot('Marlos Fernandes de Oliveira'));

            // $user = User::find(15);
            // /* Notification::route('telegram' , Config::get('services.telegram_id'))
            //  ->notify(new TelegramNotification($user));*/
            // $return =  $user->notify(new TelegramNotification($user));

            // $user = User::find(15);
            // $test =  Notification::route('telegram', '2068744014')
            // ->notify(new TelegramNotification($user));

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


        } catch (\Exception $e) {
            echo $e;
            Logger::reportException($e, [], ['Homercontroller', 'Telegram'], false);
            $change['success'] = false;
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




                    // if(auth()->user() == null){
                    //     Notification::route('telegram', $msgs->message->chat->id)
                    //     ->notify(new TelegramNotification());
                    // }else{
                    //     if(session('companyselected') == null){
                    //         Notification::route('telegram', $msgs->message->chat->id)
                    //         ->notify(new NotifyTelegramBot('Selecione uma companhia e mande o email novamente'));
                    //     }else{
                    //         if($msgs->message->text == auth()->user()->email){
                    //             $result = DB::table('company_user')
                    //             ->where('id', Crypt::decrypt(session('companyselected')['company_user_id']))
                    //             ->update([
                    //                 'telegram_chat_id' => $msgs->message->chat->id,
                    //                 'updated_by' => auth()->user()->id,
                    //             ]);

                    //             if($result){
                    //                 Notification::route('telegram', $msgs->message->chat->id)
                    //                 ->notify(new NotifyTelegramBot('Email vinculado com sucesso!, aguarde as notificações'));
                    //             }else{
                    //                 Notification::route('telegram', $msgs->message->chat->id)
                    //                 ->notify(new NotifyTelegramBot('Erro ao vincular email.'));
                    //             }
                    //         }
                    //     }
                    // }
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

    public function loginTelegram(Request $request){
        $telegram['success'] = false;
        try{

            if ($request->isMethod('post'))
            {
                if(request('type') == 'check'){
                    $array = explode('@@@',Crypt::decrypt(request('token')));

                    $user = DB::table('user_auth')
                    ->join('company_user', 'user_auth.id', 'company_user.user_auth_id')
                    ->join('company', 'company_user.company_id', 'company.id')
                    ->select('company_user.id as company_user_id', 'company.name', 'company.logo', 'user_auth.password')
                    ->where('email', $array[1])
                    ->where('company_user.is_active', 1)
                    ->where('company_user.deleted_at', null)
                    ->get();


                    if(Hash::check(request('password'), $user[0]->password)){

                        if(count($user) == 1){
                            $result = DB::table('company_user')
                            ->where('id', $user[0]->company_user_id)
                            ->update([
                                'telegram_chat_id' => $array[0],
                            ]);

                            if($result){
                                Notification::route('telegram', $array[0])
                                ->notify(new NotifyTelegramBot(Lang::get("app.bs-email-linked-successfully", [], 'pt_BR').'!'));
                            }else{
                                Notification::route('telegram', $array[0])
                                ->notify(new NotifyTelegramBot(Lang::get("app.bs-email-already-linked-or-error-linking-emai", [], 'pt_BR').'!'));
                            }

                            $telegram['success'] = true;
                            $telegram['type'] = 'finish';

                        }else{
                            //RETORNA PARA O CARA SELECIONAR A COMPANHIA
                            foreach ($user as $uu){
                                $uu->company_user_id = Crypt::encrypt($uu->company_user_id);
                            }
                            $telegram['value'] = $user;
                            $telegram['type'] = 'next';
                            $telegram['success'] = true;
                        }
                        return $telegram;
                    }
                }

                if(request('type') == 'save'){
                    $array = explode('@@@',Crypt::decrypt(request('token')));

                    $ja_vinculado = Company_user::join('user_auth', 'company_user.user_auth_id', 'user_auth.id')
                    ->where('company_user.telegram_chat_id', '!=', null)
                    ->where('user_auth.email', $array[1])
                    ->select('user_auth.email', 'company_user.id as company_user_id')
                    ->count();

                    if($ja_vinculado == 0){
                        $result = DB::table('company_user')
                        ->where('id', Crypt::decrypt(request('item')['company_user_id']))
                        ->update([
                            'telegram_chat_id' => $array[0],
                        ]);
                    }else{
                        $result = false;
                    }

                    if($result){
                        Notification::route('telegram', $array[0])
                        ->notify(new NotifyTelegramBot(Lang::get("app.bs-email-already-linked-wait-for-notification", [], 'pt_BR')));
                    }else{
                        Notification::route('telegram', $array[0])
                        ->notify(new NotifyTelegramBot(Lang::get("app.bs-email-already-linked-or-error-linking-emai", [], 'pt_BR'). '!'));
                    }

                    $telegram['success'] = $result;
                    return $telegram;
                }

            }else{
                $array = explode('@@@',Crypt::decrypt(request('token')));

                $company_user = Company_user::join('user_auth', 'company_user.user_auth_id', 'user_auth.id')
                    ->where('company_user.telegram_chat_id', '!=', null)
                    ->where('user_auth.email', $array[1])
                    ->select('user_auth.email', 'company_user.id as company_user_id')
                    ->count();

                $getuser = DB::table('user_auth')
                ->select('name', 'email')
                ->where('email', $array[1])
                ->get();

                return view('layouts2.loginTelegram', [ 'getuser' => $getuser, 'token' => request('token'), 'company_user' => $company_user ]);
            }
        } catch (\Exception $e) {
            echo $e->getMessage();
            Logger::reportException($e, [], ['Homercontroller', 'loginTelegram'], false);
            $telegram['success'] = false;
        }



    }

}
