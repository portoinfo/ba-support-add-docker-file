<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Tools\Crypt;
use App\Models\User_group;
use App\Tools\Builderall\Logger;
use Illuminate\Support\Str;
use Auth;

class AgentsController extends Controller
{
	public function index(Request $request){
		if($request->isMethod('post')){
			$setting['success'] = false;

			try {

				$Email_isValid = DB::table('user_auth')
				->select('user_auth.id', 'user_auth.email')
				->where('email', request('email'))
				->get();

				if ($Email_isValid == '[]') {
					$result = DB::table('user_auth')
					->select('user_auth.id', 'user_auth.email')
					->join('company_user', 'user_auth.id', 'company_user.user_auth_id')
					->where('company_user.company_id', Crypt::decrypt(request('csid')))
					->where('email', request('email'))
					->get();

					//verificar se o email já está em alguma empresa
					if ($result == '[]') {

						$id = DB::table('user_auth')->insertGetId([
							'subsidiary_id' => auth()->user()->subsidiary_id,
							'language' => auth()->user()->language,
							'name' => request('name'),
							'email' => request('email'),
							'hash_code' => Crypt::encrypt(request('email')),  
							'password' => bcrypt(request('password')),
							'can_create_company' => 0,
							'created_by' => auth()->user()->id,
						]);

						$company_user_id = DB::table('company_user')->insertGetId([
							'company_id' => Crypt::decrypt(session('companyselected')['id']),
							'user_auth_id' => $id,
							'is_active' => 1,
							'is_admin' => 0,
							'created_by' => auth()->user()->id,
						]);

						$setting['id'] = Crypt::encrypt($id);
						$setting['company_user_id'] = Crypt::encrypt($company_user_id);
						$setting['success'] = true;
					}else{
						$setting['type'] = 'email';
						$setting['success'] = false;
					}
				}else{
					//dd($Email_isValid[0]->id);

					$result = DB::table('user_auth')
					->join('company_user', 'user_auth.id', 'company_user.user_auth_id')
					->select('user_auth.id', 'user_auth.email', 'company_user.id as company_user_id', 'company_user.deleted_at')
					->where('user_auth.id', $Email_isValid[0]->id)
					->where('company_id', Crypt::decrypt(session('companyselected')['id']))
					->get();

					if(count($result) == 1){
						$setting['id'] = Crypt::encrypt($result[0]->id);
						$setting['company_user_id'] = Crypt::encrypt($result[0]->company_user_id);
						$setting['success'] = false;
						$setting['value'] = 'is_exist';
						
						if($result[0]->deleted_at != null){
							$result = DB::table('company_user')
							->join('user_auth', 'user_auth.id', 'company_user.user_auth_id')
							->where('user_auth.id', $Email_isValid[0]->id)
							->where('company_id', Crypt::decrypt(session('companyselected')['id']))
							->update([
								'company_user.deleted_at' => null,
								'company_user.updated_by' => auth()->user()->id,
							]);
						}

						$setting['success'] = true;
	
						//$var = new MailAdmin(Crypt::decrypt(session('companyselected')['id']), auth()->user()->id, Crypt::decrypt(request('id_department')));
						//$var->ticketReplied();

						return $setting;
					}

					$company_user_id = DB::table('company_user')->insertGetId([
						'company_id' => Crypt::decrypt(session('companyselected')['id']),
						'user_auth_id' => $Email_isValid[0]->id,
						'is_active' => 1,
						'is_admin' => 0,
						'created_by' => auth()->user()->id,
					]);

					$setting['id'] = Crypt::encrypt($Email_isValid[0]->id);
					$setting['company_user_id'] = Crypt::encrypt($company_user_id);
					$setting['success'] = true;
				}

			} catch (\Exception $e) {
				echo $e;
				Logger::reportException($e, [], ['agents-controller', 'index'], false);
				$setting['success'] = false;
			}

			echo json_encode($setting);
		}else{
			return view('functions.admin.agents.agents');
		}
	}

