<?php

namespace App\Http\Controllers;

use App\Avaliation;
use App\Events\TicketsListUpdate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Tools\Crypt;
use App\Models\Company_department;
use App\Tools\Builderall\MailCentral;
use App\Tools\Builderall\Logger;
use Exception;
use App\Tools\Tickets\Mail;
use App\Tools\Tickets\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Broadcast;
use App\Chat;
use App\Ticket;
use App\ChatHistory;
use App\Events\ClientTicketsList;
use App\Events\ClientTicketAnswer;
use App\Events\MessageSentTicket;
use App\Events\TicketsAgentListUpdate;
use App\Events\FullTicket;
use App\Models\UserClientTicket;
use App\Tools\Tickets\Feedback;
use App\Tools\SearchFilter;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;
use App\CompanyUserCompanyDepartment;
use App\Events\ChatTicketDelete;
use App\Events\TicketCommentUpdate;
use App\Models\CompanyDepartmentSettings;
use App\TicketComments;
use App\Tools\ClearEmail;
use App\User;
use App\UserClientChat;
use App\Tools\BlockUsers;
use App\Tools\ChatWorkingTimes;
use App\Tools\Tickets\SendRealtime;
use App\UserAuth;

class TicketController extends Controller
{
	public function index()
	{
		return view('functions.employee.ticket.ticket');

		// if(session('restriction')[0]->ticket_admin){
		// 	return view('functions.employee.ticket.ticketAdmin');
		// }else{
		// 	return view('functions.employee.ticket.ticket');
		// }
	}

	//CRIAÇÃO DE TICKET DO ATENDENTE
	public function createTicket(Request $request)
	{
		$department['success'] = false;

		try {

			if(request('company_user_id_selected') == null){
				$cucd_id = null;
			}else{
				//VERIFICA SE TEM ATENDENTE ATRELADO
				$cucd_id = DB::table('company_user_company_department')
				->join('company_user', 'company_user.id', 'company_user_company_department.company_user_id')
				->where('company_user_id', Crypt::decrypt(request('company_user_id_selected')))
				->where('company_department_id', Crypt::decrypt(request('department')))
				->select('company_user_company_department.id as id', 'company_user.user_auth_id')
				->first();
			}

			$description = $request['textTicket'];
	
			$result = Ticket::create([
				'company_id' => Crypt::decrypt(session('companyselected')['id']),
				'company_department_id' => Crypt::decrypt(request('department')),
				'status' => request('status'),
				'type' => 'DEFAULT',
				'priority' => request('priority'),
				'user_agent' => $_SERVER['HTTP_USER_AGENT'],
				'comments' => request('comment'),
				'created_by' => request('clientselected')['id'],
			]);

			$id = $result->id;

			//SE NÃO TEM ...
			if ($cucd_id == null) {

				$chat_id = DB::table('chat')->insertGetId([
					'company_id' => Crypt::decrypt(session('companyselected')['id']),
					'company_department_id' => Crypt::decrypt(request('department')),
					'comp_user_comp_depart_id_creator' => null,
					'comp_user_comp_depart_id_current' => null,
					'ticket_id' => $id,
					'type' => 'TICKET',
					'priority' => request('priority'),
					'user_agent' => $_SERVER['HTTP_USER_AGENT'],
					'created_by' => request('clientselected')['id'],
					'created_at' => \Carbon\Carbon::now()->toDateTimeString(),
					'updated_at' => \Carbon\Carbon::now()->toDateTimeString(),
				]);

				$arrayName = array(
					'user' => intval(auth()->user()->id),
					'companyselected' => intval(Crypt::decrypt(session('companyselected')['id'])),
					'is_admin' => intval(session('is_admin')),
					'ticket_id' => $id,
				);

				$query = 'SELECT t.id, t.status, t.description, t.created_at, t.updated_at, t.created_by, c.created_by as chat_created_by,
							t.priority, t.type, t.user_agent, t.comments, t.update_status_in_progress, null as name, null as email, cd.name AS department,
							cd.type as department_type, cd.id AS department_id, cucd.id as company_user_company_department_id, cds.settings,
							COALESCE(ua_client.id, ua_create.id)  AS id_created,
							COALESCE(ua_client.name, ua_create.name)  AS name_created,
							COALESCE(ua_client.email, ua_create.email) AS email_created,
							ua_client.builderall_account_data AS builderall_account_data, c.service_time, c.update_status_in_progress as last_update_status, c.id as chat_id, cucd.company_user_id, c.type as chat_type, IF((SELECT company_user_company_department_id FROM chat_history WHERE chat_id = c.id ORDER BY id DESC LIMIT 1) IS NULL, 1, 0) AS answered
							FROM ticket t
							JOIN company_department cd ON t.company_department_id = cd.id
							JOIN company_department_settings cds ON cd.id = cds.company_department_id
							JOIN company_user_company_department cucd ON cds.company_department_id = cucd.company_department_id
							JOIN company_user cu ON cucd.company_user_id = cu.id
							JOIN user_auth ua_create ON t.created_by = ua_create.id
							LEFT JOIN chat c ON t.id = c.ticket_id
							LEFT JOIN user_client_ticket uct ON t.id = uct.ticket_id
							LEFT JOIN user_client uc ON uct.user_client_id = uc.id
							LEFT JOIN user_auth ua_client ON uc.user_auth_id = ua_client.id
							WHERE cu.company_id = :companyselected -- AND c.id IS NULL
							AND (cu.user_auth_id = :user OR :is_admin = 1)
							AND t.`status` IN ("OPENED")
							AND t.id = :ticket_id
							AND cd.deleted_at IS NULL
							AND c.deleted_at IS NULL
							AND t.deleted_at IS NULL
							-- AND DATE(t.created_at) >= "2021-01-26"
							-- AND DATE(t.created_at) <= "2021-01-27"
							GROUP BY cd.id, t.id';

				$tickets['result'] = DB::select($query, $arrayName);

				foreach ($tickets['result'] as $key) {
					$key->department_id = Crypt::encrypt($key->department_id);
					$key->chat_id_decry = $key->chat_id;
					$key->chat_id = Crypt::encrypt($key->chat_id);
					$key->company_user_company_department_id = Crypt::encrypt($key->company_user_company_department_id);
					$key->overdue = json_decode($key->settings)->quant_limity->lateTime;
					$key->id_encrypted = Crypt::encrypt($key->id);
					$key->created_by_encrypted = Crypt::encrypt($key->created_by);
					$key->chat_created_by_encrypted = Crypt::encrypt($key->id_created);
					//$key->company_id = Crypt::encrypt($key->company_id);
				}

				if($tickets['result'] != []){
                    $realtime = new SendRealtime($id, 'push');
                    $realtime->updateTableQueue();
				}

			} else {
				//ATRELA O ATENDENTE AO TICKET
				DB::table('user_ticket')->insertGetId([
					'company_user_id' => Crypt::decrypt(request('company_user_id_selected')),
					'ticket_id' => $id,
					'created_by' => auth()->user()->id,
					'created_at' => \Carbon\Carbon::now()->toDateTimeString(),
				]);

				$chat_id = DB::table('chat')->insertGetId([
					'company_id' => Crypt::decrypt(session('companyselected')['id']),
					'company_department_id' => Crypt::decrypt(request('department')),
					'comp_user_comp_depart_id_creator' => $cucd_id->id,
					'comp_user_comp_depart_id_current' => $cucd_id->id,
					'ticket_id' => $id,
					'type' => 'TICKET',
					'priority' => request('priority'),
					'user_agent' => $_SERVER['HTTP_USER_AGENT'],
					'created_by' => request('clientselected')['id'],
					'created_at' => \Carbon\Carbon::now()->toDateTimeString(),
					'updated_at' => \Carbon\Carbon::now()->toDateTimeString(),
				]);


				$chat_history_id = DB::table('chat_history')->insertGetId([
					'chat_id' => $chat_id,
					'company_user_company_department_id' => $cucd_id->id,
					'type' => 'EVENT',
					'content' => 'bs-attendant-created-a-ticket',
					'created_by' => auth()->user()->id,
					'created_at' => \Carbon\Carbon::now()->toDateTimeString(),
				]);

				ChatWorkingTimes::Insert($chat_id, $cucd_id->id, $chat_history_id, $id);

				//realtime lista em progresso

                $realtime = new SendRealtime($id, 'push');
                $realtime->updateTableInProgress();

			}

			if (!is_null($request['images'])) {
				$images = $request['images'];
				foreach ($images as $row) {
					// Define the Base64 value you need to save as an image
					$b64 = explode(',', $row)[1];
					$image_name = Crypt::encrypt(uniqid(md5(uniqid() . microtime())));
					$data = base64_decode($b64);
					$dir = '..' . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'company' . DIRECTORY_SEPARATOR . Crypt::decrypt(session('companyselected')['id']) . DIRECTORY_SEPARATOR . 'chat' . DIRECTORY_SEPARATOR . $chat_id . DIRECTORY_SEPARATOR;
					$filename = $image_name . '.png';

					// Caso já exista o diretório, apenas movo o arquivo pra dentro dele, se não, crio a pasta e movo o arquivo em seguida...
					if (is_dir($dir)) {
						$success = file_put_contents($dir.$filename, $data);
					} else {
						mkdir($dir, 0755, true);
						$success = file_put_contents($dir.$filename, $data);
					}

					if ($success) {
						$description = str_replace($row, 'chat/files/'. Crypt::encrypt($chat_id) .'/'.$filename, $description);
					}
				}
			} 

			DB::table('ticket')->where('id', $id)->update([
				'description' => $description
			]);

			//VINCULA O CLIENTE AO TICKET E CHAT
			if (request('clientselected') != null) {
				DB::table('user_client_ticket')->insertGetId([
					'ticket_id' => $id,
					'user_client_id' => request('clientselected')['user_client_id'],
					'created_by' => auth()->user()->id,
					'created_at' => \Carbon\Carbon::now()->toDateTimeString(),
				]);
				DB::table('user_client_chat')->insertGetId([
					'user_client_id' => request('clientselected')['user_client_id'],
					'chat_id' => $chat_id,
					'created_by' => auth()->user()->id,
					'created_at' => \Carbon\Carbon::now()->toDateTimeString(),
				]);
			}

            $department_name = Company_department::select('name')->where('id', Crypt::decrypt(request('department')))->first()->name;
            $client_ticket_list = [
                'company_id' => session('companyselected')['id'],
                'user_client_id' => Crypt::encrypt(request('clientselected')['user_client_id']),
                'action' => 'push',
                'hash_id' => Crypt::encrypt($id),
                'chat_id'  => $chat_id,
                'ticket_id'  => $id,
                'description'  => $description,
                'status' => request('status'),
                'created_at' => \Carbon\Carbon::now()->toDateTimeString(),
                'department' => $department_name
            ];

            

            if (isset($cucd_id)){
                $attendant = UserAuth::select('name', 'email', 'id')
                ->where('id', $cucd_id->user_auth_id)
                ->first();
                $attendant_data = [
                    'attendant_name' => $attendant->name,
                    'attendant_email' => $attendant->email,
                    'attendant_id' => $attendant->id,
                    'cucd_id' => $cucd_id->id,
                ];

                $client_ticket_list = array_merge($client_ticket_list, $attendant_data);
            }
     
            broadcast(new ClientTicketsList($client_ticket_list));

			//REAL TIME PARA ADICIONAR NA FILA APOS CADASTRO DE FUNCIONARIO
			$department['success'] = true;
			$department['id'] = $id;
			$department['chat_id'] = Crypt::encrypt($chat_id);
			$department['company_department_id'] = request('department');
			$department['created'] = \Carbon\Carbon::now()->toDateTimeString();
		} catch (\Exception $e) {
			echo $e;
			Logger::reportException($e, [], ['department-controller', 'index'], false);
			$department['success'] = false;
		}

		echo json_encode($department);
	}

	public function ticketUpdateDate(){
		DB::table('ticket')
		->where('id', request('id'))
		->update([
			'created_at' => \Carbon\Carbon::now()->toDateTimeString(),
			'updated_by' => auth()->user()->id,
		]);
		return 'DATA ATUALIZADA';
	}
	public function getDepartmentUser()
	{
		$department['success'] = false;

		try {

			$department['result'] = Company_department::leftjoin('company_department_settings', 'company_department.id', 'company_department_settings.company_department_id')
			->select('company_department.id', 'company_department.name', 'company_department.description', 'company_department.module',
			'company_department.type', 'company_department.company_user_id', 'company_department.is_active', 'company_department_settings.settings')
			->where('company_department.company_id', Crypt::decrypt(session('companyselected')['id']))
			->where('company_department.is_active', 1)
			->where('company_department.module', '!=', 'CHAT')
			->orderBy('company_department.name')
			->get();

			foreach ($department['result'] as $key) {
				$key->id = Crypt::encrypt($key->id);
				$key->company_user_id = Crypt::encrypt($key->company_user_id);
			}

			$department['success'] = true;
		} catch (\Exception $e) {
			echo $e;
			Logger::reportException($e, [], ['department-controller', 'show'], false);
			$department['success'] = false;
		}

		return json_encode($department);
	}

