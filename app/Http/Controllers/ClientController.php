<?php

namespace App\Http\Controllers;

use App\Chat;
use App\Events\TicketsListUpdate;
use App\Tools\Crypt;
use App\User;
use App\Http\Controllers\TicketController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Tools\Builderall\Logger;
use Illuminate\Support\Facades\Validator;
use App\ChatHistory;
use App\CompanyUserCompanyDepartment;
use App\Events\ClientTicketsList;
use App\Events\MessageSentTicket;
use App\Events\TicketsAgentListUpdate;
use App\Models\Company_user;
use App\Events\GlobalNotification;
use App\Mail\sendEmailCustom;
use App\Models\Company_department;
use App\Models\CompanySettings;
use App\Models\UserClientTicket;
use App\Ticket;
use App\TicketChatAnswer;
use App\Tools\ChatWorkingTimes;
use App\Tools\ClearEmail;
use App\Tools\Tickets\Feedback;
use App\Tools\Client;
use App\Tools\telegram\TelegramBot;
use App\Tools\Tickets\MailAdmin;
use App\Tools\Tickets\SendRealtime;
use App\UserAuth;
use App\UserClientChat;
use Carbon\Carbon;

class ClientController extends Controller
{
	public function indexOld(Request $request)
	{
		// if (request('page') == null) {
		// 	return view('functions.client_old.client.client');
		// } else if (request('page') == 'chat') {
		// 	return redirect('client-chat');
		// } else if (request('page') == 'ticket') {
		// 	return redirect('client-ticket');
		// } else {
		// 	return view('functions.client_old.client.client');
		// }
		session(['user' => auth()->user()]);
		return view('client.home.index');
	}

    public function index(Request $request)
	{
		return view('client.home.index');
	}

	public function createFastTicket(Request $request)
	{
		if ($request->isMethod('post')) {
			// dd(request('item')['subjectbox']);
			// dd(request('item')['bodymessage']);
			// dd(request('item')['userLang']);

			$response['success'] = false;
			try {
	
				// Customer Success - 74 ID DA BA - DEPARTAMENTO ESPECÍFICO PARA TELA ESPECÍFICA
				$company_department_id = Crypt::decrypt(request('depart'));
				// $onlineUsers = $request->onlineUsers; @JOAO

				$company_id = Crypt::decrypt(session('companyselected')['company_id']);
				$user_id = auth()->user()->id;
				$user_client_id = Crypt::decrypt(session('companyselected')['user_client_id']);

				$created_at = \Carbon\Carbon::now()->toDateTimeString();

				$ticket = Ticket::create([
					'company_id' => $company_id,
					'company_department_id' => $company_department_id,
					'description' => request('item')['subjectbox'].': '.request('item')['bodymessage'],
					'status' => 'OPENED',
					'type' => 'DEFAULT',
					'priority' => 'NORMAL',
					'user_agent' => $_SERVER['HTTP_USER_AGENT'],
					'created_by' => $user_id,
					'created_at' => $created_at,
				]);

				UserClientTicket::create([
					'user_client_id' => $user_client_id,
					'ticket_id' => $ticket->id,
					'created_by' => $user_id,
					'created_at' => $created_at,
				]);

				$chat = Chat::create([
					'company_id' => $company_id,
					'company_department_id' => $company_department_id,
					'ticket_id' => $ticket->id,
					'type' => 'TICKET',
					'priority' => 'NORMAL',
					'user_agent' => $_SERVER['HTTP_USER_AGENT'],
					'created_by' => $user_id,
					'created_at' => $created_at,
					'updated_at' => $created_at,
				]);

				UserClientChat::create([
					'user_client_id' => $user_client_id,
					'chat_id' => $chat->id,
					'created_by' => $user_id,
					'created_at' => $created_at,
				]);

				// ENVIA O EMAIL PARA O ATENDENTE DIZENDO QUE ABRIU TICKET
				// $emailDepartment = Company_department::where('id', $company_department_id)->first()->toArray();
				// $send_mail = new sendEmailCustom($ticket->id,auth()->user(),'','','','','',session('companyselected')['hash_code']);
				// $send_mail->ticketchatOpened('TICKET', $onlineUsers, $emailDepartment);

				Feedback::directSendMB('OPENED', $ticket->id, auth()->user()->email);

				$rt = new SendRealtime($ticket->id, 'push');
				$rt->updateTableQueue();

				broadcast(new GlobalNotification([
					'title' => 'bs-ticket',
					'body' => 'bs-new-ticket-added-to-the-queue',
					'icon' => 'https://builderall.com//franquias/2/73748/editor-html/3484811.png',
					'url' => '',
					'silent' => false,
					'number' => $ticket->id,
					'type' => 'ticket',
					'status' => 'OPENED',
					'company_department_id' => $request->company_department,
					'company_id' => session('companyselected')['company_id']
				]));

				$department_name = Company_department::select('name')->where('id', $company_department_id)->first()->name;

				broadcast(new ClientTicketsList([
					'company_id' => session('companyselected')['company_id'],
					'user_client_id' => session('companyselected')['user_client_id'],
					'action' => 'push',
					'hash_id' => Crypt::encrypt($ticket->id),
					'chat_id'  => $chat->id,
					'ticket_id'  => $ticket->id,
					'description'  => $ticket->description,
					'status' => $ticket->status,
					'created_at' => $ticket->created_at,
					'department' => $department_name
				]));

				$response['hash_id'] = Crypt::encrypt($ticket->id);
				$response['success'] = true;

			} catch (\Exception $e) {
				echo  $e;
				Logger::reportException($e, [], ['client-controller', 'createFastTicket'], false);
			}

			return $response;

		}else if ($request->isMethod('get')) {
			// dd(auth()->user());

			$depart = DB::table('company_department')
				->join('company_department_settings', 'company_department.id', 'company_department_settings.company_department_id')
				->select('company_department.id as company_department_id', 'company_department.name',  'company_department_settings.id','company_department_settings.settings')
				->where('company_department.company_id', Crypt::decrypt(session('companyselected')['company_id']))
				->whereNotNull('settings')
				->get();
		
			$key = request('key');
			$value = '';
			$name = '';
			foreach ($depart as $item) {
				if(isset(json_decode($item->settings)->general->keyDepartment)){
					if(json_decode($item->settings)->general->keyDepartment == $key){
						$value = Crypt::encrypt($item->company_department_id);
						$name = $item->name;
					}
				}
			}

			if ($value == '') {
                return response()->view('errors.404', [], 404);
            }

			return view('client.home.createSimple', ['depart_id' => $value, 'depart_name' => $name]);
		}
	}

	public function chat(Request $request)
	{
		$company_id = Crypt::decrypt(session('companyselected')['company_id']);

        $company_settings = DB::table('company_settings')
        ->select('general')
        ->where('company_id', $company_id)
        ->first();

        if ($company_settings->general) {
            $settings = json_decode($company_settings->general, true);
            if($settings['showChat']) {
                return redirect()->route('ticket');
            }
        }

        return view('functions.client_old.client.chat');
	}

	public function ticket(Request $request)
	{
		return view('functions.client_old.client.ticket');
	}

	public function getCountry()
	{
		$user_id = Auth::id();
		$iso_code = User::join('subsidiary', 'subsidiary.id', "user_auth.subsidiary_id")
			->where('user_auth.id', $user_id)
			->select('subsidiary.iso_code as iso_code')
			->pluck('iso_code')[0];

		return $iso_code;
	}

	public function getDepartmentClient()
	{
		$department['success'] = false;

		try {
			//session(['show_only_dtype' => true]); // false or True
			//session(['dtype' => ['checkout', 'builderall-mentor']]);
			// 'typelanguage' => request('country'),
			// AND company_department.module != 'CHAT'
			$language_ISO = request('country');

			if(session('dtype')){

				$dtype = "'";
				$dtype .= implode("','", session('dtype'));
				$dtype .= "'";

				if(session('show_only_dtype') == true){
					$arrayName = array(
						'companyselected' => Crypt::decrypt(session('companyselected')['company_id']),
					);

					$query = "SELECT company_department.id, company_department.name, company_department.type, company_department_settings.settings
						FROM company_department_settings
						JOIN company_department on company_department_settings.company_department_id = company_department.id
						WHERE company_department.company_id = :companyselected
						AND company_department.deleted_at IS NULL
						AND company_department.type in (".$dtype.")
						AND company_department.is_active = 1";
				}else{
					$arrayName = array(
						'companyselected' => Crypt::decrypt(session('companyselected')['company_id']),
						'companyselected2' => Crypt::decrypt(session('companyselected')['company_id']),
					);

					$query = "SELECT company_department.id, company_department.name, company_department.type, company_department_settings.settings
						FROM company_department_settings
						JOIN company_department on company_department_settings.company_department_id = company_department.id
						WHERE company_department.company_id = :companyselected
						AND company_department.deleted_at IS NULL
						AND company_department.type in (".$dtype.")
						AND company_department.is_active = 1

						UNION

						SELECT company_department.id, company_department.name, company_department.type, company_department_settings.settings
						FROM company_department_settings
						JOIN company_department on company_department_settings.company_department_id = company_department.id
						WHERE company_department.company_id = :companyselected2
						AND company_department.deleted_at IS NULL
						AND company_department.type = ''
						AND company_department.is_active = 1";
				}
			}else{
				$arrayName = array(
					'companyselected' => Crypt::decrypt(session('companyselected')['company_id'])
				);

				$query = "SELECT company_department.id, company_department.name, company_department.type,
				company_department_settings.settings
				FROM company_department_settings
				JOIN company_department on company_department_settings.company_department_id = company_department.id
				WHERE company_department.company_id = :companyselected
				AND company_department.deleted_at IS NULL
				AND company_department.type = ''
				AND company_department.is_active = 1";

			}

			$department['dtype'] = session('dtype');
			$department['result'] = DB::select($query, $arrayName);

			$aux = [];

			foreach ($department['result'] as $key){
				// echo json_decode($key->settings)->general->userLang;
				foreach (json_decode($key->settings)->general->userLang as $two){
					if($two->code == 'ALL' || $two->code == request('country')){
						array_push($aux, $key);
					}
				}
			}

			if($aux == []){

				foreach ($department['result']as $key){
					// echo json_decode($key->settings)->general->userLang;
					foreach (json_decode($key->settings)->general->userLang as $two){
						if($two->code == explode('-', request('language'))[1]){
							array_push($aux, $key);
						}
					}
				}

				if($aux == []){
					foreach ($department['result']as $key){
						// echo json_decode($key->settings)->general->userLang;
						foreach (json_decode($key->settings)->general->userLang as $two){
							if($two->code == 'US'){
								array_push($aux, $key);
							}
						}
					}
				}

				$department['result'] = $aux;
			}else{
				$department['result'] = $aux;
			}

			foreach ($department['result'] as $key) {
				$key->id = Crypt::encrypt($key->id);
			}

			$department['success'] = true;
		} catch (\Exception $e) {
			Logger::reportException($e, [], ['department-controller', 'show-robot'], false);
			echo $e;
			$department['success'] = false;
		}

		echo json_encode($department);
	}