	public function showAgentInfoDashboard($id){

		try {

			$agent = DB::table('user_auth')
			->leftjoin('company_user', 'company_user.user_auth_id', 'user_auth.id')
			->select('user_auth.id', 'user_auth.name as attendants', 'company_user.id as company_user_id', 'company_user.is_active', 'user_auth.email', 'company_user.last_login')
			->where('user_auth.id', Crypt::decrypt($id))
			// ->where('company_user.created_by', auth()->user()->id)
			->where('company_user.company_id', Crypt::decrypt(session('companyselected')['id']))
			->where('company_user.deleted_at', null)
			->where('company_user.is_admin', 0)
			->get();

			if (!empty($agent) && isset($agent[0]))
			{
				$agent = $agent[0];
				$agent->id = Crypt::encrypt($agent->id);
				$agent->company_user_id = Crypt::encrypt($agent->company_user_id);
			}


		} catch (\Exception $e) {
			Logger::reportException($e, [], ['agents-controller', 'showAgentInfoDashboard'], false);
			$agent = [];
		}

		return view('functions.admin.agents.agent-info-dashboard', [
			'agent' => json_encode($agent)
		]);
	}

	public function showAgents(){
		$agentsUsers['success'] = false;

		try {
			$arrayName = array(
				'companyselected' => intval(Crypt::decrypt(session('companyselected')['id'])),
			);

			$query = "SELECT user_auth.id, user_auth.name as attendants, company_user.id as company_user_id,
			company_user.is_active, company_user.is_admin, user_auth.email, company_user.last_login,
			-- c.*, t.*
			SUM(if(c.id IS NOT NULL AND c.ticket_id IS NULL, 1, 0)) AS Open_chats,
			SUM(if(c.id IS NOT NULL AND c.ticket_id IS NOT NULL, 1, 0)) AS Open_tickets
			FROM user_auth
			INNER JOIN company_user ON user_auth.id = company_user.user_auth_id
			LEFT JOIN company_user_company_department cucd ON company_user.id = cucd.company_user_id
			LEFT JOIN chat AS c ON cucd.id = c.comp_user_comp_depart_id_current AND c.deleted_at IS NULL AND c.`status` = 'IN_PROGRESS'
			LEFT JOIN ticket t ON c.ticket_id = t.id AND t.deleted_at IS NULL AND t.`status` = 'IN_PROGRESS'
			WHERE company_user.company_id = :companyselected
			AND company_user.deleted_at IS NULL
			AND t.deleted_at IS NULL
			GROUP BY user_auth.id;";

			$agentsUsers['result'] = DB::select($query, $arrayName);

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

	public function deleteAgents(){
		$agents['success'] = false;

 		try {

 			if(request('type') == 'delete'){
 				$agents['result'] = DB::table('company_user')
				->where('id', Crypt::decrypt(request('company_user_id')))
				->where('company_id', Crypt::decrypt(request('csid')))
				->update([
					'deleted_at' => \Carbon\Carbon::now()->toDateTimeString(),
					'deleted_by' => auth()->user()->id,
					'updated_by' => auth()->user()->id,
				]);
 			}else if(request('type') == 'disable'){
 				$agents['result'] = DB::table('company_user')
				->where('id', Crypt::decrypt(request('company_user_id')))
				->where('company_id', Crypt::decrypt(request('csid')))
				->update([
					'updated_by' => auth()->user()->id,
					'is_active' => 0,
				]);
 			}else if(request('type') == 'restore'){
 				$agents['result'] = DB::table('company_user')
				->where('id', Crypt::decrypt(request('company_user_id')))
				->where('company_id', Crypt::decrypt(request('csid')))
				->update([
					'updated_by' => auth()->user()->id,
					'is_active' => 1,
				]);
 			}

			$agents['success'] = true;

		} catch (\Exception $e) {
			echo $e;
			Logger::reportException($e, [], ['agents-controller', 'delete'], false);
			$agents['success'] = false;
		}

		echo json_encode($agents);
	}

	public function updateAgents(){
 		$contact['success'] = false;

 		try {
 			if(request('type') == 'name'){
				$contact['result'] = DB::table('user_auth')
				->where('id', Crypt::decrypt(request('id')))
				->update([
					'name' => request('name'),
					'updated_by' => auth()->user()->id,
				]);
 			}

 			if(request('type') == 'password'){
				$contact['result'] = DB::table('user_auth')
				->where('id', Crypt::decrypt(request('id')))
				->update([
					'name' => request('name'),
					'password' => bcrypt(request('password')),
					'updated_by' => auth()->user()->id,
				]);
 			}

			$selectedGroup = request('selectedGroup');
			$originsGroup = request('originsGroup');

			if(request('originsDepartment') == '' || $originsGroup == ''){
				//dd('vazio: selectedGroup');
			}else{

				//ADD GRUPOS CASO NÃO TENHA

				$acrescentarGroup = array_values(array_diff($selectedGroup, $originsGroup));
				
				for ($i=0; $i < count($acrescentarGroup); $i++) {

					DB::table('company_user_user_group')->insertGetId([
						'company_user_id' => Crypt::decrypt(request('company_user_id')),
						'user_group_id' => Crypt::decrypt($acrescentarGroup[$i]),
					]);
				}

				//REMOVER GRUPO DESMARCADOS
				$resultGroup = array_values(array_diff($originsGroup, $selectedGroup));
				for ($i=0; $i < count($resultGroup); $i++) {
					DB::table('company_user_user_group')
					->where('company_user_id', Crypt::decrypt(request('company_user_id')))
					->where('user_group_id', Crypt::decrypt($resultGroup[$i]))
					->delete();
				}

				$selectedDepartment = request('selectedDepartment');
				$originsDepartment = request('originsDepartment');
				//ADD DEPARTAMENTOS CASO NÃO TENHA
				$acrescentarDepart = array_values(array_diff($selectedDepartment, $originsDepartment));
				for ($i=0; $i < count($selectedDepartment); $i++) {

					$get =  DB::table('company_user_company_department')
					->where('company_user_id', Crypt::decrypt(request('company_user_id')))
					->where('company_department_id', Crypt::decrypt($selectedDepartment[$i]))
					->count();

					if($get == 0){
						DB::table('company_user_company_department')->insertGetId([
							'company_user_id' => Crypt::decrypt(request('company_user_id')),
							'company_department_id' => Crypt::decrypt($selectedDepartment[$i]),
							'is_active' => 1,
						]);
					}else{
						DB::table('company_user_company_department')
						->where('company_user_id', Crypt::decrypt(request('company_user_id')))
						->where('company_department_id', Crypt::decrypt($selectedDepartment[$i]))
						->update([
							'is_active' => 1,
						]);
					}
				}

				//REMOVER DEPARTAMENTOS DESMARCADOS
				$resultDepart = array_values(array_diff($originsDepartment, $selectedDepartment));
				for ($i=0; $i < count($resultDepart); $i++) {
					// DB::table('company_user_company_department')
					// ->where('company_user_id', Crypt::decrypt(request('company_user_id')))
					// ->where('company_department_id', Crypt::decrypt($resultDepart[$i]))
					// ->delete();
					DB::table('company_user_company_department')
					->where('company_user_id', Crypt::decrypt(request('company_user_id')))
					->where('company_department_id', Crypt::decrypt($resultDepart[$i]))
					->update([
						'is_active' => 0,
					]);
				}
			}

			DB::table('company_user')
			->where('id', Crypt::decrypt(request('company_user_id')))
			->update([
				'opening_hours' => json_encode(request('hoursPerDay')),
				'is_working' => request('workingStatus'),
				'time_zone' => request('timezoneSelected'),
			]);

			$contact['success'] = true;

		} catch (\Exception $e) {
			echo $e;
			Logger::reportException($e, [], ['agents-controller', 'update'], false);
			$contact['success'] = false;
		}

		echo json_encode($contact);
	}

	public function updateAgentsLanguage(){
 		$angent['success'] = false;

 		try {

			$angent['result'] = DB::table('user_auth')
				->where('id', request('id'))
				->update([
					'language' => request('language'),
					'updated_by' => auth()->user()->id,
				]);

			$angent['success'] = true;

		} catch (\Exception $e) {
			echo $e;
			Logger::reportException($e, [], ['agents-controller', 'update-language'], false);
			$angent['success'] = false;
		}

		echo json_encode($angent);
	}

	public function getGroupAgents(){
		$group['success'] = false;

		try {

			$arrayName = array(
				'agents' => intval(Crypt::decrypt(json_decode(request('item'))->id)),
				'company' => intval(Crypt::decrypt(session('companyselected')['id']))
			);

			$query = 'SELECT * FROM (
						SELECT * FROM (
							SELECT ug.id AS value, ug.name AS text, u.name userName
							FROM user_group ug
							LEFT JOIN company_user_user_group cuug ON ug.id = cuug.user_group_id
							LEFT JOIN company_user cu ON cuug.company_user_id = cu.id and cu.user_auth_id = :agents
							LEFT JOIN user_auth u ON cu.user_auth_id = u.id
							WHERE ug.company_id = :company
							AND ug.is_active = 1
							AND ug.deleted_at IS NULL
							ORDER BY ug.id, u.id DESC
							LIMIT 9999999
						) sub
					) sub
					GROUP BY sub.value';

			$group['result'] = DB::select($query, $arrayName);

			$select = array();

			foreach ($group['result'] as $key) {
				$key->value = Crypt::encrypt($key->value);

				if($key->userName != null){
					array_push($select, $key->value);
				}
			}


			$group['select'] = $select;
			$group['success'] = true;

		} catch (\Exception $e) {
			echo $e;
			Logger::reportException($e, [], ['agents-controller', 'get-group'], false);
			$group['success'] = false;
		}

		return json_encode($group);
	}

	public function getDepartmentAgents($id){
		$group['success'] = false;

		try {

			$arrayName = array(
				'agents' => intval(Crypt::decrypt($id)),
				'company' => intval(Crypt::decrypt(session('companyselected')['id']))
			);

			$query = 'SELECT * FROM (
							SELECT * FROM (
								SELECT cd.id as value, cd.name as text, u.name userName, cuud.is_active
								FROM company_department cd
								LEFT JOIN company_user_company_department cuud ON cd.id = cuud.company_department_id
								LEFT JOIN company_user cu ON cuud.company_user_id = cu.id and cu.user_auth_id = :agents
								LEFT JOIN user_auth u ON cu.user_auth_id = u.id
								WHERE cd.company_id = :company
								and cd.is_active = 1
								and cd.deleted_at IS NULL
								ORDER BY cd.id, u.id DESC
								LIMIT 9999999
							) sub
						) sub
						GROUP BY sub.value';


			$group['result'] = DB::select($query, $arrayName);

			$select = array();

			foreach ($group['result'] as $key) {
				$key->value = Crypt::encrypt($key->value);

				if($key->userName != null && $key->is_active == 1){
					array_push($select, $key->value);
				}
			}

			$group['select'] = $select;
			$group['success'] = true;

		} catch (\Exception $e) {
			echo $e;
			Logger::reportException($e, [], ['agents-controller', 'get-department'], false);
			$group['success'] = false;
		}

		return json_encode($group);
	}