	public function getAgents()
	{
		$agentsUsers['success'] = false;

		try {
			if(request('department_id') == null){
				$agentsUsers['result'] = DB::table('user_auth')
				->join('company_user', 'company_user.user_auth_id', 'user_auth.id')
				->join('company_user_company_department', 'company_user.id', 'company_user_company_department.company_user_id')
				->select('user_auth.id', 'user_auth.name', 'company_user.id as company_user_id', 'company_user.is_active', 'user_auth.email', 'company_user.last_login')
				->where('company_user.company_id', Crypt::decrypt(session('companyselected')['id']))
				->where('company_user.is_admin', 0)
				->where('company_user_company_department.is_active', 1)
				->whereNull('company_user.deleted_at')
				->orderBy('user_auth.name')
				->groupBy('user_auth.id')
				->get();	
			}else{
				$agentsUsers['result'] = DB::table('user_auth')
				->join('company_user', 'company_user.user_auth_id', 'user_auth.id')
				->join('company_user_company_department', 'company_user.id', 'company_user_company_department.company_user_id')
				->select('user_auth.id', 'user_auth.name', 'company_user.id as company_user_id', 'company_user.is_active', 'user_auth.email', 'company_user.last_login')
				->where('company_user.company_id', Crypt::decrypt(session('companyselected')['id']))
				->where('company_user.is_admin', 0)
				->where('company_user_company_department.is_active', 1)
				->where('company_user_company_department.company_department_id', Crypt::decrypt(request('department_id')))
				->whereNull('company_user.deleted_at')
				->orderBy('user_auth.name')
				->get();
			}

			//add filter admin is show

			foreach ($agentsUsers['result'] as $key) {
				$key->id = Crypt::encrypt($key->id);
				$key->company_user_id = Crypt::encrypt($key->company_user_id);
			}

			$agentsUsers['success'] = true;
		} catch (\Exception $e) {
			echo $e;
			Logger::reportException($e, [], ['agents-controller', 'show'], false);
			$agentsUsers['success'] = false;
		}

		echo json_encode($agentsUsers);
	}

