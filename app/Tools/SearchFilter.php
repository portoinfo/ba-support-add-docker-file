<?php

namespace App\Tools;

use App\Chat;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Tools\Builderall\Logger;

class SearchFilter
{

    /**
     * @param string $chat_id
     */
    public static function getNameComplete(){
    	try {

			//REAL TIME PARA ADICIONAR NA FILA APOS CADASTRO DE FUNCIONARIO
			$arrayName = array(
				'companyselected' =>  intval(Crypt::decrypt(session('companyselected')['id'])),
			);

			$query =
				'SELECT * FROM (
					SELECT t.id, t.status, t.description, t.created_at, t.updated_at,
					t.created_by, t.priority, t.type, t.user_agent, t.comments, t.update_status_in_progress,
					ua.name, ua.email, cd.name AS department, cd.id AS department_id, cucd.id as company_user_company_department_id, cds.settings,
					COALESCE(ua_client.name, ua_create.name)  AS name_created,
					COALESCE(ua_client.email, ua_create.email) AS email_created,
					c.service_time, c.update_status_in_progress as last_update_status, c.id as chat_id, cucd.company_user_id, c.type as chat_type, IF((SELECT company_user_company_department_id FROM chat_history WHERE chat_id = c.id ORDER BY id DESC LIMIT 1) IS NULL, 1, 0) AS answered
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
					AND cd.deleted_at IS NULL
					AND c.deleted_at IS NULL
					AND t.deleted_at IS NULL
				    GROUP BY cd.id, t.id
			    ) sub ORDER BY answered DESC, sub.updated_at';

			$tickets['result'] = DB::select($query, $arrayName);

			foreach ($tickets['result'] as $key) {
				$key->department_id = Crypt::encrypt($key->department_id);
				$key->company_user_company_department_id = Crypt::encrypt($key->company_user_company_department_id);
				$key->overdue = json_decode($key->settings)->quant_limity->lateTime;
				$key->chat_id_decry = $key->chat_id;
				$key->chat_id = Crypt::encrypt($key->chat_id);
				//$key->company_id = Crypt::encrypt($key->company_id);
			}

			$tickets['success'] = true;
		} catch (\Exception $e) {
			echo $e;
			Logger::reportException($e, [], ['SearchFilter', 'getNameComplete'], false);
			$tickets['success'] = false;
		}