	public function registerNewAgent() {
		return view('functions.admin.agents.register-agent', []);
	}

	public function listAgents() {
		return view('functions.admin.agents.list-agents', []);
	}

	public function getDepartmentsAgentsDashboard(Request $request){
		try {
			$sql = "
				SELECT id AS value, name AS text, (deleted_at IS NOT NULL) AS deleted
				FROM company_department
				WHERE company_id = ?
				ORDER BY 3, 2;";

			$res = DB::select($sql, [
				Crypt::decrypt(request('company_id'))
			]);

			if (!empty($res)) {
				foreach($res as $key => $value) {
					$value->value = Crypt::encrypt($value->value);
				}
			}

			$res['success'] = true;

            return json_encode($res);
		} catch (\Exception $e) {
            echo $e;
            Logger::reportException($e, [], ['agents-controller', 'getDepartmentsAgentsDashboard'], false);
            $res['success'] = false;
        }
	}

	public function getAttendantsAgentsDashboard(Request $request) {
		try {
			$sql = "
				SELECT cu.user_auth_id as value, u.name as text, (cu.deleted_at IS NOT NULL) AS deleted
				FROM user_auth u
					JOIN company_user cu ON u.id = cu.user_auth_id
					JOIN company_user_company_department cucd ON cu.id = cucd.company_user_id
				WHERE cu.is_admin = 0
					AND cu.company_id = ?
			";

			$params = [
				Crypt::decrypt(request('company_id'))
			];

			if(request('department_id') !== 0 ){
				$sql .= ' AND cucd.company_department_id = ?';
				array_push($params, Crypt::decrypt(request('department_id')) );
			}

			$sql .= " GROUP BY 1 ORDER BY 3, 2";

			$res = DB::select($sql, $params);

			if (!empty($res)) {
				foreach($res as $key => $value) {
					$value->value = Crypt::encrypt($value->value);
				}
			}

			$res['success'] = true;

            return json_encode($res);
		} catch (\Exception $e) {
            echo $e;
            Logger::reportException($e, [], ['agents-controller', 'getAttendantsAgentsDashboard'], false);
            $res['success'] = false;
        }
	}

