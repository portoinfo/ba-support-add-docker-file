<?php

namespace App\Tools;

use App\Chat;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Tools\Crypt;
use DateTime;
use DateTimeZone;

class AutomaticChat
{
    /**
     * @param string $chat_id
     */
    public static function distribution($department_id_crypto, $tz)
    {
		$company_settings = DB::table('company_settings')
			->select('general', 'settings_chat')
			->where('company_id', Crypt::decrypt(session('companyselected')['company_id']))
			->first();

		if($company_settings != null && isset(json_decode($company_settings->settings_chat)[1]->chatDepartAutomaticNumberLimit) ){

			$quantidadeChatsPorAtendente = json_decode($company_settings->settings_chat)[1]->chatDepartAutomaticNumberLimit; 

			$departDecrypty = [];
			$departUniqueDecrypty = [];
			
			if(isset(json_decode($company_settings->general)->departmentsSelected)){
				foreach (json_decode($company_settings->general)->departmentsSelected as $key) {
					array_push($departDecrypty, (int) Crypt::decrypt($key));
				}
			}
			
			if(isset(json_decode($company_settings->general)->departmentsSelectedUnique)){
				foreach (json_decode($company_settings->general)->departmentsSelectedUnique as $key) {
					array_push($departUniqueDecrypty, ["id" => Crypt::decrypt($key->id), "chatLimit" => $key->chatLimit]);
				}
			}

			$openedChat = DB::table('chat')
			->where('status', 'OPENED')
			->whereNull('ticket_id')
			->whereNull('deleted_at')
			->where('company_id', Crypt::decrypt(session('companyselected')['company_id']))
			->whereIn('company_department_id', $departDecrypty)
			->count();

			if($quantidadeChatsPorAtendente > 0){
				$autoLink = true;
			}else{
				$autoLink = false;
			}
		}else{
			//AINDA NÃO CONFIGUROU.
			$autoLink = false;
		}

        $result = [];
		if($autoLink == true){
			
			$atendentes_onlines = [];

			if(isset(json_decode($company_settings->general)->modelDistribution)){
				if(json_decode($company_settings->general)->modelDistribution == 'schedule'){
					$result = DB::table('user_auth')
					->join('company_user', 'user_auth.id', 'company_user.user_auth_id')
					->where('company_user.company_id', Crypt::decrypt(session('companyselected')['company_id']))
					->where('company_user.is_active', 1)
					->whereNull('company_user.deleted_at')
					->whereNull('user_auth.deleted_at')
					->where('company_user.is_working', 1)
					->select('user_auth.*', 'company_user.is_working', 'company_user.time_zone', 'company_user.opening_hours')
					->groupBy('user_auth.id')
					->get();


					foreach ($result as $key) {
						// $tzCliente = "America/New_York";
						// $tzCliente = "Asia/Tokyo";
						// $tzCliente = "Africa/Cairo";
						// $tzCliente = "Australia/Sydney";
						// $tzCliente = "Pacific/Honolulu";
						// $tzCliente = "America/Los_Angeles";
						// $tzCliente = "Europe/Paris";
						// $tzCliente = "Asia/Dubai";

						// Fuso horário do cliente (America/Sao_Paulo)
						$tzCliente = $tz ;

						// Fuso horário do atendente (America/New_York)
						$tzAtendente = $key->time_zone;

						// Lista de horários de trabalho do atendente (JSON)
						$horarios_trabalho_json = $key->opening_hours;

						// Decodificar a lista de horários de trabalho em um array PHP
						$horarios_trabalho = json_decode($horarios_trabalho_json, true);

						// Obter o dia da semana atual (0 para domingo, 1 para segunda, etc.) no fuso horário do cliente
						$dia_semana_atual_cliente = (int) date('w');

						// Obter a data e hora atual no fuso horário do cliente
						$data_hora_atual_cliente = new DateTime('now', new DateTimeZone($tzCliente));

						// Converter a data e hora atual do cliente para o fuso horário do atendente
						$data_hora_atual_atendente = $data_hora_atual_cliente->setTimezone(new DateTimeZone($tzAtendente));

						// Verificar se o horário atual está dentro do horário de trabalho para o dia da semana atual no fuso horário do atendente
						$entry1_atendente = DateTime::createFromFormat('H:i', $horarios_trabalho[$dia_semana_atual_cliente]['entry1'], new DateTimeZone($tzAtendente));
						$exit1_atendente = DateTime::createFromFormat('H:i', $horarios_trabalho[$dia_semana_atual_cliente]['exit1'], new DateTimeZone($tzAtendente));
						$entry2_atendente = DateTime::createFromFormat('H:i', $horarios_trabalho[$dia_semana_atual_cliente]['entry2'], new DateTimeZone($tzAtendente));
						$exit2_atendente = DateTime::createFromFormat('H:i', $horarios_trabalho[$dia_semana_atual_cliente]['exit2'], new DateTimeZone($tzAtendente));

						// Verificar se o horário atual está dentro do horário de trabalho no fuso horário do atendente
						if (
							(($data_hora_atual_atendente >= $entry1_atendente && $data_hora_atual_atendente <= $exit1_atendente) 
							|| ($data_hora_atual_atendente >= $entry2_atendente && $data_hora_atual_atendente <= $exit2_atendente))
						) {
							// echo "true";
							// dd('1');
							array_push($atendentes_onlines, $key); 
						} else {
							// echo "false";
							// dd('0');
						}
					}

					if($atendentes_onlines == []){
						return (object) [
							'cucd_id' =>  null, 
							'name' =>  null, 
							'is_admin'=> null,
							'user_auth_id'=> null, 
							'user_auth'=> null,
							'language'=> null
						];
					}

					$jsonString = json_encode($atendentes_onlines); // Converte o objeto para uma string JSON
					$atendentes_onlines = json_decode($jsonString, true); // Converte a string JSON em uma matriz associativa
				}else{
					// -- VERSÃO ANTIGA DE PEGAR ATENDENTES ONLINES
					$todos_onlines = request('onlineUsers');
					// PEGO SÓ OS ATENDENTES ONLINE
					foreach ($todos_onlines as $key) {
						if($key['is_client'] == 0 && $key['status'] == 'online'){
							array_push($atendentes_onlines, $key); 
						}
					}
				}
			}else{
				// -- VERSÃO ANTIGA DE PEGAR ATENDENTES ONLINES
				$todos_onlines = request('onlineUsers');
				// PEGO SÓ OS ATENDENTES ONLINE
				foreach ($todos_onlines as $key) {
					if($key['is_client'] == 0 && $key['status'] == 'online'){
						array_push($atendentes_onlines, $key); 
					}
				}
			}

			//DEPARTAMENTOS MARCADOS PARA RECEBER CHATS 
			$departmentsSelected = json_decode($company_settings->general)->departmentsSelected;

			//ATENDENTES MARCADOS PARA RECEBER CHATS 
			$agentsSelected = json_decode($company_settings->general)->agentsSelected;

			$atendentes_disponiveis = [];
			//VERIFICAR SE O DEPARTAMENTO DO CHAT QUE ESTÁ SENDO CRIADO FOI SELECIONADO NAS CONFIGS
			foreach ($departmentsSelected as $key => $value) {
				if(Crypt::decrypt($value) == Crypt::decrypt($department_id_crypto)){
				
					//VERIFICAR SE O ATENDENTE ONLINE FOI MARCADO PARA RECEBER CHAT
					foreach ($agentsSelected as $key => $value) {
						if($value == null){
							//FAZ ND
						}else{
							foreach ($atendentes_onlines as $key) {
								if(Crypt::decrypt($value) == $key['id']){
									array_push($atendentes_disponiveis, $key);
								}
							}
						}
					}
				}
			}

			$ids = [];
			//VERIFICAR SE O ATENDENTE PERTENCE AO DEPARTAMENTO
			foreach ($atendentes_disponiveis as $key) {

				$company_user_id = DB::table('company_user')
					->select('id as company_user_id')
					->where('user_auth_id', $key['id'])
					->where('company_id', Crypt::decrypt(session('companyselected')['company_id']))
					->first()->company_user_id;

				$comp_user_comp_depart_id = DB::table('company_user_company_department')
				->where('company_user_id', $company_user_id)
				->where('company_department_id', Crypt::decrypt($department_id_crypto))
				->where('is_active',1)
				->exists();

				//ADICIONO UM VERIFICADOR PARA VER SE ELE PERTENCE AO DEPARTAMENTO
				if($comp_user_comp_depart_id){
					array_push($ids,$key['id']);
				}
			}
			
			$ids = implode(',', $ids);

			if($ids != ''){
			
				// PEGO O PROXIMO QUE VAI RECEBER UM CHAT
				$arrayName = array(
					'company' => intval(Crypt::decrypt(session('companyselected')['company_id'])),
					'company2' => intval(Crypt::decrypt(session('companyselected')['company_id'])),
				);

				$query = "SELECT cu.user_auth_id, ua.name, ua.language, cd.id as department_id, cd.name as department_name, cucd.id as comp_user_comp_depart_id,
						COUNT(c.id) AS count_chats_in_progress,
						(SELECT MAX(final_date) AS final_date FROM chat_working_times WHERE company_user_company_department_id = cucd.id) as max_final_date,
						cu.id as company_user_id, cu.is_admin, COALESCE(chat_abertos._count, 0) AS _count
					FROM company_user cu
					JOIN user_auth ua ON cu.user_auth_id = ua.id
					JOIN company_user_company_department cucd ON cu.id = cucd.company_user_id
					JOIN company_department cd ON cucd.company_department_id = cd.id
					LEFT JOIN chat c ON cd.id = c.company_department_id AND c.comp_user_comp_depart_id_current = cucd.id 
						AND c.status = 'IN_PROGRESS' AND c.deleted_at IS NULL AND c.ticket_id IS NULL
					LEFT JOIN 
					(
						SELECT cu2.user_auth_id, COUNT(*) AS _count
						FROM company_user cu2
						JOIN company_user_company_department cucd2 ON cu2.id = cucd2.company_user_id
						JOIN chat c2 ON cucd2.company_department_id = c2.company_department_id AND c2.comp_user_comp_depart_id_current = cucd2.id 
							AND c2.status = 'IN_PROGRESS' AND c2.deleted_at IS NULL AND c2.ticket_id IS NULL
						WHERE cu2.company_id = :company
						AND cu2.user_auth_id in (".$ids.") -- atendentes disponiveis 
						AND cu2.deleted_at IS NULL
						AND cu2.is_active = 1
						AND cucd2.is_active = 1
						GROUP BY 1
					) chat_abertos ON ua.id = chat_abertos.user_auth_id
					WHERE cu.company_id = :company2
					AND cucd.company_department_id = '".Crypt::decrypt($department_id_crypto)."'
					AND ua.id in (".$ids.") -- atendentes disponiveis 
					AND ua.deleted_at IS NULL AND cu.deleted_at IS NULL
					AND cu.is_active = 1
					AND cucd.is_active = 1
					GROUP BY ua.id
					ORDER BY count_chats_in_progress, chat_abertos._count, max_final_date;";
		
				$result = DB::select($query, $arrayName);

			}
		}

        $aux = true;
        $agent_cucd_id = null;
        $agent_user_auth_id = null;
        $name = null;
        $is_admin = null;
        $user_auth = null;
        $language = null;
        foreach ($result as $key) {
            //QUANTIDADE DE CHAT QUE O ATENDENTE TEM É MENOR QUE A CONFIGURADA ENTÃO ELE RECEBE O CHAT.
            if($aux){

				if($departUniqueDecrypty == '[]'){
					if($key->count_chats_in_progress < $quantidadeChatsPorAtendente){
					
						$agent_cucd_id = $key->comp_user_comp_depart_id;
						$agent_user_auth_id = $key->user_auth_id;
						$language = $key->language;
						$name = $key->name;
						$aux = false;
						$is_admin = $key->is_admin;
	
						$user_auth = DB::table('user_auth')
						->where('id', $key->user_auth_id)
						->first();
					}

				}else{
					$limiteDepartUnique = 0;
					foreach ($departUniqueDecrypty as $key22) {
						if($key22['id'] == Crypt::decrypt($department_id_crypto)){
							$limiteDepartUnique = $key22['chatLimit'];
						}
					}

					if($key->count_chats_in_progress < ($limiteDepartUnique <= 0 ? 1 : $limiteDepartUnique)){
					
						$agent_cucd_id = $key->comp_user_comp_depart_id;
						$agent_user_auth_id = $key->user_auth_id;
						$language = $key->language;
						$name = $key->name;
						$aux = false;
						$is_admin = $key->is_admin;
	
						$user_auth = DB::table('user_auth')
						->where('id', $key->user_auth_id)
						->first();
					}
				}
            }
        }

        // return response()->json(['success'=>$success, 'agent_cucd_id'=>$agent_cucd_id, 'agent_user_auth_id'=>$agent_user_auth_id]);
        return (object) [
			'cucd_id' => $agent_cucd_id, 
			'name' => $name, 
			'is_admin'=>$is_admin,
			'user_auth_id'=>$agent_user_auth_id, 
			'user_auth'=>$user_auth,
			'language'=>$language
		];
    }
}