	public function getTickets(){ // alterado

		$tickets['success'] = false;

		$data =  request()->all();
		$validator = Validator::make($data, [
			'skip' => 'required|int',
		]);

		if ($validator->fails()) {
			return response($validator->errors(), 400);
		}

		$concat = '';
		foreach ($data['departmentSelected'] as $key){
			$concat = $concat ."'" .Crypt::decrypt(json_decode($key)->id) ."',";
		}
		$concat = $concat."''";
		$OPENED = request('type');

		try {

			if ($OPENED == 'OPENED') {
				$arrayName = array(
					'user' => intval(auth()->user()->id),
					'companyselected' => intval(Crypt::decrypt(session('companyselected')['id'])),
					'is_admin' => intval(session('is_admin')),
				);

				$query = 'SELECT * FROM (
								SELECT t.id, t.status, t.description, t.created_at, t.updated_at, t.created_by, c.created_by as chat_created_by,
								t.priority, t.type, t.user_agent, t.comments, t.update_status_in_progress, null as name, null as email, cd.name AS department,
								cd.type as department_type, cd.id AS department_id, cucd.id as company_user_company_department_id, cds.settings,
								COALESCE(ua_client.id, ua_create.id)  AS id_created,
								COALESCE(ua_client.name, ua_create.name)  AS name_created,
								COALESCE(ua_client.email, ua_create.email) AS email_created,
								ua_client.builderall_account_data AS builderall_account_data, c.service_time, c.update_status_in_progress as last_update_status, c.id as chat_id, cucd.company_user_id, c.type as chat_type,
								IF((SELECT company_user_company_department_id FROM chat_history WHERE chat_id = c.id ORDER BY id DESC LIMIT 1) IS NULL, 1, 0) AS answered
								FROM ticket t
								JOIN company_department cd ON t.company_department_id = cd.id
								JOIN company_department_settings cds ON cd.id = cds.company_department_id
								JOIN company_user_company_department cucd ON cds.company_department_id = cucd.company_department_id
								JOIN company_user cu ON cucd.company_user_id = cu.id
								JOIN user_auth ua_create ON t.created_by = ua_create.id
								LEFT JOIN chat c ON t.id = c.ticket_id
								LEFT JOIN user_client_ticket uct ON t.id = uct.ticket_id
								LEFT JOIN user_client uc ON uct.user_client_id = uc.id
								LEFT JOIN user_auth ua_client ON uc.user_auth_id = ua_client.id
								WHERE cu.company_id = :companyselected -- AND c.id IS NULL
								AND (cu.user_auth_id = :user OR :is_admin = 1)
								AND cucd.is_active = 1 -- ATUALIZAÇÃO AQUI PARA PEGAR APENAS SE ESTIVAR ATIVADO
								AND t.`status` IN ("OPENED")
								AND cd.id IN ('.$concat.')
								AND t.deleted_at IS NULL
								AND c.deleted_at IS NULL
								AND cd.deleted_at IS NULL
								-- AND DATE(t.created_at) >= "2021-01-26"
								-- AND DATE(t.created_at) <= "2021-01-27"
								GROUP BY cd.id, t.id
							) sub ORDER BY sub.created_at ASC';

			}else if ($OPENED == 'ALLTICKET') {
				$arrayName = array(
					'user' => intval(auth()->user()->id),
					'companyselected' => intval(Crypt::decrypt(session('companyselected')['id'])),
					'is_admin' => intval(session('is_admin')),
				);

				$query = 'SELECT * FROM (
							SELECT t.id, t.status, t.description, t.created_at, t.updated_at, t.created_by, c.created_by as chat_created_by,
							t.priority, t.type, t.user_agent, t.comments, t.update_status_in_progress, null as name, null as email, cd.name AS department,
							cd.type as department_type, cd.id AS department_id, cucd.id as company_user_company_department_id, cds.settings,
							COALESCE(ua_client.id, ua_create.id) AS id_created,
							COALESCE(ua_client.name, ua_create.name)  AS name_created,
							COALESCE(ua_client.email, ua_create.email) AS email_created,
							ua_client.builderall_account_data AS builderall_account_data, c.service_time, c.update_status_in_progress as last_update_status, c.id as chat_id, cucd.company_user_id, c.type as chat_type, IF((SELECT company_user_company_department_id FROM chat_history WHERE chat_id = c.id ORDER BY id DESC LIMIT 1) IS NULL, 1, 0) AS answered
							FROM ticket t
							JOIN company_department cd ON t.company_department_id = cd.id
							JOIN company_department_settings cds ON cd.id = cds.company_department_id
							JOIN company_user_company_department cucd ON cds.company_department_id = cucd.company_department_id
							JOIN company_user cu ON cucd.company_user_id = cu.id
							JOIN user_auth ua_create ON t.created_by = ua_create.id
							LEFT JOIN chat c ON t.id = c.ticket_id
							LEFT JOIN user_client_ticket uct ON t.id = uct.ticket_id
							LEFT JOIN user_client uc ON uct.user_client_id = uc.id
							LEFT JOIN user_auth ua_client ON uc.user_auth_id = ua_client.id
							WHERE cu.company_id = :companyselected -- AND c.id IS NULL
							AND (cu.user_auth_id = :user OR :is_admin = 1)
							AND t.`status` != ("OPENED")
							AND cd.id IN ('.$concat.')
							AND t.deleted_at IS NULL
							AND c.deleted_at IS NULL
							AND cd.deleted_at IS NULL
							-- AND DATE(t.created_at) >= "2021-01-26"
							-- AND DATE(t.created_at) <= "2021-01-27"
							GROUP BY cd.id, t.id
						) sub ORDER BY answered DESC, sub.updated_at';
			}else {
				if($OPENED == "OVERDUE"){
					$OPENED = 'IN_PROGRESS';
				}

				$arrayName = array(
					'user' => intval(auth()->user()->id),
					'companyselected' => intval(Crypt::decrypt(session('companyselected')['id'])),
					'is_admin' => intval(session('is_admin')),
					'status' => $OPENED,
				);

				$query =
						'SELECT * FROM (
							SELECT t.id, t.status, t.description, t.created_at, t.updated_at, t.created_by, c.created_by as chat_created_by,
							t.priority, t.type, t.user_agent, t.comments, t.update_status_in_progress, ua.name, ua.email, cd.name AS department,
							cd.type as department_type, cd.id AS department_id, cucd.id as company_user_company_department_id, cds.settings,
							COALESCE(ua_client.id, ua_create.id)  AS id_created,
							COALESCE(ua_client.name, ua_create.name)  AS name_created,
							COALESCE(ua_client.email, ua_create.email) AS email_created,
							ua_client.builderall_account_data AS builderall_account_data, c.service_time, c.update_status_in_progress as last_update_status, c.id as chat_id, cucd.company_user_id, c.type as chat_type, IF((SELECT company_user_company_department_id FROM chat_history WHERE chat_id = c.id ORDER BY id DESC LIMIT 1) IS NULL, 1, 0) AS answered
							FROM ticket t
							JOIN user_ticket ut ON t.id = ut.ticket_id
							JOIN company_department cd ON t.company_department_id = cd.id
							JOIN company_department_settings cds ON cd.id = cds.company_department_id
							JOIN chat c ON t.id = c.ticket_id
							JOIN user_auth ua_create ON t.created_by = ua_create.id
							JOIN company_user_company_department cucd ON c.comp_user_comp_depart_id_current = cucd.id
							JOIN company_user cu ON cucd.company_user_id = cu.id AND (cu.user_auth_id = :user OR :is_admin = 1)
							JOIN user_auth ua ON cu.user_auth_id = ua.id
							LEFT JOIN user_client_ticket uct ON t.id = uct.ticket_id
							LEFT JOIN user_client uc ON uct.user_client_id = uc.id
							LEFT JOIN user_auth ua_client ON uc.user_auth_id = ua_client.id
							WHERE t.company_id = :companyselected
							AND t.`status` IN (:status)
							AND cd.id IN ('.$concat.')
							-- AND cucd.is_active = 1 -- ESTÁ COMENTANDO AQUI DEVIDO A SITUAÇÃO NA QUAL SE UM ATENDENTE JÁ TINHA TICKETS ANTES DE SER REMOVIDO DE UM DEPARTAMENTO ELE CONTINUA VENDO ELES.
							AND cd.deleted_at IS NULL
							AND c.deleted_at IS NULL
							AND t.deleted_at IS NULL
							GROUP BY cd.id, t.id
						) sub ORDER BY answered DESC, sub.updated_at';
			}

			$take = request('quantRows');
			$query .= " LIMIT :skip, :take";
			$arrayName['skip'] = request('skip');
			$arrayName['take'] = $take;

			$tickets['status'] = $OPENED;
			$tickets['result'] = DB::select($query, $arrayName);

			foreach ($tickets['result'] as $key) {
				$key->department_id = Crypt::encrypt($key->department_id);
				$key->chat_id_decry = $key->chat_id;
				$key->chat_id = Crypt::encrypt($key->chat_id);
				$key->company_user_company_department_id = Crypt::encrypt($key->company_user_company_department_id);
				$key->overdue = json_decode($key->settings)->quant_limity->lateTime;
				$key->id_encrypted = Crypt::encrypt($key->id);
				$key->created_by_encrypted = Crypt::encrypt($key->created_by);
				$key->chat_created_by_encrypted = Crypt::encrypt($key->id_created);
				$key->email_created = ClearEmail::clear($key->email_created);
			}

			$arrayName2 = array(
				'companyselected' => intval(Crypt::decrypt(session('companyselected')['id'])),
				'companyselected2' => intval(Crypt::decrypt(session('companyselected')['id'])),
				'company_user_id' => intval(Crypt::decrypt(session('companyselected')['company_user_id'])),
				'company_user_id2' => intval(Crypt::decrypt(session('companyselected')['company_user_id'])),
			);
			//LEMBRE ARRUMAR ISSO
			$query2 = "SELECT
							SUM(CLOSED) AS CLOSED,
							SUM(RESOLVED) AS RESOLVED,
							SUM(CANCELED) AS CANCELED,
							SUM(OPENED) AS OPENED,
							SUM(IN_PROGRESS) AS IN_PROGRESS
						FROM (
							SELECT
								COALESCE(SUM(IF(t.STATUS = 'CLOSED', 1, 0)), 0) AS CLOSED,
								COALESCE(SUM(IF(t.STATUS = 'RESOLVED', 1, 0)), 0) AS RESOLVED,
								COALESCE(SUM(IF(t.STATUS = 'CANCELED', 1, 0)), 0) AS CANCELED,
								0 AS OPENED,
								COALESCE(SUM(IF(t.STATUS = 'IN_PROGRESS', 1, 0)), 0) AS IN_PROGRESS
							FROM ticket AS t
							LEFT JOIN user_ticket AS ut ON t.id = ut.ticket_id
							LEFT JOIN company_user AS cu ON ut.company_user_id = cu.id
							LEFT JOIN company_user_company_department AS cucd ON cu.id = cucd.company_user_id
							AND t.company_department_id = cucd.company_department_id
							WHERE t.company_id = :companyselected AND cucd.company_user_id = :company_user_id
                            AND t.deleted_at IS NULL

							UNION

							SELECT
								0 AS CLOSED,
								0 AS RESOLVED,
								0 AS CANCELED,
								COUNT(tt.id) AS OPENED,
								0 AS IN_PROGRESS
								FROM ticket AS tt
								JOIN company_user_company_department cucd ON tt.company_department_id = cucd.company_department_id
								WHERE tt.company_id = :companyselected2 AND cucd.company_user_id = :company_user_id2 AND tt.status = 'OPENED'
								AND cucd.is_active = 1 -- ATUALIZAÇÃO AQUI PARA PEGAR APENAS SE ESTIVAR ATIVADO O DEPARTAMENTO
                                AND tt.deleted_at IS NULL
						) sub";

			$tickets['success'] = true;
			$tickets['count'] = DB::select($query2, $arrayName2);

			//----------------------CÁLCULO PARA VERIFICAR A QUANTIDADE DE TICKETS EM ATRAZO
			$agora = now();
			$time_final = strtotime($agora);
			$tickets['count'][0]->OVERDUE = 0; // criando a variavel

			$ticketsInprogress = $this::ticketsInprogressOverdue();

			foreach($ticketsInprogress as $key){
				$time_inicial = strtotime($key->updated_at);
				// Calcula a diferença de segundos entre as duas datas:
				$diferenca = $time_final - $time_inicial; // 19522800 segundos
				$diffInMinutes = intval($diferenca / 60);

				if($diffInMinutes >= $key->overdue && $key->overdue != 0){
					$tickets['count'][0]->OVERDUE++;
					//$key->status = 'OVERDUE';
				}
			}
			//CONVERTE EM STRING
			$tickets['count'][0]->OVERDUE = $tickets['count'][0]->OVERDUE."";
			//----------------------CÁLCULO PARA VERIFICAR A QUANTIDADE DE TICKETS EM ATRAZO

			if($tickets['result'] == []){

				$searchDepartment = DB::table('company_user_company_department')
                ->select('id')
                ->where('company_user_id', Crypt::decrypt(session('companyselected')['company_user_id']))
                ->where('is_active', 1)
                ->get();
				if($searchDepartment == '[]'){
					$tickets['department'] = 'not_department';
				}
			}

			$tickets['skip'] = request('skip') + $take;
		} catch (\Exception $e) {
			echo $e;
			Logger::reportException($e, [], ['getTickets', 'show'], false);
			$tickets['success'] = false;
		}

		echo json_encode($tickets);
	}
	public static function ticketsInprogressOverdue(){

		$arrayName = array(
			'user' => intval(auth()->user()->id),
			'companyselected' => intval(Crypt::decrypt(session('companyselected')['id'])),
			'is_admin' => intval(session('is_admin')),
			'status' => 'IN_PROGRESS',
		);

		$query =
				'SELECT t.id, t.updated_at, cds.settings
					FROM ticket t
					JOIN user_ticket ut ON t.id = ut.ticket_id
					JOIN company_department cd ON t.company_department_id = cd.id
					JOIN company_department_settings cds ON cd.id = cds.company_department_id
					JOIN chat c ON t.id = c.ticket_id
					JOIN user_auth ua_create ON t.created_by = ua_create.id
					JOIN company_user_company_department cucd ON c.comp_user_comp_depart_id_current = cucd.id
					JOIN company_user cu ON cucd.company_user_id = cu.id AND (cu.user_auth_id = :user OR :is_admin = 1)
					JOIN user_auth ua ON cu.user_auth_id = ua.id
					LEFT JOIN user_client_ticket uct ON t.id = uct.ticket_id
					LEFT JOIN user_client uc ON uct.user_client_id = uc.id
					LEFT JOIN user_auth ua_client ON uc.user_auth_id = ua_client.id
					WHERE t.company_id = :companyselected
					AND t.`status` IN (:status)
					-- AND cucd.is_active = 1 -- ESTÁ COMENTANDO AQUI DEVIDO A SITUAÇÃO NA QUAL SE UM ATENDENTE JÁ TINHA TICKETS ANTES DE SER REMOVIDO DE UM DEPARTAMENTO ELE CONTINUA VENDO ELES.
					AND cd.deleted_at IS NULL
					AND t.deleted_at IS NULL
					AND c.deleted_at IS NULL
					GROUP BY cd.id, t.id';

		$result= DB::select($query, $arrayName);

		foreach ($result as $key) {
			$key->overdue = json_decode($key->settings)->quant_limity->lateTime;
		}

		return $result;
	}

	public function getTicketsCreator($id)
	{

		$creator['success'] = false;

		try {

			$creator['result'] = DB::table('ticket')
                ->whereNull('deleted_at')
				->where('id', $id)
				->get();

			// foreach ($ticketChat['result'] as $key) {
			// 	$key->id = Crypt::encrypt($key->id);
			// }

			$creator['success'] = true;
		} catch (\Exception $e) {
			echo $e;
			Logger::reportException($e, [], ['agents-controller', 'show'], false);
			$creator['success'] = false;
		}

		echo json_encode($creator);
	}

	//ADICIONA RESPOSTA UMA RESPOSTA AO TICKET
	public function saveTicketChat(Request $request)
	{
		$rules =  [
			'text'        => 'required',
			// 'description' => 'max:255',
			// 'account'     => 'max:10',
			// 'agency'      => 'max:10',
		];
		$messages = [
			'text.required'   => 'bs-text-is-required',
			// 'name.max'        => 'O nome deve conter no máximo 50 caracteres.',
			// 'description.max' => 'A descrição deve conter no máximo 255 caracteres.',
			// 'account.max'     => 'A conta deve conter no máximo 10 caracteres.',
			// 'agency.max'      => 'A agência deve conter no máximo 10 caracteres.',
		];

		$validator = Validator::make($request->all(), $rules, $messages);

		if ($validator->fails()) {
			$ticket['success'] = false;
			$ticket['value'] = $validator->errors()->first();
			return $ticket;
		}

		$ticket['event'] = '';
		$ticket['success'] = false;

		try {

			$chat_id = DB::table('chat')
                ->whereNull('deleted_at')
				->where('ticket_id', request('id_ticket'))
				->first();

			$cucd_id = DB::table('company_user_company_department')
				->where('company_user_id', Crypt::decrypt(session('companyselected')['company_user_id']))
				->where('company_department_id', Crypt::decrypt(request('department_id')))
				->select('id')
				->first();

			if($cucd_id == null){
				//VINCULAR AGORA O ATENDENTE OU ADMIN AO DEPARTAMENTO
				$cucd_id = DB::table('company_user_company_department')->insertGetId([
					'company_user_id' => Crypt::decrypt(session('companyselected')['company_user_id']),
					'company_department_id' => Crypt::decrypt(request('department_id')),
					'is_active' => 1,
				]);
			}else{
				$cucd_id = $cucd_id->id;
			}

			$id = $chat_id->id;
			$chat_type = $chat_id->type;

			if(Crypt::decrypt(request('comp_user_comp_dep_current_id')) == $cucd_id){
				//MESMO ATENDENTE
			}else{
				//PERTENCE A OUTRO ATENDENTE
				$atendente = DB::table('company_user_company_department')
				->join('company_user', 'company_user_company_department.company_user_id', 'company_user.id')
				->join('user_auth', 'company_user.user_auth_id', 'user_auth.id')
				->where('company_user_company_department.id', Crypt::decrypt(request('comp_user_comp_dep_current_id')))
				->select('user_auth.name')
				->first();
				
				$ticket['event'] = 'change-attendant';
				$ticket['value'] = 'bs-the-ticket-has-been-linked-to-you-belonge';
				$ticket['attendant'] = $atendente->name;
			}

			//ADICIONAR FUNÇÃO PARA ATUALIZAR UM TICKET QUE JÁ ESTA ATRELADO AO CHAT
			//comp_user_comp_depart_id_current
			if($chat_type == 'TICKET'){
				DB::table('chat')
				->where('id', $id)
				->update([
					'status' => request('status'),
					'comp_user_comp_depart_id_current' => $cucd_id,
					'updated_by' => auth()->user()->id,
				]);
			}else{
				DB::table('chat')
				->where('id', $id)
				->update([
					'comp_user_comp_depart_id_current' => $cucd_id,
					'updated_by' => auth()->user()->id,
				]);
			}

	        $company_id = intval(Crypt::decrypt(session('companyselected')['id']));

			$text = request('text');

	        // verifico se existe algum arquivo enviado no request...

			if (!is_null($request['images'])) {
				$images = $request['images'];
				foreach ($images as $row) {
					// Define the Base64 value you need to save as an image
					$b64 = explode(',', $row)[1];

					$image_name = Crypt::encrypt(uniqid(md5(uniqid() . microtime())));
					$data = base64_decode($b64);
					$dir = '..' . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'company' . DIRECTORY_SEPARATOR . $company_id . DIRECTORY_SEPARATOR . 'chat' . DIRECTORY_SEPARATOR . $id . DIRECTORY_SEPARATOR;
					$filename = $image_name . '.png';

					// Caso já exista o diretório, apenas movo o arquivo pra dentro dele, se não, crio a pasta e movo o arquivo em seguida...
					if (is_dir($dir)) {
						$success = file_put_contents($dir.$filename, $data);
					} else {
						mkdir($dir, 0755, true);
						$success = file_put_contents($dir.$filename, $data);
					}

					if ($success) {
						$text = str_replace($row, 'chat/files/'. Crypt::encrypt($id) .'/'.$filename, $text);
					}
				}
			}
			
	        if ($request->hasFile('files')) {
	            $files = [];
	            // para cada um dos arquivos enviados executo o laço...
	            foreach ($_FILES["files"]["name"] as $i => $file) {
	                // Quebro o nome completo do arquivo em várias posições e crio duas váriaveis: uma armazena o nome original e outra a extensão do arquivo...
	                $explode = explode('.', $_FILES['files']['name'][$i]);
	                $original_name = str_replace('.' . end($explode), "", $_FILES['files']['name'][$i]);
	                $extension = '.' . strtolower(end($explode));
	                // Indico o diretorio para onde o arquivo deve ser enviado...
	                $dir = '..' . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'company' . DIRECTORY_SEPARATOR . $company_id . DIRECTORY_SEPARATOR . 'chat' . DIRECTORY_SEPARATOR . $id . DIRECTORY_SEPARATOR;
	                // Gero um nome único para o arquivo antes de move-lo para a pasta...
	                $unique_name = Crypt::encrypt(uniqid(md5($original_name . microtime()))) . $extension;
	                $new_name = $dir . $unique_name;

	                // Caso já exista o diretório, apenas movo o arquivo pra dentro dele, se não, crio a pasta e movo o arquivo em seguida...
	                if (is_dir($dir)) {
	                    move_uploaded_file($_FILES['files']['tmp_name'][$i], $new_name);
	                } else {
	                    mkdir($dir, 0755, true);
	                    move_uploaded_file($_FILES['files']['tmp_name'][$i], $new_name);
	                }
	                // transformo as extensoes de imagem em um array
	                $extImages = explode(',', $request->extImages);
	                // se o arquivo tiver alguma das extensões do array, ele é type IMAGE, se não, type FILE
					if (in_array(strtolower(end($explode)), $extImages)) {
						$upload_type = 'IMAGE';
					} else {
						$upload_type = 'FILE';
					}

	                $files[$i] = [
	                    'unique_name' => $unique_name,
	                    'original_name' => $original_name . $extension,
	                    'type' => $upload_type,
	                ];
	            }

	            $content =  json_encode([
	                'message' => $text,
	                'files' => $files,
	            ]);
	        } else {
	            $content = $text;
	        }

	        $type =  trim(strip_tags($request->type));


	        $create = ChatHistory::create([
	            'chat_id' => $id,
	            'company_user_company_department_id' => $cucd_id,
	            'type' => $type,
	            'content' => $content,
	            'created_by' => auth()->user()->id,
	        ]);

	        $user = Auth::user();

			$user->user_id = $user->id;
			$user->id_creator = $user->id;
			$user->user_email = $user->email;
			$user->user_name = $user->name;


	        $create->chat_id = Crypt::encrypt($id);
	        $create->ch_id = Crypt::encrypt($create->id);
	        $msg = json_decode(json_encode($create), true);
	        $user = json_decode(json_encode($user), true);
	        $result =  array_merge($user, $msg);
	        $result['created_at'] = $create->created_at;
	        $result['id'] = Crypt::encrypt($id);
	        broadcast(new MessageSentTicket($result));


			//ALERTA PARA O CLIENTE SABER QUE O ATENDENTE RESPONDEU - CASO O TICKET DO CLIENTE NÃO ESTEJA ABERTO
			if (request('status') === 'IN_PROGRESS') {
				broadcast(new ClientTicketAnswer([
					'id' => intval(request('id_ticket')),
					'action' => 'alertClient',
					'company_id' => session('companyselected')['id'],
					'status' => 'IN_PROGRESS',
				]));


				//ATUALIZAR O ATENDENTE DO TICKET
				$user_client_ticket = DB::table('user_client_ticket')
					->select('user_client_id')
					->where('ticket_id', request('id_ticket'))
					->first();

				//VERIFICAR SE PERTENCE AO MESMO ATENDENTE - SE NÃO ALTERA O ATENDENTE
				$user_ticket_result = DB::table('user_ticket')
				->where('ticket_id', request('id_ticket'))
				->first();


				if($user_ticket_result->company_user_id != Crypt::decrypt(session('companyselected')['company_user_id'])){

					DB::table('user_ticket')
					->where('ticket_id', request('id_ticket'))
					->update([
						'company_user_id' => Crypt::decrypt(session('companyselected')['company_user_id']),
						'updated_at' => \Carbon\Carbon::now()->toDateTimeString(),
						'updated_by' => auth()->user()->id,
					]);

					broadcast(new TicketsListUpdate([
						'id' => intval(request('id_ticket')),
						'company_id_realtime' => session('companyselected')['id'],
						'action' => 'remove',
						'status' => 'IN_PROGRESS',
						'user_agent_id' => intval(auth()->user()->id),
					]));
				}

				//ATUALIZAR COMENTARIO
				DB::table('ticket')
				->where('id', request('id_ticket'))
				->update([
					'comments' => request('comments') . " ",
					'updated_at' => \Carbon\Carbon::now()->toDateTimeString(),
					'updated_by' => auth()->user()->id,
				]);
			}

			if (request('status') === 'CLOSED' || request('status') === 'RESOLVED') {
				$user_client_ticket = DB::table('user_client_ticket')
					->select('user_client_id')
					->where('ticket_id', request('id_ticket'))
					->first();

				ChatWorkingTimes::Update($id);
				
				//Vai ficar aqui $cucd_id

				//'TEXT','URL','IMAGE','EMOTICON','AUDIO','GIF','EVENT','CALL','FILE','OPEN','CLOSE'
				if(request('status') == 'CLOSED'){
					$typeevent = 'bs-closed-the-ticket';
				}else{
					$typeevent = 'bs-marked-as-resolved';
				}

				$chat_id = DB::table('chat_history')->insertGetId([
					'chat_id' => $id,
					'company_user_company_department_id' => $cucd_id,
					'type' => 'EVENT',
					'content' => $typeevent,
					'created_by' => auth()->user()->id,
					'created_at' => \Carbon\Carbon::now()->toDateTimeString(),
				]);

				// REMOVER TICKET DA LISTA DO ATENDENTE APÓS ALTERAR O STATUS DELE
				broadcast(new TicketsListUpdate([
					'id' => intval(request('id_ticket')),
					'company_id_realtime' => session('companyselected')['id'],
					'action' => 'remove',
					'status' => 'IN_PROGRESS',
					'status_final' => request('status'),
					'user_agent_id' => auth()->user()->id,
				]));

				// $dataTicket = $this->getTicketToTrigger(request('id_ticket'), intval(Crypt::decrypt(session('companyselected')['id'])));
				// broadcast(new FullTicket([
				// 	'id' => intval(request('id_ticket')),
				// 	'data' => $dataTicket['result'][0],
				// 	'company_id_realtime' => session('companyselected')['id'],
				// 	'action' => 'push',
				// 	'status' => request('status'),
				// 	'status_original' => 'IN_PROGRESS',
				// 	'user_agent_id' => auth()->user()->id,
				// ]));

				//FUNÇÃO PARA ALIMENTAR TEMPO DE IN_PROGRESS DO TICKET
				DB::table('ticket')
				->where('id', request('id_ticket'))
				->update([
					'service_time' => Ticket::calculatorTimeQueueTicket(request('id_ticket'), request('status')),
					'status' => request('status'),
					'update_status_closed_resolved' => \Carbon\Carbon::now()->toDateTimeString(),
					'updated_at' => \Carbon\Carbon::now()->toDateTimeString(),
					'updated_by' => auth()->user()->id,
				]);
			}

            if (isset($user_client_ticket->user_client_id)) {
                broadcast(new ClientTicketsList([
                    'id' => intval(request('id_ticket')),
                    'company_id' => session('companyselected')['id'],
                    'user_client_id' => Crypt::encrypt($user_client_ticket->user_client_id),
                    'company_user_id' => session('companyselected')['company_user_id'],
                    'agent' => auth()->user()->name,
                    'agent_email' => auth()->user()->email,
                    'agent_id' => Crypt::encrypt(auth()->user()->id),
                    'cucd_id' => Crypt::encrypt($cucd_id),
                    'status' => request('status'),
                    'agent_answered' => true,
                    'hash_id' => Crypt::encrypt(request('id_ticket')),
                    // old info
                    'action' => 'updateStatus',
                ]));
            }

			/**
			 * Sen feedback to client
			 * Anotação: feedback apenas se for cliente
			 */
			$company_id   = Crypt::decrypt(session('companyselected')['id']);
			$company_name = isset(session('companyselected')['name']) ? session('companyselected')['name'] : 'BA Support';

            if(request('sendEmailSelected') == 'SEND') { 
				// && config('app.env') != 'local'
				Feedback::directSendMB(request('status'), request('id_ticket'), request('email'));

				// MODELO ANTIGO
                // Feedback::send(request('status'), request('id_ticket'), $company_id, $company_name);
            }

			$ticket['success'] = true;
			$ticket['updated_at'] = \Carbon\Carbon::now()->toDateTimeString();

		} catch (\Exception $e) {
			echo $e;
			Logger::reportException($e, [], ['ticket-controller', 'saveTicketChat'], false);
			$ticket['success'] = false;
		}

		$ticket['created'] = DB::table('ticket')
				->where('ticket.id', request('id_ticket'))
				->first()->update_status_in_progress;
				
		return json_encode($ticket);
	}

	public function getTicketsChat()
	{
		$ticketChat['success'] = false;

		try {

			$id = intval(request('id'));
			$chat_id = intval(Crypt::decrypt(request('chat_id')));
			$chat_type = request('chat_type');
            $created_at = request('created_at');

			$arrayName = array(
				'id_ticket' => $id,
			);

            $arrayName2 = array(
				'id_chat' => $chat_id,
			);

			// $query = "SELECT c.id, ch.name, ch.content, ch.type,
			// 			ch.created_at, ch.created_by, ch.id as chat_history_id, ch.company_user_company_department_id, c.type as chat_type
			// 			FROM chat_history ch
			// 			JOIN chat c ON ch.chat_id = c.id
			// 			JOIN ticket t ON c.ticket_id = t.id
			// 			JOIN user_auth ua ON ch.created_by = ua.id
			// 			WHERE t.id = :id_ticket
			// 			AND ch.created_at <= t.created_at";

			$query = "SELECT chat_history.chat_id AS chat_id,
                                chat_history.company_user_company_department_id,
                                chat_history.type,
                                chat_history.content,
                                chat_history.hidden_for_client,
                                DATE_FORMAT(chat_history.created_at,'%H:%i') AS TIME,
                                chat_history.created_at,
                                ua_client.name AS client_name,
                                ua_client.email AS client_email,
                                ua_client.id AS client_id,
                                ua_client.builderall_account_data,
                                ua_agent.name AS user_name,
                                ua_agent.email AS user_email,
                                ua_agent.id AS user_id,
                                chat.type AS chat_type,
								chat.id,
								chat.id as chat_history_id,
								creator.name
                        FROM chat_history
                        INNER JOIN user_client_chat ON user_client_chat.chat_id = chat_history.chat_id
                        INNER JOIN user_client ON user_client.id = user_client_chat.user_client_id
                        INNER JOIN user_auth AS ua_client ON user_client.user_auth_id = ua_client.id
                        INNER JOIN chat ON chat.id = chat_history.chat_id
                        INNER JOIN ticket ON ticket.id = chat.ticket_id
						INNER JOIN user_auth AS creator ON chat_history.created_by = creator.id
                        LEFT JOIN company_user_company_department ON chat_history.company_user_company_department_id = company_user_company_department.id
                        LEFT JOIN company_user ON company_user_company_department.company_user_id = company_user.id
                        LEFT JOIN user_auth AS ua_agent ON company_user.user_auth_id = ua_agent.id
                        WHERE (chat_history.chat_id = :id_chat)
                        AND chat.deleted_at IS NULL
                        AND ticket.deleted_at IS NULL
                        AND chat_history.created_at <= ticket.created_at
						AND chat_history.hidden_for_client = 0
                        ORDER BY chat_history.id ASC;";

			$query2 = "SELECT sub.*, ROW_NUMBER() OVER (PARTITION BY sub.aux ORDER BY chat_history_id) AS seq FROM (
                            SELECT c.id, ua.name, ua.email, ua.id as id_creator, ch.content, ch.type, ua.builderall_account_data,
                            ch.created_at, ch.created_by, ch.id as chat_history_id, ch.company_user_company_department_id, c.type as chat_type,
                            IF (ch.type = 'EVENT', 0, 1) AS aux
                            FROM chat_history ch
                            JOIN chat c ON ch.chat_id = c.id
                            JOIN ticket t ON c.ticket_id = t.id
                            JOIN user_auth ua ON ch.created_by = ua.id
                            WHERE t.id = :id_ticket
                            AND c.deleted_at IS NULL
                            AND t.deleted_at IS NULL
                            AND ch.created_at > t.created_at
                        ) sub
                        ORDER BY created_at DESC";



			//ANTES DE VIRAR TICKET
			$ticketChat['result'] = DB::select($query, $arrayName2);

			//DEPOIS DE VIRAR TICKET
			$ticketChat['result2'] = DB::select($query2, $arrayName);

			$ticketChat['description'] = DB::table('ticket')
				->select('description', 'created_at')
                ->whereNull('deleted_at')
				->where('id', $id)
				->first();


			if($chat_type == 'CHANGED_TO_TICKET'){
				$ticketChat['quests'] = DB::table('ticket_chat_answer as tt')
				->join('company_depart_settings_question as cc', 'tt.company_depart_settings_question_id', 'cc.id')
				->select('tt.id', 'question', 'answer', 'tt.created_at')
				->where('tt.chat_id', $chat_id)
				->get();
			}else{
				$ticketChat['quests'] = DB::table('ticket_chat_answer as tt')
				->join('company_depart_settings_question as cc', 'tt.company_depart_settings_question_id', 'cc.id')
				->select('tt.id', 'question', 'answer', 'tt.created_at')
				->where('ticket_id', $id)
				->get();
			}

			foreach ($ticketChat['result'] as $key) {
				$key->id = Crypt::encrypt($key->id);
				$key->chat_id = Crypt::encrypt($key->chat_id);
                $key->client_id = Crypt::encrypt($key->client_id);
                $key->user_id = Crypt::encrypt($key->user_id);
                if ($key->company_user_company_department_id) {
                    $key->company_user_company_department_id = Crypt::encrypt($key->company_user_company_department_id);
                }
                $key->active = false;
			}

			foreach ($ticketChat['result2'] as $key=>$value) {
				$value->id = Crypt::encrypt($value->id);

				$res = json_decode($value->content);
				if(json_last_error() == JSON_ERROR_NONE) {
					$value->content = $res;
				}

                $value->active = false;

				// if($value->type == 'TEXT'){
				// 	$auxTextId = $value->chat_history_id;
				// 	//echo ' TEXT';
				// }
				// if($value->type == 'IMAGE'){
				// 	$value->array_images = $auxTextId;
				// 	//echo ' IMAGE';
				// }else{
				// 	$value->array_images = null;
				// }
				// if($value->type == 'FILE'){
				// 	$value->array_files = $auxTextId;
				// 	//echo ' FILE';
				// }else{
				// 	$value->array_files = null;
				// }
			}

			if(count($ticketChat['result']) > 0){
				$ticketChat['isChat'] = ($ticketChat['result'][0]->chat_type ==  'CHANGED_TO_TICKET');
			} else if(count($ticketChat['result2']) > 0){
				$ticketChat['isChat'] = ($ticketChat['result2'][0]->chat_type ==  'CHANGED_TO_TICKET');
			} else {
				$ticketChat['isChat'] = false;
			}

			$ticketChat['success'] = true;
		} catch (\Exception $e) {
			echo $e;
			Logger::reportException($e, [], ['ticket-controller', 'getTicketsChat'], false);
			$ticketChat['success'] = false;
		}

		echo json_encode($ticketChat);
	}

	public function getFilesTicket($id){

		$id = Crypt::encrypt($id);
		$path_parts = storage_path() . "/ticket/uploads/{$id}";
		// echo "Lista de Arquivos do diretório '<strong>".$path_parts."</strong>':<br />";
		$local = 'http://localhost/ba-support/';
		$production = 'https://ba-support.builderall.io/';

		$file['success'] = false;

		try {

			$types = array('png', 'jpg', 'jpeg', 'gif');
			if ($handle = opendir($path_parts)) {
				while ($entry = readdir($handle)) {
					$ext = strtolower(pathinfo($entry, PATHINFO_EXTENSION));

					if (in_array($ext, $types)) {
						$arrayFiles = [
							"route" => $local . "storage/ticket/uploads/{$id}/" . $entry,
							"name" => $entry,
						];
					}
				}
				closedir($handle);
			}

			$file['value'] = $arrayFiles;
			$file['success'] = true;
		} catch (\Exception $e) {
			echo $e;
			Logger::reportException($e, [], ['ticket-controller', 'getFilesTicket'], false);
			$file['success'] = false;
		}

		echo json_encode($file);
	}

	public function updateTicket()
	{
		try{
			//ALTERAR DEPARTAMENTO
			if (request('type') == 2) {
				if (request('department') == null) {
					$file['success'] = false;
				} else {
					DB::table('ticket')
					->where('id', request('ticket'))
					->update([
						'company_department_id' => Crypt::decrypt(request('department')['id']),
						'status' => 'OPENED',
						'type' => 'TRANSFERED',
						'updated_at' => \Carbon\Carbon::now()->toDateTimeString(),
						'updated_by' => auth()->user()->id,
					]);

					DB::table('user_ticket')
					->where('ticket_id', request('ticket'))
					// ->where('company_user_id', Crypt::decrypt(session('companyselected')['company_user_id']))
					->delete();

					DB::table('chat')
                    ->whereNull('deleted_at')
					->where('ticket_id', request('ticket'))
					->update([
						'company_department_id' => Crypt::decrypt(request('department')['id']),
						'comp_user_comp_depart_id_current' => null,
						'updated_at' => \Carbon\Carbon::now()->toDateTimeString(),
						'updated_by' => auth()->user()->id,
					]);

					$cucd_id = DB::table('company_user_company_department')
					->where('company_user_id', Crypt::decrypt(session('companyselected')['company_user_id']))
					->where('company_department_id', Crypt::decrypt(request('department')['id']))
					->first();

					if($cucd_id == null){
						//VINCULAR AGORA O ATENDENTE OU ADMIN AO DEPARTAMENTO
						$cucd_id = DB::table('company_user_company_department')->insertGetId([
							'company_user_id' => Crypt::decrypt(session('companyselected')['company_user_id']),
							'company_department_id' => Crypt::decrypt(request('department')['id']),
							'is_active' => 0,
						]);
					}else{
						$cucd_id = $cucd_id->id;
					}

					//'TEXT','URL','IMAGE','EMOTICON','AUDIO','GIF','EVENT','CALL','FILE','OPEN','CLOSE'
					$chat_history_id = DB::table('chat_history')->insertGetId([
						'chat_id' => Crypt::decrypt(request('chat_id')),
						'company_user_company_department_id' => $cucd_id,
						'type' => 'EVENT',
						'content' => 'bs-transferred-the-ticket-to-another-departme',
						'created_by' => auth()->user()->id,
						'created_at' => \Carbon\Carbon::now()->toDateTimeString(),
					]);

					ChatWorkingTimes::Update(Crypt::decrypt(request('chat_id')));

					//GET OPEN TICKET
					$dataTicket = $this->getTicketToTriggerOpened(request('ticket'), intval(Crypt::decrypt(session('companyselected')['id'])));

                    $user_client_id = UserClientTicket::select("user_client_id")
                    ->where("ticket_id", request('ticket'))
                    ->first()->user_client_id;

                    broadcast(new ClientTicketsList([
                        'id' => request('ticket'),
                        'hash_id' => Crypt::encrypt(request('ticket')),
                        'company_id' => session('companyselected')['id'],
                        'user_client_id' => Crypt::encrypt($user_client_id),
                        'agent' => null,
                        'agent_id' => null,
                        'agent_email' => null,
                        'cucd_id' => null,
                        'status' => 'OPENED',
                        'department_name' => request('department')['name'],
                        'department_id' => request('department')['id'],
                    ]));


                    $realtime = new SendRealtime(request('ticket'), 'splice');
                    $realtime->updateTableInProgress();

                    $realtime = new SendRealtime(request('ticket'), 'push');
                    $realtime->updateTableQueue();

					$file['success'] = true;

				}
			}
			//ALTERAR ATENDENTE/AGENTE
			if (request('type') == 1) {
				
				if (request('agent') == null) {
					$file['success'] = false;
				} else {
					
					$cucd_id = DB::table('company_user_company_department as cucd')
						->join('company_user', 'cucd.company_user_id', 'company_user.id')
						->join('user_auth', 'company_user.user_auth_id', 'user_auth.id')
						->where('cucd.company_user_id', Crypt::decrypt(request('agent')['company_user_id']))
						->where('cucd.company_department_id', Crypt::decrypt(request('department')))
						->where('cucd.is_active', 1)
						->select('cucd.id', 'cucd.company_user_id', 'cucd.company_department_id', 'cucd.is_active', 'user_auth.id as user_auth_id')
						->first();

						
					if ($cucd_id == null) {
						$file['value'] = 'not_depart';
						$file['success'] = false;
					} else {
						
						DB::table('chat')
                            ->whereNull('deleted_at')
							->where('ticket_id', request('ticket'))
							->update([
								'comp_user_comp_depart_id_current' => $cucd_id->id,
								'updated_by' => auth()->user()->id,
							]);

						//'TEXT','URL','IMAGE','EMOTICON','AUDIO','GIF','EVENT','CALL','FILE','OPEN','CLOSE'
						$chat_history_id = DB::table('chat_history')->insertGetId([
							'chat_id' => Crypt::decrypt(request('chat_id')),
							'company_user_company_department_id' => $cucd_id->id,
							'type' => 'EVENT',
							'content' => 'bs-transferred-the-ticket-to-another-agent',
							'created_by' => auth()->user()->id,
							'created_at' => \Carbon\Carbon::now()->toDateTimeString(),
						]);

						ChatWorkingTimes::Update(Crypt::decrypt(request('chat_id')));
						ChatWorkingTimes::Insert(Crypt::decrypt(request('chat_id')), $cucd_id->id, $chat_history_id, request('ticket'));

						//ATUALIZA TICKET
						DB::table('ticket')
							->where('id', request('ticket'))
							->update([
								'type' => 'TRANSFERED',
								'updated_by' => auth()->user()->id,
							]);

						//ATUALIZAR ATENDENTE
						$result = DB::table('user_ticket')
						->where('ticket_id', request('ticket'))
						->update([
							'company_user_id' => $cucd_id->company_user_id,
							'updated_by' => auth()->user()->id,
						]);

                        $user_client_id = UserClientTicket::select("user_client_id")
                            ->where("ticket_id", request('ticket'))
                            ->first()->user_client_id;

                        $attendant = UserAuth::where('id', $cucd_id->user_auth_id)->first();

                        broadcast(new ClientTicketsList([
                            'id' => request('ticket'),
                            'hash_id' => Crypt::encrypt(request('ticket')),
                            'company_id' => session('companyselected')['id'],
                            'user_client_id' => Crypt::encrypt($user_client_id),
                            'agent' => $attendant->name,
                            'agent_id' => Crypt::encrypt($attendant->id),
                            'agent_email' => $attendant->email,
                            'cucd_id' => Crypt::encrypt($cucd_id->id),
                        ]));
						
                        $realtime1 = new SendRealtime(request('ticket'), 'splice');
                        $realtime1->updateTableInProgress();

                        $realtime2 = new SendRealtime(request('ticket'), 'push');
                        $realtime2->updateTableInProgress();

						$file['success'] = true;
					}
				}
			}

			//ALTERAR STATUS
			if (request('type') == 3) {

                $current_status = request('current_status');

				$check_cucd = CompanyUserCompanyDepartment::where('id', Crypt::decrypt(request('cucdiu')))
					->select('company_user_id')
					->where('is_active', 1)
					->first();
					
				if ($check_cucd) {
					// dd('segue o fluxo');
				}else {
					$file['success'] = false;
					$file['info'] = 'attendant_removed_department';
					return $file;
				}

				if(request('status') == $current_status && request('status') == 'CLOSED'){
					$file['success'] = true;
				}else{
					DB::table('ticket')
					->where('id', request('ticket'))
					->update([
						'status' => request('status'),
						'service_time' => Ticket::calculatorTimeQueueTicket(request('ticket'), request('status')),
						'update_status_closed_resolved' => \Carbon\Carbon::now()->toDateTimeString(),
						'updated_at' => \Carbon\Carbon::now()->toDateTimeString(),
						'updated_by' => auth()->user()->id,
					]);

					$chat_id = DB::table('chat')
						->where('ticket_id', request('ticket'))
						->whereNull('deleted_at')
						->select('id', 'type')
						->first();

					if($chat_id->type == 'TICKET'){
						DB::table('chat')
						->where('ticket_id', request('ticket'))
						->update([
							'status' => request('status'),
							'updated_at' => \Carbon\Carbon::now()->toDateTimeString(),
							'updated_by' => auth()->user()->id,
						]);
					}else{
						DB::table('chat')
						->where('ticket_id', request('ticket'))
						->update([
							'updated_at' => \Carbon\Carbon::now()->toDateTimeString(),
							'updated_by' => auth()->user()->id,
						]);
					}

					$cucd_id = DB::table('company_user_company_department')
						->where('company_user_id', Crypt::decrypt(session('companyselected')['company_user_id']))
						->where('company_department_id', Crypt::decrypt(request('department')))
						->first();

					if($current_status == 'IN_PROGRESS' && request('status') == 'CLOSED' || $current_status == 'IN_PROGRESS' && request('status') == 'RESOLVED') {
						$chat_history_id = DB::table('chat_history')->insertGetId([
							'chat_id' => Crypt::decrypt(request('chat_id')),
							'company_user_company_department_id' => $cucd_id->id,
							'type' => 'EVENT',
							'content' =>  request('status') == 'CLOSED' ? 'bs-closed-the-ticket' : 'bs-marked-as-resolved',
							'created_by' => auth()->user()->id,
							'created_at' => \Carbon\Carbon::now()->toDateTimeString(),
						]);

						ChatWorkingTimes::Update($chat_id->id);
					}

					if($current_status == 'CLOSED' && request('status') == 'IN_PROGRESS' || $current_status == 'RESOLVED' && request('status') == 'IN_PROGRESS'){
						$result = DB::table('chat_working_times')
						->select('id', 'company_user_company_department_id')
						->where('chat_id', $chat_id->id)
						->orderby('id','DESC')
						->first();

						$chat_history_id = DB::table('chat_history')->insertGetId([
							'chat_id' => Crypt::decrypt(request('chat_id')),
							'company_user_company_department_id' => $cucd_id->id,
							'type' => 'EVENT',
							'content' => 'bs-reopened-the-ticket',
							'created_by' => auth()->user()->id,
							'created_at' => \Carbon\Carbon::now()->toDateTimeString(),
						]);

						if($result != null){
							ChatWorkingTimes::Insert($chat_id->id, $result->company_user_company_department_id, $chat_history_id, request('ticket'));
						}
					}

					$file['success'] = true;

					$user_client_ticket = DB::table('user_client_ticket')
						->select('user_client_id')
						->where('ticket_id', request('ticket'))
						->first();


					if (isset($user_client_ticket->user_client_id)) {
						//VERSÃO DO ATENDENTE QUE ATUALIZA O STATUS DO TICKET DO CLIENTE, SE CASO FOR CLIENTE
						broadcast(new ClientTicketsList([
							'id' => request('ticket'),
							'hash_id' => Crypt::encrypt(request('ticket')),
							'company_id' => session('companyselected')['id'],
							'user_client_id' => Crypt::encrypt($user_client_ticket->user_client_id),
							'status' => request('status'),
							// old info
							'company_user_id' => null,
							'action' => 'updateStatus',
						]));

						/**
						 * Sen feedback to client
						 * Anotação: feedback apenas se for cliente
						 */
						$company_id   = Crypt::decrypt(session('companyselected')['id']);
						$company_name = isset(session('companyselected')['name']) ? session('companyselected')['name'] : 'BA Support';
						// if(config('app.env') != 'sandbox') { 
							if(request('status') == 'CLOSED' || request('status') == 'RESOLVED'){
								// Feedback::send(request('status'), request('ticket'), $company_id, $company_name);
								Feedback::directSendMB(request('status'), request('ticket'), request('email'));
							}
						// }

					}

					$realtime = new SendRealtime(request('ticket'), 'splice');

					switch ($current_status) {
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

					$realtime2 = new SendRealtime(request('ticket'), 'push');

					switch (request('status')) {
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
				}
			}

			//ALTERAR DEPARTAMENTO E ATENDENTE
			if (request('type') == 6) {
	
				if (request('department') == null) {
					return $file['success'] = false;
				} 
				
				if (request('agent') == null) {
					return $file['success'] = false;
				}
				
				DB::table('ticket')
					->where('id', request('ticket'))
					->update([
						'company_department_id' => Crypt::decrypt(request('department')['id']),
						'type' => 'TRANSFERED',
						'updated_at' => \Carbon\Carbon::now()->toDateTimeString(),
						'updated_by' => auth()->user()->id,
					]);

				
				DB::table('user_ticket')
					->where('ticket_id', request('ticket'))
					->update([
						'company_user_id' => Crypt::decrypt(request('agent')['company_user_id']),
						'updated_by' => auth()->user()->id,
					]);
			
				$cucd_id = DB::table('company_user_company_department as cucd')
					->join('company_user', 'cucd.company_user_id', 'company_user.id')
					->join('user_auth', 'company_user.user_auth_id', 'user_auth.id')
					->where('company_user_id', Crypt::decrypt(request('agent')['company_user_id']))
					->where('cucd.company_department_id', Crypt::decrypt(request('department')['id']))
					->where('cucd.is_active', 1)
					->select('cucd.id', 'cucd.company_user_id', 'cucd.company_department_id', 'cucd.is_active', 'user_auth.id as user_auth_id')
					->first();

				DB::table('chat')
					->where('ticket_id', request('ticket'))
					->update([
						'company_department_id' => Crypt::decrypt(request('department')['id']),
						'comp_user_comp_depart_id_current' => $cucd_id->id,
						'updated_at' => \Carbon\Carbon::now()->toDateTimeString(),
						'updated_by' => auth()->user()->id,
					]);
				
				//'TEXT','URL','IMAGE','EMOTICON','AUDIO','GIF','EVENT','CALL','FILE','OPEN','CLOSE'
				$chat_history_id = DB::table('chat_history')->insertGetId([
					'chat_id' => Crypt::decrypt(request('chat_id')),
					'company_user_company_department_id' => $cucd_id->id,
					'type' => 'EVENT',
					'content' => 'bs-ticket-transferred-department-and-attendan',
					'created_by' => auth()->user()->id,
					'created_at' => \Carbon\Carbon::now()->toDateTimeString(),
				]);

				ChatWorkingTimes::Update(Crypt::decrypt(request('chat_id')));
				ChatWorkingTimes::Insert(Crypt::decrypt(request('chat_id')), $cucd_id->id, $chat_history_id, request('ticket'));

				//GET OPEN TICKET
				$dataTicket = $this->getTicketToTriggerOpened(request('ticket'), intval(Crypt::decrypt(session('companyselected')['id'])));

                $user_client_id = UserClientTicket::select("user_client_id")
                    ->where("ticket_id", request('ticket'))
                    ->first()->user_client_id;

                $attendant = UserAuth::where('id', $cucd_id->user_auth_id)->first();
                broadcast(new ClientTicketsList([
                    'id' => request('ticket'),
                    'hash_id' => Crypt::encrypt(request('ticket')),
                    'company_id' => session('companyselected')['id'],
                    'user_client_id' => Crypt::encrypt($user_client_id),
                    'agent' => $attendant->name,
                    'agent_id' => Crypt::encrypt($attendant->id),
                    'agent_email' => $attendant->email,
                    'cucd_id' => Crypt::encrypt($cucd_id->id),
                    'department_name' => request('department')['name'],
                    'department_id' => request('department')['id'],
                ]));

                $realtime = new SendRealtime(request('ticket'), 'splice');
                $realtime->updateTableInProgress();

                $realtime = new SendRealtime(request('ticket'), 'push');
                $realtime->updateTableInProgress();

				
				$file['success'] = true;
			}
		} catch (\Exception $e) {
			echo $e;
			Logger::reportException($e, [], ['ticket-controller', 'updateTicket'], false);
			$file['success'] = false;
		}

		echo json_encode($file);
	}
	
    public function updateTicketGroup(){

		$ids= request('tickets_id');
		$agent= request('agent');
		$department= request('department');

		if ($department == '' || $agent == '') {
			return $transfer['success'] = false;
		}

		//ALTERAR DEPARTAMENTO E ATENDENTE 
		if(request('type') == 1){
			foreach ($ids as $key) {

				DB::table('ticket')
				->where('id', $key['ticket_id'])
				->update([
					'company_department_id' => Crypt::decrypt($department['id']),
					'type' => 'TRANSFERED',
					'updated_at' => \Carbon\Carbon::now()->toDateTimeString(),
					'updated_by' => auth()->user()->id,
				]);

				DB::table('user_ticket')
				->where('ticket_id', $key['ticket_id'])
				->update([
					'company_user_id' => Crypt::decrypt($agent['company_user_id']),
					'updated_by' => auth()->user()->id,
				]);
		
				$cucd_id = DB::table('company_user_company_department as cucd')
				->join('company_user', 'cucd.company_user_id', 'company_user.id')
				->join('user_auth', 'company_user.user_auth_id', 'user_auth.id')
				->where('company_user_id', Crypt::decrypt($agent['company_user_id']))
				->where('cucd.company_department_id', Crypt::decrypt($department['id']))
				->where('cucd.is_active', 1)
				->select('cucd.id', 'cucd.company_user_id', 'cucd.company_department_id', 'cucd.is_active', 'user_auth.id as user_auth_id')
				->first();
				
				DB::table('chat')
				->where('ticket_id', $key['ticket_id'])
				->update([
					'company_department_id' => Crypt::decrypt($department['id']),
					'comp_user_comp_depart_id_current' => $cucd_id->id,
					'updated_at' => \Carbon\Carbon::now()->toDateTimeString(),
					'updated_by' => auth()->user()->id,
				]);
	
				//'TEXT','URL','IMAGE','EMOTICON','AUDIO','GIF','EVENT','CALL','FILE','OPEN','CLOSE'
				$chat_history_id = DB::table('chat_history')->insertGetId([
					'chat_id' => $key['chat_id'],
					'company_user_company_department_id' => $cucd_id->id,
					'type' => 'EVENT',
					'content' => 'bs-ticket-transferred-department-and-attendan',
					'created_by' => auth()->user()->id,
					'created_at' => \Carbon\Carbon::now()->toDateTimeString(),
				]);

				ChatWorkingTimes::Update($key['chat_id']);
				ChatWorkingTimes::Insert($key['chat_id'], $cucd_id->id, $chat_history_id, $key['ticket_id']);

				//GET OPEN TICKET
				$dataTicket = $this->getTicketToTriggerOpened($key['ticket_id'], intval(Crypt::decrypt(session('companyselected')['id'])));

				$user_client_id = UserClientTicket::select("user_client_id")
					->where("ticket_id", $key['ticket_id'])
					->first()->user_client_id;

				$attendant = UserAuth::where('id', $cucd_id->user_auth_id)->first();

				broadcast(new ClientTicketsList([
					'id' => $key['ticket_id'],
					'hash_id' => Crypt::encrypt($key['ticket_id']),
					'company_id' => session('companyselected')['id'],
					'user_client_id' => Crypt::encrypt($user_client_id),
					'agent' => $attendant->name,
					'agent_id' => Crypt::encrypt($attendant->id),
					'agent_email' => $attendant->email,
					'cucd_id' => Crypt::encrypt($cucd_id->id),
					'department_name' => $department['name'],
					'department_id' => $department['id'],
				]));
	
				$realtime = new SendRealtime($key['ticket_id'], 'splice');
				$realtime->updateTableInProgress();

				$realtime = new SendRealtime($key['ticket_id'], 'push');
				$realtime->updateTableInProgress();

				$transfer['success'] = true;
			}
		}else if (request('type') == 2){
			//ALTERAR ATENDENTE
		}

		echo json_encode($transfer);
	}

    public function returnTicket()
    {
        try {
            if (request('department') == null) {
					$file['success'] = false;
				} else {
					DB::table('ticket')
						->where('id', request('ticket'))
						->update([
							'company_department_id' => Crypt::decrypt(request('department')),
							'status' => 'OPENED',
							'type' => 'TRANSFERED',
							'updated_at' => \Carbon\Carbon::now()->toDateTimeString(),
							'updated_by' => auth()->user()->id,
						]);

					$chat_id = DB::table('chat')
						->select('id')
						->where('ticket_id', request('ticket'))
						->first();

					ChatWorkingTimes::Update($chat_id->id);	

					DB::table('user_ticket')
					->where('ticket_id', request('ticket'))
					// ->where('company_user_id', Crypt::decrypt(session('companyselected')['company_user_id']))
					->delete();

					DB::table('chat')
					->where('ticket_id', request('ticket'))
					->update([
						'comp_user_comp_depart_id_current' => null,
						'status' => 'OPENED',
						'updated_at' => \Carbon\Carbon::now()->toDateTimeString(),
						'updated_by' => auth()->user()->id,
					]);

					//'TEXT','URL','IMAGE','EMOTICON','AUDIO','GIF','EVENT','CALL','FILE','OPEN','CLOSE'
					// $chat_id = DB::table('chat_history')->insertGetId([
					// 	'chat_id' => Crypt::decrypt(request('chat_id')),
					// 	'company_user_company_department_id' => $cucd_id,
					// 	'type' => 'EVENT',
					// 	'content' => 'bs-transferred-the-ticket-to-another-departme',
					// 	'created_by' => auth()->user()->id,
					// 	'created_at' => \Carbon\Carbon::now()->toDateTimeString(),
					// ]);

					//GET OPEN TICKET
					$dataTicket = $this->getTicketToTriggerOpened(request('ticket'), intval(Crypt::decrypt(session('companyselected')['id'])));

					if($dataTicket['result'] != []){

                        $realtime = new SendRealtime(request('ticket'), 'push');
                        $realtime->updateTableQueue();

					}

                    $user_client_id = UserClientTicket::select("user_client_id")
                        ->where("ticket_id", request('ticket'))
                        ->first()->user_client_id;

                    broadcast(new ClientTicketsList([
                        'id' => request('ticket'),
                        'hash_id' => Crypt::encrypt(request('ticket')),
                        'company_id' => session('companyselected')['id'],
                        'user_client_id' => Crypt::encrypt($user_client_id),
                        'status' => 'OPENED',
                        'agent' => null,
                        'agent_name' => null,
                        'agent_email' => null,
                        'cucd_id' => null,
                    ]));

					$file['success'] = true;

				}

                } catch (\Exception $e) {
					// echo $e;
					Logger::reportException($e, [], ['ticket-controller', 'returnTicket'], false);
					$file['success'] = false;
                }

            return $file;

    }

	public function deleteTicket(Request $request) {
		$result['success'] = false;
        try {
			if(session('restriction')[0]->ticket_delete == 1){
				$id = $request->ticket_id;

				$status = Ticket::select('status')->where('id', $id)->first();
	
				$select_chat = Chat::where('ticket_id', $id)->select('id')->first();
				ChatWorkingTimes::Update($select_chat->id);

				$deleteT = Ticket::find($id)->delete();

				$deleteC = Chat::where('ticket_id', $id)->delete();

			
				$user_client_ticket = UserClientTicket::where('ticket_id', $id)->first();
	
				if ($deleteT && $deleteC) {

                    if (isset($user_client_ticket)) {
                        broadcast(new ClientTicketsList([
                            'id' => $id,
                            'hash_id' => Crypt::encrypt($id),
                            'company_id' => session('companyselected')['id'],
                            'user_client_id' => Crypt::encrypt($user_client_ticket->user_client_id),
                            'action' => 'splice'
                        ]));
                    }
	
					broadcast(new ChatTicketDelete([
						'company_id'    => session('companyselected')['id'],
						'type'          => 'TICKET',
						'id'            => $request->ticket_id,
						'status'        => $status->status
					]));
	
					$result['success'] = true;
				}
			}else{
				$result['success'] = false;
			}
        } catch (\Exception $e) {
            Logger::reportException($e, [], ['ticket-controller', 'deleteTicket'], false);
        }
        return $result;
	}

	public function mergeTicket(Request $request) {
		$result['success'] = false;
        try {

			if(request('invert') == 'false'){
				// $textMerge = 'Ticket ' . request('ticket_merge') . ' mesclado com o ticket ' . request('ticket_id');
				$ticket_merge = request('ticket_merge');
				$ticket_id = request('ticket_id');
			}else{
				// $textMerge = 'Ticket ' . request('ticket_id') . ' mesclado com o ticket ' . request('ticket_merge');
				$ticket_merge = request('ticket_id');
				$ticket_id = request('ticket_merge');
			}

			$check1 = DB::table('ticket_merge')
				->select('ticket_id_origin')
				->where('ticket_id_origin', $ticket_merge)
				->first();

			$check2 = DB::table('ticket_merge')
				->select('ticket_id_origin')
				->where('ticket_id_origin', $ticket_id)
				->first();

			// FUNÇÃO PARA ORDENAR OS VALORES CASO UM JÁ TENHA SIDO MESCLADO
			// SE OS DOIS TICKET NÃO TEM DADO CADASTRADO - NULL - PODE FAZER INSERT
			if($check1 == null && $check2 == null){
				$merged = DB::table('ticket_merge')->insertGetId([
					'ticket_id_origin' => $ticket_id,
					'ticket_id_merge' => $ticket_merge,
					'created_by' => auth()->user()->id,
					'created_at' => \Carbon\Carbon::now()->toDateTimeString(),
				]);
				
				$ticket_id_origin = $ticket_id;
				$ticket_id_merge = $ticket_merge;
			}else{

				if($check1 != null){
					$ticket_id_origin = $ticket_merge;
					$ticket_id_merge = $ticket_id;
				}else{
					$ticket_id_origin = $ticket_id;
					$ticket_id_merge = $ticket_merge;
				}

				$merged = DB::table('ticket_merge')->insertGetId([
					'ticket_id_origin' => $ticket_id_origin,
					'ticket_id_merge' => $ticket_id_merge,
					'created_by' => auth()->user()->id,
					'created_at' => \Carbon\Carbon::now()->toDateTimeString(),
				]);
			}


			if($merged){
				$sql = DB::table('ticket')
				->select('id','description')
				->whereIn('id', [$ticket_id,$ticket_merge])
				->get();
				DB::table('ticket')
				->where('id', $ticket_id)
				->update([
					'description' => $ticket_id. ': '.$sql[0]->description .' - '.$ticket_merge. ': '.$sql[1]->description,
				]);

				DB::table('ticket')
				->where('id', $ticket_id_merge)
				->update([
					'status' => 'MERGED',
					'updated_by' => auth()->user()->id,
				]);

				$realtime = new SendRealtime($ticket_id_merge, 'splice');
				$realtime->updateTableQueue();

				$realtime = new SendRealtime($ticket_id_merge, 'splice');
				$realtime->updateTableInProgress();

                $user_client_ticket = DB::table('user_client_ticket')
					->select('user_client_id')
					->where('ticket_id', $ticket_id_merge)
					->first();

                if (isset($user_client_ticket->user_client_id)) {
                    broadcast(new ClientTicketsList([
                        'id' => intval($ticket_id_merge),
                        'hash_id' => Crypt::encrypt(intval($ticket_id_merge)),
                        'company_id' => session('companyselected')['id'],
                        'user_client_id' => Crypt::encrypt($user_client_ticket->user_client_id),
                        'action' => 'splice',
                    ]));
                }

				$result['success'] = true;
			}else{
				$result['success'] = false;
			}
  		} catch (\Exception $e) {
			  echo $e;
            Logger::reportException($e, [], ['ticket-controller', 'mergeTicket'], false);
        }
        return $result;
	}

	public function getClientHistoryMerge(Request $request){
		$id = request('client_id');

		$result = Chat::select(
			"ua_agent.name",
			"chat.id as chat_number",
			"chat.id as chat_id",
			"company_department.name as department",
			"company_department.id as department_id",
			"chat.status",
			"t2.status as ticket_status",
			"chat.created_at as chat_created_at",
			"chat.updated_at as chat_updated_at",
			"t2.id as ticket_number",
			"t2.id as ticket_id",
			"t2.created_at as ticket_created_at",
			"t2.updated_at as ticket_updated_at",
			"t2.description",
			"t2.comments",
			"t2.priority",
			"chat.type",
			"chat.comp_user_comp_depart_id_current",
		)
			->join("ticket" , "ticket.id" , "chat.ticket_id")
			->join("ticket_merge as tm" , "ticket.id", "tm.ticket_id_origin")
			->join("ticket as t2" , "tm.ticket_id_merge" , "t2.id")
			->join("user_client_chat" , "chat.id" , "user_client_chat.chat_id")
			->join("user_client as uc" , "uc.id" , "user_client_chat.user_client_id")
			->join("user_auth as ua_client" , "ua_client.id" , "uc.user_auth_id")
			->join("company_department" , "company_department.id" , "chat.company_department_id")
			->leftJoin("company_user_company_department" , "company_user_company_department.id" , "chat.comp_user_comp_depart_id_current")
			->leftJoin("company_user" , "company_user.id" , "company_user_company_department.company_user_id")
			->leftJoin("user_auth as ua_agent"  , "ua_agent.id" , "company_user.user_auth_id")
			->where("ticket_id", $id)
			->orderBy("t2.id", "desc")
			->get();

			foreach($result as $row) {
				$row->chat_id = Crypt::encrypt($row->chat_id);
			}

		return $result;
	}


	//PEGAR OU ABRIR TICKET
	public function addChatTicket()
	{
		try {
			$result['result'] = false;

			if (request('itemselected')['status'] == 'OPENED') {

				$arrayName2 = array(
					'companyselected' => intval(Crypt::decrypt(session('companyselected')['id'])),
					'company_user_id' => intval(Crypt::decrypt(session('companyselected')['company_user_id'])),
					'company_department_id' => intval(Crypt::decrypt(request('itemselected')['department_id']))
				);

				$query =	"SELECT
								COALESCE(SUM(IF(t.STATUS = 'IN_PROGRESS', 1, 0)), 0) AS IN_PROGRESS
							FROM ticket AS t
							LEFT JOIN user_ticket AS ut ON t.id = ut.ticket_id
							LEFT JOIN company_user AS cu ON ut.company_user_id = cu.id
							LEFT JOIN company_user_company_department AS cucd ON cu.id = cucd.company_user_id
							AND t.company_department_id = cucd.company_department_id
							WHERE t.company_id = :companyselected AND cucd.company_user_id = :company_user_id
                            AND t.deleted_at IS NULL
							AND t.company_department_id = :company_department_id";
				$query = DB::select($query, $arrayName2);

				if($query[0]->IN_PROGRESS >= request('restrictions')['quant_limity']['quantidadeticket'] && request('restrictions')['quant_limity']['quantidadeticket'] != 0){
					$result['value'] = 'not_opened_limity';
					return $result;
				}

				$user_ticket_result = DB::table('user_ticket')
					->join('company_user', 'user_ticket.company_user_id', 'company_user.id')
					->join('user_auth', 'company_user.user_auth_id', 'user_auth.id')
					->where('ticket_id', request('itemselected')['id'])
					->select('user_ticket.id', 'user_ticket.company_user_id', 'user_auth.name')
					->first();

				//VERICAÇÃO SE NÃO ESTÁ VINCULADO A OUTRO ATENDENTE
				if(isset($user_ticket_result)){
					if($user_ticket_result->company_user_id != Crypt::decrypt(session('companyselected')['company_user_id'])){

						$user_ticket_result->id = Crypt::encrypt($user_ticket_result->id);
						$user_ticket_result->company_user_id = Crypt::encrypt($user_ticket_result->company_user_id);

						$result['result'] = true;
						$result['value'] =$user_ticket_result;
						return $result;
					}
				}//--------------------------------------


				// NÃO FAZ NADA SE NÃO PERTENCE AO DEPARTAMENTO
				$cucd_id = DB::table('company_user_company_department')
					->where('company_user_id', Crypt::decrypt(session('companyselected')['company_user_id']))
					->where('company_department_id', Crypt::decrypt(request('itemselected')['department_id']))
					->select('id')
					->first();

				DB::table('user_ticket')->insertGetId([
					'company_user_id' => Crypt::decrypt(session('companyselected')['company_user_id']),
					'ticket_id' => request('itemselected')['id'],
					'created_by' => auth()->user()->id,
					'created_at' => \Carbon\Carbon::now()->toDateTimeString(),
				]);

				$chat_id = DB::table('chat')
                    ->whereNull('deleted_at')
					->where('ticket_id', request('itemselected')['id'])
					->first();

				if($chat_id->type == 'TICKET'){
					DB::table('chat')
						->where('id', $chat_id->id)
						->update([
							'comp_user_comp_depart_id_creator' => $cucd_id->id,
							'comp_user_comp_depart_id_current' => $cucd_id->id,
							'status' => 'IN_PROGRESS',
							'updated_at' => \Carbon\Carbon::now()->toDateTimeString(),
							'updated_by' => auth()->user()->id,
						]);
				}else{
					DB::table('chat')
					->where('id', $chat_id->id)
					->update([
						'comp_user_comp_depart_id_creator' => $cucd_id,
						'comp_user_comp_depart_id_current' => $cucd_id,
						'updated_at' => \Carbon\Carbon::now()->toDateTimeString(),
						'updated_by' => auth()->user()->id,
					]);
				}

				//'TEXT','URL','IMAGE','EMOTICON','AUDIO','GIF','EVENT','CALL','FILE','OPEN','CLOSE'
				$chat_id = DB::table('chat_history')->insertGetId([
					'chat_id' => $chat_id->id,
					'company_user_company_department_id' => $cucd_id->id,
					'type' => 'EVENT',
					'content' => 'bs-joined-the-ticket',
					'created_by' => auth()->user()->id,
					'created_at' => \Carbon\Carbon::now()->toDateTimeString(),
				]);


				//FUNÇÃO PARA ALIMENTAR TEMPO DE ESPERA DE UM TICKET NA FILA
				$datetime1 = strtotime(request('itemselected')['created_at']);
				$datetime2 = strtotime(\Carbon\Carbon::now()->toDateTimeString());
				$secs = $datetime2 - $datetime1; // == <seconds between the two times>

				$update_status_in_progress = \Carbon\Carbon::now()->toDateTimeString();
				$result['update_status_in_progress'] = $update_status_in_progress;
				$result['value'] = 'opened';

				DB::table('ticket')
					->where('id', request('itemselected')['id'])
					->update([
						'status' => 'IN_PROGRESS',
						'queue_time' => $secs,
						'update_status_in_progress' => $update_status_in_progress,
						'updated_at' => \Carbon\Carbon::now()->toDateTimeString(),
						'updated_by' => auth()->user()->id,
					]);

				// REMOVE TICKET APÓS UM ATENDENTE PEGAR ELE SE O STATUS FOR OPENED - OK
				broadcast(new TicketsListUpdate([
					'id' => request('itemselected')['id'],
					'company_id_realtime' => session('companyselected')['id'],
					'action' => 'remove',
					'status' => 'OPENED',
					'status_final' => 'IN_PROGRESS',
					'user_agent_id' => auth()->user()->id,
				]));

				//ADD TICKET NA LISTA DE UM AGENT ESPECIFICO
				$dataTicket = $this->getTicketToTrigger(request('itemselected')['id'], intval(Crypt::decrypt(session('companyselected')['id'])));
				broadcast(new TicketsAgentListUpdate([
					'data' => $dataTicket['result'][0],
					'action' => 'push',
					// variáveis para conectar no canal
					'company_id_realtime' => session('companyselected')['id'],
					'user_agent_id' => auth()->user()->id,
					'status_original' => request('itemselected')['status'],
				]));

				//ADD TICKET NA LISTA DE UM ADMIN
				broadcast(new FullTicket([
					'data' => $dataTicket['result'][0],
					'action' => 'push',
					// variáveis para conectar no canal
					'company_id_realtime' => session('companyselected')['id'],
					'status_original' => request('itemselected')['status'],
					'user_agent_id' => auth()->user()->id,
				]));

				// atualiza para o cliente
				$user_client_ticket = DB::table('user_client_ticket')
					->select('user_client_id')
					->where('ticket_id', request('itemselected')['id'])
					->get();

				if ($user_client_ticket) {
					//VERSÃO DO ATENDENTE QUE ATUALIZA O STATUS DO TICKET DO CLIENTE
					broadcast(new ClientTicketsList([
						'id' => request('itemselected')['id'],
						'company_id' => session('companyselected')['id'],
						'company_user_id' => $dataTicket['result'][0]->company_user_id,
						'agent' => auth()->user()->name,
						'user_client_id' => Crypt::encrypt($user_client_ticket[0]->user_client_id),
						'status' => 'IN_PROGRESS',
						'action' => 'updateStatus',
					]));
				}

				$result['email'] = auth()->user()->email;
			}else{
				$result['value'] = 'not_opened';
			}
		} catch (\Exception $e) {
			echo $e;
			Logger::reportException($e, [], ['ticketController', 'addChatTicket'], false);
			$result['success'] = false;
		}

		return $result;
	}

	public function checkClientTicket()
	{

		$tickets['success'] = false;

		try {

			$query = DB::table('user_auth')
				->join('user_client', 'user_auth.id', 'user_client.user_auth_id')
				->join('company_user_client', 'user_client.id', 'company_user_client.user_client_id')
				->select('user_auth.id', 'user_client.id as user_client_id', 'user_auth.email')
				->where('company_user_client.company_id', intval(Crypt::decrypt(session('companyselected')['id'])))
				->where('user_auth.deleted_at', NULL)
				->get();

			if (trim(request('email')) == "") {
				$tickets['value'] = null;
				return $tickets;
			}

			$tickets['value'] = null;
			foreach ($query as $key) {
				//$key->id = Crypt::encrypt($key->id);

				$concat = "";
				$pieces = explode("_", $key->email);
				$pieces = array_splice($pieces, 2);

				for ($i = 0; $i < count($pieces); $i++) {
					$concat .= $pieces[$i] . '_';
				}
				$key->email = substr($concat, 0, -1);

				if ($key->email == request('email')) {
					$tickets['value'] = $key;
					$tickets['success'] = true;
				}
			}
		} catch (\Exception $e) {
			echo $e;
			Logger::reportException($e, [], ['ticket-controller', 'checkClientTicket'], false);
			$tickets['success'] = false;
		}

		echo json_encode($tickets);
	}

	public function updateComment()
	{

		$tickets['success'] = false;

		try {
			DB::table('ticket')
				->where('id', request('id_ticket'))
				->update([
					'comments' => request('comments'),
					'updated_at' => \Carbon\Carbon::now()->toDateTimeString(),
					'updated_by' => auth()->user()->id,
				]);
			$tickets['success'] = true;
		} catch (\Exception $e) {
			echo $e;
			Logger::reportException($e, [], ['ticket-controller', 'updateComment'], false);
			$tickets['success'] = false;
		}

		echo json_encode($tickets);
	}

	public function updateUserTicket(){
		$tickets['success'] = false;

		try {
			DB::table('user_ticket')
				->where('id', Crypt::decrypt(request('user_ticket_id')))
				->update([
					'company_user_id' => Crypt::decrypt(session('companyselected')['company_user_id']),
					'updated_at' => \Carbon\Carbon::now()->toDateTimeString(),
					'updated_by' => auth()->user()->id,
				]);
			$tickets['success'] = true;
		} catch (\Exception $e) {
			echo $e;
			Logger::reportException($e, [], ['ticket-controller', 'updateUserTicket'], false);
			$tickets['success'] = false;
		}

		echo json_encode($tickets);
	}

	/** query
	 * Get ticket IN_PROGRESS,CLOSE,RESOLVED
	*/
	public function getTicketToTrigger($ticket_id, $company_id)
	{
		try {

			//REAL TIME PARA ADICIONAR NA FILA APOS CADASTRO DE FUNCIONARIO
			$arrayName = array(
				'companyselected' => $company_id, //intval(Crypt::decrypt(session('companyselected')['id'])),
				'id' => $ticket_id
			);

			$query =
				'SELECT * FROM (
					SELECT t.id, t.status, t.description, t.created_at, t.updated_at, t.created_by, c.created_by as chat_created_by,
					t.priority, t.type, t.user_agent, t.comments, t.update_status_in_progress, ua.name, ua.email, cd.name AS department,
					cd.type as department_type, cd.id AS department_id, cucd.id as company_user_company_department_id, cds.settings,
					COALESCE(ua_client.id, ua_create.id)  AS id_created,
					COALESCE(ua_client.name, ua_create.name)  AS name_created,
					COALESCE(ua_client.email, ua_create.email) AS email_created,
					ua_client.builderall_account_data AS builderall_account_data, c.service_time, c.update_status_in_progress as last_update_status, c.id as chat_id, cucd.company_user_id, c.type as chat_type, IF((SELECT company_user_company_department_id FROM chat_history WHERE chat_id = c.id ORDER BY id DESC LIMIT 1) IS NULL, 1, 0) AS answered
				    FROM ticket t
					JOIN company_department cd ON t.company_department_id = cd.id
					JOIN company_department_settings cds ON cd.id = cds.company_department_id
					JOIN chat c ON t.id = c.ticket_id
					JOIN user_auth ua_create ON t.created_by = ua_create.id
					JOIN company_user_company_department cucd ON c.comp_user_comp_depart_id_current = cucd.id
					JOIN company_user cu ON cucd.company_user_id = cu.id
					JOIN user_auth ua ON cu.user_auth_id = ua.id
					LEFT JOIN user_client_ticket uct ON t.id = uct.ticket_id
				    LEFT JOIN user_client uc ON uct.user_client_id = uc.id
				    LEFT JOIN user_auth ua_client ON uc.user_auth_id = ua_client.id
					WHERE t.company_id = :companyselected
					AND t.id = :id
					AND cd.deleted_at IS NULL
					AND c.deleted_at IS NULL
					AND t.deleted_at IS NULL
				    GROUP BY cd.id, t.id
			    ) sub ORDER BY answered DESC, sub.updated_at';

			$tickets['result'] = DB::select($query, $arrayName);

			foreach ($tickets['result'] as $key) {
				$key->department_id = Crypt::encrypt($key->department_id);
				$key->chat_id_decry = $key->chat_id;
				$key->chat_id = Crypt::encrypt($key->chat_id);
				$key->company_user_company_department_id = Crypt::encrypt($key->company_user_company_department_id);
				$key->overdue = json_decode($key->settings)->quant_limity->lateTime;
				$key->id_encrypted = Crypt::encrypt($key->id);
				$key->created_by_encrypted = Crypt::encrypt($key->created_by);
				$key->chat_created_by_encrypted = Crypt::encrypt($key->id_created);
				$key->email_created = ClearEmail::clear($key->email_created);
			}

			$tickets['success'] = true;
		} catch (\Exception $e) {
			echo $e;
			Logger::reportException($e, [], ['ticket-controller', 'getTicketToTrigger'], false);
			$tickets['success'] = false;
		}

		return $tickets;
	}


	public function getTicketToTriggerOpened($ticket_id, $company_id){

		try {

			$arrayName = array(
				'user' => intval(auth()->user()->id),
				'companyselected' => $company_id,
				'is_admin' => intval(session('is_admin')),
				'ticket_id' => $ticket_id,
			);

			$query = 'SELECT t.id, t.status, t.description, t.created_at, t.updated_at, t.created_by, c.created_by as chat_created_by,
						t.priority, t.type, t.user_agent, t.comments, t.update_status_in_progress, null as name, null as email, cd.name AS department,
						cd.type as department_type, cd.id AS department_id, cucd.id as company_user_company_department_id, cds.settings,
						COALESCE(ua_client.id, ua_create.id)  AS id_created,
						COALESCE(ua_client.name, ua_create.name)  AS name_created,
						COALESCE(ua_client.email, ua_create.email) AS email_created,
						ua_client.builderall_account_data AS builderall_account_data, c.service_time, c.update_status_in_progress as last_update_status, c.id as chat_id, cucd.company_user_id, c.type as chat_type, IF((SELECT company_user_company_department_id FROM chat_history WHERE chat_id = c.id ORDER BY id DESC LIMIT 1) IS NULL, 1, 0) AS answered
						FROM ticket t
						JOIN company_department cd ON t.company_department_id = cd.id
						JOIN company_department_settings cds ON cd.id = cds.company_department_id
						JOIN company_user_company_department cucd ON cds.company_department_id = cucd.company_department_id
						JOIN company_user cu ON cucd.company_user_id = cu.id
						JOIN user_auth ua_create ON t.created_by = ua_create.id
						LEFT JOIN chat c ON t.id = c.ticket_id
						LEFT JOIN user_client_ticket uct ON t.id = uct.ticket_id
						LEFT JOIN user_client uc ON uct.user_client_id = uc.id
						LEFT JOIN user_auth ua_client ON uc.user_auth_id = ua_client.id
						WHERE cu.company_id = :companyselected -- AND c.id IS NULL
						AND (cu.user_auth_id = :user OR :is_admin = 1)
						AND t.`status` IN ("OPENED")
						AND t.id = :ticket_id
						AND cd.deleted_at IS NULL
						AND c.deleted_at IS NULL
						AND t.deleted_at IS NULL
						-- AND DATE(t.created_at) >= "2021-01-26"
						-- AND DATE(t.created_at) <= "2021-01-27"
						GROUP BY cd.id, t.id';

			$tickets['result'] = DB::select($query, $arrayName);

			foreach ($tickets['result'] as $key) {
				$key->department_id = Crypt::encrypt($key->department_id);
				$key->chat_id_decry = $key->chat_id;
				$key->chat_id = Crypt::encrypt($key->chat_id);
				$key->company_user_company_department_id = Crypt::encrypt($key->company_user_company_department_id);
				$key->overdue = json_decode($key->settings)->quant_limity->lateTime;
				$key->id_encrypted = Crypt::encrypt($key->id);
				$key->created_by_encrypted = Crypt::encrypt($key->created_by);
				$key->chat_created_by_encrypted = Crypt::encrypt($key->id_created);
				$key->email_created = ClearEmail::clear($key->email_created);
			}
		} catch (\Exception $e) {
			echo $e;
			Logger::reportException($e, [], ['ticket-controller', 'getTicketToTriggerOpened'], false);
			$tickets['success'] = false;
		}

		return $tickets;
	}

	public function files($chat_id, $filename)
	{
		if(isset(session('restriction')[0]) && (session('restriction')[0]->ticket_admin || session('restriction')[0]->ticket_alllist)){
            $company_id = Crypt::decrypt(session('companyselected')['id']);

        // se for o cliente do chat
        } else if (isset(session('companyselected')['user_client_id'])) {
            UserClientChat::where('chat_id', Crypt::decrypt($chat_id))
                ->where('user_client_id', Crypt::decrypt(session('companyselected')['user_client_id']))
                ->firstOrFail();

            $company_id = Crypt::decrypt(session('companyselected')['company_id']);
            // se não, faço a lógica pro atendente
        } else {
            CompanyUserCompanyDepartment::join('chat', 'chat.comp_user_comp_depart_id_current', 'company_user_company_department.id')
                ->whereNull('chat.deleted_at')
                ->where('chat.id', Crypt::decrypt($chat_id))
                ->where('company_user_company_department.company_user_id', Crypt::decrypt(session('companyselected')['company_user_id']))
                ->firstOrFail();

            $company_id = Crypt::decrypt(session('companyselected')['id']);
        }

        if(!isset($company_id)){
            abort(404);
        } else {
            $path = '..' . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'company' . DIRECTORY_SEPARATOR . $company_id . DIRECTORY_SEPARATOR . 'chat' . DIRECTORY_SEPARATOR . Crypt::decrypt($chat_id) . DIRECTORY_SEPARATOR . $filename;

            if (!File::exists($path)) {
                abort(404);
            }

            $file = File::get($path);
            $type = File::mimeType($path);

			ob_end_clean();

            $response = Response::make($file, 200);
            $response->header("Content-Type", $type);
            // caso queira que o arquivo seja baixado automaticamente e com o nome original ao acessar a rota,
            // descomentar a linha abaixo e deixar o parametro filename com o nome original do arquivo...
            //->header('Content-disposition','attachment; filename="nome-do-arquivo.pdf"');

            return $response;
        }
	}


	public function searchTicket(){
		$tickets['success'] = false;

		try {

			$filtertype = request('filtertype');

			$searchQuery = request('searchQuery');
			$is_admin = request('is_admin');


			if($filtertype == 'nameComplete'){

				// $query = "SELECT * from chat_history where :content LIKE :value";
				// $query = str_replace(':content', 'content', $query);
				// $tickets['result'] = DB::select( DB::raw($query), array(
				//    'value' =>  '%'.request('searchQuery').'%',
			 // 	));

				$tickets['result'] = SearchFilter::getNameComplete($searchQuery);
			}

			if($filtertype == 'ticketId'){

				$tickets['result'] = SearchFilter::getIdTicket($searchQuery);

			}

			if($filtertype == 'description'){

				$tickets['result'] = SearchFilter::getDescription($searchQuery);
			}

			if($filtertype == 'email'){

				$tickets['result'] = SearchFilter::getEmail($searchQuery);
			}

			if($filtertype == 'company'){

				$tickets['result'] = SearchFilter::getCompany($searchQuery);
			}

			// if($filtertype == 'phone'){

			// 	$tickets['result'] = SearchFilter::getPhone($searchQuery);
			// }

			if($filtertype == 'operator'){

				$tickets['result'] = SearchFilter::getOperator($searchQuery);
			}

			if($filtertype == 'comment'){

				$tickets['result'] = SearchFilter::getComment($searchQuery);
			}




			$tickets['success'] = true;

		} catch (\Exception $e) {
			echo $e;
			Logger::reportException($e, [], ['ticket-controller', 'searchTicket'], false);
			$tickets['success'] = false;
		}


		return $tickets;
	}
	public function setBlockClientTicket(Request $request) {

		$client['success'] = false;
		// BLOCK CLIENT
		try {
			if(request('status') == 1){
				$client['result'] = BlockUsers::blockClient(request('id'), request('textReason'));
			}else{
				$client['result'] = BlockUsers::unlockClient(request('id'));
			}
			$client['success'] = true;

		} catch (\Exception $e) {
			echo $e;
			Logger::reportException($e, [], ['ticket-controller', 'setBlockClientTicket'], false);
			$client['success'] = false;
		}

		echo json_encode($client);
	}

	public function checkBlockTicket(){
		$users['success'] = false;
		// BLOCK CLIENT
		try {

			$users['result'] = DB::table('blacklist_user')
			->where('blocked_id', Crypt::decrypt(request('id')))
			->select('reason')
			->first();

			if ($users['result']) {

				if(json_decode($users['result']->reason) != null){
					$users['selectedTime'] = json_decode($users['result']->reason)[1];
					$users['selectedBan'] = json_decode($users['result']->reason)[2];
					$users['reason'] = json_decode($users['result']->reason)[0];
				}else{
					$users['reason'] = $users['result']->reason;
					$users['selectedTime'] = 'permanent';
					$users['selectedBan'] = 'system';
				}

				$users['result'] = true;
				$users['value'] = true;
				$users['success'] = true;
			}else{
				$users['result'] = false;
				$users['value'] = false;
				$users['success'] = false;
                $users['reason'] = "";
			}

			

		} catch (\Exception $e) {
			echo $e;
			Logger::reportException($e, [], ['ticket-controller', 'checkBlockTicket'], false);
			$users['success'] = false;
		}

		echo json_encode($users);
	}

    public function getComments() {
        $comments['success'] = false;

		try {

			$comments['result'] = DB::table('ticket_comments')->select(
                'ua.id',
                'ua.name',
                'ua.email',
                'ua.id',
                'ticket_comments.comment',
                'ticket_comments.created_at',
            )
            ->join('ticket AS t', 't.id', 'ticket_comments.ticket_id')
            ->leftjoin('user_auth AS ua', 'ua.id', 'ticket_comments.user_auth_id')
            ->where('t.id', request('ticket_id'))
            ->whereNull('t.deleted_at')
			->orderBy('ticket_comments.id','DESC')
            ->get();
			

			if ($comments['result']) {
				$comments['success'] = true;
			}else{
				$comments['success'] = false;
			}

            return response()->json($comments);

		} catch (\Exception $e) {
			Logger::reportException($e, [], ['ticket-controller', 'getComments'], false);
			$comments['success'] = false;
		}

    }

    public function setComments(Request $request) {
		$comment['success'] = false;
        $ticket_id = $request['ticket_id'];
        $content = $request['comment'];
        $created_at = \Carbon\Carbon::now()->toDateTimeString();
		
		try {
			
			$comment['result'] = TicketComments::create([
				'user_auth_id' => Auth::id(),
                'ticket_id' =>  $ticket_id,
                'comment' =>  $content,
                'created_at' => $created_at,
            ]);
			
			if ($comment['result']) {
				$comment['success'] = true;
				
                broadcast(new TicketCommentUpdate([
					'name'          => Auth::user()->name,
                    'email'         => Auth::user()->email,
                    'id'            => Auth::id(),
                    'comment'       => $content,
                    'ticket_id'     => $ticket_id,
                    'created_at'    => $created_at
                ]));
				
			}else{
				$comment['success'] = false;
			}
			
            return $comment;
			
		} catch (\Exception $e) {
			echo $e;
			Logger::reportException($e, [], ['ticket-controller', 'getComments'], false);
			$comment['success'] = false;
		}
    }
	
	public function setCategory(Request $request) {

		$caregory['success'] = false;
		
		try {
			$categorySelected = request('concatCategory');
			$category_id = null;
			$chat_id = request('chat_id');
			$ticket_id = request('ticket_id');
			$filter_not_category = request('filter_not_category');
			$cttype = request('cttype');

			if($cttype == 'CHAT'){
				$chat_id = Crypt::decrypt($chat_id);
			}

			foreach ($categorySelected as $key) {
				$category_id = $key['id'];
			}
			if($category_id != null && $chat_id != null){

				$check = DB::table('chat_category')
				->where('category_id', $category_id)
				->where('chat_id', $chat_id)
				->first();

				if($check == null){
					DB::table('chat_category')->insertGetId([
						'category_id' => $category_id,
						'chat_id' => $chat_id,
						'created_by' => auth()->user()->id,
						'created_at' => \Carbon\Carbon::now()->toDateTimeString(),
					]);
					
					if($cttype == 'TICKET' && $ticket_id != null && $filter_not_category == 1){
						$realtime = new \App\Tools\Tickets\SendRealtime($ticket_id, 'splice');
						$realtime->updateTableQueue();
						$realtime->updateTableInProgress();
						$realtime->updateTableResolved();
						$realtime->updateTableClosed();
					}else if($cttype == 'CHAT' && $filter_not_category == 1){
						$realtime = new \App\Tools\Chats\SendRealtime($chat_id, 'splice');
						$realtime->updateTableQueue();
						$realtime->updateTableInProgress();
						$realtime->updateTableResolved();
						$realtime->updateTableClosed();
					}

					$caregory['success'] = true;
				}else{
					$caregory['error'] = 'bs-this-category-has-already-been-defined';
					$caregory['success'] = false;	
				}
			}else{
				$caregory['error'] = 'bs-select-one-ticket';
				$caregory['success'] = false;	
			}

		} catch (\Exception $e) {
			echo $e;
			Logger::reportException($e, [], ['ticket-controller', 'setCategory'], false);
			$caregory['success'] = false;
		}
		
		return $caregory;
	}

	public function removeCategory(Request $request) {
		$caregory['success'] = false;
		
		try {
			DB::table('chat_category')
			->where('id', request('chat_category_id'))
			->delete();
			$caregory['success'] = true;

		} catch (\Exception $e) {
			echo $e;
			Logger::reportException($e, [], ['ticket-controller', 'removeCategory'], false);
			$caregory['success'] = false;
		}
		return $caregory;
	}

    public function checkCategory(Request $request) {
		$caregory['success'] = false;
		
		try {
			if(request('cttype') == 'CHAT'){
				$chat_id = Crypt::decrypt((request('chat_id')));
			}else{
				$chat_id = request('chat_id');
			}

			$result = DB::table('chat_category')
			->where('chat_id', $chat_id)
			->exists();

			$caregory['result'] = $result;
			$caregory['success'] = true;

		} catch (\Exception $e) {
			echo $e;
			Logger::reportException($e, [], ['ticket-controller', 'removeCategory'], false);
			$caregory['success'] = false;
		}
		return $caregory;
	}

    public function getTicketInfoClient(Request $request) {
        $result['success'] = false;
        try {
            $company_id = Crypt::decrypt(session('companyselected')['company_id']);
            $ticket_id = Crypt::decrypt($request->id);
            $ticket = UserClientTicket::select(
                    'ticket.id',
                    'ticket.created_at',
                    'ticket.status', 
                    'ticket.description',
                    'ticket.update_status_closed_resolved',
                    'chat.type as chat_type',
                    'chat.comp_user_comp_depart_id_current as cucd_id',
                    'chat.id as chat_id',
                    'company_department.id as department_id',
                    'company_department.name as department_name'
                )
                ->join('user_client', 'user_client_ticket.user_client_id', 'user_client.id')
                ->join('user_auth', 'user_client.user_auth_id', 'user_auth.id')
                ->join('ticket', 'user_client_ticket.ticket_id', 'ticket.id')
                ->join('company_department', 'ticket.company_department_id', 'company_department.id')
                ->join('chat', 'chat.ticket_id', 'ticket.id')
                ->where('ticket.company_id', $company_id)
                ->where('ticket.id', $ticket_id)
                ->where('user_auth.id', Auth::id())
                ->where('ticket.status', '!=', 'CANCELED')
                // ->where('ticket.status', '!=', 'MERGED')
                ->whereNull('ticket.deleted_at')
                ->first();

            if (isset($ticket)) {

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
                        $ticket->attendant_id = Crypt::encrypt($cucd->attendant_id);
                        $ticket->attendant_name = $cucd->attendant_name;
                        $ticket->attendant_email = $cucd->attendant_email;
                    }

                    $ticket->cucd_id = Crypt::encrypt($ticket->cucd_id);

                } else {
                    $ticket->attendant_id = null;
                    $ticket->attendant_name = null;
                    $ticket->attendant_email = null;
                }

                if (is_null($ticket->description)) {
                    $ticket->description = 'bs-no-description';
                }

                $department_settings = CompanyDepartmentSettings::select('settings')->where('company_department_id', $ticket->department_id)->first()->settings;

                $ticket->department_id = Crypt::encrypt($ticket->department_id);
                $ticket->hash_id = Crypt::encrypt($ticket->id);
                $ticket->chat_hash_id = Crypt::encrypt($ticket->chat_id);

                $result['ticket'] = $ticket;
                $result['settings'] = json_decode($department_settings);
                $result['success'] = true;
            }

            
        } catch (\Exception $e) {
            Logger::reportException($e, [], ['chat-controller', 'getChatInfo'], false);
        }
        return $result;
    }

    public function checkEvaluation(Request $request)
	{
		$data = $request->all();

		$validator = Validator::make($data, [
			'ticket_id' => 'required|string',
		]);

		if ($validator->fails()) {
			return response($validator->errors(), 400);
		}

		$check = Avaliation::select(DB::raw('count(id) as evaluation'))
			->where('ticket_id', Crypt::decrypt($data['ticket_id']))
			->first();

		if ($check->evaluation) {
			return response()->json([
				'status' => true
			]);
		} else {
			return response()->json([
				'status' => false
			]);
		}
	}
}