	public function getTicketClient()
	{
		$tickets['success'] = false;

		try {

			$sts_order = DB::raw('(CASE
				WHEN ticket.status = \'OPENED\' THEN 1
				WHEN ticket.status = \'IN_PROGRESS\' THEN 2
				ELSE 3
			END) AS status_order');
			$answered = DB::raw('IF((SELECT company_user_company_department_id FROM chat_history WHERE chat_id = chat.id ORDER BY id DESC LIMIT 1) IS NULL, 0, 1) AS answered');
			$tickets['result'] = DB::table('ticket')
				->join('company_department', 'ticket.company_department_id', 'company_department.id')
				->join('company_department_settings', 'company_department.id', 'company_department_settings.company_department_id')
				->join('user_client_ticket', 'ticket.id', 'user_client_ticket.ticket_id')
				->join('user_client', 'user_client_ticket.user_client_id', 'user_client.id')
				->leftjoin('chat', 'ticket.id', 'chat.ticket_id')
				->leftjoin('company_user_company_department', 'chat.comp_user_comp_depart_id_current', 'company_user_company_department.id')
				->leftjoin('company_user', 'company_user_company_department.company_user_id', 'company_user.id')
				->leftjoin('user_auth', 'company_user.user_auth_id', 'user_auth.id')
				->leftjoin('avaliation', 'ticket.id', 'avaliation.ticket_id')
				->leftjoin('company_settings', 'ticket.company_id', 'company_settings.company_id')
				->select('ticket.id', 'ticket.status', $sts_order, $answered, 'ticket.description', 'ticket.priority', 'ticket.created_at',
				'ticket.update_status_in_progress as last_update_status', 'ticket.update_status_closed_resolved', 'company_department.name as department', 'company_department.id as department_id',
				'company_department.type as department_type', 'chat.id as chat_id', 'user_auth.name as agent', 'avaliation.stars_atendent',
				'avaliation.stars_service', 'company_settings.settings_ticket', 'company_department_settings.settings', 'company_user_company_department.company_user_id')
				->where('user_client_ticket.user_client_id', Crypt::decrypt(session('companyselected')['user_client_id']))
                ->whereNull('chat.deleted_at')
                ->whereNull('ticket.deleted_at')
				->where('ticket.status', '!=', 'MERGED')
				->orderBy('status_order', 'asc')
				->orderBy('ticket.created_at', 'desc')
				->get();

			foreach ($tickets['result'] as $key) {
				$key->department_id = Crypt::encrypt($key->department_id);
				$key->chat_id = Crypt::encrypt($key->chat_id);
			}

			$tickets['success'] = true;
		} catch (\Exception $e) {
			echo $e;
			Logger::reportException($e, [], ['agents-controller', 'getTicketClient'], false);
			$tickets['success'] = false;
		}

		echo json_encode($tickets);
	}

	public function getTicketClientChat($id)
	{
		$tickets['success'] = false;

		try {
			// GET DO CHATHISTORY PASSANDO COMO PARAMETRO O ID DO TICKET SELECIONADO
			$tickets['result'] = DB::table('chat')
				->join('ticket', 'chat.ticket_id', 'ticket.id')
				->leftjoin('chat_history', 'chat.id', 'chat_history.chat_id')
				->join('user_client_ticket', 'ticket.id', 'user_client_ticket.ticket_id')
				->join('user_client', 'user_client_ticket.user_client_id', 'user_client.id')
				->leftjoin('user_auth as user_client2', 'user_client.user_auth_id', 'user_client2.id')
				->leftJoin('company_user_company_department', 'chat_history.company_user_company_department_id', 'company_user_company_department.id')
				->leftJoin('company_user', 'company_user_company_department.company_user_id', 'company_user.id')
				->leftJoin('user_auth AS ua_agent', 'company_user.user_auth_id', 'ua_agent.id')
				->select(
					'chat.id as chat_id',
					'chat_history.type',
					'chat_history.content',
					'chat_history.created_at',
					'chat_history.created_by',
					'chat.comp_user_comp_depart_id_creator',
					'chat_history.company_user_company_department_id',
					'user_client2.id as client_id',
					'user_client2.name as client_name',
					'user_client2.email as client_email',
					'ua_agent.name as user_name',
					'ua_agent.email as user_email',
					'ua_agent.id as user_id',
					'user_client2.builderall_account_data AS builderall_account_data'
				)
				->where('ticket.id', $id)
                ->whereNull('chat.deleted_at')
                ->whereNull('ticket.deleted_at')
                ->where('chat_history.hidden_for_client', 0)
				->orderBy('chat_history.created_at', 'asc')
				->get();

			$chat_id = '';
			foreach ($tickets['result'] as $key) {
				$key->chat_id = Crypt::encrypt($key->chat_id);
				$chat_id = $key->chat_id;
				$key->client_email = Client::forceCleanEmail($key->client_email);
				$key->email = Client::forceCleanEmail($key->client_email);
				$tickets['client_id'] = Crypt::encrypt($key->client_id);
				$tickets['client_name'] = $key->client_name;
				$tickets['client_email'] = Client::forceCleanEmail($key->client_email);
				$res = json_decode($key->content);
				if(json_last_error() == JSON_ERROR_NONE) {
					$key->content = $res;
				}
			}

			$quest['result'] = DB::table('ticket_chat_answer as tt')
				->join('company_depart_settings_question as cc', 'tt.company_depart_settings_question_id', 'cc.id')
				->select('tt.id', 'question', 'answer', 'tt.created_at')
				->where('ticket_id', $id)
				->get();

			foreach ($quest['result'] as $key) {
				$key->id = Crypt::encrypt($key->id);
			}

			$tickets['ticket'] = DB::table('ticket')
				->select('description', 'created_at')
				->where('id', $id)
                ->whereNull('deleted_at')
				->first();

			try {
				$quest['settings'] = DB::table('company_department_settings')
					->select('settings')
					->where('company_department_id', Crypt::decrypt(request('department_id')))
					->first();

				$tickets['questionTicket'] = json_decode($quest['settings']->settings)->ticket->desriptionTicket;

			} catch (\Exception $e) {
				$tickets['questionTicket'] = null;
			}

			$tickets['success'] = true;
			$tickets['quests'] = $quest['result'];
			$tickets['id'] = $chat_id;
		} catch (\Exception $e) {
			echo $e;
			Logger::reportException($e, [], ['client-controller', 'getTicketClientChat'], false);
			$tickets['success'] = false;
		}

		echo json_encode($tickets);
	}

	
	public function getInfoCategorySelected($id){
		$tickets['success'] = false;

		try {

			$arrayName = array(
				'chat_id' =>Crypt::decrypt($id)
			);

			$query = "SELECT c.id as chat_id, IF(c.ticket_id IS NULL, c.status, t.status) as status, ucc.user_client_id, 
						ua_client.id as user_auth_id, ua_client.name, ua_client.email, ua_client.builderall_account_data, c.comp_user_comp_depart_id_current, 
						c.company_department_id, cd.name as department_name, JSON_VALUE(ua_client.builderall_account_data, '$.is_vip') as is_vip, c.created_at
					FROM chat c
					JOIN user_client_chat ucc ON c.id = ucc.chat_id
					JOIN user_client AS uc ON uc.id = ucc.user_client_id
					JOIN user_auth AS ua_client ON ua_client.id = uc.user_auth_id
					-- JOIN company_user_company_department cucd ON cucd.id = c.comp_user_comp_depart_id_current
					JOIN company_department cd ON cd.id = c.company_department_id
					-- JOIN company_user cu ON cu.id = cucd.company_user_id
					LEFT JOIN ticket t ON c.ticket_id = t.id
					WHERE c.id = :chat_id";

			$tickets['infoUser'] = DB::select($query, $arrayName);

			foreach ($tickets['infoUser'] as $key) {
				$key->user_client_id = Crypt::encrypt($key->user_client_id);
				$key->email = Client::forceCleanEmail($key->email);
				$key->comp_user_comp_depart_id_current = Crypt::encrypt($key->comp_user_comp_depart_id_current);
				$key->company_department_id = Crypt::encrypt($key->company_department_id);
				$key->d_user_auth_id = $key->user_auth_id;
				$key->user_auth_id = Crypt::encrypt($key->user_auth_id);
				$key->chat_id = Crypt::encrypt($key->chat_id);
			}

			$arrayName2 = array(
				'chat_id' =>Crypt::decrypt($id)
			);

			$query2 = "SELECT c.id as chat_id, ua_agent.*
						FROM chat c
						JOIN company_user_company_department cucd ON cucd.id = c.comp_user_comp_depart_id_current
						JOIN company_user cu ON cu.id = cucd.company_user_id
						JOIN user_auth AS ua_agent ON ua_agent.id = cu.user_auth_id
						WHERE c.id = :chat_id;";

			$tickets['infoAgent'] = DB::select($query2, $arrayName2);

			foreach ($tickets['infoAgent'] as $key) {
			}

			$tickets['success'] = true;
		} catch (\Exception $e) {
			echo $e;
			Logger::reportException($e, [], ['client-controller', 'getInfoCategorySelected'], false);
			$tickets['success'] = false;
		}

		echo json_encode($tickets);
	}