	public function getTotalsAndPercentagesAgentsDashboard(Request $request) {
		try {

			$sql = "call pro_dashboard_attendant_totals_and_percentages(?, ?, ?, ?)";

			$res = DB::select($sql, [
				Crypt::decrypt(request('company_id')),
				request('department_id') === 0 ? 0 : Crypt::decrypt(request('department_id')),
				request('attendant_id') === 0 ? 0 : Crypt::decrypt(request('attendant_id')),
				0
			]);

			if(empty($res)) {
				$res[0] = [
					'count' => 0,
					'perc_em_atendimento' => 0,
					'perc_fechados' => 0,
					'type' => "Chat"
				];
				$res[1] = [
					'count' => 0,
					'perc_em_atendimento' => 0,
					'perc_fechados' => 0,
					'type' => "Ticket"
				];
			}

			$res['success'] = true;

            return json_encode($res);
		} catch (\Exception $e) {
            echo $e;
            Logger::reportException($e, [], ['agents-controller', 'getTotalsAndPercentagesAgentsDashboard'], false);
            $res['success'] = false;
        }
	}

	public function getGeneralAvgForAttendancesAgentsDashboard(Request $request) {
		try {

			$sql = "call pro_dashboard_attendant_general_average_for_attendances(?, ?, ?, ?)";

			$res = DB::select($sql, [
				Crypt::decrypt(request('company_id')),
				request('department_id') === 0 ? 0 : Crypt::decrypt(request('department_id')),
				request('attendant_id') === 0 ? 0 : Crypt::decrypt(request('attendant_id')),
				0
			]);

			if(empty($res)){
				$res[0] = [
					'media_stars_atendent' => "0",
					'media_stars_service' => "0"
				];
			}

			$res['success'] = true;

            return json_encode($res);
		} catch (\Exception $e) {
            echo $e;
            Logger::reportException($e, [], ['agents-controller', 'getGeneralAvgForAttendancesAgentsDashboard'], false);
            $res['success'] = false;
        }
	}

