<?php

namespace App\Http\Controllers;

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
use App\Events\MessageSent;
use App\Events\TicketsAgentListUpdate;
use App\Events\FullTicket;
use App\Models\UserClientTicket;
use App\Tools\ChatWorkingTimes;
use App\Tools\ClearEmail;
use App\Tools\Tickets\Feedback;
use App\Tools\Tickets\SendRealtime;
use Illuminate\Support\Facades\Validator;

class FullTicketController extends Controller
{
    public function index(){
		return view('functions.full-ticket.index');
	}

	public function getTicketsAdmin(){ // alterado

		$tickets['success'] = false;

		$data =  request()->all();
		$validator = Validator::make($data, [
			'skip' => 'required|int',
		]);

		if ($validator->fails()) {
			return response($validator->errors(), 400);
		}

		if(gettype(request('departmentSelected')) == 'string'){
			$concat = "0";
		}else if(request('departmentSelected') != null){
			$concat = '';
			foreach ($data['departmentSelected'] as $key){
				$concat = $concat ."'" .Crypt::decrypt(json_decode($key)->id) ."',";
			}
			$concat = $concat."''";
		}else{
			$concat = "0";
		}

		$OPENED = request('type');
		try {
			//LEMBRETE, ATUALIZAR ESSAS QUERYS DE FILTROS USANDO APENAS ELOQUENT!! (QUANDO SOBRAR TEMPO, (DIFÍCIL))

			//FILTRO PARA TRAZER TODOS OS TICKETS EM ABERTO
			if ($OPENED == 'OPENED') {
				if(intval(session('restriction')[0]->ticket_admin)){
					$arrayName = array(
						'companyselected' => intval(Crypt::decrypt(session('companyselected')['id'])),
						'company_user_id' => Crypt::decrypt(session('companyselected')['company_user_id']),
					);

					$query = 'SELECT * FROM (
						SELECT t.id, t.status, t.description, t.created_at, t.updated_at, t.created_by, c.created_by as chat_created_by,
						t.priority, t.type, t.user_agent, t.comments, t.update_status_in_progress, null as name, null as email, cd.name AS department,
						cd.type as department_type, cd.id AS department_id, null as company_user_company_department_id, cds.settings,
						COALESCE(ua_client.id, ua_create.id)  AS id_created,
						COALESCE(ua_client.name, ua_create.name)  AS name_created,
						COALESCE(ua_client.email, ua_create.email) AS email_created,
						ua_client.builderall_account_data AS builderall_account_data, c.service_time, c.update_status_in_progress as last_update_status, c.id as chat_id, cu.id AS company_user_id, c.type as chat_type, IF((SELECT company_user_company_department_id FROM chat_history WHERE chat_id = c.id ORDER BY id DESC LIMIT 1) IS NULL, 1, 0) AS answered
						FROM ticket t
						JOIN company_department cd ON t.company_department_id = cd.id
						JOIN company_department_settings cds ON cd.id = cds.company_department_id
						JOIN user_auth ua_create ON t.created_by = ua_create.id
						JOIN company_user cu ON :company_user_id = cu.id
						LEFT JOIN chat c ON t.id = c.ticket_id
						LEFT JOIN user_client_ticket uct ON t.id = uct.ticket_id
						LEFT JOIN user_client uc ON uct.user_client_id = uc.id
						LEFT JOIN user_auth ua_client ON uc.user_auth_id = ua_client.id
						WHERE cu.company_id = :companyselected -- AND c.id IS NULL
						AND t.`status` IN ("OPENED")
						AND cd.id IN ('.$concat.')
						AND t.deleted_at IS NULL
						AND c.deleted_at IS NULL
						AND cd.deleted_at IS NULL
						-- AND DATE(t.created_at) >= "2021-01-26"
						-- AND DATE(t.created_at) <= "2021-01-27"
						GROUP BY cd.id, t.id
					) sub ORDER BY sub.created_at ASC';

				}else{

					$arrayName = array(
						'user' => intval(auth()->user()->id),
						'companyselected' => intval(Crypt::decrypt(session('companyselected')['id'])),
					);

					$query = 'SELECT * FROM (
						SELECT t.id, t.status, t.description, t.created_at, t.updated_at, t.created_by, c.created_by as chat_created_by,
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
						AND cu.user_auth_id = :user
						AND cucd.is_active = 1
						AND t.`status` IN ("OPENED")
						AND cd.id IN ('.$concat.')
						AND t.deleted_at IS NULL
						AND c.deleted_at IS NULL
						AND cd.deleted_at IS NULL
						-- AND DATE(t.created_at) >= "2021-01-26"
						-- AND DATE(t.created_at) <= "2021-01-27"
						GROUP BY cd.id, t.id
					) sub ORDER BY sub.created_at ASC';
				}

			//FILTRO PARA TRAZER TODOS OS OUTROS TICKETS ADMIN/ALLLIST
			}else {
				if(intval(session('restriction')[0]->ticket_admin) == 1){
					if($OPENED == "OVERDUE"){
						$OPENED = 'IN_PROGRESS';
					}

					$arrayName = array(
						'user' => intval(auth()->user()->id),
						'companyselected' => intval(Crypt::decrypt(session('companyselected')['id'])),
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
							ua_client.builderall_account_data AS builderall_account_data,c.service_time, c.update_status_in_progress as last_update_status, c.id as chat_id, cucd.company_user_id, c.type as chat_type, IF((SELECT company_user_company_department_id FROM chat_history WHERE chat_id = c.id ORDER BY id DESC LIMIT 1) IS NULL, 1, 0) AS answered
							FROM ticket t
							JOIN user_ticket ut ON t.id = ut.ticket_id
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
							AND t.`status` IN (:status)
							AND cd.id IN ('.$concat.')
							AND cd.deleted_at IS NULL
							AND c.deleted_at IS NULL
							AND t.deleted_at IS NULL
							AND (1 = 1 OR cucd.company_department_id IN (
								SELECT cucd2.company_department_id FROM company_user_company_department cucd2
								JOIN company_user cu2 ON cucd2.company_user_id = cu2.id
								WHERE cu2.user_auth_id = :user
							))
							GROUP BY cd.id, t.id
						) sub ORDER BY answered DESC, sub.updated_at';

				}else if(intval(session('restriction')[0]->ticket_alllist) == 1){
					if($OPENED == "OVERDUE"){
						$OPENED = 'IN_PROGRESS';
					}

					$arrayName = array(
						'user' => intval(auth()->user()->id),
						'companyselected' => intval(Crypt::decrypt(session('companyselected')['id'])),
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
							JOIN company_user cu ON cucd.company_user_id = cu.id
							JOIN user_auth ua ON cu.user_auth_id = ua.id
							LEFT JOIN user_client_ticket uct ON t.id = uct.ticket_id
							LEFT JOIN user_client uc ON uct.user_client_id = uc.id
							LEFT JOIN user_auth ua_client ON uc.user_auth_id = ua_client.id
							WHERE t.company_id = :companyselected
							AND t.`status` IN (:status)
							AND cd.id IN ('.$concat.')
							AND cd.deleted_at IS NULL
							AND c.deleted_at IS NULL
							AND t.deleted_at IS NULL
							AND (0 = 1 OR cucd.company_department_id IN (
								SELECT cucd2.company_department_id FROM company_user_company_department cucd2
								JOIN company_user cu2 ON cucd2.company_user_id = cu2.id
								WHERE cu2.user_auth_id = :user
								AND cucd2.is_active = 1 -- ATUALIZAÇÃO AQUI PARA PEGAR APENAS SE ESTIVAR ATIVADO
							))
							GROUP BY cd.id, t.id
						) sub ORDER BY answered DESC, sub.updated_at';
				}
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
			);