	public function createTicketChatHistory(Request $request)
	{
		$rules =  [
			'content' => 'required',
		];

		$messages = [
			'content.required' => 'bs-text-is-required',
		];

		$validator = Validator::make($request->all(), $rules, $messages);

		if ($validator->fails()) {
			$ticket['success'] = false;
			$ticket['value'] = $validator->errors()->first();
			return $ticket;
		}

		$ticket['success'] = false;

		$created_at = \Carbon\Carbon::now()->toDateTimeString();

		try {
			$chat_id = intval(Crypt::decrypt($request->id));
			$content = $request['content'];

			if (request('typeSendMessage') == 'addmessage') {
				//VERIFICADOR SE EXISTE CHAT VINCULADO AO TICKET
				if ($chat_id == 0) {
					$ticket['success'] = false;
					$ticket['value'] = 'not_message';
					echo json_encode($ticket);
					return;
				}
			} else if (request('typeSendMessage') == 'createticket') {

				$id_ticket = DB::table('ticket')->insertGetId([
					'company_id' => Crypt::decrypt(session('companyselected')['company_id']),
					'company_department_id' => Crypt::decrypt(request('id_department')),
					'status' => 'OPENED',
					'type' => 'DEFAULT',
					'priority' => 'NORMAL',
					'user_agent' => $_SERVER['HTTP_USER_AGENT'],
					'created_by' => auth()->user()->id,
					'created_at' => $created_at,
				]);

				DB::table('user_client_ticket')->insertGetId([
					'user_client_id' => Crypt::decrypt(session('companyselected')['user_client_id']),
					'ticket_id' => $id_ticket,
					'created_by' => auth()->user()->id,
					'created_at' => $created_at,
				]);

				$id_chat = DB::table('chat')->insertGetId([
					'company_id' => Crypt::decrypt(session('companyselected')['company_id']),
					'company_department_id' => Crypt::decrypt(request('id_department')),
					'ticket_id' => $id_ticket,
					'type' => 'TICKET',
					'priority' => 'NORMAL',
					'user_agent' => $_SERVER['HTTP_USER_AGENT'],
					'created_by' => auth()->user()->id,
					'created_at' => $created_at,
					'updated_at' => $created_at,
				]);

				if (!is_null($request['images'])) {
					$images = $request['images'];
					foreach ($images as $row) {
						// Define the Base64 value you need to save as an image
						$b64 = explode(',', $row)[1];
						$image_name = Crypt::encrypt(uniqid(md5(uniqid() . microtime())));
						$data = base64_decode($b64);
						$dir = '..' . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'company' . DIRECTORY_SEPARATOR . Crypt::decrypt(session('companyselected')['company_id']) . DIRECTORY_SEPARATOR . 'chat' . DIRECTORY_SEPARATOR . $id_chat . DIRECTORY_SEPARATOR;
						$filename = $image_name . '.png';

						// Caso já exista o diretório, apenas movo o arquivo pra dentro dele, se não, crio a pasta e movo o arquivo em seguida...
						if (is_dir($dir)) {
							$success = file_put_contents($dir.$filename, $data);
						} else {
							mkdir($dir, 0755, true);
							$success = file_put_contents($dir.$filename, $data);
						}

						if ($success) {
							$content = str_replace($row, 'chat/files/'. Crypt::encrypt($id_chat) .'/'.$filename, $content);
						}
					}
				}

				DB::table('ticket')->where('id', $id_ticket)->update([
					'description' => $content
				]);


				DB::table('user_client_chat')->insertGetId([
					'user_client_id' => Crypt::decrypt(session('companyselected')['user_client_id']),
					'chat_id' => $id_chat,
					'created_by' => auth()->user()->id,
					'created_at' => $created_at,
				]);

				if(!empty(request('questionsConfirm'))){
					foreach (request('questionsConfirm') as $key) {
						if ($key['answer'] != null) {
							if (isset($key['images']) && count($key['images']) > 0) {
								foreach ($key['images'] as $row) {
									// Define the Base64 value you need to save as an image
									$b64 = explode(',', $row)[1];
									$image_name = Crypt::encrypt(uniqid(md5(uniqid() . microtime())));
									$data = base64_decode($b64);
									$dir = '..' . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'company' . DIRECTORY_SEPARATOR . Crypt::decrypt(session('companyselected')['company_id']) . DIRECTORY_SEPARATOR . 'chat' . DIRECTORY_SEPARATOR . $id_chat . DIRECTORY_SEPARATOR;
									$filename = $image_name . '.png';

									// Caso já exista o diretório, apenas movo o arquivo pra dentro dele, se não, crio a pasta e movo o arquivo em seguida...
									if (is_dir($dir)) {
										$success = file_put_contents($dir.$filename, $data);
									} else {
										mkdir($dir, 0755, true);
										$success = file_put_contents($dir.$filename, $data);
									}

									if ($success) {
										$key['answer'] = str_replace($row, 'chat/files/'. Crypt::encrypt($id_chat) .'/'.$filename, $key['answer']);
									}
								}
							}
							DB::table('ticket_chat_answer')->insertGetId([
								'company_depart_settings_question_id' => Crypt::decrypt($key['id']),
								'ticket_id' => $id_ticket,
								'answer' => $key['answer'],
								'created_by' => auth()->user()->id,
								'created_at' => $created_at,
							]);
						}
					}
				}

				if(config('app')['is_helpdesk'] == false && config('app.env') != 'sandbox' && config('app.env') != 'local'){
					$bot = new TelegramBot();
        			$bot->notificationAllUsers(session('companyselected'), 'ticket', 'create', $id_ticket, request('id_department'), request('content'), request('onlineUsers'));
				}

				// Email - cliente antigo
				// $send_mail = new MailAdmin(Crypt::decrypt(session('companyselected')['company_id']), auth()->user()->id, Crypt::decrypt(request('id_department')));
				// $send_mail->ticketchatOpened('TICKET', request('onlineUsers'));

				//----------------------------//SELECT PRO REALTIME//-----------------------------//
				$sts_order = DB::raw('(CASE
				WHEN ticket.status = \'OPENED\' THEN 1
				WHEN ticket.status = \'IN_PROGRESS\' THEN 2
				ELSE 3
				END) AS status_order');
				$answered = DB::raw('IF((SELECT company_user_company_department_id FROM chat_history WHERE chat_id = chat.id ORDER BY id DESC LIMIT 1) IS NULL, 0, 1) AS answered');
				$tickets['result'] = DB::table('ticket')
				->join('company_department', 'ticket.company_department_id', 'company_department.id')
				->join('company_department_settings', 'company_department.id', 'company_department_settings.company_department_id')
				->join('user_client_ticket', 'ticket.id', 'user_client_ticket.ticket_id')
				->join('user_client', 'user_client_ticket.user_client_id', 'user_client.id')
				->leftjoin('chat', 'ticket.id', 'chat.ticket_id')
				->leftjoin('company_user_company_department', 'chat.comp_user_comp_depart_id_current', 'company_user_company_department.id')
				->leftjoin('company_user', 'company_user_company_department.company_user_id', 'company_user.id')
				->leftjoin('user_auth', 'company_user.user_auth_id', 'user_auth.id')
				->leftjoin('avaliation', 'ticket.id', 'avaliation.ticket_id')
				->leftjoin('company_settings', 'ticket.company_id', 'company_settings.company_id')
				->select('ticket.id', 'ticket.status', $sts_order, $answered, 'ticket.description', 'ticket.priority', 'ticket.created_at',
				'ticket.update_status_in_progress as last_update_status', 'company_department.name as department', 'company_department.id as department_id',
				'company_department.type as department_type', 'chat.id as chat_id', 'user_auth.name as agent', 'avaliation.stars_atendent',
				'avaliation.stars_service', 'company_settings.settings_ticket', 'company_department_settings.settings', 'company_user_company_department.company_user_id',
				'ticket.created_by', 'company_user_company_department.id as company_user_company_department_id')
				->where('user_client_ticket.user_client_id', Crypt::decrypt(session('companyselected')['user_client_id']))
				->where('ticket.id', $id_ticket)
                ->whereNull('chat.deleted_at')
                ->whereNull('ticket.deleted_at')
				->orderBy('status_order', 'asc')
				->orderBy('ticket.created_at', 'desc')
				->get();

				foreach ($tickets['result'] as $key) {
					$key->department_id = Crypt::encrypt($key->department_id);
					$key->chat_id_decry = $key->chat_id;
					$key->chat_id = Crypt::encrypt($key->chat_id);
					$key->company_user_company_department_id = Crypt::encrypt($key->company_user_company_department_id);
					$key->overdue = json_decode($key->settings)->quant_limity->lateTime;
					$key->id_encrypted = Crypt::encrypt($key->id);
					$key->created_by_encrypted = Crypt::encrypt($key->created_by);
					$key->chat_created_by_encrypted = Crypt::encrypt(auth()->user()->id);
					$key->email_created = ClearEmail::clear(auth()->user()->email);
					$key->name_created = ClearEmail::clear(auth()->user()->name);
					$key->answered = '';
				}

				//REALTIME ADICIONANDO TICKET A LISTA DO ATENDENTE
                $rt = new SendRealtime($id_ticket, 'push');
                $rt->updateTableQueue();

				//FALTA - REALTIME PARA ADICIONAR A LISTA DE TICKET DO PROPRIO CLIENTE "CASO DUAS ABAS" - FURURO PROXIMO -

				//FALTA - ALERTA PARA O ATENDENTE SABER QUE O CLIENTE RESPONDEU -
				broadcast(new GlobalNotification([
					// título da notificação
					'title' => 'bs-ticket',
					// O corpo(mensagem) da notificação.
					'body' => 'bs-new-ticket-added-to-the-queue',
					// A URL da imagem usada como um ícone da notificação.
					'icon' => 'https://builderall.com//franquias/2/73748/editor-html/3484811.png',
					// (Opcional) URL para qual a notificação deve redirecionar
					'url' => '',
					//se for true, a notificação fica sem som (PS: está sendo disparado som no browser, então pode ficar true aqui que vai sair som igual)
					'silent' => false,
					// numero de identificação do chat / ticket
					'number' => $id_ticket,
					// identifica se a notificação é de chat ou ticket
					'type' => 'ticket',
					// identifica o status do chat/ticket para fazer a lógica do disparo
					'status' => 'OPENED',
					// identifica o id do departamento para qual a notificação deve ser dispara
					'company_department_id' => request('id_department'),
					// envia o company user id do atendente que precisa receber a notificação (caso seja individual)
					// 'company_user_id' => session('companyselected')['company_user_id'],
					// Paramêtro de conexão no canal global de notificação, sempre deve ser passado
					'company_id' => session('companyselected')['company_id']
				]));


				//---------------------------------/ SELECT PRO REALTIME //----------------------------------------//

				/**
				 * Sen feedback to client
				 * Anotação: feedback apenas se for cliente
				 */
				$company_id   = Crypt::decrypt(session('companyselected')['id']);
				$company_name = isset(session('companyselected')['name']) ? session('companyselected')['name'] : 'BA Support';

                // if(config('app.env') != 'sandbox' && config('app.env') != 'local') {
                //     Feedback::send('OPENED', $id_ticket, $company_id, $company_name);
                // }

				Feedback::directSendMB('OPENED', $id_ticket, auth()->user()->email);

				$ticket['result'] = $tickets['result'][0];
				$ticket['value'] = 'ticket_create';
				$ticket['success'] = true;
				$ticket['id'] = $id_ticket;
				$ticket['status'] = 'OPENED';
				$ticket['created_at'] = $created_at;
				echo json_encode($ticket);
				return;
			}

			//SE CASO TIVER ATENDENTE NO TICKET
			if (!is_null(request('company_user_id'))) {
				broadcast(new GlobalNotification([
					// título da notificação
					'title' => 'bs-ticket',
					// O corpo(mensagem) da notificação.
					'body' => request('content'),
					// A URL da imagem usada como um ícone da notificação.
					'icon' => 'https://builderall.com//franquias/2/73748/editor-html/3484811.png',
					// (Opcional) URL para qual a notificação deve redirecionar
					'url' => '',
					//se for true, a notificação fica sem som (PS: está sendo disparado som no browser, então pode ficar true aqui que vai sair som igual)
					'silent' => false,
					// numero de identificação do chat / ticket
					'number' => request('id_ticket'),
					// identifica se a notificação é de chat ou ticket
					'type' => 'ticket',
					// identifica o status do chat/ticket para fazer a lógica do disparo
					'status' => 'OPENED',
					// identifica o id do departamento para qual a notificação deve ser dispara
					'company_department_id' => request('id_department'),
					// envia o company user id do atendente que precisa receber a notificação (caso seja individual)
					'company_user_id' => request('company_user_id'),
					// Paramêtro de conexão no canal global de notificação, sempre deve ser passado
					'company_id' => session('companyselected')['company_id']
				]));
			}

			//ALERTA PARA O ATENDENTE CASO SEJA ENVIADO UMA MENSAGEM PARA O ATENDENTE
			broadcast(new TicketsListUpdate([
				'company_id_realtime' => session('companyselected')['company_id'],
				'action' => 'alert_status',
				'id_ticket' => intval(request('id_ticket')),
				'company_department_id' => request('id_department'),
			]));


			// ADICIONA TEXTO NO CHAT HISTORY
			$create = ChatHistory::create([
				'chat_id' => $chat_id,
				'company_user_company_department_id' => null,
				'type' => 'TEXT',
				'content' => request('content'),
				'created_by' => auth()->user()->id,
			]);

			$user = Auth::user();

			$create->chat_id = Crypt::encrypt($chat_id);
			$msg = json_decode(json_encode($create), true);
			$user = json_decode(json_encode($user), true);
			$result =  array_merge($user, $msg);
			$result['created_by'] = $create->created_by;

			broadcast(new MessageSentTicket($result));

			DB::table('ticket')
				->where('id', request('id_ticket'))
				->update([
					'updated_at' => $created_at,// manter, pois segundo log do git, ja existia antes  do user_agent ser inserido
					'user_agent' => $_SERVER['HTTP_USER_AGENT'],
					'updated_by' => auth()->user()->id,
				]);

			$ticket['success'] = true;
			$ticket['created_by'] = $created_at;
		} catch (\Exception $e) {
			echo $e;
			Logger::reportException($e, [], ['cliente-create-ticket-controller', 'index'], false);
			$ticket['success'] = false;
		}

		echo json_encode($ticket);
	}