		return $tickets;
    }

    public static function getIdTicket($ticket_id){
    	try {

			//REAL TIME PARA ADICIONAR NA FILA APOS CADASTRO DE FUNCIONARIO
			$arrayName = array(
				'companyselected' =>  intval(Crypt::decrypt(session('companyselected')['id'])),
				'id' => $ticket_id
			);

			$query =
				'SELECT * FROM (
					SELECT t.id, t.status, t.description, t.created_at, t.updated_at,
					t.created_by, t.priority, t.type, t.user_agent, t.comments, t.update_status_in_progress,
					ua.name, ua.email, cd.name AS department, cd.id AS department_id, cucd.id as company_user_company_department_id, cds.settings,
					COALESCE(ua_client.name, ua_create.name)  AS name_created,
					COALESCE(ua_client.email, ua_create.email) AS email_created,
					c.service_time, c.update_status_in_progress as last_update_status, c.id as chat_id, cucd.company_user_id, c.type as chat_type, IF((SELECT company_user_company_department_id FROM chat_history WHERE chat_id = c.id ORDER BY id DESC LIMIT 1) IS NULL, 1, 0) AS answered
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
				$key->company_user_company_department_id = Crypt::encrypt($key->company_user_company_department_id);
				$key->overdue = json_decode($key->settings)->quant_limity->lateTime;
				$key->chat_id_decry = $key->chat_id;
				$key->chat_id = Crypt::encrypt($key->chat_id);
				$tickets['status'] = $key->status;
			}

			$tickets['success'] = true;
		} catch (\Exception $e) {
			echo $e;
			Logger::reportException($e, [], ['SearchFilter', 'getIdTicket'], false);
			$tickets['success'] = false;
		}

		return $tickets;
    }

    public static function getDescription(){
    	try {

			//REAL TIME PARA ADICIONAR NA FILA APOS CADASTRO DE FUNCIONARIO
			$arrayName = array(
				'companyselected' =>  intval(Crypt::decrypt(session('companyselected')['id'])),
			);

			$query =
				'SELECT * FROM (
					SELECT t.id, t.status, t.description, t.created_at, t.updated_at,
					t.created_by, t.priority, t.type, t.user_agent, t.comments, t.update_status_in_progress,
					ua.name, ua.email, cd.name AS department, cd.id AS department_id, cucd.id as company_user_company_department_id, cds.settings,
					COALESCE(ua_client.name, ua_create.name)  AS name_created,
					COALESCE(ua_client.email, ua_create.email) AS email_created,
					c.service_time, c.update_status_in_progress as last_update_status, c.id as chat_id, cucd.company_user_id, c.type as chat_type, IF((SELECT company_user_company_department_id FROM chat_history WHERE chat_id = c.id ORDER BY id DESC LIMIT 1) IS NULL, 1, 0) AS answered
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
					AND cd.deleted_at IS NULL
					AND c.deleted_at IS NULL
					AND t.deleted_at IS NULL
				    GROUP BY cd.id, t.id
			    ) sub ORDER BY answered DESC, sub.updated_at';

			$tickets['result'] = DB::select($query, $arrayName);

			foreach ($tickets['result'] as $key) {
				$key->department_id = Crypt::encrypt($key->department_id);
				$key->company_user_company_department_id = Crypt::encrypt($key->company_user_company_department_id);
				$key->overdue = json_decode($key->settings)->quant_limity->lateTime;
				$key->chat_id_decry = $key->chat_id;
				$key->chat_id = Crypt::encrypt($key->chat_id);
				//$key->company_id = Crypt::encrypt($key->company_id);
			}

			$tickets['success'] = true;
		} catch (\Exception $e) {
			echo $e;
			Logger::reportException($e, [], ['SearchFilter', 'getDescription'], false);
			$tickets['success'] = false;
		}

		return $tickets;
    }

    public static function getEmail(){
    	try {

			//REAL TIME PARA ADICIONAR NA FILA APOS CADASTRO DE FUNCIONARIO
			$arrayName = array(
				'companyselected' =>  intval(Crypt::decrypt(session('companyselected')['id'])),
			);

			$query =
				'SELECT * FROM (
					SELECT t.id, t.status, t.description, t.created_at, t.updated_at,
					t.created_by, t.priority, t.type, t.user_agent, t.comments, t.update_status_in_progress,
					ua.name, ua.email, cd.name AS department, cd.id AS department_id, cucd.id as company_user_company_department_id, cds.settings,
					COALESCE(ua_client.name, ua_create.name)  AS name_created,
					COALESCE(ua_client.email, ua_create.email) AS email_created,
					c.service_time, c.update_status_in_progress as last_update_status, c.id as chat_id, cucd.company_user_id, c.type as chat_type, IF((SELECT company_user_company_department_id FROM chat_history WHERE chat_id = c.id ORDER BY id DESC LIMIT 1) IS NULL, 1, 0) AS answered
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
					AND cd.deleted_at IS NULL
					AND c.deleted_at IS NULL
					AND t.deleted_at IS NULL
				    GROUP BY cd.id, t.id
			    ) sub ORDER BY answered DESC, sub.updated_at';

			$tickets['result'] = DB::select($query, $arrayName);

			foreach ($tickets['result'] as $key) {
				$key->department_id = Crypt::encrypt($key->department_id);
				$key->company_user_company_department_id = Crypt::encrypt($key->company_user_company_department_id);
				$key->overdue = json_decode($key->settings)->quant_limity->lateTime;
				$key->chat_id_decry = $key->chat_id;
				$key->chat_id = Crypt::encrypt($key->chat_id);
				//$key->company_id = Crypt::encrypt($key->company_id);
			}

			$tickets['success'] = true;
		} catch (\Exception $e) {
			echo $e;
			Logger::reportException($e, [], ['SearchFilter', 'getEmail'], false);
			$tickets['success'] = false;
		}

		return $tickets;
    }

    public static function getCompany(){
    	try {

			//REAL TIME PARA ADICIONAR NA FILA APOS CADASTRO DE FUNCIONARIO
			$arrayName = array(
				'companyselected' =>  intval(Crypt::decrypt(session('companyselected')['id'])),
			);

			$query =
				'SELECT * FROM (
					SELECT t.id, t.status, t.description, t.created_at, t.updated_at,
					t.created_by, t.priority, t.type, t.user_agent, t.comments, t.update_status_in_progress,
					ua.name, ua.email, cd.name AS department, cd.id AS department_id, cucd.id as company_user_company_department_id, cds.settings,
					COALESCE(ua_client.name, ua_create.name)  AS name_created,
					COALESCE(ua_client.email, ua_create.email) AS email_created,
					c.service_time, c.update_status_in_progress as last_update_status, c.id as chat_id, cucd.company_user_id, c.type as chat_type, IF((SELECT company_user_company_department_id FROM chat_history WHERE chat_id = c.id ORDER BY id DESC LIMIT 1) IS NULL, 1, 0) AS answered
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
					AND cd.deleted_at IS NULL
					AND c.deleted_at IS NULL
					AND t.deleted_at IS NULL
				    GROUP BY cd.id, t.id
			    ) sub ORDER BY answered DESC, sub.updated_at';

			$tickets['result'] = DB::select($query, $arrayName);

			foreach ($tickets['result'] as $key) {
				$key->department_id = Crypt::encrypt($key->department_id);
				$key->company_user_company_department_id = Crypt::encrypt($key->company_user_company_department_id);
				$key->overdue = json_decode($key->settings)->quant_limity->lateTime;
				$key->chat_id_decry = $key->chat_id;
				$key->chat_id = Crypt::encrypt($key->chat_id);
				//$key->company_id = Crypt::encrypt($key->company_id);
			}

			$tickets['success'] = true;
		} catch (\Exception $e) {
			echo $e;
			Logger::reportException($e, [], ['SearchFilter', 'getCompany'], false);
			$tickets['success'] = false;
		}

		return $tickets;
    }

    public static function getOperator(){
    	try {

			//REAL TIME PARA ADICIONAR NA FILA APOS CADASTRO DE FUNCIONARIO
			$arrayName = array(
				'companyselected' =>  intval(Crypt::decrypt(session('companyselected')['id'])),
			);

			$query =
				'SELECT * FROM (
					SELECT t.id, t.status, t.description, t.created_at, t.updated_at,
					t.created_by, t.priority, t.type, t.user_agent, t.comments, t.update_status_in_progress,
					ua.name, ua.email, cd.name AS department, cd.id AS department_id, cucd.id as company_user_company_department_id, cds.settings,
					COALESCE(ua_client.name, ua_create.name)  AS name_created,
					COALESCE(ua_client.email, ua_create.email) AS email_created,
					c.service_time, c.update_status_in_progress as last_update_status, c.id as chat_id, cucd.company_user_id, c.type as chat_type, IF((SELECT company_user_company_department_id FROM chat_history WHERE chat_id = c.id ORDER BY id DESC LIMIT 1) IS NULL, 1, 0) AS answered
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
					AND cd.deleted_at IS NULL
					AND c.deleted_at IS NULL
					AND t.deleted_at IS NULL
				    GROUP BY cd.id, t.id
			    ) sub ORDER BY answered DESC, sub.updated_at';

			$tickets['result'] = DB::select($query, $arrayName);

			foreach ($tickets['result'] as $key) {
				$key->department_id = Crypt::encrypt($key->department_id);
				$key->company_user_company_department_id = Crypt::encrypt($key->company_user_company_department_id);
				$key->overdue = json_decode($key->settings)->quant_limity->lateTime;
				$key->chat_id_decry = $key->chat_id;
				$key->chat_id = Crypt::encrypt($key->chat_id);
				//$key->company_id = Crypt::encrypt($key->company_id);
			}

			$tickets['success'] = true;
		} catch (\Exception $e) {
			echo $e;
			Logger::reportException($e, [], ['SearchFilter', 'getOperator'], false);
			$tickets['success'] = false;
		}

		return $tickets;
    }

    public static function getComment(){
    	try {

			//REAL TIME PARA ADICIONAR NA FILA APOS CADASTRO DE FUNCIONARIO
			$arrayName = array(
				'companyselected' =>  intval(Crypt::decrypt(session('companyselected')['id'])),
			);

			$query =
				'SELECT * FROM (
					SELECT t.id, t.status, t.description, t.created_at, t.updated_at,
					t.created_by, t.priority, t.type, t.user_agent, t.comments, t.update_status_in_progress,
					ua.name, ua.email, cd.name AS department, cd.id AS department_id, cucd.id as company_user_company_department_id, cds.settings,
					COALESCE(ua_client.name, ua_create.name)  AS name_created,
					COALESCE(ua_client.email, ua_create.email) AS email_created,
					c.service_time, c.update_status_in_progress as last_update_status, c.id as chat_id, cucd.company_user_id, c.type as chat_type, IF((SELECT company_user_company_department_id FROM chat_history WHERE chat_id = c.id ORDER BY id DESC LIMIT 1) IS NULL, 1, 0) AS answered
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
					AND cd.deleted_at IS NULL
					AND c.deleted_at IS NULL
					AND t.deleted_at IS NULL
				    GROUP BY cd.id, t.id
			    ) sub ORDER BY answered DESC, sub.updated_at';

			$tickets['result'] = DB::select($query, $arrayName);

			foreach ($tickets['result'] as $key) {
				$key->department_id = Crypt::encrypt($key->department_id);
				$key->company_user_company_department_id = Crypt::encrypt($key->company_user_company_department_id);
				$key->overdue = json_decode($key->settings)->quant_limity->lateTime;
				$key->chat_id_decry = $key->chat_id;
				$key->chat_id = Crypt::encrypt($key->chat_id);
				//$key->company_id = Crypt::encrypt($key->company_id);
			}

			$tickets['success'] = true;
		} catch (\Exception $e) {
			echo $e;
			Logger::reportException($e, [], ['SearchFilter', 'getComment'], false);
			$tickets['success'] = false;
		}

		return $tickets;
    }
}