	public function getBarChartAgentsDashboard(Request $request){
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
            echo $e;
            Logger::reportException($e, [], ['agents-controller', 'getBarChartAgentsDashboard'], false);
            $res['success'] = false;
        }

	}

	public function getLineChartAgentsDashboard(Request $request){
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
            Logger::reportException($e, [], ['agents-controller', 'getLineChartAgentsDashboard'], false);
            $res['success'] = false;
        }
	}

	public function getTicketTimeCardsAgentsDashboard(Request $request) {
		try {
			$sql = "CALL pro_dashboard_attendant_tickets_average_time_queue_and_service(?, ?, ?, ?, ?);";

			$res = DB::select($sql, [
				Crypt::decrypt(request('company_id')),
				request('department_id') === 0 ? 0 : intval( Crypt::decrypt(request('department_id')) ),
				request('attendant_id') === 0 ? 0 : Crypt::decrypt(request('attendant_id')),
				strtoupper(request('period')),
				0
			]);

			if(!empty($res) && isset($res[0]) ) {
				foreach($res[0] as $key => &$val){
					$val = $val != null ? intval($val) : $val;
				}
			} else {
				$res[0] = [
					'avg_queue_time' => 0,
					'avg_service_time' => 0
				];
			}

			$res['success'] = true;

            return json_encode($res);
        } catch (\Exception $e) {

            Logger::reportException($e, [], ['agents-controller', 'getTicketTimeCardsAgentsDashboard'], false);
            $res['success'] = false;
        }
	}

	public function getChatTimeCardsAgentsDashboard(Request $request){
		try {
			$sql = "CALL pro_dashboard_attendant_chats_average_time_queue_and_service(?, ?, ?, ?, ?);";

			$res = DB::select($sql, [
				Crypt::decrypt(request('company_id')),
				request('department_id') === 0 ? 0 : intval( Crypt::decrypt(request('department_id')) ),
				request('attendant_id') === 0 ? 0 : Crypt::decrypt(request('attendant_id')),
				strtoupper(request('period')),
				0
			]);

			if(!empty($res) && isset($res[0]) ) {
				foreach($res[0] as $key => &$val){
					$val = $val != null ? intval($val) : $val;
				}
			} else {
				$res[0] = [
					'avg_queue_time' => 0,
					'avg_service_time' => 0
				];
			}

			$res['success'] = true;

            return json_encode($res);
        } catch (\Exception $e) {

            Logger::reportException($e, [], ['agents-controller', 'getChatTimeCardsAgentsDashboard'], false);
            $res['success'] = false;
        }
	}


	public function getDepartmentsAgentInfoDashboard(Request $request) {
		try {
			$sql = "
				SELECT
					cd.id as value,	cd.name as text, (cd.deleted_at IS NOT NULL) AS deleted
				FROM
					user_auth as u
					INNER JOIN company_user as cu
						ON u.id = cu.user_auth_id
					INNER JOIN company_user_company_department cucd
						ON cu.id = cucd.company_user_id
					INNER JOIN company_department as cd
						ON cd.id = cucd.company_department_id
				WHERE cu.is_admin = 0
					AND cu.company_id = :company_id
					AND cu.user_auth_id = :attendant_id
					AND cd.company_id = :company_id2
				GROUP BY 1 ORDER BY 3, 2;
			";

			$res = DB::select($sql, [
				'company_id' => Crypt::decrypt(request('company_id')),
				'attendant_id' => Crypt::decrypt(request('attendant_id')),
				'company_id2' => Crypt::decrypt(request('company_id')),
			]);


			if (!empty($res)) {
				foreach($res as $key => $value) {
					$value->value = Crypt::encrypt($value->value);
				}
			}

			$res['success'] = true;

            return json_encode($res);
		} catch (\Exception $e) {
            echo $e;
            Logger::reportException($e, [], ['agents-controller', 'getDepartmentsAgentInfoDashboard'], false);
            $res['success'] = false;
        }
	}

	public function getQuantitativeAndTimeCardsAgentInfoDashboard(Request $request) {
		try {
			$sql = "CALL pro_dashboard_details_numbers_attendant(?, ?, ?, ?, ?, ?);";

			$res = DB::select($sql, [
				Crypt::decrypt(request('company_id')),
				Crypt::decrypt(request('attendant_id')),
				request('department_id') === 0 ? 0 : Crypt::decrypt(request('department_id')),
				request('initial_date') == null ? 0 : request('initial_date'),
				request('final_date') == null ? 0 : request('final_date'),
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
				$res = array_merge($aux, $res);
			}
			$res = $aux;

			$res['success'] = true;

            return json_encode($res);
		} catch (\Exception $e) {
            echo $e;
            Logger::reportException($e, [], ['agents-controller', 'getQuantitativeAndTimeCardsAgentInfoDashboard'], false);
            $res['success'] = false;
        }
	}

	public function getQualitativeCardsAgentInfoDashboard(Request $request) {
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
			
			foreach ($res as $key) {
				if($key->count_1_atendent == null){
					$key->count_1_atendent = '0';
				}
				if($key->count_2_atendent == null){
					$key->count_2_atendent = '0';
				}
				if($key->count_3_atendent == null){
					$key->count_3_atendent = '0';
				}
				if($key->count_4_atendent == null){
					$key->count_4_atendent = '0';
				}
				if($key->count_5_atendent == null){
					$key->count_5_atendent = '0';
				}
				if($key->count_1_service == null){
					$key->count_1_service = '0';
				}
				if($key->count_2_service == null){
					$key->count_2_service = '0';
				}
				if($key->count_3_service == null){
					$key->count_3_service = '0';
				}
				if($key->count_4_service == null){
					$key->count_4_service = '0';
				}
				if($key->count_5_service == null){
					$key->count_5_service = '0';
				}
				if($key->media_stars_atendent == null){
					$key->media_stars_atendent = '0';
				}
				if($key->media_stars_service == null){
					$key->media_stars_service = '0';
				}
			}
			// $aux = [
			// 	[
			// 		'type' => "chat",
			// 		'approval_percentage_atendent' => 0,
			// 		'approval_percentage_service' => 0,
			// 		'count_negative_atendent' => 0,
			// 		'count_negative_service' => 0,
			// 		'count_positive_atendent' => 0,
			// 		'count_positive_service' => 0,
			// 		'media_stars_atendent' => 0,
			// 		'media_stars_service' => 0
			// 	],
			// 	[
			// 		'type' => "ticket",
			// 		'approval_percentage_atendent' => 0,
			// 		'approval_percentage_service' => 0,
			// 		'count_negative_atendent' => 0,
			// 		'count_negative_service' => 0,
			// 		'count_positive_atendent' => 0,
			// 		'count_positive_service' => 0,
			// 		'media_stars_atendent' => 0,
			// 		'media_stars_service' => 0
			// 	]
			// ];

			// if(!empty($res)) {
			// 	foreach($aux as $i => &$item) {
			// 		foreach($item as $j => &$value){
			// 			if( $j == 'type' ) {
			// 				$value = strtolower(( (array) $res[$i] )[$j]);
			// 			} else {
			// 				$value = intval(( (array) $res[$i] )[$j]);
			// 			}
			// 		}
			// 	}
			// 	$res = array_merge($aux, $res);
			// }
			// $res = $aux;

			$res['success'] = true;

            return json_encode($res);
		} catch (\Exception $e) {
            echo $e;
            Logger::reportException($e, [], ['agents-controller', 'getQualitativeCardsAgentInfoDashboard'], false);
            $res['success'] = false;
        }
	}


	public function destroy(){

	}
}