	public function clientAvaliationTicket()
	{

		$ticket['success'] = false;

		$avaliation_id = DB::table('avaliation')
			->where('ticket_id', request('id'))
			->select('id')
			->first();

		if ($avaliation_id != null) {
			$ticket['result'] = 'checked';
			$ticket['success'] = true;
			return json_encode($ticket);
		}

		try {
			if ($avaliation_id == null) {
				$avaliation_id = DB::table('avaliation')->insertGetId([
					'ticket_id' => request('id'),
					'chat_id' => Crypt::decrypt(request('chat_id')),
					'stars_atendent' => request('atendant'),
					'stars_service' => request('service'),
					'comment' => request('comment'),
					'created_by' => auth()->user()->id,
					'created_at' => \Carbon\Carbon::now()->toDateTimeString(),
				]);
			}

			$ticket['success'] = true;
		} catch (\Exception $e) {
			echo $e;
			Logger::reportException($e, [], ['department-controller', 'index'], false);
			$ticket['success'] = false;
		}

		echo json_encode($ticket);
	}

	public function clientStatusTicket()
	{
		$ticket['success'] = false;

		try {
			$vars = [];
			//'OPENED','IN_PROGRESS','CLOSED','RESOLVED','CANCELED'

			//LEMBRETE ADICIONAR AQUI A FUNÇÃO PARA O DASHBOARD
			// update_status_closed_resolved' = \Carbon\Carbon::now()->toDateTimeString();
			if (request('status') == 0) {

				$vars['status'] = 'CLOSED';
				$vars['updated_by'] = auth()->user()->id;
				$vars['update_status_closed_resolved'] = Carbon::now()->toDateTimeString();
				$typeevent = 'bs-closed-the-ticket';
			} else if (request('status') == 1) {
				$vars['status'] = 'RESOLVED';
				$vars['updated_by'] = auth()->user()->id;
				$vars['update_status_closed_resolved'] = Carbon::now()->toDateTimeString();
				$typeevent = 'bs-marked-as-resolved';
			} else if (request('status') == 2) {
				$vars['status'] = 'CANCELED';
				$vars['updated_by'] = auth()->user()->id;
				$vars['update_status_canceled'] = Carbon::now()->toDateTimeString();
				$typeevent = 'bs-canceled-the-ticket';
			} else if (request('status') == 3) {
				//REABRINDO TICKET
				$vars['status'] = 'IN_PROGRESS';
				$vars['updated_by'] = auth()->user()->id;
				$typeevent = 'bs-reopened-the-ticket';

				$comp_department = Crypt::decrypt(request('company_department_id'));
				$comp_user = request('company_user_id');

				$check_cucd = CompanyUserCompanyDepartment::where('company_department_id', $comp_department)
					->join('company_user', 'company_user_company_department.company_user_id', 'company_user.id')
					->where('company_user_id', $comp_user)
					->whereNull('company_user.deleted_at')
					->where('company_user.is_active', 1)
					->where('company_user_company_department.is_active', 1)
					->exists();

				if (!$check_cucd) {
					$vars['status'] = 'OPENED';
					$vars['updated_by'] = auth()->user()->id;
					$typeevent = 'bs-reopened-the-ticket';
					$ticket_id = request('id');

					DB::table('user_ticket')
					->where('ticket_id', $ticket_id)
					->delete();

					Chat::where('ticket_id', $ticket_id)->update([
						'comp_user_comp_depart_id_current' => null
					]);
				}
			}

			$check = DB::table('chat_history')
			->where('chat_id', Crypt::decrypt(request('chat_id')))
			->orderBy('id', 'DESC')
			->first();

			if($check == null || $check->content != $typeevent){
				$chat_history_id = DB::table('chat_history')->insertGetId([
					'chat_id' => Crypt::decrypt(request('chat_id')),
					'company_user_company_department_id' => null,
					'type' => 'EVENT',
					'content' => $typeevent,
					'created_by' => auth()->user()->id,
					'created_at' => \Carbon\Carbon::now()->toDateTimeString(),
				]);
			}else{
				$chat_history_id = $check->id;
			}

			if($vars['status'] == 'IN_PROGRESS'){
				$check_cucd = CompanyUserCompanyDepartment::where('company_department_id', $comp_department)
					->where('company_user_id', $comp_user)
					->where('is_active', 1)
					->first();

				ChatWorkingTimes::Insert(Crypt::decrypt(request('chat_id')), $check_cucd->id, $chat_history_id, request('id'));
			}else{
				ChatWorkingTimes::Update(Crypt::decrypt(request('chat_id')));
			}

			$update = DB::table('ticket')
				->where('id', request('id'))
				->update($vars);

			if ($update) {
				// REMOVER TICKET DA LISTA DO ATENDENTE APÓS ALTERAR O STATUS DELE
                $realtime = new SendRealtime(request('id'), 'splice');

                switch (request('original_status_ticket')) {
                    case 'IN_PROGRESS':
                        $realtime->updateTableInProgress();
                        break;

                    case 'CLOSED':
                        $realtime->updateTableClosed();
                        break;

                    case 'RESOLVED':
                        $realtime->updateTableResolved();
                        break;

                    case 'CANCELED':
                        $realtime->updateTableCanceled();
                        break;
                }

                $realtime2 = new SendRealtime(request('id'), 'push');

                switch ($vars['status']) {
					case 'OPENED':
                        $realtime2->updateTableQueue();
                        break;
                    case 'IN_PROGRESS':
                        $realtime2->updateTableInProgress();
                        break;

                    case 'CLOSED':
                        $realtime2->updateTableClosed();
                        break;

                    case 'RESOLVED':
                        $realtime2->updateTableResolved();
                        break;

                    case 'CANCELED':
                        $realtime2->updateTableCanceled();
                        break;
                }

				//ATUALIZAR TICKET DA LISTA DO CLIENTE
				broadcast(new ClientTicketsList([
					'id' => request('id'),
					'hash_id' => Crypt::encrypt(request('id')),
					'company_id' => session('companyselected')['company_id'],
					'user_client_id' => session('companyselected')['user_client_id'],
					'status' => $vars['status'],
					'action' => 'updateStatus',
				]));
			}

			$ticket['success'] = true;
			$ticket['value'] = $vars['status'];
		} catch (\Exception $e) {
			echo $e;
			Logger::reportException($e, [], ['cliente-controller', 'index'], false);
			$ticket['success'] = false;
		}

		echo json_encode($ticket);
	}

