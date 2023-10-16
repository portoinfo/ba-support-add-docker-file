<?php

namespace App\Http\Controllers;

use App\Models\Company_department;
use App\Ticket;
use App\Tools\Builderall\Logger;
use App\Tools\Crypt;
use App\Tools\exportExcel;
use App\User;
use Carbon\Carbon;
use DateTime;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AnalyzeController extends Controller
{
    public function index(){
		return view('functions.admin.analyze.analyze');
 	}

	public function getAgents(){
		$agentsUsers['success'] = false;

		try {
			// pro_get_statistics_chats_tickets_for_attendant
			$sql = "CALL pro_get_statistics_chats_tickets_for_attendant(?, ?, ?, ?, ?, ?);";

			$pInitialDate = $this->convertDate(request('selectedTime1'), request('tz'), request('pInitialDate'), request('pFinalDate'))[0];
			$pFinalDate = $this->convertDate(request('selectedTime1'), request('tz'), request('pInitialDate'), request('pFinalDate'))[1];

			
			if(request('ChatorTicket') == 'ALL'){
				$chat = DB::select($sql, [
					'CHAT',
					intval(Crypt::decrypt(session('companyselected')['id'])),
					request('selectedDepartment') == 'ALL' ? NULL : intval(Crypt::decrypt(request('selectedDepartment'))),
					$pInitialDate,
					$pFinalDate,
					0,
				]);

				$ticket = DB::select($sql, [
					'TICKET',
					intval(Crypt::decrypt(session('companyselected')['id'])),
					request('selectedDepartment') == 'ALL' ? NULL : intval(Crypt::decrypt(request('selectedDepartment'))),
					$pInitialDate,
					$pFinalDate,
					0,
				]);

				foreach ($chat as $keychat) {
					foreach ($ticket as $keyticket) {
						if($keychat->id == $keyticket->id){
							$keychat->opened = $keychat->opened + $keyticket->opened;
							$keychat->finished = $keychat->finished + $keyticket->finished;
							$keychat->canceled = $keychat->canceled + $keyticket->canceled;
							$keychat->avg_queue_time = $keychat->avg_queue_time + $keyticket->avg_queue_time;
							if($keychat->media_stars_atendent != null){
								$keychat->media_stars_atendent = $keychat->media_stars_atendent + $keyticket->media_stars_atendent;
							}

							if($keychat->media_stars_service != null){
								$keychat->media_stars_service = $keychat->media_stars_service + $keyticket->media_stars_service;
							}
						}
					}
				}

				foreach ($chat as $keychat) {
					$keychat->media_stars_atendent = $keychat->media_stars_atendent/2;
					$keychat->media_stars_service = $keychat->media_stars_service/2;
				}

				$agentsUsers['result'] = $chat;
			}else{
				$agentsUsers['result'] = DB::select($sql, [
					request('ChatorTicket'),
					intval(Crypt::decrypt(session('companyselected')['id'])),
					request('selectedDepartment') == 'ALL' ? NULL : intval(Crypt::decrypt(request('selectedDepartment'))),
					$pInitialDate,
					$pFinalDate,
					0,
				]);
				//TESTE
			}
			$att = [];

			
			foreach ($agentsUsers['result']  as $key) {
                $key->average = '';
                $key->company_user_id = Crypt::encrypt($key->company_user_id);
	
				if(request('checkedDelete') == 'true'){
					if($key->user_removed == 1){
						array_push($att, $key);
					}
				}else{
					if($key->user_removed == 0){
						array_push($att, $key);
					}
				}
				
            }

			$agentsUsers['result'] = $att;

			// dd($agentsUsers['success']);

			$agentsUsers['success'] = true;
		} catch (\Exception $e) {
			echo $e;
			Logger::reportException($e, [], ['AnalyzeController', 'getAgents'], false);
			$agentsUsers['success'] = false;
		}

		return json_encode($agentsUsers);
 	}

	public function generateFileExcel(){
		$agentsUsers['success'] = false;

		try {
			// pro_get_statistics_chats_tickets_for_attendant  -----------------------------------------------------------------------------------
			$sql = "CALL pro_get_statistics_chats_tickets_for_attendant(?, ?, ?, ?, ?, ?);";

			$pInitialDate = $this->convertDate(request('selectedTime1'), request('tz'), request('pInitialDate'), request('pFinalDate'))[0];
			$pFinalDate = $this->convertDate(request('selectedTime1'), request('tz'), request('pInitialDate'), request('pFinalDate'))[1];

			
			if(request('ChatorTicket') == 'ALL'){
				$chat = DB::select($sql, [
					'CHAT',
					intval(Crypt::decrypt(session('companyselected')['id'])),
					request('selectedDepartment') == 'ALL' ? NULL : intval(Crypt::decrypt(request('selectedDepartment'))),
					$pInitialDate,
					$pFinalDate,
					0,
				]);

				$ticket = DB::select($sql, [
					'TICKET',
					intval(Crypt::decrypt(session('companyselected')['id'])),
					request('selectedDepartment') == 'ALL' ? NULL : intval(Crypt::decrypt(request('selectedDepartment'))),
					$pInitialDate,
					$pFinalDate,
					0,
				]);

				foreach ($chat as $keychat) {
					foreach ($ticket as $keyticket) {
						if($keychat->id == $keyticket->id){
							$keychat->opened = $keychat->opened + $keyticket->opened;
							$keychat->finished = $keychat->finished + $keyticket->finished;
							$keychat->canceled = $keychat->canceled + $keyticket->canceled;
							$keychat->avg_queue_time = $keychat->avg_queue_time + $keyticket->avg_queue_time;
							if($keychat->media_stars_atendent != null){
								$keychat->media_stars_atendent = $keychat->media_stars_atendent + $keyticket->media_stars_atendent;
							}

							if($keychat->media_stars_service != null){
								$keychat->media_stars_service = $keychat->media_stars_service + $keyticket->media_stars_service;
							}
						}
					}
				}

				foreach ($chat as $keychat) {
					$keychat->media_stars_atendent = $keychat->media_stars_atendent/2;
					$keychat->media_stars_service = $keychat->media_stars_service/2;
				}

				$agentsUsers['result'] = $chat;
			}else{
				$agentsUsers['result'] = DB::select($sql, [
					request('ChatorTicket'),
					intval(Crypt::decrypt(session('companyselected')['id'])),
					request('selectedDepartment') == 'ALL' ? NULL : intval(Crypt::decrypt(request('selectedDepartment'))),
					$pInitialDate,
					$pFinalDate,
					0,
				]);
				//TESTE
			}
			$att = [];

			
			foreach ($agentsUsers['result']  as $key) {
                $key->average = '';
                $key->company_user_id = Crypt::encrypt($key->company_user_id);
	
				if(request('checkedDelete') == 'true'){
					if($key->user_removed == 1){
						array_push($att, $key);
					}
				}else{
					if($key->user_removed == 0){
						array_push($att, $key);
					}
				}
				
            }

			$agentsUsers['result'] = $att;
		
			// pro_get_attendant_response_times -----------------------------------------------------------------------------------
			$sql = "CALL pro_get_attendant_response_times(?, ?, ?, ?, ?, ?);";

			$resultt = DB::select($sql, [
				request('ChatorTicket'),
				intval(Crypt::decrypt(session('companyselected')['id'])),
				request('selectedDepartment') == 'ALL' ? NULL : intval(Crypt::decrypt(request('selectedDepartment'))),
				$pInitialDate,
				$pFinalDate,
				0,
			]);

			foreach ($agentsUsers['result'] as $key) {
				$ss = $key->avg_queue_time%60;
				$mm = floor(($key->avg_queue_time%3600)/60);
				$hh = floor(($key->avg_queue_time%86400)/3600);
				$key->avg_queue_time = $hh.':'.$mm.':'.$ss;

				foreach ($resultt as $key2) {
					if($key->id == $key2->user_auth_id){
						$s = $key2->average%60;
						$m = floor(($key2->average%3600)/60);
						$h = floor(($key2->average%86400)/3600);
						$key->average = $h.':'.$m.':'.$s;
					}
				}
			}

			// TEMPO DE SERVIÇO TRABALHADO EM SEGUNDOS -----------------------------------------------------------------------------------
			$sql = "CALL pro_get_avaliations_attendants(?, ?, ?, ?, ?, ?, ?);";

			if(request('ChatorTicket') == 'ALL'){
				$chat = DB::select($sql, [
					'CHAT',
					intval(Crypt::decrypt(session('companyselected')['id'])),
					request('selectedDepartment') == 'ALL' ? NULL : intval(Crypt::decrypt(request('selectedDepartment'))),
					intval(Crypt::decrypt(request('company_user_id'))),
					$pInitialDate,
					$pFinalDate,
					0,
				]);

				$ticket = DB::select($sql, [
					'TICKET',
					intval(Crypt::decrypt(session('companyselected')['id'])),
					request('selectedDepartment') == 'ALL' ? NULL : intval(Crypt::decrypt(request('selectedDepartment'))),
					intval(Crypt::decrypt(request('company_user_id'))),
					$pInitialDate,
					$pFinalDate,
					0,
				]);

				$result_aux = array_merge($chat, $ticket);

			}else{
				$result_aux = DB::select($sql, [
					request('ChatorTicket'),
					intval(Crypt::decrypt(session('companyselected')['id'])),
					request('selectedDepartment') == 'ALL' ? NULL : intval(Crypt::decrypt(request('selectedDepartment'))),
					intval(Crypt::decrypt(request('company_user_id'))),
					$pInitialDate,
					$pFinalDate,
					0,
				]);
			}
			
			$avaliation = $result_aux;

			$config = new exportExcel;

			if(request('ChatorTicket') == 'TICKET'){
				$alias = [
					'bs-agents',  
					'bs-opened',  
					'bs-closed',   
					'bs-average-response-time', 
					'bs-queue-time',  
					'bs-stars-atendent',  
					'bs-stars-service', 
				];
				$colunm = [
					'attendant', 
					'opened', 
					'finished', 
					'average', 
					'avg_queue_time', 
					'media_stars_atendent', 
					'media_stars_service'
				];
			}else{
				$alias = [
					'bs-agents',  
					'bs-opened',  
					'bs-closed',  
					'bs-moved-to-ticket',  
					'bs-average-response-time', 
					'bs-queue-time',  
					'bs-stars-atendent',  
					'bs-stars-service', 
				];
				$colunm = [
					'attendant', 
					'opened', 
					'finished', 
					'moved_to_ticket', 
					'average', 
					'avg_queue_time', 
					'media_stars_atendent', 
					'media_stars_service'
				];
			}

			$link = $config->generate($alias, $colunm, $agentsUsers['result'], $avaliation, request('ChatorTicket'));
			$agentsUsers['success'] = true;
		} catch (\Exception $e) {
			echo $e;
			Logger::reportException($e, [], ['AnalyzeController', 'generateFileExcel'], false);
			$agentsUsers['success'] = false;
		}
		
		return $link;

	}

	public function documentAgents(){
		$company_id = DB::table('company')
		->where('id', Crypt::decrypt(session('companyselected')['id']))
		->first();

		if (!isset($company_id)) {
			abort(404);
		} else {
			$path = '..' . DIRECTORY_SEPARATOR . 'storage' . 
            DIRECTORY_SEPARATOR . 'app' . 
            DIRECTORY_SEPARATOR . 'public' . 
            DIRECTORY_SEPARATOR . 'document' . 
			DIRECTORY_SEPARATOR .session('companyselected')['id']. 
			DIRECTORY_SEPARATOR . 'agentsInfos.xlsx';

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
			//$response->header('Content-disposition','attachment; filename="nome-do-arquivo.pdf"');

			return $response;
		}
	}


	public function getAgentsTime(){
		$agentsUsers['success'] = false;

		try {

			$pInitialDate = $this->convertDate(request('selectedTime1'), request('tz'), request('pInitialDate'), request('pFinalDate'))[0];
			$pFinalDate = $this->convertDate(request('selectedTime1'), request('tz'), request('pInitialDate'), request('pFinalDate'))[1];

			// pro_get_attendant_response_times
			$sql = "CALL pro_get_attendant_response_times(?, ?, ?, ?, ?, ?);";

			$agentsUsers['result'] = DB::select($sql, [
				request('ChatorTicket'),
				intval(Crypt::decrypt(session('companyselected')['id'])),
				request('selectedDepartment') == 'ALL' ? NULL : intval(Crypt::decrypt(request('selectedDepartment'))),
				$pInitialDate,
				$pFinalDate,
				0,
			]);
			
			$agentsUsers['success'] = true;
		} catch (\Exception $e) {
			echo $e;
			Logger::reportException($e, [], ['AnalyzeController', 'getAgentsTime'], false);
			$agentsUsers['success'] = false;
		}

		return json_encode($agentsUsers);
	}

	public function getInfos(){

		$infos['success'] = false;
		try {

			$pInitialDate = $this->convertDate(request('selectedTime1'), request('tz'), request('pInitialDate'), request('pFinalDate'))[0];
			$pFinalDate = $this->convertDate(request('selectedTime1'), request('tz'), request('pInitialDate'), request('pFinalDate'))[1];


			$sql = "CALL pro_get_chats_tickets_waiting_responses_customer_attendant(?, ?, ?, ?, ? ,? ,?);";
			$infos['result_answer'] = DB::select($sql, [
				request('ChatorTicket'),
				intval(Crypt::decrypt(session('companyselected')['id'])),
				request('selectedDepartment') == 'ALL' ? NULL : intval(Crypt::decrypt(request('selectedDepartment'))),
				intval(Crypt::decrypt(request('company_user_id'))),
				$pInitialDate,
				$pFinalDate,
				0,
			]);

			// AGUARDANDO INTERAÇÃO DE UM DOS LADOS.
			$sql = "CALL pro_get_chats_tickets_without_attendant_customer_interaction(?, ?, ?, ?, ? ,? ,?);";

			$infos['result_interaction'] = DB::select($sql, [
				request('ChatorTicket'),
				intval(Crypt::decrypt(session('companyselected')['id'])),
				request('selectedDepartment') == 'ALL' ? NULL : intval(Crypt::decrypt(request('selectedDepartment'))),
				intval(Crypt::decrypt(request('company_user_id'))),
				$pInitialDate,
				$pFinalDate,
				0,
			]);

			// TEMPO DE SERVIÇO TRABALHADO EM SEGUNDOS
			$sql = "CALL pro_times_of_attendants_in_service(?, ?, ?, ?, ?, ?, ?);";

			$result_aux = DB::select($sql, [
				request('ChatorTicket'),
				intval(Crypt::decrypt(session('companyselected')['id'])),
				request('selectedDepartment') == 'ALL' ? NULL : intval(Crypt::decrypt(request('selectedDepartment'))),
				intval(Crypt::decrypt(request('company_user_id'))),
				$pInitialDate,
				$pFinalDate,
				0,
			]);

			$anterior_final = null;
			$inicial = null;
			$final = null;
			$tempo_trabalhando = 0;
			for ($f=0; $f < count($result_aux); $f++) { 

				if($anterior_final == null){
					$anterior_final = $result_aux[$f]->final_date;
					$inicial = $result_aux[$f]->initial_date;
					$final = $result_aux[$f]->final_date;
					$tempo_trabalhando += $result_aux[$f]->tempo_segundos;
				}else{
					if($final <= $result_aux[$f]->initial_date){
						$tempo_trabalhando += $result_aux[$f]->tempo_segundos;

						$anterior_final = $result_aux[$f]->final_date;
						$inicial = $result_aux[$f]->initial_date;
						$final = $result_aux[$f]->final_date;

					}else if($final >= $result_aux[$f]->initial_date && $final <= $result_aux[$f]->final_date){
						$tempo_trabalhando += (strtotime($result_aux[$f]->final_date) - strtotime($anterior_final));

						$anterior_final = $result_aux[$f]->final_date;
						$inicial = $result_aux[$f]->initial_date;
						$final = $result_aux[$f]->final_date;

					}else{
						// echo 'não faz nd';
					}
				}
			}

			$infos['result_time'] = $tempo_trabalhando;
			$infos['result_count'] = count($result_aux);

			$infos['success'] = true;
			$infos['type'] = request('ChatorTicket');
		} catch (\Exception $e) {
			echo $e;
			Logger::reportException($e, [], ['AnalyzeController', 'getInfos'], false);
			$infos['success'] = false;
		}

		return json_encode($infos);
	}

	public function getAvaliations(){

		$avaliation['success'] = false;
		// dd(request('selectedTime2'));

        try {

			$pInitialDate = $this->convertDate(request('selectedTime1'), request('tz'), request('pInitialDate'), request('pFinalDate'))[0];
			$pFinalDate = $this->convertDate(request('selectedTime1'), request('tz'), request('pInitialDate'), request('pFinalDate'))[1];

			// TEMPO DE SERVIÇO TRABALHADO EM SEGUNDOS
			$sql = "CALL pro_get_avaliations_attendants(?, ?, ?, ?, ?, ?, ?);";

			$result_aux = DB::select($sql, [
				request('ChatorTicket'),
				intval(Crypt::decrypt(session('companyselected')['id'])),
				request('selectedDepartment') == 'ALL' ? NULL : intval(Crypt::decrypt(request('selectedDepartment'))),
				intval(Crypt::decrypt(request('company_user_id'))),
				$pInitialDate,
				$pFinalDate,
				0,
			]);

			$avaliation['result'] = $result_aux;

            $avaliation['success'] = true;
        } catch (\Exception $e) {
            echo $e;
            Logger::reportException($e, [], ['AnalyzeController', 'getAvaliations'], false);
            $avaliation['success'] = false;
        }

        return json_encode($avaliation);
	}

	public function getGraphic(){
		$graphic['success'] = false;

        try {
			// AGUARDANDO INTERAÇÃO DE UM DOS LADOS.
			$sql = "CALL pro_dashboard_attendant_tickets_chats_opened_x_closed(?, ?, ?, ?, ?);";

			$graphic['result'] = DB::select($sql, [
				intval(Crypt::decrypt(session('companyselected')['id'])),
				request('selectedDepartment') == 'ALL' ? NULL : intval(Crypt::decrypt(request('selectedDepartment'))),
				// intval(Crypt::decrypt(request('company_user_id'))),
				request('id'),
				request('selectedTime2'),
				0
			]);

			// dd($graphic['result']);
			$graphic['success'] = true;
		} catch (\Exception $e) {
			echo $e;
			Logger::reportException($e, [], ['AnalyzeController', 'getGraphic'], false);
			$graphic['success'] = false;
		}
		return json_encode($graphic);
	}

	public function getStatusUser(){

		// $result = DB::table('user_auth_status')
        // ->where('user_auth_id', request('id'))
        // ->select('status','created_at')
		// ->limit(25)
        // ->latest()
        // ->get();

		$result = DB::table('log_company_user_working as log')
		->join('company_user as cu', 'cu.id','=','log.company_user_id')
		->join('user_auth as ua', 'ua.id','=','cu.user_auth_id')
        ->where('company_user_id', Crypt::decrypt(request('company_user_id')))
        ->select('ua.name','log.is_working as status','log.created_at')
		->limit(50)
        ->latest()
        ->get();

		return json_encode($result);
	}

	public function getDepartments(){
		$department['success'] = false;

        try {

            $department['result'] = Company_department::select('company_department.id as value', 'company_department.name as text')
                ->where('company_department.company_id', Crypt::decrypt(session('companyselected')['id']))
                ->get();

            foreach ($department['result'] as $key) {
                $key->value = Crypt::encrypt($key->value);
                $key->company_user_id = Crypt::encrypt($key->company_user_id);
            }

            $department['success'] = true;
        } catch (\Exception $e) {
            // echo $e;
            Logger::reportException($e, [], ['AnalyzeController', 'getDepartments'], false);
            $department['success'] = false;
        }

        return json_encode($department);
	}

	public function convertDate($type, $tz, $pInitialDate, $pFinalDate){
		switch($type){
			case 'CUSTOM': 
				$pInitialDate=  gmdate('d/m/Y', strtotime($pInitialDate));
				$pInitialDate = $pInitialDate . ' 00:00:00';
				$pInitialDate = Carbon::createFromFormat('d/m/Y H:i:s', $pInitialDate, $tz)->setTimezone('UTC');

				$pFinalDate=  gmdate('d/m/Y', strtotime($pFinalDate));
				$pFinalDate = $pFinalDate . ' 23:59:59';
				$pFinalDate = Carbon::createFromFormat('d/m/Y H:i:s', $pFinalDate, $tz)->setTimezone('UTC');

				$pInitialDate = $pInitialDate->format('Y/m/d H:i:s');
				$pFinalDate = $pFinalDate->format('Y/m/d H:i:s');
				break;
			case 'LAST_24_HOURS': 
			
				$date = new \DateTime("now", new \DateTimeZone($tz));
				$date = $date->format('d/m/Y');
				$date = $date . ' 00:00:00';
				$pInitialDate = Carbon::createFromFormat('d/m/Y H:i:s', $date, $tz)->setTimezone('UTC');
				$pInitialDate = $pInitialDate->format('Y/m/d H:i:s');
	
				$pFinalDate = Carbon::createFromFormat('d/m/Y H:i:s', $date, $tz)->setTimezone('UTC');
				$pFinalDate = $pFinalDate->addDay(1);
				$pFinalDate = $pFinalDate->format('Y/m/d H:i:s');

				break;
			case 'LAST_7_DAYS':
				case 'WEEK':
				$pInitialDate = date('d/m/Y', strtotime("last Sunday"));
				$pInitialDate = $pInitialDate . ' 00:00:00';
				$pInitialDate = Carbon::createFromFormat('d/m/Y H:i:s', $pInitialDate, $tz)->setTimezone('UTC');

				$pFinalDate = new \DateTime("now", new \DateTimeZone($tz));
				$pFinalDate = $pFinalDate->format('d/m/Y');
				$pFinalDate = $pFinalDate . ' 00:00:00';
				$pFinalDate = Carbon::createFromFormat('d/m/Y H:i:s', $pFinalDate, $tz)->setTimezone('UTC');
				$pFinalDate = $pFinalDate->addDay(1);

				$pInitialDate = $pInitialDate->format('Y/m/d H:i:s');
				$pFinalDate = $pFinalDate->format('Y/m/d H:i:s');
				break;
			case 'LAST_30_DAYS': 
				case 'MONTH': 
				$pInitialDate = date('01/m/Y',strtotime('this month'));
				$pInitialDate = $pInitialDate . ' 00:00:00';
				$pInitialDate = Carbon::createFromFormat('d/m/Y H:i:s', $pInitialDate, $tz)->setTimezone('UTC');
				
				$pFinalDate = new \DateTime("now", new \DateTimeZone($tz));
				$pFinalDate = $pFinalDate->format('d/m/Y');
				$pFinalDate = $pFinalDate . ' 00:00:00';
				$pFinalDate = Carbon::createFromFormat('d/m/Y H:i:s', $pFinalDate, $tz)->setTimezone('UTC');
				$pFinalDate = $pFinalDate->addDay(1);

				$pInitialDate = $pInitialDate->format('Y/m/d H:i:s');
				$pFinalDate = $pFinalDate->format('Y/m/d H:i:s');
				break;
			case 'LAST_365_DAYS': 
				case 'YEAR': 
				$pInitialDate = date('01/01/Y',strtotime('this year'));
				$pInitialDate = $pInitialDate . ' 00:00:00';
				$pInitialDate = Carbon::createFromFormat('d/m/Y H:i:s', $pInitialDate, $tz)->setTimezone('UTC');

				$pFinalDate = new \DateTime("now", new \DateTimeZone($tz));
				$pFinalDate = $pFinalDate->format('d/m/Y');
				$pFinalDate = $pFinalDate . ' 00:00:00';
				$pFinalDate = Carbon::createFromFormat('d/m/Y H:i:s', $pFinalDate, $tz)->setTimezone('UTC');
				$pFinalDate = $pFinalDate->addDay(1);
				
				$pInitialDate = $pInitialDate->format('Y/m/d H:i:s');
				$pFinalDate = $pFinalDate->format('Y/m/d H:i:s');
				break;
		}

		return [0 => $pInitialDate, 1 => $pFinalDate];

	}

}