			//LEMBRETE - ARRUMAR ISSO
			$query2 = "SELECT
						    COALESCE(SUM(IF(t.STATUS = 'CLOSED', 1, 0)), 0) AS CLOSED,
						    COALESCE(SUM(IF(t.STATUS = 'RESOLVED', 1, 0)), 0) AS RESOLVED,
						    COALESCE(SUM(IF(t.STATUS = 'CANCELED', 1, 0)), 0) AS CANCELED,
						    COALESCE(SUM(IF(t.STATUS = 'OPENED', 1, 0)), 0) AS OPENED,
						    COALESCE(SUM(IF(t.STATUS = 'IN_PROGRESS', 1, 0)), 0) AS IN_PROGRESS
						FROM ticket t
						WHERE t.company_id = :companyselected
                        AND t.deleted_at IS NULL;
                    ";

			if(session('restriction')[0]->ticket_alllist){
				$arrayName2 = array(
					'companyselected' => intval(Crypt::decrypt(session('companyselected')['id'])),
					'companyselected2' => intval(Crypt::decrypt(session('companyselected')['id'])),
					'user' => intval(auth()->user()->id),
					'user2' => intval(auth()->user()->id),
				);

				$query2 = 	"SELECT
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
								WHERE t.company_id = :companyselected -- AND cucd.company_user_id = 46
                                AND t.deleted_at IS NULL

								AND (0 = 1 OR cucd.company_department_id IN (
									SELECT cucd2.company_department_id FROM company_user_company_department cucd2
									JOIN company_user cu2 ON cucd2.company_user_id = cu2.id
									WHERE cu2.user_auth_id = :user
									AND cucd2.is_active = 1
								))

								UNION

								SELECT
									0 AS CLOSED,
									0 AS RESOLVED,
									0 AS CANCELED,
									COUNT(DISTINCT tt.id) AS OPENED,
									0 AS IN_PROGRESS
									FROM ticket AS tt
									JOIN company_user_company_department cucd ON tt.company_department_id = cucd.company_department_id
									WHERE tt.company_id = :companyselected2 -- AND cucd.company_user_id = 32
									AND tt.status = 'OPENED'
									AND tt.deleted_at IS NULL

									AND (0 = 1 OR cucd.company_department_id IN (
										SELECT cucd2.company_department_id FROM company_user_company_department cucd2
										JOIN company_user cu2 ON cucd2.company_user_id = cu2.id
										WHERE cu2.user_auth_id = :user2
										AND cucd2.is_active = 1
									))
							) sub;";
			}

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
			Logger::reportException($e, [], ['agents-controller', 'show'], false);
			$tickets['success'] = false;
		}

		echo json_encode($tickets);
	}

	public static function ticketsInprogressOverdue(){

		if(intval(session('restriction')[0]->ticket_admin) == 1){

			$arrayName = array(
				'user' => intval(auth()->user()->id),
				'companyselected' => intval(Crypt::decrypt(session('companyselected')['id'])),
				'status' => 'IN_PROGRESS',
			);

			$query =
				'SELECT t.id,t.updated_at, cds.settings
					FROM ticket t
					JOIN user_ticket ut ON t.id = ut.ticket_id
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
					AND t.`status` IN (:status)
					AND cd.deleted_at IS NULL
					AND c.deleted_at IS NULL
					AND t.deleted_at IS NULL
					AND (1 = 1 OR cucd.company_department_id IN (
						SELECT cucd2.company_department_id FROM company_user_company_department cucd2
						JOIN company_user cu2 ON cucd2.company_user_id = cu2.id
						WHERE cu2.user_auth_id = :user
					))
					GROUP BY cd.id, t.id';
		}else if(intval(session('restriction')[0]->ticket_alllist) == 1){

			$arrayName = array(
				'user' => intval(auth()->user()->id),
				'companyselected' => intval(Crypt::decrypt(session('companyselected')['id'])),
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
					JOIN company_user cu ON cucd.company_user_id = cu.id
					JOIN user_auth ua ON cu.user_auth_id = ua.id
					LEFT JOIN user_client_ticket uct ON t.id = uct.ticket_id
					LEFT JOIN user_client uc ON uct.user_client_id = uc.id
					LEFT JOIN user_auth ua_client ON uc.user_auth_id = ua_client.id
					WHERE t.company_id = :companyselected
					AND t.`status` IN (:status)
					AND cd.deleted_at IS NULL
					AND t.deleted_at IS NULL
					AND c.deleted_at IS NULL
					AND (0 = 1 OR cucd.company_department_id IN (
						SELECT cucd2.company_department_id FROM company_user_company_department cucd2
						JOIN company_user cu2 ON cucd2.company_user_id = cu2.id
						WHERE cu2.user_auth_id = :user
						AND cucd2.is_active = 1 -- ATUALIZAÇÃO AQUI PARA PEGAR APENAS SE ESTIVAR ATIVADO
					))
					GROUP BY cd.id, t.id';
		}
		$result= DB::select($query, $arrayName);

		foreach ($result as $key) {
			$key->overdue = json_decode($key->settings)->quant_limity->lateTime;
		}

		return $result;
	}

	public static function MyticketsInprogressOverdue($departamento_id){

		$arrayName = array(
			'user' => intval(auth()->user()->id),
			'companyselected' => intval(Crypt::decrypt(session('companyselected')['id'])),
			'company_user_id' => intval(Crypt::decrypt(session('companyselected')['company_user_id'])),
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
				JOIN company_user cu ON cucd.company_user_id = cu.id
				JOIN user_auth ua ON cu.user_auth_id = ua.id
				LEFT JOIN user_client_ticket uct ON t.id = uct.ticket_id
				LEFT JOIN user_client uc ON uct.user_client_id = uc.id
				LEFT JOIN user_auth ua_client ON uc.user_auth_id = ua_client.id
				WHERE t.company_id = :companyselected AND cucd.company_user_id = :company_user_id
				AND t.`status` IN (:status)
				AND cd.id IN ('.$departamento_id.')
				AND cd.deleted_at IS NULL
				AND c.deleted_at IS NULL
				AND t.deleted_at IS NULL
				AND (0 = 1 OR cucd.company_department_id IN (
					SELECT cucd2.company_department_id FROM company_user_company_department cucd2
					JOIN company_user cu2 ON cucd2.company_user_id = cu2.id
					WHERE cu2.user_auth_id = :user
					AND cucd2.is_active = 1 -- ATUALIZAÇÃO AQUI PARA PEGAR APENAS SE ESTIVAR ATIVADO
				))
				GROUP BY cd.id, t.id';

		$result= DB::select($query, $arrayName);

		foreach ($result as $key) {
			$key->overdue = json_decode($key->settings)->quant_limity->lateTime;
		}

		return $result;
	}

	public function addChatTicketAdmin(){
		
		try {
			$result['success'] = false;

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

				//Verificando no back se ainda está opened
				$ticket  = Ticket::find(request('itemselected')['id']);
				if($ticket->status != 'OPENED'){
					return $result;
				}

				$cucd_id = DB::table('company_user_company_department')
					->where('company_user_id', Crypt::decrypt(session('companyselected')['company_user_id']))
					->where('company_department_id', Crypt::decrypt(request('itemselected')['department_id']))
					->select('id')
					->first();

				if($cucd_id == null){
					//VINCULAR AGORA O ATENDENTE AO DEPARTAMENTO
					$cucd_id = DB::table('company_user_company_department')->insertGetId([
						'company_user_id' => Crypt::decrypt(session('companyselected')['company_user_id']),
						'company_department_id' => Crypt::decrypt(request('itemselected')['department_id']),
					]);
				}else{
					$cucd_id = $cucd_id->id;
				}

				DB::table('user_ticket')
				->where('ticket_id', request('itemselected')['id'])
				->delete();

				DB::table('user_ticket')->insertGetId([
					'company_user_id' => Crypt::decrypt(session('companyselected')['company_user_id']),
					'ticket_id' => request('itemselected')['id'],
					'created_by' => auth()->user()->id,
					'created_at' => \Carbon\Carbon::now()->toDateTimeString(),
				]);

				$chat_id = DB::table('chat')
					->where('ticket_id', request('itemselected')['id'])
					->first();

				if($chat_id == null){
					DB::table('chat')->insertGetId([
						'company_id' => Crypt::decrypt(session('companyselected')['id']),
						'company_department_id' => Crypt::decrypt(request('itemselected')['department_id']),
						'comp_user_comp_depart_id_creator' => null,
						'comp_user_comp_depart_id_current' => null,
						'ticket_id' => request('itemselected')['id'],
						'type' => 'TICKET',
						'priority' => 'NORMAL',
						'user_agent' => $_SERVER['HTTP_USER_AGENT'],
						'created_by' => auth()->user()->id,
						'created_at' => \Carbon\Carbon::now()->toDateTimeString(),
					]);

					$chat_id = DB::table('chat')
					->where('ticket_id', request('itemselected')['id'])
					->first();
				}

				$check = DB::table('chat_history')
					->where('chat_id', $chat_id->id)
					->orderBy('id', 'DESC')
					->first();

				if($check == [] || $check->content != 'bs-joined-the-ticket'){
					//'TEXT','URL','IMAGE','EMOTICON','AUDIO','GIF','EVENT','CALL','FILE','OPEN','CLOSE'
					$chat_history_id = DB::table('chat_history')->insertGetId([
						'chat_id' => $chat_id->id,
						'company_user_company_department_id' => $cucd_id,
						'type' => 'EVENT',
						'content' => 'bs-joined-the-ticket',
						'created_by' => auth()->user()->id,
						'created_at' => \Carbon\Carbon::now()->toDateTimeString(),
					]);

					ChatWorkingTimes::Insert($chat_id->id, $cucd_id, $chat_history_id, request('itemselected')['id']);
				}else{
					ChatWorkingTimes::Insert($chat_id->id, $cucd_id, $check->id, request('itemselected')['id']);
				}
				
				if($chat_id->type == 'TICKET'){
					DB::table('chat')
					->where('id', $chat_id->id)
                    ->whereNull('deleted_at')
					->update([
						'comp_user_comp_depart_id_creator' => $cucd_id,
						'comp_user_comp_depart_id_current' => $cucd_id,
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
				
				$first_register = DB::table('ticket')
				->where('id', request('itemselected')['id'])
				->select('update_status_in_progress')->first();

				if($first_register->update_status_in_progress == null){
					$update_status_in_progress = \Carbon\Carbon::now()->toDateTimeString(); 
					$result['update_status_in_progress'] = $update_status_in_progress;
					$result['value'] = 'opened';
				}else{
					$update_status_in_progress = $first_register->update_status_in_progress;
					$result['update_status_in_progress'] = $update_status_in_progress;
					$result['value'] = 'opened';
				}
				
				DB::table('ticket')
					->where('id', request('itemselected')['id'])
					->update([
						'status' => 'IN_PROGRESS',
						'queue_time' => Ticket::calculatorTimeQueueTicket(request('itemselected')['id'], 'IN_PROGRESS'),
						'update_status_in_progress' => $update_status_in_progress,
						'updated_at' => \Carbon\Carbon::now()->toDateTimeString(),
						'updated_by' => auth()->user()->id,
					]);
			
				// REMOVE TICKET APÓS UM ATENDENTE PEGAR ELE SE O STATUS FOR OPENED - OK
				
                $realtime = new SendRealtime(request('itemselected')['id'], 'push');
                $realtime->updateTableInProgress();

                $realtime2 = new SendRealtime(request('itemselected')['id'], 'splice');
                $realtime2->updateTableQueue();

				//ADICIONA NA LISTA DO PROPRIO ATENDENTE - OK
				$dataTicket = $this->getTicketToTriggerAdmin(request('itemselected')['id'], intval(Crypt::decrypt(session('companyselected')['id'])));

				// atualiza para o cliente
				$user_client_ticket = DB::table('user_client_ticket')
					->select('user_client_id')
					->where('ticket_id', request('itemselected')['id'])
					->get();

                $user = auth()->user();

				if ($user_client_ticket) {
					//VERSÃO DO ATENDENTE QUE ATUALIZA O STATUS DO TICKET DO CLIENTE
					broadcast(new ClientTicketsList([
                        'id' => request('itemselected')['id'],
                        'hash_id' => Crypt::encrypt(request('itemselected')['id']),
                        'company_id' => session('companyselected')['id'],
                        'user_client_id' => Crypt::encrypt($user_client_ticket[0]->user_client_id),
                        'agent' => $user->name,
                        'agent_email' => $user->email,
                        'agent_id' => $user->id,
                        'cucd_id' => Crypt::encrypt($cucd_id),
                        'status' => 'IN_PROGRESS',
                        //old info
						'company_user_id' => $dataTicket['result'][0]->company_user_id,
						'action' => 'updateStatus',
					]));
				}
				$result['id'] = request('itemselected')['id'];
				$result['email'] = $user->email;
			}else{

				$first_register = DB::table('ticket')
				->where('id', request('itemselected')['id'])
				->select('update_status_in_progress')->first();

				if($first_register->update_status_in_progress == null){
					$update_status_in_progress = \Carbon\Carbon::now()->toDateTimeString(); 
					$result['update_status_in_progress'] = $update_status_in_progress;
				}else{
					$update_status_in_progress = $first_register->update_status_in_progress;
					$result['update_status_in_progress'] = $update_status_in_progress;
				}
				
				$result['value'] = 'not_opened';
			}
			$result['success'] = true;
		} catch (\Exception $e) {
			echo $e;
			Logger::reportException($e, [], ['ticketController', 'addChatTicketAdmin'], false);
			$result['success'] = false;
		}

		return $result;
	}

	public function getMyTickets(Request $request){

		$tickets['success'] = false;
		$selectedtype = json_decode(request('activeMenuControls'));

		$concat = '';
		$departamento_id_array = [];
		foreach (request('departmentSelected') as $key){
			$concat = $concat ."'" .Crypt::decrypt(json_decode($key)->id)."',";
			array_push($departamento_id_array, intval(Crypt::decrypt(json_decode($key)->id)));
		}
		$departamento_id = $concat."''";

		try {
			if($selectedtype->active->opened){

				$arrayName = array(
					'user' => intval(auth()->user()->id),
					'companyselected' => intval(Crypt::decrypt(session('companyselected')['id'])),
					'is_admin' => intval(session('restriction')[0]->ticket_admin),
				);

				$query = 'SELECT * FROM (
							SELECT t.id, t.status, t.description, t.created_at, t.updated_at, t.created_by, c.created_by as chat_created_by,
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
							AND cucd.is_active = 1 -- ATUALIZAÇÃO AQUI PARA PEGAR APENAS SE ESTIVAR ATIVADO
							AND t.`status` IN ("OPENED")
							AND cd.id IN ('.$departamento_id.')
							AND t.deleted_at IS NULL
							AND c.deleted_at IS NULL
							AND cd.deleted_at IS NULL
							-- AND DATE(t.created_at) >= "2021-01-26"
							-- AND DATE(t.created_at) <= "2021-01-27"
							GROUP BY cd.id, t.id
						) sub ORDER BY sub.created_at ASC';


						$take = request('quantRows');
						$query .= " LIMIT :skip, :take";
						$arrayName['skip'] = request('skip');
						$arrayName['take'] = $take;

						$tickets['status'] = 'OPENED';
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
							'user' => intval(auth()->user()->id),
							'user2' => intval(auth()->user()->id),
						);

						$query2 = 	"SELECT
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
										LEFT JOIN chat AS c ON c.ticket_id = t.id
										LEFT JOIN user_ticket AS ut ON t.id = ut.ticket_id
										LEFT JOIN company_user AS cu ON ut.company_user_id = cu.id
										LEFT JOIN company_user_company_department AS cucd ON cu.id = cucd.company_user_id
										AND t.company_department_id = cucd.company_department_id
										WHERE t.company_id = :companyselected
										AND cucd.company_user_id = :company_user_id
										AND c.comp_user_comp_depart_id_current = cucd.id
                                        AND t.deleted_at IS NULL
                                        AND c.deleted_at IS NULL

										AND (0 = 1 OR cucd.company_department_id IN (
											SELECT cucd2.company_department_id FROM company_user_company_department cucd2
											JOIN company_user cu2 ON cucd2.company_user_id = cu2.id
											WHERE cu2.user_auth_id = :user
											AND cucd2.is_active = 1
										))

										UNION

										SELECT
											0 AS CLOSED,
											0 AS RESOLVED,
											0 AS CANCELED,
											COUNT(DISTINCT tt.id) AS OPENED,
											0 AS IN_PROGRESS
											FROM ticket AS tt
											JOIN company_user_company_department cucd ON tt.company_department_id = cucd.company_department_id
											WHERE tt.company_id = :companyselected2 -- AND cucd.company_user_id = 32
											AND tt.status = 'OPENED'
                                            AND tt.deleted_at IS NULL

											AND (0 = 1 OR cucd.company_department_id IN (
												SELECT cucd2.company_department_id FROM company_user_company_department cucd2
												JOIN company_user cu2 ON cucd2.company_user_id = cu2.id
												WHERE cu2.user_auth_id = :user2
												AND cucd2.is_active = 1
											))
									) sub;";


					$tickets['success'] = true;
					$tickets['count'] = DB::select($query2, $arrayName2);


					//----------------------CÁLCULO PARA VERIFICAR A QUANTIDADE DE TICKETS EM ATRAZO
					$agora = now();
					$time_final = strtotime($agora);
					$tickets['count'][0]->OVERDUE = 0; // criando a variavel

					$ticketsInprogress = $this::MyticketsInprogressOverdue($departamento_id);

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

					$tickets['skip'] = request('skip') + $take;


					return json_encode($tickets);
			}


			if($selectedtype->active->inProgress){
				$status = 'IN_PROGRESS';
			}else if($selectedtype->active->overdue){
				$status = 'IN_PROGRESS';
			}else if($selectedtype->closed){
				$status = 'CLOSED';
			}else if($selectedtype->resolved){
				$status = 'RESOLVED';
			}else if($selectedtype->canceled){
				$status = 'CANCELED';
			}

			$sub = Ticket::select(
	            'ticket.id',
	            'ticket.status',
	            'ticket.description',
	            'ticket.created_at',
	            'ticket.updated_at',
	            'ticket.created_by',
	            'ticket.priority',
	            'ticket.type',
	            'ticket.user_agent',
	            'ticket.comments',
	            'ticket.update_status_in_progress',
	            'ua.name',
	            'ua.email',
	            'cd.name AS department',
				'cd.type as department_type',
	            'cd.id AS department_id',
	            'cucd.id as company_user_company_department_id',
	            'cds.settings',
				DB::raw('COALESCE(ua_client.id, ua_create.id) AS id_created'),
	            DB::raw('COALESCE(ua_client.name, ua_create.name) AS name_created'),
	            DB::raw('COALESCE(ua_client.email, ua_create.email) AS email_created'),
	            'ua_client.builderall_account_data AS builderall_account_data',
				'c.service_time',
	            'c.update_status_in_progress as last_update_status',
	            'c.id as chat_id',
	            'cucd.company_user_id',
	            'c.type as chat_type',
	            DB::raw('IF((SELECT company_user_company_department_id FROM chat_history WHERE chat_id = c.id ORDER BY id DESC LIMIT 1) IS NULL, 1, 0) AS answered')
	        )
	            ->join('company_department as cd', 'ticket.company_department_id', 'cd.id')
	            ->join('company_department_settings as cds', 'cd.id', 'cds.company_department_id')
	            ->join('chat as c', 'ticket.id', 'c.ticket_id')
	            ->join('user_auth as ua_create', 'ticket.created_by', 'ua_create.id')
	            ->join('company_user_company_department as cucd', 'c.comp_user_comp_depart_id_current', 'cucd.id')
	            ->join('company_user as cu', 'cucd.company_user_id', 'cu.id')
	            ->join('user_auth as ua', 'cu.user_auth_id', 'ua.id')
	            ->leftJoin('user_client_ticket as uct', 'ticket.id', 'uct.ticket_id')
	            ->leftJoin('user_client as uc', 'uct.user_client_id', 'uc.id')
	            ->leftJoin('user_auth as ua_client', 'uc.user_auth_id', 'ua_client.id')
	            ->where('ticket.company_id', Crypt::decrypt(session('companyselected')['id']))
	            ->whereNull('cd.deleted_at')
				->whereIn('cd.id', $departamento_id_array)
	            ->groupBy('cd.id', 'ticket.id');

            $sub->whereRaw("cu.user_auth_id = ".auth()->user()->id);

            if ($request->status  !== 'OVERDUE') {
                $sub->where('ticket.status', $status);
            }

          	$result = DB::table( DB::raw("({$sub->toSql()}) as sub") )
            ->mergeBindings($sub->getQuery())
            ->orderBy('answered', 'DESC')->orderBy('sub.updated_at')->paginate(request('quantRows'));

	        foreach ($result as $key) {
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
				'user' => intval(auth()->user()->id),
				'user2' => intval(auth()->user()->id),
			);

			$query2 = 	"SELECT
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
							LEFT JOIN chat AS c ON c.ticket_id = t.id
							LEFT JOIN user_ticket AS ut ON t.id = ut.ticket_id
							LEFT JOIN company_user AS cu ON ut.company_user_id = cu.id
							LEFT JOIN company_user_company_department AS cucd ON cu.id = cucd.company_user_id
							AND t.company_department_id = cucd.company_department_id
							WHERE t.company_id = :companyselected
							AND cucd.company_user_id = :company_user_id
							AND c.comp_user_comp_depart_id_current = cucd.id
                            AND t.deleted_at IS NULL
                            AND c.deleted_at IS NULL

							AND (0 = 1 OR cucd.company_department_id IN (
								SELECT cucd2.company_department_id FROM company_user_company_department cucd2
								JOIN company_user cu2 ON cucd2.company_user_id = cu2.id
								WHERE cu2.user_auth_id = :user
								AND cucd2.is_active = 1
							))

							UNION

							SELECT
								0 AS CLOSED,
								0 AS RESOLVED,
								0 AS CANCELED,
								COUNT(DISTINCT tt.id) AS OPENED,
								0 AS IN_PROGRESS
								FROM ticket AS tt
								JOIN company_user_company_department cucd ON tt.company_department_id = cucd.company_department_id
								WHERE tt.company_id = :companyselected2 -- AND cucd.company_user_id = 32
								AND tt.status = 'OPENED'
                                AND tt.deleted_at IS NULL

								AND (0 = 1 OR cucd.company_department_id IN (
									SELECT cucd2.company_department_id FROM company_user_company_department cucd2
									JOIN company_user cu2 ON cucd2.company_user_id = cu2.id
									WHERE cu2.user_auth_id = :user2
									AND cucd2.is_active = 1
								))
						) sub;";


			$tickets['success'] = true;
			$tickets['count'] = DB::select($query2, $arrayName2);

			//----------------------CÁLCULO PARA VERIFICAR A QUANTIDADE DE TICKETS EM ATRAZO
			$agora = now();
			$time_final = strtotime($agora);
			$tickets['count'][0]->OVERDUE = 0; // criando a variavel

			$ticketsInprogress = $this::MyticketsInprogressOverdue($departamento_id);

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

			$tickets['status'] = $status;
			$tickets['result'] = $result;
			$tickets['success'] = true;
		} catch (\Exception $e) {
			echo $e;
			Logger::reportException($e, [], ['fullticketcontroller', 'getMyTickets'], false);
			$tickets['success'] = false;
		}

		echo json_encode($tickets);
	}

	public function getDatabase(){
		$tickets['success'] = false;
		try{
			// search
			$type = request('type');
			$category = [];
			$description = [];

			foreach (request('search') as $key) {
				if(json_decode($key)[0] == 'category'){
					array_push($category, json_decode($key)[1]);
				}else{
					array_push($description, json_decode($key)[1]);
				}
			}

			if(count($category) == 1){
				$string = "WHERE cat.company_id = ".Crypt::decrypt(session('companyselected')['id'])." AND (cat.categoria LIKE '%".json_decode($key)[1]."%')";
			}if(count($category) > 1){
				$count = 0;
				foreach ($category as $key => $value) {
					if($count == 0){
						$string = "WHERE cat.company_id = ".Crypt::decrypt(session('companyselected')['id'])." AND (cat.categoria LIKE '%".$value."%' ";
					}else{
						$string = $string . " OR cat.categoria LIKE '%".$value."%' ";
					}
					$count++;
				}
				$string = $string.')';
			}

			if($type == 'TICKET'){
				$left = ' 	JOIN ticket t ON c.ticket_id = t.id
							LEFT JOIN chat_favorite cf ON c.id = cf.chat_id AND cf.company_user_id = '.Crypt::decrypt(session('companyselected')['company_user_id']).'
							WHERE c.deleted_at IS NULL
							GROUP BY t.id
							ORDER BY cf.company_user_id IS NOT NULL DESC
							LIMIT 50;';
			}else{
				$left = ' 	LEFT JOIN ticket t ON c.ticket_id = t.id
							LEFT JOIN chat_favorite cf ON c.id = cf.chat_id AND cf.company_user_id = '.Crypt::decrypt(session('companyselected')['company_user_id']).'
							WHERE c.deleted_at IS NULL 
							AND c.ticket_id IS NULL 
							GROUP BY c.id
							ORDER BY cf.company_user_id IS NOT NULL DESC
							LIMIT 50;';
			}

			$query2 = "WITH recursive categories_final
			AS
			(
				SELECT company_id, id, categoria, categoria_pai_id FROM (
					WITH recursive categories
					AS
					(
						SELECT cat.company_id, cat.id, JSON_VALUE(cat.description, '$[0].description') AS categoria,
							cat.category_id as categoria_pai_id, CAST(NULL AS VARCHAR(500)) AS categoria_pai-- , CAST(cat.id AS CHAR) as path
						FROM category cat 
						WHERE cat.category_id IS NULL
						UNION ALL
						SELECT cat.company_id, cat.id, JSON_VALUE(cat.description, '$[0].description') AS categoria,
							cat.category_id as categoria_pai_id, pai.categoria AS categoria_pai
						FROM category cat 
						JOIN categories pai ON cat.category_id = pai.id
					)
					SELECT * FROM categories cat 
					".$string."
				) sub
				UNION ALL
				SELECT cat.company_id, cat.id, JSON_VALUE(cat.description, '$[0].description') AS categoria,
					cat.category_id as categoria_pai_id
				FROM category cat 
				JOIN categories_final pai ON cat.category_id = pai.id
			)
			SELECT c.ticket_id, t.status, t.description, t.created_at, 
			c.type as chat_type, c.id as chat_id, 'category.description', cf.company_user_id 
			FROM categories_final cat
			JOIN chat_category cc ON cat.id = cc.category_id
			JOIN chat c ON cc.chat_id = c.id 
			JOIN category ON cc.category_id = category.id 
			".$left;

			$tickets['result'] = DB::select($query2);
			
			if($type == 'CHAT'){
				foreach ($tickets['result'] as $key) {
					$key->content = ChatHistory::whereRaw('id = (
						SELECT MAX(id) FROM chat_history WHERE chat_id = '.$key->chat_id.'
					) AND type = "TEXT" ')->first();
					if($key->content == null){
						$key->content = ChatHistory::whereRaw('id = (
							SELECT MAX(id) FROM chat_history WHERE chat_id = '.$key->chat_id.'
						)')->first()->content;
					}
				}
			}

			foreach ($tickets['result'] as $key) {
				if($key->company_user_id != null){
					$key->company_user_id = Crypt::encrypt($key->company_user_id);
				}
			}

			$tickets['success'] = true;
			
		} catch (\Exception $e) {
			echo $e;
			Logger::reportException($e, [], ['FullTicketController', 'getDatabase'], false);
			$tickets['success'] = false;
		}
		return $tickets;
	}

	public function setFavorite(){

		$favorite['success'] = false;
		try{
			$chat_id = request('chat_id');

			$result = DB::table('chat_favorite')
				->where('company_user_id', Crypt::decrypt(session('companyselected')['company_user_id']))
				->where('chat_id', $chat_id)
				->exists();

			if($result == true){
				DB::table('chat_favorite')
				->where('company_user_id', Crypt::decrypt(session('companyselected')['company_user_id']))
				->where('chat_id', $chat_id)
				->delete();

				$favorite['value'] = null;
			}else{
				DB::table('chat_favorite')->insertGetId([
					'company_user_id' => Crypt::decrypt(session('companyselected')['company_user_id']),
					'chat_id' => $chat_id,
				]);
				$favorite['value'] = session('companyselected')['company_user_id'];
			}
			
			$favorite['success'] = true;
		} catch (\Exception $e) {
			echo $e;
			Logger::reportException($e, [], ['FullTicketController', 'setFavorite'], false);
			$favorite['success'] = false;
		}

		return $favorite;
	}

	public function getChatHistory(){
		$ticketChat['success'] = false;

			$id = json_decode(request('itemselected'))->ticket_id;
			$chat_id = json_decode(request('itemselected'))->chat_id;
			$chat_type = json_decode(request('itemselected'))->chat_type;
			$created_at = json_decode(request('itemselected'))->created_at;
			$type = request('type');

		try{

			if($type == 'TICKET'){
				$arrayName = array(
					'id_ticket' => $id,
				);
	
				$arrayName2 = array(
					'id_chat' => $chat_id,
				);
				
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
								AND ch.hidden_for_client = 0
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
				}
	
				if(count($ticketChat['result']) > 0){
					$ticketChat['isChat'] = ($ticketChat['result'][0]->chat_type ==  'CHANGED_TO_TICKET');
				} else if(count($ticketChat['result2']) > 0){
					$ticketChat['isChat'] = ($ticketChat['result2'][0]->chat_type ==  'CHANGED_TO_TICKET');
				} else {
					$ticketChat['isChat'] = false;
				}
	
			}else{
				$arrayName2 = array(
					'id_chat' => $chat_id,
				);
				
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
				INNER JOIN user_auth AS creator ON chat_history.created_by = creator.id
				LEFT JOIN company_user_company_department ON chat_history.company_user_company_department_id = company_user_company_department.id
				LEFT JOIN company_user ON company_user_company_department.company_user_id = company_user.id
				LEFT JOIN user_auth AS ua_agent ON company_user.user_auth_id = ua_agent.id
				WHERE (chat_history.chat_id = :id_chat)
				AND chat.deleted_at IS NULL
				AND chat_history.hidden_for_client = 0
				
				GROUP BY chat_history.id
				ORDER BY chat_history.id ASC;";

				//ANTES DE VIRAR TICKET
				$ticketChat['result'] = DB::select($query, $arrayName2);
				$ticketChat['result2'] = [];

				foreach ($ticketChat['result'] as $key) {
					$key->id = Crypt::encrypt($key->id);
					$key->chat_id = Crypt::encrypt($key->chat_id);
					$key->client_id = Crypt::encrypt($key->client_id);
					$key->user_id = Crypt::encrypt($key->user_id);
					if ($key->company_user_company_department_id) {
						$key->company_user_company_department_id = Crypt::encrypt($key->company_user_company_department_id);
					}
				}

				$ticketChat['quests'] = DB::table('ticket_chat_answer as tt')
				->join('company_depart_settings_question as cc', 'tt.company_depart_settings_question_id', 'cc.id')
				->select('tt.id', 'question', 'answer', 'tt.created_at')
				->where('tt.chat_id', $chat_id)
				->get();


			}
			
			$ticketChat['success'] = true;

		} catch (\Exception $e) {
			echo $e;
			Logger::reportException($e, [], ['fullticketcontroller', 'getChatHistory'], false);
			$ticketChat['success'] = false;
		}

		return $ticketChat;
	}
	public function getTicketToTriggerAdmin($ticket_id, $company_id){
		try {

			//REAL TIME PARA ADICIONAR NA FILA APOS CADASTRO DE FUNCIONARIO
			$arrayName = array(
				'companyselected' => $company_id, //intval(Crypt::decrypt(session('companyselected')['id'])),
				'id' => $ticket_id
			);

			$query =
				'SELECT t.id, t.status, t.description, t.created_at, t.updated_at, c.created_by as chat_created_by,
				t.created_by, t.priority, t.type, t.user_agent, t.comments, t.update_status_in_progress, cd.type as department_type,
				ua.name, ua.email, cd.name AS department, cd.id AS department_id, cucd.id as company_user_company_department_id, cds.settings,
				COALESCE(ua_client.id, ua_create.id)  AS id_created,
				COALESCE(ua_client.name, ua_create.name) AS name_created,
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
			Logger::reportException($e, [], ['ticket-controller', 'getTicketToTriggerAdmin'], false);
			$tickets['success'] = false;
		}

		return $tickets;
	}


	public function model(){
	}

    public function getTicket(Request $request) {

        $ticket['success'] = false;

        try {

            $id = $request->id;

            $rt1 = new SendRealtime($id, 'tabs');
            $result = $rt1->getTicket();

            $ticket['ticket'] = $result;
            $ticket['success'] = true;

        } catch (\Exception $e) {
            Logger::reportException($e, [], ['full-ticket-controller', 'getTicket'], false);
			$ticket['success'] = false;
        }

        return $ticket;
    }
}