    public function clientStatusTicketOld() //apagar
	{
		$ticket['success'] = false;

		try {
			$vars = [];
			//'OPENED','IN_PROGRESS','CLOSED','RESOLVED','CANCELED'

			//LEMBRETE ADICIONAR AQUI A FUNÇÃO PARA O DASHBOARD
			// update_status_closed_resolved' = \Carbon\Carbon::now()->toDateTimeString();
			if (request('status') == 0) {

				$vars['status'] = 'CLOSED';
				$vars['updated_by'] = auth()->user()->id;
				$vars['update_status_closed_resolved'] = Carbon::now()->toDateTimeString();
				$typeevent = 'bs-closed-the-ticket';
			} else if (request('status') == 1) {
				$vars['status'] = 'RESOLVED';
				$vars['updated_by'] = auth()->user()->id;
				$vars['update_status_closed_resolved'] = Carbon::now()->toDateTimeString();
				$typeevent = 'bs-marked-as-resolved';
			} else if (request('status') == 2) {
				$vars['status'] = 'CANCELED';
				$vars['updated_by'] = auth()->user()->id;
				$vars['update_status_canceled'] = Carbon::now()->toDateTimeString();
				$typeevent = 'bs-canceled-the-ticket';
			} else if (request('status') == 3) {
				//REABRINDO TICKET
				$vars['status'] = 'IN_PROGRESS';
				$vars['updated_by'] = auth()->user()->id;
				$typeevent = 'bs-reopened-the-ticket';

				$comp_department = Crypt::decrypt(request('company_department_id'));
				$comp_user = request('company_user_id');

				$check_cucd = CompanyUserCompanyDepartment::where('company_department_id', $comp_department)
					->join('company_user', 'company_user_company_department.company_user_id', 'company_user.id')
					->where('company_user_id', $comp_user)
					->whereNull('company_user.deleted_at')
					->where('company_user.is_active', 1)
					->where('company_user_company_department.is_active', 1)
					->exists();

				if (!$check_cucd) {
					$vars['status'] = 'OPENED';
					$vars['updated_by'] = auth()->user()->id;
					$typeevent = 'bs-reopened-the-ticket';
					$ticket_id = request('id');

					DB::table('user_ticket')
					->where('ticket_id', $ticket_id)
					->delete();

					Chat::where('ticket_id', $ticket_id)->update([
						'comp_user_comp_depart_id_current' => null
					]);
				}
			}

			$check = DB::table('chat_history')
			->where('chat_id', Crypt::decrypt(request('chat_id')))
			->orderBy('id', 'DESC')
			->first();

			if($check == null || $check->content != $typeevent){
				$chat_history_id = DB::table('chat_history')->insertGetId([
					'chat_id' => Crypt::decrypt(request('chat_id')),
					'company_user_company_department_id' => null,
					'type' => 'EVENT',
					'content' => $typeevent,
					'created_by' => auth()->user()->id,
					'created_at' => \Carbon\Carbon::now()->toDateTimeString(),
				]);
			}else{
				$chat_history_id = $check->id;
			}

			if($vars['status'] == 'IN_PROGRESS'){
				$check_cucd = CompanyUserCompanyDepartment::where('company_department_id', $comp_department)
					->where('company_user_id', $comp_user)
					->where('is_active', 1)
					->first();

				ChatWorkingTimes::Insert(Crypt::decrypt(request('chat_id')), $check_cucd->id, $chat_history_id, request('id'));
			}else{
				ChatWorkingTimes::Update(Crypt::decrypt(request('chat_id')));
			}

			$update = DB::table('ticket')
				->where('id', request('id'))
				->update($vars);

			if ($update) {
				// REMOVER TICKET DA LISTA DO ATENDENTE APÓS ALTERAR O STATUS DELE
                $realtime = new SendRealtime(request('id'), 'splice');

                switch (request('original_status_ticket')) {
                    case 'IN_PROGRESS':
                        $realtime->updateTableInProgress();
                        break;

                    case 'CLOSED':
                        $realtime->updateTableClosed();
                        break;

                    case 'RESOLVED':
                        $realtime->updateTableResolved();
                        break;

                    case 'CANCELED':
                        $realtime->updateTableCanceled();
                        break;
                }

                $realtime2 = new SendRealtime(request('id'), 'push');

                switch ($vars['status']) {
					case 'OPENED':
                        $realtime2->updateTableQueue();
                        break;
                    case 'IN_PROGRESS':
                        $realtime2->updateTableInProgress();
                        break;

                    case 'CLOSED':
                        $realtime2->updateTableClosed();
                        break;

                    case 'RESOLVED':
                        $realtime2->updateTableResolved();
                        break;

                    case 'CANCELED':
                        $realtime2->updateTableCanceled();
                        break;
                }

				//ATUALIZAR TICKET DA LISTA DO CLIENTE
				broadcast(new ClientTicketsList([
					'id' => request('id'),
					'company_id' => session('companyselected')['company_id'],
					'user_client_id' => session('companyselected')['user_client_id'],
					'status' => $vars['status'],
					'action' => 'updateStatus',
				]));
			}

			$ticket['success'] = true;
			$ticket['value'] = $vars['status'];
		} catch (\Exception $e) {
			echo $e;
			Logger::reportException($e, [], ['cliente-controller', 'index'], false);
			$ticket['success'] = false;
		}

		echo json_encode($ticket);
	}

	public function getQuestionsClient($id)
	{
		$quest['success'] = false;

		try {
			$quest['settings'] = DB::table('company_department_settings')
				->select('settings')
				->where('company_department_id', Crypt::decrypt($id))
				->first();

			$quest['questionTicket'] = json_decode($quest['settings']->settings)->ticket->desriptionTicket;

		} catch (\Exception $e) {
			$quest['questionTicket'] = null;
		}


		try {
			$quest['result'] = DB::table('company_depart_settings_question')
				->select('id', 'question as quest', 'type', 'mandatory', 'language', 'active')
				->where('company_department_id', Crypt::decrypt($id))
				->where('type_question', 'TICKET')
				->where('active', '1')
				->where('deleted_at', null)
				->get();

			foreach ($quest['result'] as $key) {
				$key->id = Crypt::encrypt($key->id);
			}
			$quest['success'] = true;
		} catch (\Exception $e) {
			Logger::reportException($e, [], ['cliente-get-question-controller', 'show-robot'], false);
			echo $e;
			$quest['success'] = false;
		}
		echo json_encode($quest);
	}

	public function destroy()
	{
	}

	public function teste()
	{
		return view('functions.teste.teste');
	}

	/** HISTORICO DO CLIENTE (CHATS E TICKETS) */

	public function getClientHistory(Request $request)
	{
		$id = Crypt::decrypt($request['client_id']);

		$result = Chat::select(
			"ua_agent.name",
			"chat.id as chat_number",
			"chat.id as chat_id",
			"company_department.name as department",
			"company_department.id as department_id",
			"chat.status",
			"ticket.status as ticket_status",
			"chat.created_at as chat_created_at",
			"chat.updated_at as chat_updated_at",
			"ticket.id as ticket_number",
			"ticket.id as ticket_id",
			"ticket.created_at as ticket_created_at",
			"ticket.updated_at as ticket_updated_at",
			"ticket.description",
			"ticket.comments",
			"ticket.priority",
			"chat.type",
            "chat.comp_user_comp_depart_id_current",
		)
			->join("user_client_chat" , "chat.id" , "user_client_chat.chat_id")
			->join("user_client as uc" , "uc.id" , "user_client_chat.user_client_id")
			->join("user_auth as ua_client" , "ua_client.id" , "uc.user_auth_id")
			->join("company_department" , "company_department.id" , "chat.company_department_id")
            ->leftJoin("company_user_company_department" , "company_user_company_department.id" , "chat.comp_user_comp_depart_id_current")
            ->leftJoin("company_user" , "company_user.id" , "company_user_company_department.company_user_id")
            ->leftJoin("user_auth as ua_agent"  , "ua_agent.id" , "company_user.user_auth_id")
            ->leftJoin("ticket" , "ticket.id" , "chat.ticket_id")
            ->where("ua_client.id", $id)
            ->orderBy("chat.id", "desc")
			->get();

            foreach($result as $row) {
                $row->chat_id = Crypt::encrypt($row->chat_id);
            }

		return $result;
	}

	/** HISTORICO DO CLIENTE (TICKETS) -
	 * TEM QUE SER ALTERADO DEVIDO A CRIAÇÃO DE TICKETS
	 * UM ATENDENTE PODE CRIAR UM TICKET PARA UM CLIENTE
	 * FAZENDO O CREATED_BY NÃO SER DO CLIENTE E SIM DO ATENDENTE
	 * SOLUÇÃO POR HORA, SE UM ATENDENTE CRIAR UM TICKET PARA UM CLIENTE ESTÁ SENDO SETADO O ID DO CLIENTE NA CRIAÇÃO DO TICKET E NÃO DO ATENDENTE.
	 * MAS POR HORA OS ANTIGOS AINDA VÃO FICAR INCORRETOS.
	 * FAZER UMA LIGAÇÃO COM USER_CLIENT_CHAT E USER_CLIENT_TICKET PARA VERIFICAR QUEM É O CLIENTE.
	**/

	// AINDA NÃO ESTÁ SENDO USADO A FUNÇÃO getClientHistoryTicket.
	public function getClientHistoryTicket(Request $request)
	{
		$id = Crypt::decrypt($request['client_id']);

		$result = Chat::select(
			"ua_agent.name",
			"chat.id as chat_number",
			"chat.id as chat_id",
			"company_department.name as department",
			"company_department.id as department_id",
			"chat.status",
			"ticket.status as ticket_status",
			"chat.created_at as chat_created_at",
			"chat.updated_at as chat_updated_at",
			"ticket.id as ticket_number",
			"ticket.id as ticket_id",
			"ticket.created_at as ticket_created_at",
			"ticket.updated_at as ticket_updated_at",
			"ticket.description",
			"ticket.comments",
			"ticket.priority",
			"chat.type",
            "chat.comp_user_comp_depart_id_current",
		)
			->join("user_client_chat" , "chat.id" , "user_client_chat.chat_id")
			->join("user_client as uc" , "uc.id" , "user_client_chat.user_client_id")
			->join("user_auth as ua_client" , "ua_client.id" , "uc.user_auth_id")
			->join("company_department" , "company_department.id" , "chat.company_department_id")
            ->leftJoin("company_user_company_department" , "company_user_company_department.id" , "chat.comp_user_comp_depart_id_current")
            ->leftJoin("company_user" , "company_user.id" , "company_user_company_department.company_user_id")
            ->leftJoin("user_auth as ua_agent"  , "ua_agent.id" , "company_user.user_auth_id")
            ->leftJoin("ticket" , "ticket.id" , "chat.ticket_id")
            ->where("ua_client.id", $id)
            ->orderBy("chat.id", "desc")
			->get();

            foreach($result as $row) {
                $row->chat_id = Crypt::encrypt($row->chat_id);
            }

		return $result;
	}

    public function getChatsData() {
        $response['success'] = false;
        try {

            $company_id = Crypt::decrypt(session('companyselected')['company_id']);

            $qtd_chats_by_status = UserClientChat::select(DB::raw('COUNT(*) as qtd'), 'chat.status')
                ->join('user_client', 'user_client_chat.user_client_id', 'user_client.id')
                ->join('user_auth', 'user_client.user_auth_id', 'user_auth.id')
                ->join('chat', 'user_client_chat.chat_id', 'chat.id')
                ->where('chat.company_id', $company_id)
                ->whereNull('chat.deleted_at')
                ->where('chat.type', 'DEFAULT')
                ->where('user_auth.id', Auth::id())
                ->groupBy('chat.status')
                ->get();

            $qtd_tickets_by_status = UserClientTicket::select(DB::raw('COUNT(*) as qtd'), 'ticket.status')
                ->join('user_client', 'user_client_ticket.user_client_id', 'user_client.id')
                ->join('user_auth', 'user_client.user_auth_id', 'user_auth.id')
                ->join('ticket', 'user_client_ticket.ticket_id', 'ticket.id')
                ->where('ticket.company_id', $company_id)
                ->whereNull('ticket.deleted_at')
                ->where('user_auth.id', Auth::id())
                ->groupBy('ticket.status')
                ->get();

            $temp_array = array(
                'OPENED'        => 0,
                'IN_PROGRESS'   => 0,
                'CLOSED'        => 0,
                'RESOLVED'      => 0,
                'CANCELED'      => 0,
            );

            $response['chats'] = $temp_array;
            $response['tickets'] = $temp_array;

            foreach ($qtd_chats_by_status as $row) {
                $response['chats'] = array($row->status=>$row->qtd)+$response['chats'];
            }

            foreach ($qtd_tickets_by_status as $row) {
                $response['tickets'] = array($row->status=>$row->qtd)+$response['tickets'];
            }

            $response['success'] = true;
        } catch (\Exception $e) {
            Logger::reportException($e, [], ['chat-controller', 'getChatsData'], false);
        }

        return $response;
    }

    public function getLast2Tickets() {
        $response['success'] = false;
        try {
            $company_id = Crypt::decrypt(session('companyselected')['company_id']);
            $tickets = UserClientTicket::select(
                    'ticket.id',
                    'ticket.status',
                    'ticket.created_at as date',
                    'ticket.description',
                    'company_department.name as department'
                )
                ->join('user_client', 'user_client_ticket.user_client_id', 'user_client.id')
                ->join('user_auth', 'user_client.user_auth_id', 'user_auth.id')
                ->join('ticket', 'user_client_ticket.ticket_id', 'ticket.id')
                ->join('company_department', 'ticket.company_department_id', 'company_department.id')
                ->where('ticket.company_id', $company_id)
                ->where('ticket.status', '!=', 'CANCELED')
                ->where('ticket.status', '!=', 'MERGED')
                ->whereNull('ticket.deleted_at')
                ->where('user_auth.id', Auth::id())
                ->groupBy('ticket.id')
                ->orderBy('ticket.id', 'desc')
                ->limit(2)
                ->get();

            foreach ($tickets as $ticket) {
                if (is_null($ticket->description)) {
                    $ticket->description = 'bs-no-description';
                }
                $ticket->hash_id = Crypt::encrypt($ticket->id);
            }

            $response['success'] = true;
            $response['tickets'] = $tickets;

        } catch (\Exception $e) {
            Logger::reportException($e, [], ['chat-controller', 'getLast2Tickets'], false);
        }

        return $response;
    }

    public function getLast2Chats() {
        $response['success'] = false;
        try {
            $company_id = Crypt::decrypt(session('companyselected')['company_id']);

            // pego os 2 ultimos chats
            $chats = UserClientChat::select(
                    'chat.id',
                    'chat.status',
                    'chat.created_at as date',
                    'company_department.name as department'
                )
                ->join('user_client', 'user_client_chat.user_client_id', 'user_client.id')
                ->join('user_auth', 'user_client.user_auth_id', 'user_auth.id')
                ->join('chat', 'user_client_chat.chat_id', 'chat.id')
                ->join('company_department', 'chat.company_department_id', 'company_department.id')
                ->where('chat.company_id', $company_id)
                ->whereNull('ticket_id')
                ->whereNull('chat.deleted_at')
                ->where('chat.is_robot', 0)
                ->where('user_auth.id', Auth::id())
                ->groupBy('chat.id')
                ->orderBy('chat.id', 'desc')
                ->limit(2)
                ->get();

            // verifico se existem os chats (um pelo menos)
            if (count($chats)) {
                $i = 0;
                // pego somente a ultima mensagem, para mostrar na dash (descrição do chat)
                foreach ($chats as $chat) {

                    $last_ch = ChatHistory::select('content')->whereRaw('id = (
                        SELECT MAX(id) FROM chat_history WHERE chat_id = '.$chat->id.'
                    )')->first();
                    $chats[$i]['description'] = !is_null($last_ch) ? $last_ch->content : null;
                    $i++;
                }
            }

            $response['success'] = true;
            $response['chats'] = $chats;

        } catch (\Exception $e) {
            Logger::reportException($e, [], ['chat-controller', 'getLast2Chats'], false);
        }

        return $response;
    }

    public function getChats(Request $request) {
        $response['success'] = false;
        try {
            $company_id = Crypt::decrypt(session('companyselected')['company_id']);
            $content = $request->content;
            $departments = $request->departments;
            $status = $request->status;

            $query = UserClientChat::select(
                'chat.id',
                'chat.description',
                'chat.status',
                'chat.created_at as date',
                'chat.queue_time',
                'chat.comp_user_comp_depart_id_current as cucd_id',
                'company_department.name as department'
            )
            ->join('user_client', 'user_client_chat.user_client_id', 'user_client.id')
            ->join('user_auth', 'user_client.user_auth_id', 'user_auth.id')
            ->join('chat', 'user_client_chat.chat_id', 'chat.id')
            ->join('company_department', 'chat.company_department_id', 'company_department.id')
            ->where('chat.company_id', $company_id)
            ->where('user_auth.id', Auth::id())
            ->whereNull('chat.deleted_at')
            ->whereNull('chat.ticket_id')
            ->where('chat.type', 'DEFAULT')
            ->where('chat.status', '!=', 'CANCELED')
            ->orderBy('chat.id', 'desc')
            ->groupBy('chat.id');

            if ($departments && count($departments)) {
                $i = 0;
                foreach ($departments as $row) {
                    $departments[$i] = Crypt::decrypt($row);
                    $i++;
                }
                $query->whereIn('company_department.id', $departments);
            }

            if ($status && count($status)) {
                $query->whereIn('chat.status', $status);
            }

            if ($content != "") {
                $query->join('chat_history', 'chat_history.chat_id', 'chat.id')
                    ->leftjoin('ticket_chat_answer AS tca_c', 'tca_c.chat_id', 'chat.id');

                $query->where(function ($filter) use ($content) {
                    $filter->where('chat_history.hidden_for_client', 0)
                    ->where('chat_history.type', 'TEXT')
                    ->where('chat_history.content', 'like', '%'.$content.'%')
                    ->orWhere('tca_c.answer', 'like', '%'.$content.'%')
                    ->orWhere('chat.id', 'like', '%'.$content.'%');
                });
            }

            $chats = $query->paginate();

            $array = [];
            foreach ($chats as $chat) {
                array_push($array, $chat);
            }
            $i = 0;
            foreach ($array as $chat) {
                if (isset($chat->cucd_id)) {
                    $cucd = CompanyUserCompanyDepartment::select(
                            'user_auth.id as attendant_id',
                            'user_auth.name as attendant_name',
                            'user_auth.email as attendant_email',
                        )
                        ->join('company_user', 'company_user_company_department.company_user_id','company_user.id')
                        ->join('user_auth', 'company_user.user_auth_id', 'user_auth.id')
                        ->where('company_user_company_department.id', $chat->cucd_id)
                        ->first();
                    if (isset($cucd)) {
                        $array[$i]['attendant_id'] = Crypt::encrypt($cucd->attendant_id);
                        $array[$i]['attendant_name'] = $cucd->attendant_name;
                        $array[$i]['attendant_email'] = $cucd->attendant_email;
                    }
                    $chat->cucd_id = Crypt::encrypt($chat->cucd_id);
                } else {
                    $array[$i]['attendant_id'] = null;
                    $array[$i]['attendant_name'] = null;
                    $array[$i]['attendant_email'] = null;
                }

                $last_ch = ChatHistory::select('content', 'created_at', 'created_by')->whereRaw('id = (
                    SELECT MAX(id) FROM chat_history WHERE chat_id = '.$chat->id.'
                )')->first();
                $array[$i]['latest_ch'] = !is_null($last_ch) ? $last_ch->content : null;
                $array[$i]['date_latest_ch'] = !is_null($last_ch) ? $last_ch->created_at : null;
                $array[$i]['hash_id'] = Crypt::encrypt($chat->id);
                $array[$i]['visible'] = true;
                $array[$i]['agent_answered'] = $chat->status == 'IN_PROGRESS' && $last_ch->created_by != Auth::id();
                $i++;
            }
            $response['success'] = true;
            $response['chats'] = $array;
        } catch (\Exception $e) {
            echo $e;
            Logger::reportException($e, [], ['chat-controller', 'getChats'], false);
        }
        return $response;
    }

    public function getTickets(Request $request) {
        $response['success'] = false;
        try {
            $company_id = Crypt::decrypt(session('companyselected')['company_id']);
            $content = $request->content;
            $departments = $request->departments;
            $status = $request->status;

            $query = UserClientTicket::select(
                'ticket.id',
                'ticket.status',
                'ticket.description',
                'ticket.created_at as date',
                'chat.comp_user_comp_depart_id_current as cucd_id',
                'chat.id as chat_id',
                'company_department.name as department'
            )
            ->join('user_client', 'user_client_ticket.user_client_id', 'user_client.id')
            ->join('user_auth', 'user_client.user_auth_id', 'user_auth.id')
            ->join('ticket', 'user_client_ticket.ticket_id', 'ticket.id')
            ->join('company_department', 'ticket.company_department_id', 'company_department.id')
            ->join('chat', 'chat.ticket_id', 'ticket.id')
            ->where('ticket.company_id', $company_id)
            ->where('ticket.status', '!=', 'CANCELED')
            // ->where('ticket.status', '!=', 'MERGED')
            ->whereNull('ticket.deleted_at')
            ->where('user_auth.id', Auth::id())
            ->orderBy('ticket.id', 'desc')
            ->groupBy('ticket.id');

            if ($departments && count($departments)) {
                $i = 0;
                foreach ($departments as $row) {
                    $departments[$i] = Crypt::decrypt($row);
                    $i++;
                }
                $query->whereIn('company_department.id', $departments);
            }

            if ($status && count($status)) {
                $query->whereIn('ticket.status', $status);
            }

            if ($content != "") {
                $query->leftJoin('chat_history', 'chat_history.chat_id', 'chat.id')
                    ->leftJoin('ticket_chat_answer AS tca_t', 'tca_t.ticket_id', 'ticket.id')
                    ->leftJoin('ticket_chat_answer AS tca_c', 'tca_c.chat_id', 'chat.id');

                $query->where(function ($filter) use ($content) {
                    $filter->where('chat_history.hidden_for_client', 0)
                    ->where('chat_history.type', 'TEXT')
                    ->where('chat_history.content', 'like', '%'.$content.'%')
                    ->orWhere('ticket.description', 'like', '%'.$content.'%')
                    ->orWhere('tca_t.answer', 'like', '%'.$content.'%')
                    ->orWhere('tca_c.answer', 'like', '%'.$content.'%')
                    ->orWhere('ticket.id', 'like', '%'.$content.'%');
                });
            }

            $tickets = $query->paginate();

            $array = [];
            foreach ($tickets as $ticket) {
                array_push($array, $ticket);
            }
            $i = 0;
            foreach ($array as $ticket) {
                if (isset($ticket->cucd_id)) {
                    $cucd = CompanyUserCompanyDepartment::select(
                            'user_auth.id as attendant_id',
                            'user_auth.name as attendant_name',
                            'user_auth.email as attendant_email',
                        )
                        ->join('company_user', 'company_user_company_department.company_user_id','company_user.id')
                        ->join('user_auth', 'company_user.user_auth_id', 'user_auth.id')
                        ->where('company_user_company_department.id', $ticket->cucd_id)
                        ->first();
                    if (isset($cucd)) {
                        $array[$i]['attendant_id'] = Crypt::encrypt($cucd->attendant_id);
                        $array[$i]['attendant_name'] = $cucd->attendant_name;
                        $array[$i]['attendant_email'] = $cucd->attendant_email;
                    }
                    $ticket->cucd_id = Crypt::encrypt($ticket->cucd_id);
                } else {
                    $array[$i]['attendant_id'] = null;
                    $array[$i]['attendant_name'] = null;
                    $array[$i]['attendant_email'] = null;
                }

                if (is_null($ticket->description)) {
                    $ticket->description = 'bs-no-description';
                }

                $last_ch = ChatHistory::select('content', 'created_at', 'created_by')->whereRaw('id = (
                    SELECT MAX(id) FROM chat_history WHERE chat_id = '.$ticket->chat_id.'
                )')->first();
                $array[$i]['latest_ch'] = !is_null($last_ch) ? $last_ch->content : null;
                $array[$i]['date_latest_ch'] = !is_null($last_ch) ? $last_ch->created_at : null;
                $array[$i]['hash_id'] = Crypt::encrypt($ticket->id);
                $array[$i]['visible'] = true;
                $array[$i]['agent_answered'] = $ticket->status == 'IN_PROGRESS' && $last_ch->created_by != Auth::id();
                $i++;
            }
            $response['success'] = true;
            $response['tickets'] = $array;
        } catch (\Exception $e) {
            Logger::reportException($e, [], ['chat-controller', 'getTickets'], false);
        }
        return $response;
    }

    public function getActiveChatsFromCompany() {
        $response['success'] = false;
        try {
            $company_id = Crypt::decrypt(session('companyselected')['company_id']);
            $user_client_id = Crypt::decrypt(session('companyselected')['user_client_id']);

            $settings_chat = CompanySettings::where('company_id', $company_id)->first()->settings_chat;

			if(json_decode($settings_chat)){
				$settings_chat = json_decode($settings_chat)[0]->chatSimCli;
			}

            if (intval($settings_chat) > 0) {
			
                $active_chats = UserClientChat::join('chat', 'chat.id', 'user_client_chat.chat_id')
                    ->where('user_client_chat.user_client_id', $user_client_id)
                    ->whereNull('chat.ticket_id')
                    ->whereNull('chat.deleted_at')
                    ->whereIn('chat.status', ['OPENED', 'IN_PROGRESS'])
                    ->count();

                if ($active_chats < $settings_chat ) {
                    $response['success'] = true;
                    $response['exceeded'] = false;
                } else {
                    $response['success'] = true;
                    $response['exceeded'] = true;
                }

            } else {
                $response['success'] = true;
                $response['exceeded'] = false;
            }
        } catch (\Exception $e) {
            Logger::reportException($e, [], ['chat-controller', 'getActiveChatsFromCompany'], false);
        }
        return $response;
    }

    public function getActiveTicketsFromCompany() {
        $response['success'] = false;
        try {
            $company_id = Crypt::decrypt(session('companyselected')['company_id']);
            $user_client_id = Crypt::decrypt(session('companyselected')['user_client_id']);

            $settings_ticket = CompanySettings::where('company_id', $company_id)->first()->settings_ticket;

            if (intval($settings_ticket) > 0) {
                $active_tickets = UserClientChat::join('chat', 'chat.id', 'user_client_chat.chat_id')
                    ->join('ticket', 'ticket.id', 'chat.ticket_id')
                    ->where('user_client_chat.user_client_id', $user_client_id)
                    ->whereNull('ticket.deleted_at')
                    ->whereIn('ticket.status', ['OPENED', 'IN_PROGRESS'])
                    ->count();

                if ($active_tickets < $settings_ticket ) {
                    $response['success'] = true;
                    $response['exceeded'] = false;
                } else {
                    $response['success'] = true;
                    $response['exceeded'] = true;
                }

            } else {
                $response['success'] = true;
                $response['exceeded'] = false;
            }
        } catch (\Exception $e) {
            Logger::reportException($e, [], ['chat-controller', 'getActiveTicketsFromCompany'], false);
        }
        return $response;
    }

    public function storeTicket(Request $request) {
        $response['success'] = false;
        try {

            $answers = $request->answers;
            $answers_images = $request->answers_images;
            $company_department_id = Crypt::decrypt($request->company_department);
            $questions = $request->questions;
            $onlineUsers = $request->onlineUsers;

            $company_id = Crypt::decrypt(session('companyselected')['company_id']);
            $user_id = auth()->user()->id;
            $user_client_id = Crypt::decrypt(session('companyselected')['user_client_id']);

            $created_at = \Carbon\Carbon::now()->toDateTimeString();

            $ticket = Ticket::create([
                'company_id' => $company_id,
                'company_department_id' => $company_department_id,
                'status' => 'OPENED',
                'type' => 'DEFAULT',
                'priority' => 'NORMAL',
                'user_agent' => $_SERVER['HTTP_USER_AGENT'],
                'created_by' => $user_id,
                'created_at' => $created_at,
            ]);

            UserClientTicket::create([
                'user_client_id' => $user_client_id,
                'ticket_id' => $ticket->id,
                'created_by' => $user_id,
                'created_at' => $created_at,
            ]);

            $chat = Chat::create([
                'company_id' => $company_id,
                'company_department_id' => $company_department_id,
                'ticket_id' => $ticket->id,
                'type' => 'TICKET',
                'priority' => 'NORMAL',
                'user_agent' => $_SERVER['HTTP_USER_AGENT'],
                'created_by' => $user_id,
                'created_at' => $created_at,
                'updated_at' => $created_at,
            ]);

            UserClientChat::create([
                'user_client_id' => $user_client_id,
                'chat_id' => $chat->id,
                'created_by' => $user_id,
                'created_at' => $created_at,
            ]);

            if (count($questions) && count($answers)) {
                for ($i=0; $i < count($answers); $i++) {
                    if (count($answers_images[$i])) {
                        $images = $answers_images[$i];
                        foreach ($images as $image) {
                            $b64 = explode(',', $image)[1];
                            $image_name = Crypt::encrypt(uniqid(md5(uniqid() . microtime())));
                            $img_data = base64_decode($b64);
                            $dir = '..' . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'company' . DIRECTORY_SEPARATOR . $company_id . DIRECTORY_SEPARATOR . 'chat' . DIRECTORY_SEPARATOR . $chat->id . DIRECTORY_SEPARATOR;
                            $filename = $image_name . '.png';
                            if (is_dir($dir)) {
                                $success = file_put_contents($dir.$filename, $img_data);
                            } else {
                                mkdir($dir, 0755, true);
                                $success = file_put_contents($dir.$filename, $img_data);
                            }
                            if ($success) {
                                $answers[$i] = str_replace($image, 'chat/files/'. Crypt::encrypt($chat->id) .'/'.$filename, $answers[$i]);
                            }
                        }
                    }
                    if ($i == 0) {
                        Ticket::where('id', $ticket->id)->update([
                            'description' => $answers[$i]
                        ]);
                        $ticket->description = $answers[$i];
                    } else {
                        TicketChatAnswer::create([
                            'company_depart_settings_question_id' => Crypt::decrypt($questions[$i]['id']),
                            'ticket_id' => $ticket->id,
                            'answer' => $answers[$i],
                            'created_by' => $user_id,
                            'created_at' => $created_at,
                        ]);
                    }
                }
            }

            // Email
            // $send_mail = new MailAdmin($company_id, $user_id, $company_department_id);
            // $send_mail->ticketchatOpened('TICKET', $onlineUsers);
			
			// ENVIA O EMAIL PARA O ATENDENTE DIZENDO QUE ABRIU TICKET
			$emailDepartment = Company_department::where('id', $company_department_id)->first()->toArray();
			$send_mail = new sendEmailCustom($ticket->id,auth()->user(),'','','','','',session('companyselected')['hash_code']);
			$send_mail->ticketchatOpened('TICKET', $onlineUsers, $emailDepartment);

            $company_name = isset(session('companyselected')['name']) ? session('companyselected')['name'] : 'BA Support';
            // if(config('app.env') != 'sandbox' && config('app.env') != 'local') {
            //     // Feedback
            //     Feedback::send('OPENED', $ticket->id, $company_id, $company_name);
            // }
			// ENVIA O EMAIL PARA O CLIENTE DIZENDO QUE ABRIU TICKET
			Feedback::directSendMB('OPENED', $ticket->id, auth()->user()->email);
			

            $rt = new SendRealtime($ticket->id, 'push');
            $rt->updateTableQueue();

            broadcast(new GlobalNotification([
                'title' => 'bs-ticket',
                'body' => 'bs-new-ticket-added-to-the-queue',
                'icon' => 'https://builderall.com//franquias/2/73748/editor-html/3484811.png',
                'url' => '',
                'silent' => false,
                'number' => $ticket->id,
                'type' => 'ticket',
                'status' => 'OPENED',
                'company_department_id' => $request->company_department,
                'company_id' => session('companyselected')['company_id']
            ]));

            $department_name = Company_department::select('name')->where('id', $company_department_id)->first()->name;

            broadcast(new ClientTicketsList([
                'company_id' => session('companyselected')['company_id'],
                'user_client_id' => session('companyselected')['user_client_id'],
                'action' => 'push',
                'hash_id' => Crypt::encrypt($ticket->id),
                'chat_id'  => $chat->id,
                'ticket_id'  => $ticket->id,
                'description'  => $ticket->description,
                'status' => $ticket->status,
                'created_at' => $ticket->created_at,
                'department' => $department_name
            ]));

            $response['success'] = true;

        } catch (\Exception $e) {
            Logger::reportException($e, [], ['client-controller', 'storeTicket'], false);
        }
        return $response;
    }


    public function getChatInProgress() {
		$response['success'] = false;
        try {
			$company_id = Crypt::decrypt(session('companyselected')['company_id']);
			
            $chat = UserClientChat::select('chat.id')
			->join('user_client', 'user_client_chat.user_client_id', 'user_client.id')
			->join('user_auth', 'user_client.user_auth_id', 'user_auth.id')
			->join('chat', 'user_client_chat.chat_id', 'chat.id')
                ->join('company_department', 'chat.company_department_id', 'company_department.id')
                ->where('chat.company_id', $company_id)
                ->whereNull('ticket_id')
                ->whereNull('chat.deleted_at')
                ->whereIn('chat.status', ['IN_PROGRESS', 'OPENED', 'ROBOT'])
                ->where('user_auth.id', Auth::id())
                ->groupBy('chat.id')
                ->orderBy('chat.id', 'desc')
                ->first();
				
				$id = $chat ? Crypt::encrypt($chat->id) : null;
				
				$response['success'] = true;
				$response['exists'] = !is_null($chat);
				$response['id'] = $id;
				
			} catch (\Exception $e) {
				Logger::reportException($e, [], ['chat-controller', 'getChatInProgress'], false);
			}
			
		return $response;
	}

	public function getConfigCompany() {
		$response['success'] = false;
        try {

			$company_id = Crypt::decrypt(session('companyselected')['company_id']);

			$company_settings = DB::table('company_settings')
			->select('general')
			->where('company_id', $company_id)
			->first();

			if(isset(json_decode($company_settings->general)->titlechatclient) && isset(json_decode($company_settings->general)->titleticketclient)){
				$var = [json_decode($company_settings->general)->titlechatclient, json_decode($company_settings->general)->titleticketclient];
			}else{
				$var = 'false';
			}
			
			$response['value'] = $var;
			$response['success'] = true;
			
		} catch (\Exception $e) {
			Logger::reportException($e, [], ['chat-controller', 'getConfigCompany'], false);
		}
			
		return $response;
		
	}

	public function getNameRobot() {
		$response['success'] = false;
        try {
			$company_id = Crypt::decrypt(session('companyselected')['company_id']);

			$company_settings = DB::table('company_settings')
			->select('general')
			->where('company_id', $company_id)
			->first();
			
			if(isset(json_decode($company_settings->general)->nameRobot)){
				$var = json_decode($company_settings->general)->nameRobot;
			}else{
				$var = null;
			}
			
			if($var == null){
				$check = DB::table('company_faq_robot')
					->join('company_faq_robot_info', 'company_faq_robot.id', 'company_faq_robot_info.company_faq_robot_id')
					->where('company_id',Crypt::decrypt(session('companyselected')['id']))
					->where('is_active', 1)
					->where('company_faq_robot_info.language', auth()->user()->language)
					->select('name_robot')
					->first();
				
				if($check != null){
					$var = $check->name_robot;
				}
			}

			if (empty(trim($var))) {
				$var = null;
			} 
			
			$response['value'] = $var;
			$response['success'] = true;
			
		} catch (\Exception $e) {
			Logger::reportException($e, [], ['chat-controller', 'getConfigCompany'], false);
		}
			
		return $response;
		
	}









}
