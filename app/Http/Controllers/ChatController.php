<?php

namespace App\Http\Controllers;

use App\Avaliation;
use App\Chat;
use App\ChatHistory;
use App\CompanyUserCompanyDepartment;
use App\Events\CanceledUpdated;
use App\Events\ChatStatusChanger;
use App\Events\ChatTicketDelete;
use App\Events\ClientQueueStatus;
use App\Events\ClosedUpdated;
use App\Events\EvaluationUpdated;
use App\Events\FullChatCanceled;
use App\Events\FullChatClosed;
use App\Events\FullChatProgress;
use App\Events\FullChatResolved;
use App\Events\GlobalNotification;
use App\Events\InProgressUpdated;
use App\Events\MessageSent;
use App\Events\QueueUpdated;
use App\Events\ResolvedUpdated;
use App\Events\Tabs;
use App\Events\TicketsAgentListUpdate;
use App\Events\TicketsListUpdate;
use App\Events\TransferredUpdated;
use App\Jobs\alertEndOfChat;
use App\Mail\sendEmailCustom;
use App\Models\Company_department;
use App\Models\Company_user;
use App\Models\CompanyDepartmentSettings;
use App\Ticket;
use App\TicketChatAnswer;
use App\Tools\AutomaticChat;
use App\Tools\Builderall\Logger;
use App\Tools\Chats\SendRealtime;
use App\Tools\ChatWorkingTimes;
use App\Tools\Checkout;
use App\Tools\Client;
use App\Tools\Crypt;
use App\Tools\SystemState;
use App\Tools\telegram\TelegramBot;
use App\Tools\Tickets\Feedback;
use App\Tools\Tickets\MailAdmin;
use App\User_client;
use App\UserClientChat;
use Carbon\Carbon;
use DateTime;
use DateTimeZone;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;

class ChatController extends Controller
{
	public function index()
	{
		return view('functions.employee.chat.chat');
        // return view('functions.full-chat.index');
	}

	public function getChatsOnQueue(Request $request)
	{
		/** begin */
		try {
			$data =  $request->all();
			/** validator */
			$validator = Validator::make($request->all(), [
				'departments' => 'required|array',
			]);

			if ($validator->fails()) {
				return response($validator->errors(), 400);
			}
			/** filter by department */
			$departments = [];

			foreach ($data['departments'] as $row) {
				array_push($departments, (int) Crypt::decrypt(json_decode($row)->id));
			}

			/** query */
			$query = Chat::join('company_department', 'company_department.id', '=', 'chat.company_department_id')
				->join('chat_history', 'chat_history.chat_id', '=', 'chat.id')
				->join('user_client_chat', 'user_client_chat.chat_id', '=', 'chat.id')
				->join('user_client', 'user_client.id', '=', 'user_client_chat.user_client_id')
				->join('user_auth', 'user_auth.id', '=', 'user_client.user_auth_id')
				->leftJoin('chat_category AS cc', 'chat.id', 'cc.chat_id')
				->where('chat.company_id', Crypt::decrypt(session('companyselected')['id']))
				->whereIn('chat.company_department_id', $departments)
				->whereNull('chat.ticket_id')
				->where('chat.type', 'DEFAULT')
				->where('chat.status', 'OPENED')
				->select(
					'chat.id AS chat_id',
					'chat.id AS number',
					'chat.company_id',
					'chat.company_department_id',
					'chat.comp_user_comp_depart_id_current',
					DB::raw('DATE_FORMAT(chat.created_at,"%d/%m/%Y") as date'),
					DB::raw('DATE_FORMAT(chat.created_at,"%H:%i:%s") as time'),
					'chat.created_at',
					'company_department.name as department',
					'company_department.type as dep_type',
					'user_auth.name',
					'user_auth.builderall_account_data',
					'user_auth.email',
					'user_auth.id as client_id',
					'chat_history.content',
					'chat.status',
					'chat.description',
					'chat.type',
					'chat.user_agent',
                    'chat.turn_into_ticket_at_closing',
					'cc.chat_id as category_chat_id'
				);
				
				if (request('filter_not_category') == 'true' || request('filter_not_category') == 1) {
                    $query->where('cc.chat_id', null);
                }

                $query->groupBy('chat.id')
				->orderBy('chat.updated_at');

				$result = $query->get();


			foreach ($result as $row) {
				$row->chat_id = Crypt::encrypt($row->chat_id);
				$row->company_id = Crypt::encrypt($row->company_id);
				$row->company_department_id = Crypt::encrypt($row->company_department_id);
				$row->email = Client::getCleanEmail($row->email, Crypt::decrypt($row->company_id));
				$row->client_id = Crypt::encrypt($row->client_id);
			}

            return response()->json([
                'chats' => $result,
                'company_id' => session('companyselected')['id'],
            ]);


		} catch (\Exception $e) {
			Logger::reportException($e, [], ['chat-controller', 'getChatsOnQueue'], false);
		}
	}

	public function getChatsInProgress(Request $request)
	{
		/** begin */
		try {
			$data =  $request->all();
			/** validator */
			$validator = Validator::make($request->all(), [
				'departments' => 'required|array',
			]);

			if ($validator->fails()) {
				return response($validator->errors(), 400);
			}
			/** filter by department */
			$departments = [];

			foreach ($request->departments as $row) {
				array_push($departments, (int) Crypt::decrypt(json_decode($row)->id));
			}

			/** query */
			$query = Chat::join('company_department', 'company_department.id', 'chat.company_department_id')
				->join('user_client_chat', 'user_client_chat.chat_id', 'chat.id')
				->join('user_client', 'user_client.id', 'user_client_chat.user_client_id')
				->join('user_auth AS ua_client', 'user_client.user_auth_id', 'ua_client.id')
				->join('company_user_company_department', 'chat.comp_user_comp_depart_id_current', 'company_user_company_department.id')
				->join('company_user', 'company_user_company_department.company_user_id', 'company_user.id')
				->join('user_auth AS ua_agent', 'company_user.user_auth_id', 'ua_agent.id')
				->leftJoin('chat_category AS cc', 'chat.id', 'cc.chat_id')
				->select(
					'chat.id AS chat_id',
					'chat.id AS number',
					'chat.company_id',
					'chat.company_department_id',
					'chat.comp_user_comp_depart_id_current',
					'chat.status',
					'chat.description',
					'chat.type',
					'chat.user_agent',
                    'chat.turn_into_ticket_at_closing',
					DB::raw('DATE_FORMAT(chat.created_at, "%d/%m/%Y") AS date'),
					DB::raw('DATE_FORMAT(chat.created_at, "%H:%i:%s") AS time'),
					'chat.created_at',
					'company_department.name AS department',
                    'company_department.type as dep_type',
					'ua_client.name',
					'ua_client.email',
					'ua_client.id as client_id',
                    'ua_client.builderall_account_data',
					'ua_agent.name AS operator',
                    'ua_agent.id AS operator_id',
                    'ua_agent.email AS operator_email',
					DB::raw('IF((SELECT company_user_company_department_id FROM chat_history WHERE chat_id = chat.id ORDER BY id DESC LIMIT 1) IS NULL, 1, 0) AS answered'),
					'cc.chat_id as category_chat_id'
				)
				->where('chat.company_id', Crypt::decrypt(session('companyselected')['id']))
				->where('ua_agent.id', Auth::id())
				->whereIn('chat.company_department_id', $departments)
				->whereNull('chat.ticket_id')
				->where('chat.status', 'IN_PROGRESS')
				->where('chat.type', 'DEFAULT');

				if (request('filter_not_category') == 'true' || request('filter_not_category') == 1) {
                    $query->where('cc.chat_id', null);
                }

				$query->groupBy('chat.id')
				->orderBy('sub.history_id', 'desc');

				$result= $query->get();

			foreach ($result as $row) {
				$row->chat_id = Crypt::encrypt($row->chat_id);
				$row->company_id = Crypt::encrypt($row->company_id);
				$row->company_department_id = Crypt::encrypt($row->company_department_id);
				$row->comp_user_comp_depart_id_current = Crypt::encrypt($row->comp_user_comp_depart_id_current);
				$row->email = Client::getCleanEmail($row->email, Crypt::decrypt($row->company_id));
				$row->client_id = Crypt::encrypt($row->client_id);
                $row->operator_id = Crypt::encrypt($row->operator_id);
			}
			return response()->json([
				'chats' => $result,
				'company_id' => session('companyselected')['id'],
			]);
		} catch (\Exception $e) {
			Logger::reportException($e, [], ['chat-controller', 'getChatsInProgress'], false);
		}
	}

	public function getChatsTransferred(Request $request)
	{
		/** begin */
		try {
			$data =  $request->all();
			/** validator */
			$validator = Validator::make($request->all(), [
				'take' => 'required|int',
				'skip' => 'required|int',
				'departments' => 'required|array',
			]);

			if ($validator->fails()) {
				return response($validator->errors(), 400);
			}
			/** filter by department */
			$departments = [];

			foreach ($request->departments as $row) {
				array_push($departments, (int) Crypt::decrypt(json_decode($row)->id));
			}

			$result = Chat::join('company_department', 'company_department.id', '=', 'chat.company_department_id')
				->join('chat_history', 'chat_history.chat_id', '=', 'chat.id')
				->join('user_client_chat', 'user_client_chat.chat_id', '=', 'chat.id')
				->join('user_client', 'user_client.id', '=', 'user_client_chat.user_client_id')
				->join('user_auth', 'user_auth.id', '=', 'user_client.user_auth_id')
				->where('chat.company_id', Crypt::decrypt(session('companyselected')['id']))
				->whereIn('chat.company_department_id', $departments)
				->whereNull('chat.ticket_id')
				->where('chat.status', 'IN_PROGRESS')
				->where('chat.type', 'TRANSFERED')
				->select(
					'chat.id AS chat_id',
					'chat.id AS number',
					'chat.company_id',
					'chat.status',
					'chat.description',
					'chat.type',
					'chat.company_department_id',
					'chat.comp_user_comp_depart_id_current',
					'chat.created_at',
					'chat.user_agent',
                    'chat.turn_into_ticket_at_closing',
					DB::raw('DATE_FORMAT(chat.created_at,"%d/%m/%Y") as date'),
					DB::raw('DATE_FORMAT(chat.created_at,"%H:%i:%s") as time'),
					'company_department.name as department',
                    'company_department.type as dep_type',
					'user_auth.name',
					'user_auth.email',
					'user_auth.id as client_id',
                    'user_auth.builderall_account_data',
					'chat_history.content'
				)
				->groupBy('chat.id')
				->orderByDesc('chat.updated_at')
				->skip($data['skip'])
				->take($data['take'])
				->get();

			foreach ($result as $row) {
				$row->chat_id = Crypt::encrypt($row->chat_id);
				$row->company_id = Crypt::encrypt($row->company_id);
				$row->company_department_id = Crypt::encrypt($row->company_department_id);
				$row->comp_user_comp_depart_id_current = Crypt::encrypt($row->comp_user_comp_depart_id_current);
				$row->email = Client::getCleanEmail($row->email, Crypt::decrypt($row->company_id));
				$row->client_id = Crypt::encrypt($row->client_id);
			}
			return response()->json([
				'chats' => $result,
				'company_id' => session('companyselected')['id'],
				'skip' => $data['skip'] + $data['take'],
			]);
		} catch (\Exception $e) {
			Logger::reportException($e, [], ['chat-controller', 'getChatsTransferred'], false);
		}
	}

	public function getChatsClosed(Request $request)
	{
		/** begin */
		try {
			$data =  $request->all();
			/** validator */
			$validator = Validator::make($request->all(), [
				'take' => 'required|int',
				'skip' => 'required|int',
				'departments' => 'required|array',
			]);

			if ($validator->fails()) {
				return response($validator->errors(), 400);
			}
			/** filter by department */
			$departments = [];

			foreach ($request->departments as $row) {
				array_push($departments, (int) Crypt::decrypt(json_decode($row)->id));
			}

			$query = Chat::join('company_department', 'company_department.id', '=', 'chat.company_department_id')
				->join('chat_history', 'chat_history.chat_id', '=', 'chat.id')
				->join('user_client_chat', 'user_client_chat.chat_id', '=', 'chat.id')
				->join('user_client', 'user_client.id', '=', 'user_client_chat.user_client_id')
				->join('user_auth AS ua_client', 'user_client.user_auth_id', 'ua_client.id')
				->join('company_user_company_department', 'chat.comp_user_comp_depart_id_current', 'company_user_company_department.id')
				->join('company_user', 'company_user_company_department.company_user_id', 'company_user.id')
				->join('user_auth AS ua_agent', 'company_user.user_auth_id', 'ua_agent.id')
				->leftJoin('chat_category AS cc', 'chat.id', 'cc.chat_id')
				->where('ua_agent.id', Auth::id())
				->where('chat.company_id', Crypt::decrypt(session('companyselected')['id']))
				->whereIn('chat.company_department_id', $departments)
				->whereNull('chat.ticket_id')
				->where('chat.status', 'CLOSED')
				->where('chat.type', 'DEFAULT');

				if (request('filter_not_category') == 'true' || request('filter_not_category') == 1) {
                    $query->where('cc.chat_id', null);
                }

				$query->select(
					'chat.id AS chat_id',
					'chat.id AS number',
					'chat.company_id',
					'chat.status',
					'chat.description',
					'chat.type',
					'chat.company_department_id',
					'chat.comp_user_comp_depart_id_current',
					'chat.created_at',
					'chat.updated_at as end',
					'chat.user_agent',
                    'chat.turn_into_ticket_at_closing',
					DB::raw('DATE_FORMAT(chat.created_at,"%d/%m/%Y") as date'),
					DB::raw('DATE_FORMAT(chat.created_at,"%H:%i:%s") as time'),
					'company_department.name as department',
                    'company_department.type as dep_type',
					'ua_client.name',
					'ua_client.email',
					'ua_client.id as client_id',
                    'ua_client.builderall_account_data',
					'ua_agent.name AS operator',
                    'ua_agent.id AS operator_id',
                    'ua_agent.email AS operator_email',
					'chat_history.content',
					'cc.chat_id as category_chat_id'
				)
				->groupBy('chat.id')
				->orderByDesc('chat.updated_at')
				->skip($data['skip'])
				->take($data['take']);

				$result = $query->get();

			foreach ($result as $row) {
				$row->chat_id = Crypt::encrypt($row->chat_id);
				$row->company_id = Crypt::encrypt($row->company_id);
				$row->company_department_id = Crypt::encrypt($row->company_department_id);
				$row->comp_user_comp_depart_id_current = Crypt::encrypt($row->comp_user_comp_depart_id_current);
				$row->email = Client::getCleanEmail($row->email, Crypt::decrypt($row->company_id));
				$row->client_id = Crypt::encrypt($row->client_id);
                $row->operator_id = Crypt::encrypt($row->operator_id);
			}
			return response()->json([
				'chats' => $result,
				'company_id' => session('companyselected')['id'],
				'skip' => $data['skip'] + $data['take'],
			]);
		} catch (\Exception $e) {
			Logger::reportException($e, [], ['chat-controller', 'getChatsClosed'], false);
		}
	}

	public function getChatsResolved(Request $request)
	{
		/** begin */
		try {
			$data =  $request->all();
			/** validator */
			$validator = Validator::make($request->all(), [
				'take' => 'required|int',
				'skip' => 'required|int',
				'departments' => 'required|array',
			]);

			if ($validator->fails()) {
				return response($validator->errors(), 400);
			}
			/** filter by department */
			$departments = [];

			foreach ($request->departments as $row) {
				array_push($departments, (int) Crypt::decrypt(json_decode($row)->id));
			}

			$query = Chat::join('company_department', 'company_department.id', '=', 'chat.company_department_id')
				->join('chat_history', 'chat_history.chat_id', '=', 'chat.id')
				->join('user_client_chat', 'user_client_chat.chat_id', '=', 'chat.id')
				->join('user_client', 'user_client.id', '=', 'user_client_chat.user_client_id')
				->join('user_auth AS ua_client', 'user_client.user_auth_id', 'ua_client.id')
				->join('company_user_company_department', 'chat.comp_user_comp_depart_id_current', 'company_user_company_department.id')
				->join('company_user', 'company_user_company_department.company_user_id', 'company_user.id')
				->join('user_auth AS ua_agent', 'company_user.user_auth_id', 'ua_agent.id')
				->leftJoin('chat_category AS cc', 'chat.id', 'cc.chat_id')
				->where('ua_agent.id', Auth::id())
				->where('chat.company_id', Crypt::decrypt(session('companyselected')['id']))
				->whereIn('chat.company_department_id', $departments)
				->whereNull('chat.ticket_id')
				->where('chat.status', 'RESOLVED')
				->where('chat.type', 'DEFAULT');

				if (request('filter_not_category') == 'true' || request('filter_not_category') == 1) {
					$query->where('cc.chat_id', null);
				}
				
				$query->select(
					'chat.id AS chat_id',
					'chat.id AS number',
					'chat.company_id',
					'chat.status',
					'chat.description',
					'chat.type',
					'chat.company_department_id',
					'chat.comp_user_comp_depart_id_current',
					'chat.created_at',
					'chat.updated_at as end',
					'chat.user_agent',
                    'chat.turn_into_ticket_at_closing',
					DB::raw('DATE_FORMAT(chat.created_at,"%d/%m/%Y") as date'),
					DB::raw('DATE_FORMAT(chat.created_at,"%H:%i:%s") as time'),
					'company_department.name as department',
                    'company_department.type as dep_type',
					'ua_client.name',
					'ua_client.email',
					'ua_client.id as client_id',
                    'ua_client.builderall_account_data',
					'ua_agent.name AS operator',
                    'ua_agent.email AS operator_email',
                    'ua_agent.id AS operator_id',
					'chat_history.content',
					'cc.chat_id as category_chat_id'
				)
				->groupBy('chat.id')
				->orderByDesc('chat.updated_at')
				->skip($data['skip'])
				->take($data['take']);

				$result = $query->get();

			foreach ($result as $row) {
				$row->chat_id = Crypt::encrypt($row->chat_id);
				$row->company_id = Crypt::encrypt($row->company_id);
				$row->company_department_id = Crypt::encrypt($row->company_department_id);
				$row->comp_user_comp_depart_id_current = Crypt::encrypt($row->comp_user_comp_depart_id_current);
				$row->email = Client::getCleanEmail($row->email, Crypt::decrypt($row->company_id));
				$row->client_id = Crypt::encrypt($row->client_id);
                $row->operator_id = Crypt::encrypt($row->operator_id);
			}
			return response()->json([
				'chats' => $result,
				'company_id' => session('companyselected')['id'],
				'skip' => $data['skip'] + $data['take'],
			]);
		} catch (\Exception $e) {
			Logger::reportException($e, [], ['chat-controller', 'getChatsClosed'], false);
		}
	}

	public function getChatsFinished(Request $request)
	{
		/** begin */
		try {
			$data =  $request->all();
			/** validator */
			$validator = Validator::make($request->all(), [
				'departments' => 'required|array',
			]);

			if ($validator->fails()) {
				return response($validator->errors(), 400);
			}
			/** filter by department */
			$departments = [];

			foreach ($request->departments as $row) {
				array_push($departments, (int) Crypt::decrypt(json_decode($row)->id));
			}

			$result = Chat::join('company_department', 'company_department.id', '=', 'chat.company_department_id')
				->join('user_client_chat', 'user_client_chat.chat_id', '=', 'chat.id')
				->join('user_client', 'user_client.id', '=', 'user_client_chat.user_client_id')
				->join('user_auth AS ua_client', 'user_client.user_auth_id', 'ua_client.id')
				->leftjoin('company_user_company_department', 'chat.comp_user_comp_depart_id_current', 'company_user_company_department.id')
				->leftjoin('company_user', 'company_user_company_department.company_user_id', 'company_user.id')
				->leftjoin('user_auth AS ua_agent', 'company_user.user_auth_id', 'ua_agent.id')
				->leftJoin('chat_category AS cc', 'chat.id', 'cc.chat_id')
				->where('ua_agent.id', Auth::id())
				->where('chat.company_id', Crypt::decrypt(session('companyselected')['id']))
				->whereIn('chat.company_department_id', $departments)
				->whereNull('chat.ticket_id')
				->where('chat.type', 'DEFAULT')
				->where(function ($f) {
					$f->where([['chat.status', 'RESOLVED']])
						->orWhere([['chat.status', 'CLOSED']]);
				});

				if (request('filter_not_category') == 'true' || request('filter_not_category') == 1) {
					$result->where('cc.chat_id', null);
				}
				
				$result->select(
					'chat.id AS chat_id',
					'chat.id AS number',
                    'chat.is_robot AS is_robot',
					'chat.company_id',
					'chat.status',
					'chat.description',
					'chat.type',
					'chat.company_department_id',
					'chat.comp_user_comp_depart_id_current',
					'chat.created_at',
					'chat.updated_at as end',
					'chat.user_agent',
                    'chat.turn_into_ticket_at_closing',
					DB::raw('DATE_FORMAT(chat.created_at,"%d/%m/%Y") as date'),
					DB::raw('DATE_FORMAT(chat.created_at,"%H:%i:%s") as time'),
					'company_department.name as department',
                    'company_department.type as dep_type',
					'ua_client.name',
					'ua_client.email',
					'ua_client.id as client_id',
                    'ua_client.builderall_account_data',
					'ua_agent.name AS operator',
                    'ua_agent.id AS operator_id',
                    'ua_agent.email AS operator_email',
                    DB::Raw('NULL as content'),
					'cc.chat_id as category_chat_id'
				)
				->groupBy('chat.id')
				->orderByDesc('chat.updated_at');

                $query = $result->simplePaginate($data['per_page']);

			foreach ($query as $row) {
				$row->chat_id = Crypt::encrypt($row->chat_id);
				$row->company_id = Crypt::encrypt($row->company_id);
				$row->company_department_id = Crypt::encrypt($row->company_department_id);
                $row->comp_user_comp_depart_id_current = isset($row->comp_user_comp_depart_id_current) ? Crypt::encrypt($row->comp_user_comp_depart_id_current) : null;
				$row->email = Client::getCleanEmail($row->email, Crypt::decrypt($row->company_id));
				$row->client_id = Crypt::encrypt($row->client_id);
                $row->operator_id = Crypt::encrypt($row->operator_id);
			}


            return response()->json($query);


		} catch (\Exception $e) {
			Logger::reportException($e, [], ['chat-controller', 'getChatsFinished'], false);
		}
	}

	public function getChatsCanceled(Request $request)
	{
		/** begin */
		try {
			$data =  $request->all();
			/** validator */
			$validator = Validator::make($request->all(), [
				// 'take' => 'required|int',
				// 'skip' => 'required|int',
				'departments' => 'required|array',
			]);

			if ($validator->fails()) {
				return response($validator->errors(), 400);
			}
			/** filter by department */
			$departments = [];

			foreach ($request->departments as $row) {
				array_push($departments, (int) Crypt::decrypt(json_decode($row)->id));
			}

			$query = Chat::join('company_department', 'company_department.id', '=', 'chat.company_department_id')
				->join('chat_history', 'chat_history.chat_id', '=', 'chat.id')
				->join('user_client_chat', 'user_client_chat.chat_id', '=', 'chat.id')
				->join('user_client', 'user_client.id', '=', 'user_client_chat.user_client_id')
				->join('user_auth AS ua_client', 'user_client.user_auth_id', 'ua_client.id')
				->join('company_user_company_department', 'chat.comp_user_comp_depart_id_current', 'company_user_company_department.id')
				->join('company_user', 'company_user_company_department.company_user_id', 'company_user.id')
				->join('user_auth AS ua_agent', 'company_user.user_auth_id', 'ua_agent.id')
				->where('ua_agent.id', Auth::id())
				->where('chat.company_id', Crypt::decrypt(session('companyselected')['id']))
				->whereIn('chat.company_department_id', $departments)
				->whereNull('chat.ticket_id')
				->where('chat.status', 'CANCELED')
				->where('chat.type', 'DEFAULT')
				->select(
					'chat.id AS chat_id',
					'chat.id AS number',
                    'chat.is_robot AS is_robot',
					'chat.company_id',
					'chat.status',
					'chat.description',
					'chat.type',
					'chat.company_department_id',
					'chat.comp_user_comp_depart_id_current',
					'chat.created_at',
					'chat.updated_at as end',
					'chat.user_agent',
                    'chat.turn_into_ticket_at_closing',
					DB::raw('DATE_FORMAT(chat.created_at,"%d/%m/%Y") as date'),
					DB::raw('DATE_FORMAT(chat.created_at,"%H:%i:%s") as time'),
					'company_department.name as department',
                    'company_department.type as dep_type',
					'ua_client.name',
					'ua_client.email',
					'ua_client.id as client_id',
                    'ua_client.builderall_account_data',
					'ua_agent.name AS operator',
                    'ua_agent.id AS operator_id',
                    'ua_agent.email AS operator_email',
					'chat_history.content'
				)
				->groupBy('chat.id')
				->orderBy('chat.id');

            if (!empty($request->take)) {
                $query->skip($data['skip'])->take($data['take']);
                $result = $query->get();
            } else {
                $result = $query->paginate($data['per_page']);
            }

			foreach ($result as $row) {
				$row->chat_id = Crypt::encrypt($row->chat_id);
				$row->company_id = Crypt::encrypt($row->company_id);
				$row->company_department_id = Crypt::encrypt($row->company_department_id);
				$row->comp_user_comp_depart_id_current = Crypt::encrypt($row->comp_user_comp_depart_id_current);
				$row->email = Client::getCleanEmail($row->email, Crypt::decrypt($row->company_id));
				$row->client_id = Crypt::encrypt($row->client_id);
                $row->operator_id = Crypt::encrypt($row->operator_id);
			}
			if (!empty($request->take)) {
                return response()->json([
                    'chats' => $result,
                    'company_id' => session('companyselected')['id'],
                    'skip' => $data['skip'] + $data['take'],
                ]);
            } else {
                return response()->json($result);
            }
		} catch (\Exception $e) {
			Logger::reportException($e, [], ['chat-controller', 'getChatsCanceled'], false);
		}
	}

    public function getChatsRobot(Request $request) {
        /** begin */
		try {
			$data =  $request->all();
			/** validator */
			$validator = Validator::make($request->all(), [
				'take' => 'required|int',
				'skip' => 'required|int',
				'departments' => 'required|array',
			]);

			if ($validator->fails()) {
				return response($validator->errors(), 400);
			}
			/** filter by department */
			$departments = [];

			foreach ($request->departments as $row) {
				array_push($departments, (int) Crypt::decrypt(json_decode($row)->id));
			}

			$result = Chat::join('company_department', 'company_department.id', '=', 'chat.company_department_id')
				->join('chat_history', 'chat_history.chat_id', '=', 'chat.id')
				->join('user_client_chat', 'user_client_chat.chat_id', '=', 'chat.id')
				->join('user_client', 'user_client.id', '=', 'user_client_chat.user_client_id')
				->join('user_auth AS ua_client', 'user_client.user_auth_id', 'ua_client.id')
				->join('company_user_company_department', 'chat.comp_user_comp_depart_id_current', 'company_user_company_department.id')
				->join('company_user', 'company_user_company_department.company_user_id', 'company_user.id')
				->join('user_auth AS ua_agent', 'company_user.user_auth_id', 'ua_agent.id')
				->where('ua_agent.id', Auth::id())
				->where('chat.company_id', Crypt::decrypt(session('companyselected')['id']))
				->whereIn('chat.company_department_id', $departments)
				->whereNull('chat.ticket_id')
				->where('chat.status', 'CLOSED')
				->where('chat.type', 'DEFAULT')
				->select(
					'chat.id AS chat_id',
					'chat.id AS number',
					'chat.company_id',
					'chat.status',
					'chat.type',
					'chat.company_department_id',
					'chat.comp_user_comp_depart_id_current',
					'chat.created_at',
					'chat.updated_at as end',
					'chat.user_agent',
					DB::raw('DATE_FORMAT(chat.created_at,"%d/%m/%Y") as date'),
					DB::raw('DATE_FORMAT(chat.created_at,"%H:%i:%s") as time'),
					'company_department.name as department',
                    'company_department.type as dep_type',
					'ua_client.name',
					'ua_client.email',
					'ua_client.id as client_id',
                    'ua_client.builderall_account_data',
					'ua_agent.name AS operator',
                    'ua_agent.id AS operator_id',
                    'ua_agent.email AS operator_email',
					'chat_history.content'
				)
				->groupBy('chat.id')
				->orderByDesc('chat.updated_at')
				->skip($data['skip'])
				->take($data['take'])
				->get();

			foreach ($result as $row) {
				$row->chat_id = Crypt::encrypt($row->chat_id);
				$row->company_id = Crypt::encrypt($row->company_id);
				$row->company_department_id = Crypt::encrypt($row->company_department_id);
				$row->comp_user_comp_depart_id_current = Crypt::encrypt($row->comp_user_comp_depart_id_current);
				$row->email = Client::getCleanEmail($row->email, Crypt::decrypt($row->company_id));
				$row->client_id = Crypt::encrypt($row->client_id);
                $row->operator_id = Crypt::encrypt($row->operator_id);
			}
			return response()->json([
				'chats' => $result,
				'company_id' => session('companyselected')['id'],
				'skip' => $data['skip'] + $data['take'],
			]);
		} catch (\Exception $e) {
			Logger::reportException($e, [], ['chat-controller', 'getChatsRobot'], false);
		}
    }

	public function getAgentTablesCount(Request $request)
	{

		$request_departments = [];
        $departments = [];

        foreach ($request->departments as $row) {
            array_push($request_departments,json_decode($row, true));
        }

        foreach ($request_departments as $dep) {
            array_push($departments, Crypt::decrypt($dep['id']));
        }

		$firstCount	= Chat::join('company_department', 'company_department.id', 'chat.company_department_id')
			->join('chat_history', 'chat_history.chat_id', 'chat.id')
			->join('user_client_chat', 'user_client_chat.chat_id', 'chat.id')
			->join('user_client', 'user_client.id', 'user_client_chat.user_client_id')
			->join('user_auth', 'user_auth.id', 'user_client.user_auth_id')
			->select(
				DB::raw("
					IF(`chat`.`type` = 'DEFAULT' AND `chat`.`status` = 'OPENED', 'queue',
						IF(`chat`.`type` = 'TRANSFERED' AND `chat`.`status` = 'IN_PROGRESS', 'transferred', '')) AS type,
					COUNT(DISTINCT `chat`.`id`) as count
				")
			)
			->where('chat.company_id', Crypt::decrypt(session('companyselected')['id']))
			->whereIn('chat.company_department_id', $departments)
			->whereNull('chat.ticket_id')
			->where(function ($f) {
				$f->where([['chat.type', 'DEFAULT'], ['chat.status', 'OPENED']])
					->orWhere([['chat.type', 'TRANSFERED'], ['chat.status', 'IN_PROGRESS']]);
			})
			->groupBy(DB::raw(1));

		$secondCount = Chat::join('company_department', 'company_department.id', 'chat.company_department_id')
			->join('user_client_chat', 'user_client_chat.chat_id', 'chat.id')
			->join('user_client', 'user_client.id', 'user_client_chat.user_client_id')
			->join('user_auth AS ua_client', 'user_client.user_auth_id', 'ua_client.id')
			->join('company_user_company_department', 'chat.comp_user_comp_depart_id_current', 'company_user_company_department.id')
			->join('company_user', 'company_user_company_department.company_user_id', 'company_user.id')
			->join('user_auth AS ua_agent', 'company_user.user_auth_id', 'ua_agent.id')
			->join(
				DB::raw('(
					SELECT * FROM
						(SELECT id AS history_id, chat_id FROM chat_history ORDER BY chat_id, id DESC LIMIT 9999999999
					) sub
					GROUP BY sub.chat_id
				) sub'),
				function ($join) {
					$join->on('chat.id', 'sub.chat_id');
				}
			)
			->select(
				DB::raw("
					'in_progress' AS type,
					COUNT(DISTINCT `chat`.`id`) as count
				")
			)
			->whereIn('chat.company_department_id', $departments)
			->whereNull('chat.ticket_id')
			->where([
				['ua_agent.id', Auth::id()],
				['chat.company_id', Crypt::decrypt(session('companyselected')['id'])],
				['chat.status', 'IN_PROGRESS'],
				['chat.type', 'DEFAULT']
			]);

		$lastCount = Chat::join('company_department', 'company_department.id', 'chat.company_department_id')
			->join('chat_history', 'chat_history.chat_id', 'chat.id')
			->join('user_client_chat', 'user_client_chat.chat_id', 'chat.id')
			->join('user_client', 'user_client.id', 'user_client_chat.user_client_id')
			->join('user_auth AS ua_client', 'user_client.user_auth_id', 'ua_client.id')
			->join('company_user_company_department', 'chat.comp_user_comp_depart_id_current', 'company_user_company_department.id')
			->join('company_user', 'company_user_company_department.company_user_id', 'company_user.id')
			->join('user_auth AS ua_agent', 'company_user.user_auth_id', 'ua_agent.id')
			->select(
				DB::raw("
					CASE `chat`.`status`
						WHEN 'CLOSED' THEN 'closed'
						WHEN 'RESOLVED' THEN 'resolved'
						WHEN 'CANCELED' THEN 'canceled'
					END AS type,
					COUNT(DISTINCT `chat`.`id`) as count")
			)
			->where([
				['ua_agent.id', Auth::id()],
				['chat.company_id', Crypt::decrypt(session('companyselected')['id'])],
				['chat.type', 'DEFAULT']
			])
			->whereIn('chat.company_department_id', $departments)
			->whereIn('chat.status', ['CLOSED', 'RESOLVED', 'CANCELED'])
			->whereNull('chat.ticket_id')
			->groupBy(DB::raw(1))
			->union($firstCount)
			->union($secondCount)
			->get();

		$count = [];
		foreach ($lastCount as $row) {
			$count[$row->type] = $row->count;
		}

		return response()->json($count);
	}

	public function getClientChats()
	{
		$result = Chat::join('company_department', 'company_department.id', 'chat.company_department_id')
			->join('company_department_settings', 'company_department_settings.company_department_id', 'company_department.id')
			->join('user_client_chat', 'user_client_chat.chat_id', 'chat.id')
			->join('user_client', 'user_client.id', 'user_client_chat.user_client_id')
			->join('user_auth AS ua_client', 'user_client.user_auth_id', 'ua_client.id')
			// ->join(
			// 	DB::raw('(
			// 		SELECT * FROM
			// 			(SELECT id AS history_id,chat_id FROM chat_history ORDER BY chat_id, id DESC LIMIT 9999999999) sub
			// 			GROUP BY sub.chat_id
			// 	) sub'),
			// 	function ($join) {
			// 		$join->on('chat.id', 'sub.chat_id');
			// 	}
			// )
			->leftJoin('company_user_company_department', 'company_user_company_department.id', 'chat.comp_user_comp_depart_id_current')
			->leftJoin('company_user', 'company_user.id', 'company_user_company_department.company_user_id')
			->leftJoin('user_auth AS ua_agent',	'ua_agent.id', 'company_user.user_auth_id')

			->select(
				'chat.id AS chat_id',
				'chat.id AS number',
				'chat.company_id',
				'chat.company_department_id',
				'chat.comp_user_comp_depart_id_current',
				'chat.status',
				'chat.type',
				'chat.priority',
				'chat.user_agent',
				DB::raw('DATE_FORMAT(chat.created_at, "%d/%m/%Y") AS date'),
				DB::raw('DATE_FORMAT(chat.created_at, "%H:%i:%s") AS time'),
				'chat.created_at',
				'company_department.name AS department',
				'company_department_settings.settings',
				'ua_agent.name AS agent',
                'company_department.type AS dep_type',
				DB::raw('IF((SELECT company_user_company_department_id FROM chat_history WHERE chat_id = chat.id ORDER BY id DESC LIMIT 1) IS NULL, 0, 1) AS answered')
			)
			->where('chat.company_id', Crypt::decrypt(session('companyselected')['company_id']))
			->where('ua_client.id', Auth::id())
			->whereNull('chat.ticket_id')
			->where(function ($f) {
				$f->where('chat.type', 'DEFAULT')
					->orWhere('chat.type', 'TRANSFERED');
			})
			->orderBy('chat.id', 'desc')
            ->groupBy('chat_id')
			->get();



		foreach ($result as $row) {

			$row->chat_id = Crypt::encrypt($row->chat_id);
			$row->company_id = Crypt::encrypt($row->company_id);
			$row->company_department_id = Crypt::encrypt($row->company_department_id);
			if ($row->comp_user_comp_depart_id_current !== null) {
				$row->comp_user_comp_depart_id_current = Crypt::encrypt($row->comp_user_comp_depart_id_current);
			}

			$settings = json_decode($row->settings);
			$row['inactivityMessage']  = (int) $settings->quant_limity->inactivityMessage;
			$row['timewait'] =  (int) $settings->quant_limity->timewait;
		}



		return response()->json([
			'queue' => $result,
			'company_id' => session('companyselected')['company_id']
		]);
	}

	public function setEmployee(Request $request, $next_attendant = null)
	{
		
        //return $request;
		/** begin */
		try {
			/** validator */
			$validator = Validator::make($request->all(), [
				'chat' => 'required|array',
				'company_department_id' => 'required|string',
				'is_admin' => 'required|boolean',
			]);

			if ($validator->fails()) {
				return response($validator->errors(), 400);
			}
			$id = intval(Crypt::decrypt($request->chat['chat_id']));

			if (Chat::where([['id', $id], ['comp_user_comp_depart_id_current', NULL], ['status', 'OPENED']])->exists()) {
				if(isset($next_attendant)){
					$session = CompanyUserCompanyDepartment::join('company_department', 'company_user_company_department.company_department_id', 'company_department.id')
					->join('company_user', 'company_user_company_department.company_user_id', 'company_user.id')
					->select('company_user.id as company_user_id', 'company_department.id as company_department_id', 'company_user_company_department.id as company_user_company_department_id')
					->where('company_user.user_auth_id', $next_attendant->user_auth_id)
					->where('company_user.company_id', Crypt::decrypt(session('companyselected')['company_id']))
					->get();

					foreach ($session as $row) {
						$row->company_user_id = Crypt::encrypt($row->company_user_id);
						$row->company_department_id = Crypt::encrypt($row->company_department_id);
						$row->company_user_company_department_id = Crypt::encrypt($row->company_user_company_department_id);
					}

				}else{
					$session = session('company_user_company_departments');
				}

				if ($request->is_admin) {

					$company_user_company_department_id = false;

					foreach ($session as $row) {
						if ((int) Crypt::decrypt($row->company_department_id) === (int) Crypt::decrypt($request->chat['companyDepartmentId'])) {
							$company_user_company_department_id = (int) Crypt::decrypt($row->company_user_company_department_id);
						}
					}

					if (!$company_user_company_department_id) {
						$cucdc = new CompanyUserCompanyDepartmentController();
						$cucd = $cucdc->addAgentToDepartment($request, $session);
					
						$vars = get_object_vars($cucd);

						$company_user_company_department_id = (int) Crypt::decrypt($vars['original']['cucdic']);
						$session = $vars['original']['session_user_cucd'];
					}
				} else {
					$department = intval(Crypt::decrypt($request->chat['companyDepartmentId']));
					if($next_attendant == null){
						$company_user_company_department_id = CompanyUserCompanyDepartment::join('company_user', 'company_user.id', 'company_user_company_department.company_user_id')
						->select('company_user_company_department.id')
						->where('company_user.user_auth_id', Auth::id())
						->where('company_user_company_department.company_department_id', $department)
						->first()
						->id;
					}else{
						$company_user_company_department_id = CompanyUserCompanyDepartment::join('company_user', 'company_user.id', 'company_user_company_department.company_user_id')
						->select('company_user_company_department.id')
						->where('company_user.user_auth_id', $next_attendant->user_auth_id)
						->where('company_user_company_department.company_department_id', $department)
						->first()
						->id;
					}
				}

                if (Chat::where('id', $id)->whereNull('update_status_in_progress')->exists()) {
                    $update_status_in_progress = Carbon::now()->toDateTimeString();
                } else {
                    $q = Chat::where('id', $id)->first();
                    $update_status_in_progress = $q->update_status_in_progress;
                }

				$result = Chat::where('id', $id)
					->update([
						'comp_user_comp_depart_id_current' => $company_user_company_department_id,
						'status' => 'IN_PROGRESS',
						'type' => 'DEFAULT',
						'update_status_in_progress' => $update_status_in_progress,
						'queue_time' => Chat::calculatorTimeQueue($request->chat['created_at'], 'IN_PROGRESS', null)
					]);

				if ($result) {
					$history = ChatHistory::create([
						'chat_id' => $id,
						'company_user_company_department_id' => $company_user_company_department_id,
						'type' => 'EVENT',
						'content' => 'bs-joined-the-chat',
						'created_by' => is_null($next_attendant) ? Auth::id() : $next_attendant->user_auth_id,
					]);

					if ($history) {

                        ChatWorkingTimes::insert($id, $company_user_company_department_id, $history->id);

						if(is_null($next_attendant)){
							$user = Auth::user();
						}else{
							$user =$next_attendant->user_auth;
						}

						$history->chat_id = Crypt::encrypt($id);
						$msg = json_decode(json_encode($history), true);
						$user = json_decode(json_encode($user), true);
						$employee = json_decode(json_encode(['employee' => true]), true);
						$result =  array_merge($msg, $user, $employee);

						broadcast(new MessageSent($result));

						broadcast(new ChatStatusChanger([
							'chat_id' => $request->chat['chat_id'],
							'number' => Crypt::decrypt($request->chat['chat_id']),
							'status' => 'IN_PROGRESS'
						]));

						$removed_chat_queue_position = $this->getQueuePosition($request->chat['chat_id'], $request->chat['companyDepartmentId']);
						broadcast(new QueueUpdated([
							'chat_id' => $request->chat['chat_id'],
							'companyDepartmentId' => $request->chat['companyDepartmentId'],
							'number' => Crypt::decrypt($request->chat['chat_id']),
							'removed_chat_queue_position' => $removed_chat_queue_position,
							'company_id' => session('companyselected')['id'],
							'action' => 'splice'
						]));

                        $realtime = new SendRealtime(Crypt::decrypt($request->chat['chat_id']), null);
                        $realtime->updateTableInProgress();

						broadcast(new TransferredUpdated([
							'chat_id' => $request->chat['chat_id'],
							'number' => Crypt::decrypt($request->chat['chat_id']),
							"company_id" => session('companyselected')['id'],
							"action" => 'splice'
						]));

						broadcast(new ClientQueueStatus([
							'chat_id' => $request->chat['chat_id'],
							'number' => Crypt::decrypt($request->chat['chat_id']),
							'user_client_id' => Crypt::encrypt(UserClientChat::select('user_client_id')->where('chat_id', $id)->first()->user_client_id),
							'comp_user_comp_depart_id_current' => Crypt::encrypt($company_user_company_department_id),
							'company_id' => session('companyselected')['id'],
							'sent_by' => is_null($next_attendant) ? Auth::user()->name : $next_attendant->name,
							'agent' => is_null($next_attendant) ? Auth::user()->name : $next_attendant->name,
							'agent_email' => is_null($next_attendant) ? Auth::user()->email : $next_attendant->user_auth->email,
							'agent_id' => is_null($next_attendant) ? Crypt::encrypt(Auth::user()->id) : Crypt::encrypt($next_attendant->user_auth_id),
							'agent_answered' => 1,
							'status' => 'IN_PROGRESS',
                            'content' => $history->content
						]));

						return response()->json([
							'status' => true,
							'flag' => 1,
							'comp_user_comp_depart_id_current' => Crypt::encrypt($company_user_company_department_id),
							'session_user_cucd' => $session,
							'is_admin' => $request->is_admin,
						]);
					} else {
						return response()->json([
							'status' => false,
							'message' => 'bs-error-catching-the-chat',
							'flag' => 2,
						]);
					}
				} else {
					return response()->json([
						'status' => false,
						'message' => 'bs-error-catching-the-chat',
						'flag' => 3,
					]);
				}
			} else {
				return response()->json([
					'status' => false,
                    'message' => 'bs-another-agent-has-already-caught-this-chat',
					'flag' => 0
				]);
			}
		} catch (\Exception $e) {
			echo $e;
			Logger::reportException($e, [], ['company_user_company_department-controller', 'addAgentToDepartment'], false);
		}
	}

	public function store(Request $request)
	{
		
		try{
			// armazeno todo o request na variavel
			$data =  $request->all();
			// crio o chat
			$status = $data['status'];

			if ($status == 'OPENED') {
				$is_robot = false;
			} else if ($status == 'ROBOT') {
				$is_robot = true;
			}

			if ($status == 'ROBOT_TO_OPENED') {

				$settings = DB::table('company_department_settings')
				->where('company_department_id', Crypt::decrypt(request('company_department')['id']))
				->first()->settings;
				$settings = json_decode($settings);
				$departmentTimezone = $settings->general->language; // Timezone do departamento.

				$dateTime = new \DateTime(
					'now',
					new \DateTimeZone($departmentTimezone)
				);
				$dayOfTheWeek = $dateTime->format('N');

				if ($dayOfTheWeek == 7) {
					$dayOfTheWeek = 0;
				}

				$openHour = $settings->chat->openDepartment[$dayOfTheWeek]->am . ":00";
				$closeHour = $settings->chat->openDepartment[$dayOfTheWeek]->ap . ":00";

				$serverDate = date('d/m/Y H:i:s');
				$date = new \DateTime("now", new \DateTimeZone($departmentTimezone));
				$date = $date->format('d/m/Y');
				
				$openDate = $date . " " . $openHour;
				$closeDate = $date . " " . $closeHour;

				$serverDateToUTC = Carbon::createFromFormat('d/m/Y H:i:s', $serverDate, "UTC"); // Criação da data do server para UTC
				if ($departmentTimezone == 'UTC') {
					$openDateToUTC = Carbon::createFromFormat('d/m/Y H:i:s', $openDate, $departmentTimezone);
					$closeDateToUTC = Carbon::createFromFormat('d/m/Y H:i:s', $closeDate, $departmentTimezone);
				} else {
					$openDateToUTC = Carbon::createFromFormat('d/m/Y H:i:s', $openDate, $departmentTimezone)->setTimezone('UTC'); // Conversão da data de abertura para UTC
					$closeDateToUTC = Carbon::createFromFormat('d/m/Y H:i:s', $closeDate, $departmentTimezone)->setTimezone('UTC'); // Conversão da data de fechamento para UTC
				}

				if (($serverDateToUTC >= $openDateToUTC) && ($serverDateToUTC <= $closeDateToUTC)) {
					// DEPARTAMENTO ABERTO
				}else{
					return response()->json(['close_department'=>true,'message'=>'bs-closed-department']);
				}

				Chat::find(Crypt::decrypt($request['id']))->update([
					'status' => 'OPENED',
					'created_at' => Carbon::now()->toDateTimeString()
				]);

				$result = Chat::find(Crypt::decrypt($request['id']));

			} else {
				$settings = DB::table('company_department_settings')
				->where('company_department_id', Crypt::decrypt(request('company_department')['id']))
				->first()->settings;
				$settings = json_decode($settings);
				$departmentTimezone = $settings->general->language; // Timezone do departamento.

				$dateTime = new \DateTime(
					'now',
					new \DateTimeZone($departmentTimezone)
				);
				$dayOfTheWeek = $dateTime->format('N');

				if ($dayOfTheWeek == 7) {
					$dayOfTheWeek = 0;
				}

				$openHour = $settings->chat->openDepartment[$dayOfTheWeek]->am . ":00";
				$closeHour = $settings->chat->openDepartment[$dayOfTheWeek]->ap . ":00";

				$serverDate = date('d/m/Y H:i:s');
				$date = new \DateTime("now", new \DateTimeZone($departmentTimezone));
				$date = $date->format('d/m/Y');
				
				$openDate = $date . " " . $openHour;
				$closeDate = $date . " " . $closeHour;

				$serverDateToUTC = Carbon::createFromFormat('d/m/Y H:i:s', $serverDate, "UTC"); // Criação da data do server para UTC
				if ($departmentTimezone == 'UTC') {
					$openDateToUTC = Carbon::createFromFormat('d/m/Y H:i:s', $openDate, $departmentTimezone);
					$closeDateToUTC = Carbon::createFromFormat('d/m/Y H:i:s', $closeDate, $departmentTimezone);
				} else {
					$openDateToUTC = Carbon::createFromFormat('d/m/Y H:i:s', $openDate, $departmentTimezone)->setTimezone('UTC'); // Conversão da data de abertura para UTC
					$closeDateToUTC = Carbon::createFromFormat('d/m/Y H:i:s', $closeDate, $departmentTimezone)->setTimezone('UTC'); // Conversão da data de fechamento para UTC
				}

				if (($serverDateToUTC >= $openDateToUTC) && ($serverDateToUTC <= $closeDateToUTC)) {
					$result = Chat::create([
						'company_id' => Crypt::decrypt(session('companyselected')['company_id']),
						'company_department_id' => Crypt::decrypt($data['company_department']['id']),
						'type' => 'DEFAULT',
						'status' => $status,
						'priority' => 'NORMAL',
						'user_agent' => $_SERVER['HTTP_USER_AGENT'],
						'created_by' => Auth::id(),
						'is_robot' => $is_robot,
					]);

					UserClientChat::create([
						'user_client_id' => Crypt::decrypt(session('companyselected')['user_client_id']),
						'chat_id' => $result->id
					]);
				}else{
					return response()->json(['close_department'=>true,'message'=>'bs-closed-department']);
				}
			}

			if(config('app')['is_helpdesk'] == false && config('app.env') != 'sandbox' && config('app.env') != 'local' && $status !== 'ROBOT'){
				$bot = new TelegramBot();
				$bot->notificationAllUsers(session('companyselected'), 'chat', 'create', $result->id, $data['company_department']['id'], 'Novo chat foi adicionado na fila.', request('onlineUsers'));
			}

			// Email
			// $send_mail = new MailAdmin(Crypt::decrypt(session('companyselected')['company_id']), auth()->user()->id, Crypt::decrypt($data['company_department']['id']));
			// $send_mail->ticketchatOpened('CHAT', request('onlineUsers'));
			try {
				$send_mail = new sendEmailCustom($result->id,auth()->user(),'','','','','',session('companyselected')['hash_code']);
				$send_mail->ticketchatOpened('CHAT', request('onlineUsers'), $data['company_department']);
			} catch (\Throwable $th) {
				//throw $th;
			}
			
			if($data['checkout']) {
				Checkout::chat_extra_data($result->id);
			}
		
			$i = 0;
			foreach ($data['questions'] as $row) {
				if (isset($data['answers'][$i]) && trim($data['answers'][$i]) !== '' && $status !== 'ROBOT')  {
					$answers = $data['answers'][$i];
					if (isset($data['answers_images']) && count($data['answers_images'][$i])) {
						$images = $data['answers_images'][$i];
						foreach ($images as $image) {
							$b64 = explode(',', $image)[1];
							$image_name = Crypt::encrypt(uniqid(md5(uniqid() . microtime())));
							$img_data = base64_decode($b64);
							$dir = '..' . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'company' . DIRECTORY_SEPARATOR . Crypt::decrypt(session('companyselected')['company_id']) . DIRECTORY_SEPARATOR . 'chat' . DIRECTORY_SEPARATOR . $result->id . DIRECTORY_SEPARATOR;
							$filename = $image_name . '.png';
		
							if (is_dir($dir)) {
								$success = file_put_contents($dir.$filename, $img_data);
							} else {
								mkdir($dir, 0755, true);
								$success = file_put_contents($dir.$filename, $img_data);
							}
		
							if ($success) {
								$answers = str_replace($image, 'chat/files/'. Crypt::encrypt($result->id) .'/'.$filename, $answers);
							}
						}
					}
					//TODO PRIMEIRO LOOP É PARA PEGAR A DESCRIÇÃO DO CHAT
					if($i == 0){
						DB::table('chat')
						->where('id', $result->id)
						->update([
							'description' => $answers,
						]);
					}else{
						TicketChatAnswer::create([
							'company_depart_settings_question_id' => Crypt::decrypt($row['id']),
							'chat_id' => $result->id,
							'answer' => $answers
						]);
					}
				}
				$i++;
			}	

			if (request('is_faqRobot')) {
				foreach (request('messages') as $key) {
					if(isset($key['user_id'])){
						$result->chat_histories()->create([
							'type'  => 'TEXT',
							'content'  => $key['content'],
							'created_by' => $key['user_id'],
						]);
					}else{
						if(isset($key['content']['title'])){
							$result->chat_histories()->create([
								'type'  => 'FAQ_ROBOT',
								'content'  => json_encode($key['content']),
								'created_by' => -1,
							]);
						}
					}
				}
			}
			if ($status == 'ROBOT') {
				$result->chat_histories()->create([
					'type'  => 'ROBOT',
					'content'  => json_encode($data['first_message']),
					'created_by' => -1,
				]);
			}

			// crio uma mensagem type event contendo uma chave de tradução que indica que o cliente inciou o chat
			else {
				$result->chat_histories()->create([
					'type'  => 'EVENT',
					'content'  => 'bs-started-the-chat',
					'created_by' => Auth::id(),
				]);
			}

			$settings = DB::table('company_department_settings')
				->select('settings')
				->where('company_department_id', Crypt::decrypt($data['company_department']['id']))
				->first()
				->settings;

			$obj = json_decode($settings, true);
			
			if (isset($obj['chat']['arrayTranslate']) && $status !== 'ROBOT') {
				$msg_open = $obj['chat']['arrayTranslate']['msgOpen'];

				$user_lang = explode("_", auth()->user()->language)[1];
				foreach ($msg_open as $row) {
					if(isset($row['code'])){
						if ($row['code'] == $user_lang) {
							if($row['text'] !== '') {
								$result->chat_histories()->create([
									'type'  => 'OPEN',
									'content'  => $row['text'],
									'created_by' => Auth::id(),
								]);
							}
						}
					}
				}
			} else if ($obj['chat']['msgOpen'] !== null && $status !== 'ROBOT') {
				$result->chat_histories()->create([
					'type'  => 'OPEN',
					'content'  => $obj['chat']['msgOpen'],
					'created_by' => Auth::id(),
				]);
			}

			if ($status == 'ROBOT_TO_OPENED') {
				$status = 'OPENED';
				broadcast(new ClientQueueStatus([
					'chat_id' => Crypt::encrypt($result->id),
					'company_id' => Crypt::encrypt($result->company_id),
					'status' => $status,
					'user_client_id' => session('companyselected')['user_client_id']
				]));

				$arrayStatusChanger = [
					'chat_id' => Crypt::encrypt($result->id),
					'status'  => $status,
				];

				if (isset($data['company_department'])) {
					$arrayStatusChanger = array_merge($arrayStatusChanger, ['company_department_id' => $data['company_department']['id']]);
				}

				broadcast(new ChatStatusChanger($arrayStatusChanger));
			} else {
				broadcast(new ClientQueueStatus([
					'company_id' => Crypt::encrypt($result->company_id),
					'user_client_id' => session('companyselected')['user_client_id'],
					'action' => 'push',
					'chat_id' => Crypt::encrypt($result->id),
					'number'  => $result->id,
					'status' => $result->status,
					'created_at' => $result->created_at,
					'department' => $data['company_department']['name'],
				]));
			}

			$next_attendant = AutomaticChat::distribution($data['company_department']['id'], request('tz'));
			
			if ($status == 'OPENED') {
				if(is_null($next_attendant->user_auth_id)){
					$realtime = new SendRealtime($result->id, 'push');
					$realtime->updateTableQueue();
					$message = 'bs-new-chat-added-to-the-queue';
				}else{
					$message = Feedback::tl('bs-new-chat-has-been-linked-to-an-attendant', $next_attendant->language).': '.$next_attendant->name;
				}
			
				broadcast(new GlobalNotification([
					// título da notificação
					'title' => 'bs-chat',
					// O corpo(mensagem) da notificação.
					'body' => $message,
					// A URL da imagem usada como um ícone da notificação.
					'icon' => 'https://builderall.com//franquias/2/73748/editor-html/3484811.png',
					// (Opcional) URL para qual a notificação deve redirecionar
					'url' => '',
					//se for true, a notificação fica sem som (PS: está sendo disparado som no browser, então pode ficar true aqui que vai sair som igual)
					'silent' => false,
					// numero de identificação do chat / ticket
					'number' => $result->id,
					// identifica se a notificação é de chat ou ticket
					'type' => 'chat',
					// identifica o status do chat/ticket para fazer a lógica do disparo
					'status' => $status,
					// identifica o id do departamento para qual a notificação deve ser dispara
					'company_department_id' => Crypt::encrypt($result->company_department_id),
					// envia o company user id do atendente que precisa receber a notificação (caso seja individual)
					//'company_user_id' => $company_user_id,
					// Paramêtro de conexão no canal global de notificação, sempre deve ser passado
					'company_id' => session('companyselected')['company_id'],
					// Alerta para atendentes que foram vinculados ao um chat 
					'timermessager' => is_null($next_attendant->user_auth_id) ? false : true
				]));
			}

			
			$department_id = request('company_department')['id'];

			if($next_attendant->cucd_id != null){
				
				$content = new Request();
				$content['chat'] = [
					"companyDepartmentId" => $department_id, 
					"chat_id" => Crypt::encrypt($result->id), 
					"created_at" => $result->created_at, 
				];
				$content['is_admin'] = $next_attendant->is_admin;
				$content['company_department_id'] = $department_id;

				$this->setEmployee($content, $next_attendant);
				
				$requestTabs = new Request();
				$requestTabs['chat'] = Chat::get($result->id, 'add');

				if($requestTabs['chat']['status'] == 'IN_PROGRESS'){
					$this->tabs($requestTabs, $next_attendant);
				}
			}

			// retorno o id do chat criado para que o mesmo possa ser aberto no front
			return response()->json([
				'chat_id' => Crypt::encrypt($result->id),
				'number'  => $result->id,
				'company_id' => Crypt::encrypt($result->company_id),
				'company_department_id' => Crypt::encrypt($result->company_department_id),
				'comp_user_comp_depart_id_current' => NULL,
				'status' => $status,
				'type' => 'DEFAULT',
				'priority' => 'NORMAL',
				'date' => date('d/m/Y'),
				'time' => date('H:i:s'),
				'created_at' => $result->created_at,
				'department' => $data['company_department']['name'],
				'dep_type' => $data['company_department']['type'],
				'settings' => $settings,
				'agent' => NULL,
				'answered' => 0,
				'inactivityMessage' => 0,
				'timewait' => 0
			]);
		} catch (\Exception $e) {
			echo $e;
			Logger::reportException($e, [], ['Chat-controller', 'store'], false);
		}
	}
	
	public function storeRobot(Request $request) {
		$string = request('paste');
		$paste = strtolower(preg_replace('/[^a-zA-Z0-9]/', '',  iconv('UTF-8', 'ASCII//TRANSLIT', $string)));
		$var = 'vazio';
		$hostname = request('hostname');
		$this->setPython('Alltools', auth()->user()->language, request('text'));
		// $var = shell_exec('/usr/bin/python3.9 /srv/ba-support/chatbot/chatBot.py reply -c '.Crypt::decrypt(session('companyselected')['id']).' -t '.$paste.' -l '.auth()->user()->language.' "'.request('text').'"');
		
		if($hostname == 'ba-support.builderall.io' || $hostname == 'localhost'){
			echo $this->getValuePython('HOMOLOG');
			$var = shell_exec($this->getValuePython('HOMOLOG'));
		}

		if($hostname == 'ba-support.builderall.com' || $hostname == 'https://hs.builderall.com'){
			echo $this->getValuePython('PRODUCTION');
			$var = shell_exec($this->getValuePython('PRODUCTION'));
		}
		// echo "========] - 0s 106ms/step [['CHECKRESULTEXIST144'], ['CHECKRESULTEXIST145'], ['CHECKRESULTEXIST148'], ['CHECKRESULTEXIST155'], ['CHECKRESULTEXIST602']]";
		// echo "============================] - 0s 107ms/step ['CHECKRESULTEXIST536']";
		// echo "- ETA: 0s===============] - 0s 107ms/step ['CHECKRESULTEXISTAGENT']";
		echo "- ETA: 0s===============] - 0s 107ms/step ['CHECKRESULTUNDERSTAND']";
		// echo $var;
	}

	public function setPython($paste, $language, $text) {
		// add to return error ' 2>&1' 
		putenv('HOMOLOG=/usr/bin/python3.9 /srv/ba-support/chatbot/chatBot.py reply -c  '.Crypt::decrypt(session('companyselected')['id']).' -t '.$paste.' -l '.$language.' "'.base64_encode($text).'"');
		putenv('PRODUCTION=/usr/local/bin/python3.10 /srv/ba-support/chatbot/chatBot.py reply -c  '.Crypt::decrypt(session('companyselected')['id']).' -t '.$paste.' -l '.$language.' "'.base64_encode($text).'"');
	}

	public function getValuePython($nome_variavel) {
		return getenv($nome_variavel);
	}
	
	public function getRobot(Request $request){
		$string = str_replace("CHECKRESULTEXIST", '', $request->array);
		$string = str_replace("'", '"', $string);
		$ids = json_decode($string);
		
		$result = DB::table('company_faq_robot_tools as cfrt')
			->join('company_faq_robot', 'company_faq_robot.id', 'cfrt.company_faq_robot_id')
			->where('company_faq_robot.company_id', Crypt::decrypt(session('companyselected')['id']))
			->whereIn('cfrt.id', $ids)
			->select('cfrt.id', 'cfrt.title', 'cfrt.description')
			// ->where('cfrt.language', auth()->user()->language == 'pt_BR' ? 'pt_BR' : 'en_US')
			->whereNull('cfrt.deleted_at')
			->get();


		return json_encode($result);

		//TRADUZIR AS RESPOSTAS PARA LINGUAGEM DO CLIENTE
		// if(auth()->user()->language == 'pt_BR' || auth()->user()->language == 'en_US'){
		// 	return json_encode($result);
		// }else{
		// 	$url = "https://translation.googleapis.com/language/translate/v2?key=" . env('MIX_KEY_GOOGLE_TRANSLATOR');
		// 	foreach ($result as $key) {
		// 		// TITLE T / DESCRIPTION D
		// 		$postDataT = array(
		// 			'target' => 'es_ES',
		// 			'q' => $key->title
		// 		);
		// 		$postDataD = array(
		// 			'target' => 'es_ES',
		// 			'q' => $key->description
		// 		);
		// 		$optionsT = array(
		// 		'http' => array(
		// 			'header' => "Content-type: application/json\r\n",
		// 			'method' => 'POST',
		// 			'content' => json_encode($postDataT)
		// 		)
		// 		);
		// 		$optionsD = array(
		// 		'http' => array(
		// 			'header' => "Content-type: application/json\r\n",
		// 			'method' => 'POST',
		// 			'content' => json_encode($postDataD)
		// 		)
		// 		);
		// 		$contextT = stream_context_create($optionsT);
		// 		$contextD = stream_context_create($optionsD);
		// 		$responseT = file_get_contents($url, false, $contextT);
		// 		$responseD = file_get_contents($url, false, $contextD);
		// 		$dataT = json_decode($responseT, true);
		// 		$dataD = json_decode($responseD, true);

		// 		if(isset($dataT['data']['translations'][0]['translatedText'])){
		// 			$key->title = $dataT['data']['translations'][0]['translatedText'];
		// 		}

		// 		if(isset($dataD['data']['translations'][0]['translatedText'])){
		// 			$key->description = $dataD['data']['translations'][0]['translatedText'];
		// 		}
		// 	}
		// 	return json_encode($result);
		// }
	}

	public function upload(Request $request)
	{
		$data = $request->All();

		if ($request->is_client) {
			Chat::where('id', Crypt::decrypt($request->chat_id))->update(['user_agent' => $_SERVER['HTTP_USER_AGENT'], 'updated_at' => DB::raw('updated_at')]);

			if ($request->is_ticket) {
				DB::table('ticket')
					->where('id', $request->id_ticket) // mantendo padronizacao
					->update([
						'updated_at' => DB::raw('updated_at'),
						'user_agent' => $_SERVER['HTTP_USER_AGENT']
					]);
			}
		}

		if (isset(session('companyselected')['user_client_id'])) {
			$company_id = Crypt::decrypt(session('companyselected')['company_id']);
			$company_user_company_department_id = NULL;
		} else {
			$company_id = Crypt::decrypt(session('companyselected')['id']);
			$company_user_company_department_id = CompanyUserCompanyDepartment::join('company_user', 'company_user.id', 'company_user_company_department.company_user_id')
				->select('company_user_company_department.id')
				->where('company_user.user_auth_id', Auth::id())
				->where('company_user_company_department.company_department_id', Crypt::decrypt($data['company_department_id']))
				->first()
				->id;
		}

		// verifico se existe algum arquivo enviado no request...
		if ($request->hasFile('files')) {
			// para cada um dos arquivos enviados executo o laço...
			foreach ($_FILES["files"]["name"] as $i => $file) {
				// Quebro o nome completo do arquivo em várias posições e crio duas váriaveis: uma armazena o nome original e outra a extensão do arquivo...
				$explode = explode('.', $_FILES['files']['name'][$i]);
				$original_name = str_replace('.' . end($explode), "", $_FILES['files']['name'][$i]);
				$extension = '.' . strtolower(end($explode));
				// Indico o diretorio para onde o arquivo deve ser enviado...
				$dir = '..' . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'company' . DIRECTORY_SEPARATOR . $company_id . DIRECTORY_SEPARATOR . 'chat' . DIRECTORY_SEPARATOR . Crypt::decrypt($data['chat_id']) . DIRECTORY_SEPARATOR;
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

				$extImages = explode(',', $request->extImages);
				if (in_array(strtolower(end($explode)), $extImages)) {
					$type = 'IMAGE';
				} else {
					$type = 'FILE';
				}

				if ($request->is_incognito) {
					$hidden_for_client = 1;
				} else {
					$hidden_for_client = 0;
				}

				$create = ChatHistory::create([
					'chat_id' => Crypt::decrypt($data['chat_id']),
					'company_user_company_department_id' => $company_user_company_department_id,
					'type' => $type,
					'created_by' => Auth::id(),
					'content' => json_encode([
						'unique_name' => $unique_name,
						'original_name' => $original_name . $extension,

					]),
					'hidden_for_client' => $hidden_for_client,
				]);

				$user = Auth::user();

                if ($company_user_company_department_id == NULL) {
                    $gravatar = [
                        "client_email" => Auth::user()->email,
                        "client_name" => Auth::user()->name,
                        "client_id" => Crypt::encrypt(Auth::user()->id),
                    ];
                } else {
                    $gravatar = [
                        "user_email" => Auth::user()->email,
                        "user_name" => Auth::user()->name,
                        "user_id" => Crypt::encrypt(Auth::user()->id),
                    ];
                }

				$create->chat_id = $data['chat_id'];
				$msg = json_decode(json_encode($create), true);
				$user = json_decode(json_encode($user), true);
				$result =  array_merge($msg, $user, $gravatar);

				$result['created_at'] = $create->created_at;

				broadcast(new MessageSent($result));

                $user_client_id = Crypt::encrypt(UserClientChat::select('user_client_id')->where('chat_id', Crypt::decrypt($data['chat_id']))->first()->user_client_id);
                

                if (!$hidden_for_client) {
                    broadcast(new ClientQueueStatus([
                        'chat_id' => $data['chat_id'],
                        'company_id' => Crypt::encrypt($company_id),
                        'user_client_id' => $user_client_id,
                        'agent_answered' => isset(session('companyselected')['user_client_id']) ? 0 : 1,
                        'content' => $create->content,
                        'sent_by' => isset(session('companyselected')['user_client_id']) ? null : Auth::user()->name
                    ]));
                }

                if (!isset(session('companyselected')['user_client_id'])) {
                    if ($request->time_for_inactivity_message > 0) {
                        if (!$hidden_for_client) {
                            $chat = [
                                "chat_id" => Crypt::decrypt($data['chat_id']),
                                "chat_history_id" => $create->id,
                                "chat" => $request->chat,
                                "company_id" => $request->company_id,
                                "user" => Auth::user(),
                                "company_user_id" => session('companyselected')['company_user_id'],
                                "company_department_id" => $request->company_department_id,
                                "user_client_id" => $user_client_id
                            ];

                            if($request->time_for_inactivity_message == 1) {
                                $delay = 2;
                            } else {
                                $delay = $request->time_for_inactivity_message;
                            }
                            $delay = $delay - 1;
                            alertEndOfChat::dispatch($chat)->delay(now()->addMinutes($delay));
                        }
                    }
                }
			}
		}
	}
	public function files($chat_id, $filename) {
		try {
			if (isset(session('restriction')[0]) && (session('restriction')[0]->chat_admin || session('restriction')[0]->chat_alllist || session('is_admin'))) {
				$company_id = Crypt::decrypt(session('companyselected')['id']);

				// se for o cliente do chat
			} else if (isset(session('companyselected')['user_client_id'])) {
				UserClientChat::where('chat_id', Crypt::decrypt($chat_id))
					->where('user_client_id', Crypt::decrypt(session('companyselected')['user_client_id']))
					->firstOrFail();

				$company_id = Crypt::decrypt(session('companyselected')['company_id']);
				// se não, faço a lógica pro atendente
			} else {
				// CompanyUserCompanyDepartment::join('chat', 'chat.comp_user_comp_depart_id_current', 'company_user_company_department.id')
				// ->whereNull('chat.deleted_at')
				// ->where('chat.id', Crypt::decrypt($chat_id))
				// ->where('company_user_company_department.company_user_id', Crypt::decrypt(session('companyselected')['company_user_id']))
				// ->firstOrFail();

				$company_id = Crypt::decrypt(session('companyselected')['id']);
			}

			if (!isset($company_id)) {
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
				//$response->header('Content-disposition','attachment; filename="nome-do-arquivo.pdf"');

				return $response;
			}
		} catch (\Exception $e) {
			echo $e;
			Logger::reportException($e, [], ['chat-controller', 'files'], false);
		}
	}
	public function files2($chat_id, $filename)
	{
		$company_id = Chat::where('id', Crypt::decrypt($chat_id))
		->select('company_id')->firstOrFail()->company_id;

		if (!isset($company_id)) {
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
			//$response->header('Content-disposition','attachment; filename="nome-do-arquivo.pdf"');

			return $response;
		}
	}


	public function getClientActiveChats()
	{
		$result = Chat::join('user_client_chat', 'user_client_chat.chat_id', 'chat.id')
			->select(DB::raw('count(chat.id) as client_active_chats'))
			->where('user_client_chat.user_client_id', Crypt::decrypt(session('companyselected')['user_client_id']))
			->where(function ($f) {
				$f->where('chat.type', 'DEFAULT')
					->orWhere('chat.type', 'TRANSFERED');
			})
			->where(function ($f) {
				$f->where('chat.status', 'OPENED')
					->orWhere('chat.status', 'IN_PROGRESS');
			})
			->first();

		return response()->json($result);
	}

	public function getAgentActiveChats(Request $request)
	{
		$data =  $request->all();

		$validator = Validator::make($data, [
			'company_department_id' => 'required|string|max:255',
		]);

		if ($validator->fails()) {
			return response($validator->errors(), 400);
		}

		$result = Chat::join('company_user_company_department', 'company_user_company_department.id', 'chat.comp_user_comp_depart_id_current')
			->join('company_department_settings', 'company_department_settings.company_department_id', 'chat.company_department_id')
			->select(
				DB::raw('count(chat.id) as agent_active_chats'),
				'company_department_settings.settings'
			)
			->where('company_user_company_department.company_user_id', Crypt::decrypt(session('companyselected')['company_user_id']))
			->where('chat.company_department_id', Crypt::decrypt($data['company_department_id']))
			->where(function ($f) {
				$f->where('chat.type', 'DEFAULT')
					->orWhere('chat.type', 'TRANSFERED');
			})
			->where('chat.status', 'IN_PROGRESS')
			->first();

		// se a quantidade de chats ativos do atendente for 0, permite pegar o chat
		if ((int) $result->agent_active_chats === 0) {
			return response()->json([
				'status' => true,
				'flag' => 1
			]);
			// caso contrário...
		} else {
			$settings = json_decode($result->settings);
			// verifica se existe quantidadechat nas configuração
			if (isset($settings->quant_limity->quantidadechat)) {
				// verifica se a quantidadechat é 0, se sim permite o user pegar o chat
				if ((int) $settings->quant_limity->quantidadechat === 0) {
					return response()->json([
						'status' => true,
						'flag' => 2
					]);
					// verifica se os chats ativos do agente é menor que a quantidadechat da config, se sim permite o user pegar o chat
				} else if ((int) $result->agent_active_chats < (int) $settings->quant_limity->quantidadechat) {
					return response()->json([
						'status' => true,
						'flag' => 3
					]);
					// caso contrário não permite que o user pegue o chat
				} else {
					return response()->json([
						'status' => false
					]);
				}
				// caso não exista quantidadechat definida, permite o usuario a pegar o chat
			} else {
				return response()->json([
					'status' => true,
					'flag' => 4
				]);
			}
		}
	}

	public function getActiveChatsFromDepartment(Request $request)
	{
		$result = Chat::join('user_client_chat', 'user_client_chat.chat_id', 'chat.id')
			->select(DB::raw('count(*) as active_chats_from_department'))
			->where('chat.company_department_id', Crypt::decrypt($request->company_department_id))
			->where('user_client_chat.user_client_id', Crypt::decrypt(session('companyselected')['user_client_id']))
            ->whereNull('chat.deleted_at')
			->where('chat.type', 'DEFAULT')
			->whereIn('status', ['OPENED', 'IN_PROGRESS'])
			->first();

		return response()->json($result);
	}
	public function getInfoChatCheckoutOpened(Request $request)
	{
		$result = Chat::select('chat.id', 'company_department.name')
		->join('company_department', 'chat.company_department_id', 'company_department.id')
		->whereIn('company_department.type', ['checkout', 'builderall-mentor'])
		->whereIn('status', ['OPENED', 'IN_PROGRESS'])
		->whereNull('chat.ticket_id')
		->count();

		if($result == 0){
			return 'create_checkout';
		}else{
			return 'chat_checkout_opened';
		}
	}
	public function getQueuePosition($chat_id, $company_department_id)
	{
		$data =  [
			'id' => $chat_id,
			'company_department_id' => $company_department_id
		];


		$validator = Validator::make($data, [
			'id' => 'required|string',
			'company_department_id' => 'required|string',
		]);

		if ($validator->fails()) {
			return response($validator->errors(), 400);
		}

		$chat = Chat::select('updated_at')->where('id', Crypt::decrypt($data['id']))->first();

		$result = Chat::select(DB::raw('count(*) as queue_position'))
			->where('updated_at', '<=', $chat['updated_at'])
			->where('company_department_id', Crypt::decrypt($data['company_department_id']))
			->where('status', 'OPENED')
			->where('type', 'DEFAULT')
			->whereNull('ticket_id')
			->whereNull('deleted_at')
			->first();

		return response()->json($result);
	}

    public function changeRobotToTicket(Request $request) {

        $response['success'] = false;

        try {
			$chat_id = Crypt::decrypt($request->id);
            $company_department = Crypt::decrypt($request->company_department);
            $description = 'bs-turned-chat-into-ticket';
            $turn = Chat::turnIntoTicket($chat_id, null, $company_department, $description);
            if ($turn) {
                $response['ticket_id'] = Crypt::encrypt($turn->id);
                $response['success'] = true;
            }
        } catch (\Exception $e) {
			Logger::reportException($e, [], ['chat-controller', 'changeRobotToTicket'], false);
        }

        return response()->json($response);
    }

    public function clientTransfer(Request $request) {

        $response['success'] = false;

        try {

            $department_id = Crypt::decrypt($request['department_id']);
            $chat_id = Crypt::decrypt($request['chat_id']);

            $department = Company_department::find($department_id);

            if ($department) {
                Chat::find($chat_id)->update([
                    'company_department_id' => $department->id
                ]);
            }

            broadcast(new ClientQueueStatus([
                'chat_id' => $request['chat_id'],
                'company_id' => session('companyselected')['company_id'],
                'user_client_id' => session('companyselected')['user_client_id'],
                'department_name' => $department->name,
                'department_id' => Crypt::encrypt($department->id),
                'agent_answered' => 0,
            ]));

            $response['success'] = true;

        } catch (\Exception $e) {
			Logger::reportException($e, [], ['chat-controller', 'clientTransfer'], false);
            $response['success'] = false;
        }

        return response()->json($response);
    }

	public function updateStatus(Request $request)
	{
		$data = $request->all();

		$other_items_to_trigger = [
			'transferred_to_department' => false,
			'transferred_to_agent' => false,
			'deparment_name' => "",
			'agent_name' => "",
			'company_user_id' => "",
		];

        $chat_item = [];

        if (isset($data['status']) && $data['status'] != 'OPENED' && $data['action'] == 'CANCELED') {
            $data['action'] = 'RESOLVED';
        }

        $chat_id = Crypt::decrypt($data['id']);
        $turn_into_ticket_at_closing = Chat::checkTurnIntoTicketAtClosing($chat_id);
        $close_action = $data['action'] == 'CANCELED' || $data['action'] == 'CLOSED' || $data['action'] == 'RESOLVED';

        if ($turn_into_ticket_at_closing && $close_action) {
            $info = Chat::getInfoToTurnIntoTicket($chat_id);

            if ($info) {
                $company_department = $info->company_department;
                $cucd_id = $info->cucd_id;
                $description = $info->description;

                $turn_into_ticket = Chat::turnIntoTicket($chat_id, $cucd_id, $company_department, $description);

                if ($turn_into_ticket) {
                    return response()->json([
                        'status' => 'TICKET',
                        'ticket' => $turn_into_ticket,
                    ]);
                } else {
                    return response()->json([
                        'status' => false
                    ]);
                }

            }
        } else {
            switch ($data['action']) {
                case 'CANCELED':
                    $comp_user_comp_depart_id_current = false;
                    $result = Chat::where('id', Crypt::decrypt($data['chat']['id']))->update([
                        'status' => 'CANCELED',
                        'update_status_canceled' => Carbon::now()->toDateTimeString(),
                        'queue_time' => Chat::calculatorTimeQueue($request->chat['created_at'], 'CANCELED', Crypt::decrypt($data['id'])),
                    ]);
                    $content = 'bs-canceled-the-chat';
                    $status = 'CANCELED';
                    $body_notification = 'bs-the-chat-was-canceled';

                    broadcast(new ClientQueueStatus([
                        'chat_id' => $data['chat']['id'],
                        'company_id' => session('companyselected')['company_id'],
                        'user_client_id' => session('companyselected')['user_client_id'],
                        'status' => $status,
                        'agent_answered' => 0
                    ]));

                    if ($data['status'] === 'OPENED') {
                        broadcast(new QueueUpdated([
                            'chat_id' => $data['chat']['id'],
                            'company_id' => session('companyselected')['company_id'],
                            'action' => 'splice'
                        ]));

                        $rt1 = new SendRealtime(Crypt::decrypt($data['chat']['id']), 'push');
                        $rt1->updateTableCanceled();

                    } else if ($data['chat']['status'] === 'IN_PROGRESS') {
                        $chat = Chat::select('type')->where('id', Crypt::decrypt($data['chat']['id']))->first();
                        if ($chat->type === 'DEFAULT') {
                            $query = Chat::join('company_user_company_department', 'company_user_company_department.id', 'chat.comp_user_comp_depart_id_current')
                                ->join('company_user', 'company_user.id', 'company_user_company_department.company_user_id')
                                ->select('company_user.user_auth_id')
                                ->where('chat.id', Crypt::decrypt($data['chat']['id']))
                                ->first();

                            if (isset($query)) {
                                broadcast(new InProgressUpdated([
                                    "chat_id" => $data['chat']['id'],
                                    'company_id' => session('companyselected')['id'],
                                    'user_agent_id' => $query->user_auth_id,
                                    'action' => 'splice',
                                ]));
                                // trigger realtime to admin / see complete list
                                broadcast(new FullChatProgress([
                                    "chat_id" => $data['chat']['id'],
                                    'company_id' => session('companyselected')['id'],
                                    'user_agent_id' => $query->user_auth_id,
                                    'action' => 'splice',
                                ]));

                                $rt2 = new SendRealtime(Crypt::decrypt($data['chat']['id']), 'push');
                                $rt2->updateTableCanceled();

                            }
                        } else if ($chat->type === 'TRANSFERED') {
                            broadcast(new TransferredUpdated([
                                "chat_id" => $data['chat']['id'],
                                'company_id' => session('companyselected')['id'],
                                'action' => 'splice',
                            ]));
                        }
                    }
                    break;
                case 'CLOSED':
                    $comp_user_comp_depart_id_current = Chat::select('comp_user_comp_depart_id_current')
                        ->where('id', Crypt::decrypt($data['id']))
                        ->first()
                        ->comp_user_comp_depart_id_current;

                    $result = Chat::where('id', Crypt::decrypt($data['id']))->update([
                        'status' => 'CLOSED',
                        'update_status_closed_resolved' => Carbon::now()->toDateTimeString(),
                        'service_time' => Chat::calculatorTimeQueue($request->chat['created_at'], 'CLOSED', Crypt::decrypt($data['id'])),
                    ]);

                    $content = 'bs-closed-the-chat';
                    $status = 'CLOSED';
                    $body_notification = 'bs-the-chat-was-closed';

                    $user_client = UserClientChat::select('user_client_id as id')->where('chat_id', Crypt::decrypt($data['id']))->first();
                    if (isset($user_client->id)) {
                        broadcast(new ClientQueueStatus([
                            'chat_id' => $data['id'],
                            'company_id' => session('companyselected')['id'],
                            'user_client_id' => Crypt::encrypt($user_client->id),
                            'status' => $status,
                            'agent_answered' => 1,
                            'sent_by' => Auth::user()->name
                        ]));
                    }
                    broadcast(new InProgressUpdated([
                        'chat_id' => $request->chat['chat_id'],
                        'number' => Crypt::decrypt($request->chat['chat_id']),
                        'company_id' => session('companyselected')['id'],
                        'user_agent_id' => Auth::id(),
                        'action' => 'splice',
                    ]));
                    // trigger realtime to admin / see complete list
                    broadcast(new FullChatProgress([
                        'chat_id' => $request->chat['chat_id'],
                        'number' => Crypt::decrypt($request->chat['chat_id']),
                        'company_id' => session('companyselected')['id'],
                        'user_agent_id' => Auth::id(),
                        'action' => 'splice',
                    ]));

                    $rt3 = new SendRealtime(Crypt::decrypt($request->chat['chat_id']), 'push');
                    $rt3->updateTableClosed();

                    break;
                case 'RESOLVED':
                    if (isset(session('companyselected')['user_client_id'])) {
                        $comp_user_comp_depart_id_current = false;
                        $company_id = session('companyselected')['company_id'];
                        $user_client_id = session('companyselected')['user_client_id'];
                        $name = Auth::user()->name;
                        $email = Client::getCleanEmail(Auth::user()->email, Crypt::decrypt($company_id));
                        $client_id = Crypt::encrypt(Auth::user()->id);
                        $agent_name = $request->agent;
                        $department_name = $request->department;
                        $chat = $request->chat;
                        $chat_id = $request->chat['id'];
                        $result = Chat::where('id', Crypt::decrypt($chat_id))->update([
                            'status' => 'CLOSED',
                            'update_status_closed_resolved' => Carbon::now()->toDateTimeString(),
                            'service_time' => Chat::calculatorTimeQueue($request->chat['created_at'], 'CLOSED', Crypt::decrypt($data['id'])),
                        ]);
                        $content = 'bs-closed-the-chat';
                        $status = 'CLOSED';
                        $body_notification = 'bs-the-chat-was-closed';
                        $ba_acct_data = Auth::user()->builderall_account_data;
                    } else {
                        $comp_user_comp_depart_id_current = Chat::select('comp_user_comp_depart_id_current')
                            ->where('id', Crypt::decrypt($data['id']))
                            ->first()
                            ->comp_user_comp_depart_id_current;

                        $company_id = session('companyselected')['id'];
                        $user_client_id = UserClientChat::select('user_client_id as id')->where('chat_id', Crypt::decrypt($data['id']))->first()->id;
                        $name = $request->chat['client']['name'];
                        $email = $request->chat['client']['email'];
                        $client_id = $request->chat['client']['id'];
                        $agent_name = Auth::user()->name;
                        $department_name = $request->chat['department'];
                        $chat = $request->chat;
                        $chat_id = $request->chat['chat_id'];
                        $result = Chat::where('id', Crypt::decrypt($chat_id))->update([
                            'status' => 'RESOLVED',
                            'update_status_closed_resolved' => Carbon::now()->toDateTimeString(),
                            'service_time' => Chat::calculatorTimeQueue($request->chat['created_at'], 'CLOSED', Crypt::decrypt($data['id'])),
                        ]);
                        $content = 'bs-marked-as-resolved';
                        $status = 'RESOLVED';
                        $body_notification = 'bs-the-chat-was-resolved';
                        $ba_acct_data = $request->chat['client']['builderall_account_data'];
                    }

                    $query = Chat::join('company_user_company_department', 'company_user_company_department.id', 'chat.comp_user_comp_depart_id_current')
                        ->join('company_user', 'company_user.id', 'company_user_company_department.company_user_id')
                        ->select('company_user.user_auth_id', 'chat.status')
                        ->where('chat.id', Crypt::decrypt($chat_id))
                        ->first();


                    broadcast(new ClientQueueStatus([
                        'chat_id' => $chat_id,
                        'company_id' => $company_id,
                        'user_client_id' => Crypt::encrypt($user_client_id),
                        'status' => $status,
                        'agent_answered' => 0
                    ]));

                    if (isset($query)) {
                        if ($query->status === 'IN_PROGRESS' || $query->status === 'RESOLVED') {
                            broadcast(new InProgressUpdated([
                                "chat_id" => $chat_id,
                                'company_id' =>  $company_id,
                                'user_agent_id' => $query->user_auth_id,
                                'action' => 'splice',
                            ]));
                            // trigger realtime to admin / see complete list
                            broadcast(new FullChatProgress([
                                "chat_id" => $chat_id,
                                'company_id' =>  $company_id,
                                'user_agent_id' => $query->user_auth_id,
                                'action' => 'splice',
                            ]));
                        } else if ($query->status === 'CLOSED') {
                            broadcast(new InProgressUpdated([
                                "chat_id" => $chat_id,
                                'company_id' =>  $company_id,
                                'user_agent_id' => $query->user_auth_id,
                                'action' => 'splice',
                            ]));
                            // trigger realtime to admin / see complete list
                            broadcast(new FullChatProgress([
                                "chat_id" => $chat_id,
                                'company_id' =>  $company_id,
                                'user_agent_id' => $query->user_auth_id,
                                'action' => 'splice',
                            ]));
                            broadcast(new ClosedUpdated([
                                "chat_id" => $chat_id,
                                'company_id' => $company_id,
                                'user_agent_id' => $query->user_auth_id,
                                'action' => 'splice',
                            ]));
                            // trigger realtime to admin / see complete list
                            broadcast(new FullChatClosed([
                                "chat_id" => $chat_id,
                                'company_id' => $company_id,
                                'user_agent_id' => $query->user_auth_id,
                                'action' => 'splice',
                            ]));
                        }

                        $rt4 = new SendRealtime(Crypt::decrypt($chat_id), 'push');
                        $rt4->updateTableResolved();

                    } else {
                        // broadcast(new TransferredUpdated([
                        //     "chat_id" => $chat->id,
                        //     'company_id' => $company_id,
                        //     'action' => 'splice',
                        // ]));
                    }

                    break;
                case 'TRANSFERRED_TO_DEPARTMENT':

                    $check = DB::table('chat_history')
                    ->select('content')
                    ->where('chat_id', Crypt::decrypt($data['id']))
                    ->orderBy('id', 'DESC')
                    ->first();
                    // @JOÃO FAZ UMA SOLUÇÃO NO FRONT OU UM ALERTA
                    if($check->content == 'bs-transferred-the-chat-to-another-department'){
                        return 'transfered';
                    }

                    $query = Chat::join('company_user_company_department', 'company_user_company_department.id', 'chat.comp_user_comp_depart_id_current')
                        ->join('company_user', 'company_user.id', 'company_user_company_department.company_user_id')
                        ->select(
                            'chat.comp_user_comp_depart_id_current',
                            'company_user.user_auth_id'
                        )
                        ->where('chat.id', Crypt::decrypt($data['id']))
                        ->first();

                    $comp_user_comp_depart_id_current = $query->comp_user_comp_depart_id_current;
                    $user_agent_id = $query->user_auth_id;

                    $result = Chat::where('id', Crypt::decrypt($data['id']))->update([
                        'company_department_id' => Crypt::decrypt($data['company_department']),
                        'status' => 'OPENED',
                        'comp_user_comp_depart_id_current' => NULL
                    ]);
                    $content = 'bs-transferred-the-chat-to-another-department';
                    $status = 'OPENED';
                    $body_notification = 'bs-the-chat-was-transferred-to-another-depart';

                    $other_items_to_trigger['transferred_to_department'] = true;
                    $other_items_to_trigger['department_name'] = $data['department_name'];

                    broadcast(new InProgressUpdated([
                        'chat_id' => $request->chat['chat_id'],
                        'number' => Crypt::decrypt($request->chat['chat_id']),
                        'company_id' => session('companyselected')['id'],
                        'user_agent_id' => $user_agent_id,
                        'action' => 'splice',
                    ]));
                    // trigger realtime to admin / see complete list
                    broadcast(new FullChatProgress([
                        'chat_id' => $request->chat['chat_id'],
                        'number' => Crypt::decrypt($request->chat['chat_id']),
                        'company_id' => session('companyselected')['id'],
                        'user_agent_id' => $user_agent_id,
                        'action' => 'splice',
                    ]));

                    $user_client = UserClientChat::select('user_client_id as id')->where('chat_id', Crypt::decrypt($data['id']))->first();

                    if (isset($user_client->id)) {
                        broadcast(new ClientQueueStatus([
                            'chat_id' => $data['id'],
                            'company_id' => session('companyselected')['id'],
                            'user_client_id' => Crypt::encrypt($user_client->id),
                            'status' => $status,
                            'department_id' => $data['company_department'],
                            'department_name' => $data['department_name'],
                            'agent_answered' => 0,
                            'transferred_to_department' => true
                        ]));

                        $rt6 = new SendRealtime(Crypt::decrypt($request->chat['chat_id']), 'push');
                        $rt6->updateTableQueue();
                    }

                    break;
                case 'TRANSFERRED_TO_AGENT':

                    $check = DB::table('chat_history')
                    ->select('content')
                    ->where('chat_id', Crypt::decrypt($data['id']))
                    ->orderBy('id', 'DESC')
                    ->first();
                    // @JOÃO FAZ UMA SOLUÇÃO NO FRONT OU UM ALERTA
                    if($check->content == 'bs-transferred-the-chat-to-another-agent'){
                        return 'created';
                    }

                    $query = Chat::join('company_user_company_department', 'company_user_company_department.id', 'chat.comp_user_comp_depart_id_current')
                        ->join('company_user', 'company_user.id', 'company_user_company_department.company_user_id')
                        ->select(
                            'chat.comp_user_comp_depart_id_current',
                            'company_user.user_auth_id',
                        )
                        ->where('chat.id', Crypt::decrypt($data['id']))
                        ->first();

                    $comp_user_comp_depart_id_current = $query->comp_user_comp_depart_id_current;
                    $user_agent_id = $query->user_auth_id;

                    $result = Chat::where('id', Crypt::decrypt($data['id']))->update([
                        //'type' => 'DEFAULT',
                        'company_department_id' => Crypt::decrypt($data['company_department']['id']),
                        'comp_user_comp_depart_id_current' => Crypt::decrypt($data['agent']['company_user_company_department_id'])

                    ]);
                    $content = 'bs-transferred-the-chat-to-another-agent';
                    $status = 'IN_PROGRESS';
                    $body_notification = 'bs-the-chat-was-transferred-to-another-agent';

                    $other_items_to_trigger['transferred_to_agent'] = true;
                    $other_items_to_trigger['agent_name'] = $data['agent']['name'];
                    $other_items_to_trigger['department_name'] = $data['company_department']['name'];

                    $new_agent_info = CompanyUserCompanyDepartment::select('user_auth.name', 'user_auth.email', 'user_auth.id', 'company_user.id as company_user_id')
                        ->join('company_user', 'company_user_company_department.company_user_id', 'company_user.id')
                        ->join('user_auth', 'company_user.user_auth_id', 'user_auth.id')
                        ->where('company_user_company_department.id', Crypt::decrypt($data['agent']['company_user_company_department_id']))
                        ->first();

                    $other_items_to_trigger['company_user_id'] = $new_agent_info->company_user_id;

                    $user_client = UserClientChat::select('user_client_id as id')->where('chat_id', Crypt::decrypt($data['id']))->first();
                    if (isset($user_client->id)) {
                        broadcast(new ClientQueueStatus([
                            'chat_id' => $data['id'],
                            'company_id' => session('companyselected')['id'],
                            'user_client_id' => Crypt::encrypt($user_client->id),
                            'status' => $status,
                            'agent_answered' => 1,
                            'comp_user_comp_depart_id_current' => $data['agent']['company_user_company_department_id'],
                            'sent_by' =>  $new_agent_info->name,
                            'agent' =>  $new_agent_info->name,
                            'agent_email' =>  $new_agent_info->email,
                            'agent_id' =>  Crypt::encrypt($new_agent_info->id),
                            'transferred_to_agent' => 1,
                            'department_id' => $data['company_department']['id'],
                            'department_name' => $data['company_department']['name'],
                        ]));
                    }

                    $chat_item = [
                        'chat_id' => $request->chat['chat_id'],
                        'number' => Crypt::decrypt($request->chat['chat_id']),
                        "company_id" => session('companyselected')['id'],
                        "company_department_id" => $data['company_department']['id'],
                        'comp_user_comp_depart_id_current' => $data['agent']['company_user_company_department_id'],
                        'date' => date('d/m/Y'),
                        'time' => date('H:i:s'),
                        'created_at' => $request->chat['created_at'],
                        "department" => $data['company_department']['name'],
                        "name" => $request->chat['client']['name'],
                        "email" => $request->chat['client']['email'],
                        "builderall_account_data" => $request->chat['client']['builderall_account_data'],
                        'user_agent' => $request->chat['client']['browser'],
                        "dep_type" => $data['chat']['dep_type'],
                        "client_id" => $request->chat['client']['id'],
                        "content" => $request->chat['content'],
                        "operator" => $data['agent']['name'],
                        'status' => 'IN_PROGRESS',
                        'user_agent_id' => Crypt::decrypt($data['agent']['id']),
                        'action' => 'transferred_to_another_agent'
                    ];

                    $rt7 = new SendRealtime(Crypt::decrypt($request->chat['chat_id']), 'transferred_to_another_agent');
                    $rt7->updateTableInProgress();

                    break;
                case 'TICKET':
                    $company_department = Crypt::decrypt($data['company_department']);
                    $cucd_id = is_null($data['cucd_id']) ? null : Crypt::decrypt($data['cucd_id']); // CUCD novo
                    $description = $data['description'];

					if (!is_null($request->images)) {
						$images = $request->images;
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

                    $turn_into_ticket = Chat::turnIntoTicket($chat_id, $cucd_id, $company_department, $description);

                    if ($turn_into_ticket) {
                        return response()->json([
                            'status' => 'TICKET',
                            'ticket' => $turn_into_ticket,
                        ]);
                    } else {
                        return response()->json([
                            'status' => false
                        ]);
                    }
                    break;
            };

            if ($comp_user_comp_depart_id_current) {
                $company_user_company_department_id = $comp_user_comp_depart_id_current;
            } else {
                $company_user_company_department_id = NULL;
            }

            if ($result) {
                $create = ChatHistory::create([
                    'chat_id' => Crypt::decrypt($data['id']),
                    'type' => 'EVENT',
                    'content' => $content,
                    'company_user_company_department_id' => $company_user_company_department_id,
                    'created_by' => Auth::id(),
                ]);

                $update_cwt = ChatWorkingTimes::Update(Crypt::decrypt($data['id']));

                if ($update_cwt) {
                    if ($data['action'] == 'TRANSFERRED_TO_AGENT') {
                        ChatWorkingTimes::Insert(Crypt::decrypt($data['id']),  Crypt::decrypt($data['agent']['company_user_company_department_id']), $create->id);
                    }
                }

                $user = Auth::user();

                if ($company_user_company_department_id == NULL) {
                    $gravatar = [
                        "client_email" => Auth::user()->email,
                        "client_name" => Auth::user()->name,
                        "client_id" => Crypt::encrypt(Auth::user()->id),
                    ];
                } else {
                    $gravatar = [
                        "user_email" => Auth::user()->email,
                        "user_name" => Auth::user()->name,
                        "user_id" => Crypt::encrypt(Auth::user()->id),
                    ];
                }

                $create->chat_id = $data['id'];
                $msg = json_decode(json_encode($create), true);
                $user = json_decode(json_encode($user), true);
                $result =  array_merge($msg, $user, $other_items_to_trigger, $gravatar);

                broadcast(new MessageSent($result));

                $user_client_id = Crypt::encrypt(UserClientChat::select('user_client_id')->where('chat_id', Crypt::decrypt($data['id']))->first()->user_client_id);

                broadcast(new ClientQueueStatus([
                    'chat_id' => $data['id'],
                    'company_id' => isset(session('companyselected')['user_client_id']) ? session('companyselected')['company_id'] : session('companyselected')['id'],
                    'user_client_id' => $user_client_id,
                    'agent_answered' => isset(session('companyselected')['user_client_id']) ? 0 : 1,
                    'content' => $create->content,
                    'sent_by' => isset(session('companyselected')['user_client_id']) ? null : Auth::user()->name,
                ]));

                $arrayStatusChanger = [
                    'chat_id' => $data['id'],
                    'status'  => $status,
                ];

                if (isset($data['company_department'])) {
                    $arrayStatusChanger = array_merge($arrayStatusChanger, ['company_department_id' => $data['company_department']]);
                }

                broadcast(new ChatStatusChanger($arrayStatusChanger));

                $arrayGlobalNotificationItems = [];
                if (isset(session('companyselected')['user_client_id'])) {
                    if ($status !== 'OPENED') {
                        $company_user = Chat::join('company_user_company_department', 'company_user_company_department.id', 'chat.comp_user_comp_depart_id_current')
                            ->join('company_user', 'company_user.id', 'company_user_company_department.company_user_id')
                            ->select('company_user.id')
                            ->where('chat.id', Crypt::decrypt($data['id']))
                            ->first();

                        if (isset($company_user->id)) {
                            $arrayGlobalNotificationItems['company_user_id'] = Crypt::encrypt($company_user->id);
                        }
                    }
                    $arrayGlobalNotificationItems['company_department_id'] = $data['company_department'];
                    $arrayGlobalNotificationItems['company_id'] = session('companyselected')['company_id'];
                } else {
                    if (isset($other_items_to_trigger['transferred_to_agent']) && $other_items_to_trigger['transferred_to_agent'] === true) {
                        $arrayGlobalNotificationItems['company_department_id'] = $data['company_department']['id'];
                        $arrayGlobalNotificationItems['company_user_id'] = $other_items_to_trigger['company_user_id'];
                    } else if ($status !== 'OPENED') {
                        $arrayGlobalNotificationItems['company_department_id'] = $data['company_department'];
                        $arrayGlobalNotificationItems['company_user_id'] = session('companyselected')['company_user_id'];
                    }
                    $arrayGlobalNotificationItems['company_id'] = session('companyselected')['id'];
                }
                $arrayGlobalNotification = array_merge([
                    'title' => 'bs-chat',
                    // O corpo(mensagem) da notificação.
                    'body' => $body_notification,
                    // A URL da imagem usada como um ícone da notificação.
                    'icon' => 'https://builderall.com//franquias/2/73748/editor-html/3484811.png',
                    // (Opcional) URL para qual a notificação deve redirecionar
                    'url' => '',
                    //se for true, a notificação fica sem som (PS: está sendo disparado som no browser, então pode ficar true aqui que vai sair som igual)
                    'silent' => false,
                    // numero de identificação do chat / ticket
                    'number' => Crypt::decrypt($data['id']),
                    // identifica se a notificação é de chat ou ticket
                    'type' => 'chat',
                    // identifica o status do chat/ticket para fazer a lógica do disparo
                    'status' => $status,
                    'item' => $chat_item,
                ], $arrayGlobalNotificationItems);

                broadcast(new GlobalNotification($arrayGlobalNotification));

                if ($status === 'CLOSED') {
                    $settings = DB::table('company_department_settings')
                        ->select('settings')
                        ->where('company_department_id', Crypt::decrypt($data['company_department']))
                        ->first()
                        ->settings;

                    $obj = json_decode($settings, true);

                    if (isset($obj['chat']['arrayTranslate'])) {
                        $msg_close = $obj['chat']['arrayTranslate']['msgClose'];
                        $user_lang =  explode("_", auth()->user()->language)[1];
                        foreach ($msg_close as $row) {
                            if ($row['code'] == $user_lang) {
                                if(isset($row['text']) && $row['text'] !== '') {
                                    $create = ChatHistory::create([
                                        'chat_id' => Crypt::decrypt($data['id']),
                                        'type' => 'CLOSE',
                                        'company_user_company_department_id' => $company_user_company_department_id,
                                        'content' => $row['text'],
                                        'created_by' => Auth::id(),
                                    ]);
                                }
                            }
                        }
                    } else if (isset($obj['chat']['msgClose'])) {
                        if ($obj['chat']['msgClose'] !== null) {
                            $create = ChatHistory::create([
                                'chat_id' => Crypt::decrypt($data['id']),
                                'type' => 'CLOSE',
                                'company_user_company_department_id' => $company_user_company_department_id,
                                'content' => $obj['chat']['msgClose'],
                                'created_by' => Auth::id(),
                            ]);
                        }
                    }

                    if ($create) {
                        if ($company_user_company_department_id == NULL) {
                            $gravatar = [
                                "client_email" => Auth::user()->email,
                                "client_name" => Auth::user()->name,
                                "client_id" => Crypt::encrypt(Auth::user()->id),
                            ];
                        } else {
                            $gravatar = [
                                "user_email" => Auth::user()->email,
                                "user_name" => Auth::user()->name,
                                "user_id" => Crypt::encrypt(Auth::user()->id),
                            ];
                        }

                        $create->chat_id = $data['id'];
                        $msg = json_decode(json_encode($create), true);
                        $user = json_decode(json_encode($user), true);
                        $result =  array_merge($msg, $user, $gravatar);

                        broadcast(new MessageSent($result));

                        broadcast(new ClientQueueStatus([
                            'chat_id' => $data['id'],
                            'company_id' => isset(session('companyselected')['user_client_id']) ? session('companyselected')['company_id'] : session('companyselected')['id'],
                            'user_client_id' => $user_client_id,
                            'agent_answered' => 1,
                            'content' => $create->content,
                            'sent_by' => null
                        ]));
                    }
                }

                if ($result) {
                    return response()->json([
                        'status' => $status
                    ]);
                } else {
                    return response()->json([
                        'status' => false
                    ]);
                }
            }
        }
	}

	public function checkDepartment(Request $request){
		try {

			$cucd = DB::table('company_user_company_department')
				->join('company_department', 'company_department.id', 'company_user_company_department.company_department_id')
				->where('company_user_company_department.company_user_id', Crypt::decrypt(session('companyselected')['company_user_id']))
				->where('company_department.is_active', 1)
				->where('company_user_company_department.is_active', 1)
				->get();

			$chats_in_queue = request('chats_in_queue');
			
			usort($chats_in_queue, function ($a, $b) {
				return $a['number'] - $b['number'];
			});

			foreach ($chats_in_queue as $key) {
				$pertenc = false; // Definir como falso no início de cada iteração externa
				$item = [];
				foreach ($cucd as $cd) {
					if (Crypt::decrypt($key['company_department_id']) == $cd->company_department_id) {
						$pertenc = true;
						$item = $key;
						break; // Interrompe o loop interno
					}
				}
			
				if ($pertenc) {
					break; // Interrompe o loop externo
				}
			}

			if($pertenc){
				return $item['number'];
			}else{
				return false;
			}
		} catch (\Exception $e) {
			echo $e;
			Logger::reportException($e, [], ['chat-controller', 'checkDepartment'], false);
		}
	}

	public function chatEvaluation(Request $request)
	{
		$data = $request->all();

        $chat_id = Crypt::decrypt($data['chat_id']);
        $chat_hash_id = $data['chat_id'];
        $user = Auth::user();
        $user->client_name = $user->name;

		$result = Avaliation::create([
			'chat_id'        => $chat_id,
			'stars_atendent' => $data['stars_atendent'],
			'stars_service'  => $data['stars_service'],
			'comment'        => trim($data['comment']),
            'created_by' => $user->id,
		]);


		if ($result) {


			$create = ChatHistory::create([
				'chat_id' => $chat_id,
				'type' => 'EVENT',
				'content' => 'bs-rated-the-chat',
				'created_by' => $user->id,
			]);

            $company_selected = session('companyselected') ?? SystemState::getCacheForApi(auth('api')->user()->id, 'companyselected', null);

            $evaluation_realtime['company_id'] = $company_selected['id'];
            $evaluation_realtime['chat_id'] = $chat_hash_id;
            broadcast(new EvaluationUpdated($evaluation_realtime));

			$create->chat_id = $chat_hash_id;
			$msg = json_decode(json_encode($create), true);
			$user = json_decode(json_encode($user), true);
			$result =  array_merge($msg, $user);

            broadcast(new ClientQueueStatus([
                'chat_id' => $chat_hash_id,
                'company_id' => $company_selected['id'],
                'user_client_id' => session('companyselected')['user_client_id'],
                'agent_answered' => 0,
                'content' => $create->content,
            ]));

			if (broadcast(new MessageSent($result))) {
				return response()->json([
					'status' => true
				]);
			} else {
				return response()->json([
					'status' => false
				]);
			}
		} else {
			return response()->json([
				'status' => false
			]);
		}
	}
	public function checkEvaluation(Request $request)
	{
		$data = $request->all();

		$validator = Validator::make($data, [
			'chat_id' => 'required|string',
		]);

		if ($validator->fails()) {
			return response($validator->errors(), 400);
		}

		$check = Avaliation::select(DB::raw('count(id) as evaluation'))
			->where('chat_id', Crypt::decrypt($data['chat_id']))
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

	public function verifyActiveFooterChats(Request $request)
	{
		$validator = Validator::make($request->all(), [
			'chats' => 'required|array',
		]);

		if ($validator->fails()) {
			return response($validator->errors(), 400);
		}

		$failed_chat_id = [];

		foreach ($request->chats as $row) {
            if ($row['type'] == 'chat') {
                $chat = Chat::where([
                    ['id', Crypt::decrypt($row['chat_id'])],
                    ['status', 'IN_PROGRESS'],
                    ['type', '!=', 'TICKET'],
                    ['type', '!=', 'CHANGED_TO_TICKET'],
                    ['company_id', Crypt::decrypt(session('companyselected')['id'])],
                    ['comp_user_comp_depart_id_current', Crypt::decrypt($row['comp_user_comp_depart_id_current'])]
                ])
                    ->first();

                if (!$chat) {
                    array_push($failed_chat_id, $row['chat_id']);
                }
            }
        }

		return response()->json(['failed_chat_id' => $failed_chat_id]);
	}

	public function takeTheChat(Request $request)
	{
		try {
			$validator = Validator::make($request->all(), [
				'chat' => 'required|array',
				'company_department_id' => 'required|string',
				'comp_user_comp_depart_id_current' => 'required|string',
				'is_admin' => 'required|boolean',
			]);

			if ($validator->fails()) {
				return response($validator->errors(), 400);
			}

			$session = session('company_user_company_departments');
			$company_user_company_department_id = false;

			if ($request->is_admin) {
				foreach ($session as $row) {
					if ((int) Crypt::decrypt($row->company_department_id) === (int) Crypt::decrypt($request->chat['companyDepartmentId'])) {
						$company_user_company_department_id = (int) Crypt::decrypt($row->company_user_company_department_id);
					}
				}

				if (!$company_user_company_department_id) {
					$cucdc = new CompanyUserCompanyDepartmentController();
					$cucd = $cucdc->addAgentToDepartment($request);

					$vars = get_object_vars($cucd);

					$company_user_company_department_id = (int) Crypt::decrypt($vars['original']['cucdic']);
					$session = $vars['original']['session_user_cucd'];
				}
			} else {
				foreach ($session as $row) {
					if ((int) Crypt::decrypt($row->company_department_id) === (int) Crypt::decrypt($request->chat['companyDepartmentId'])) {
						$company_user_company_department_id = (int) Crypt::decrypt($row->company_user_company_department_id);
					}
				}
			}
			if ($company_user_company_department_id) {

				$chat = Chat::where('id', Crypt::decrypt($request->chat['chat_id']))->update(['comp_user_comp_depart_id_current' => $company_user_company_department_id]);

				if ($chat) {
					$chat_history = ChatHistory::create([
						'chat_id' => Crypt::decrypt($request->chat['chat_id']),
						'company_user_company_department_id' => $company_user_company_department_id,
						'type' => 'EVENT',
						'content' => 'bs-took-over-the-chat',
						'hidden_for_client' => 0,
						'created_by' => Auth::id()
					]);

					if ($chat_history) {

                        $update_cwt = ChatWorkingTimes::Update(Crypt::decrypt($request->chat['chat_id']));

                        if ($update_cwt) {
                            ChatWorkingTimes::Insert(Crypt::decrypt($request->chat['chat_id']), $company_user_company_department_id, $chat_history->id);
                        }


						$user = Auth::user();

                        if ($company_user_company_department_id == NULL) {
                            $gravatar = [
                                "client_email" => Auth::user()->email,
                                "client_name" => Auth::user()->name,
                                "client_id" => Crypt::encrypt(Auth::user()->id),
                            ];
                        } else {
                            $gravatar = [
                                "user_email" => Auth::user()->email,
                                "user_name" => Auth::user()->name,
                                "user_id" => Crypt::encrypt(Auth::user()->id),
                            ];
                        }

						$chat_history->chat_id = $request->chat['chat_id'];
						$chat_history->company_user_company_department_id = Crypt::encrypt($company_user_company_department_id);
						$msg = json_decode(json_encode($chat_history), true);
						$user = json_decode(json_encode($user), true);

						$result =  array_merge($msg, $user, $gravatar, [
							'took_over' => true,
							'comp_user_comp_depart_id_current' => Crypt::encrypt($company_user_company_department_id),
							'operator' => Auth::user()->name,
							'user_name' => Auth::user()->name,
							'created_at' => $chat_history->created_at,
							'employee' => true
						]);
						/** realtime mensagem enviada */
						broadcast(new MessageSent($result));
						/** realtime para atendentes comuns */
						$user_agent = CompanyUserCompanyDepartment::join('company_user', 'company_user.id', 'company_user_company_department.company_user_id')
							->select('company_user.user_auth_id as id')
							->where('company_user_company_department.id', Crypt::decrypt($request->comp_user_comp_depart_id_current))
							->first();

						if ($user_agent) {
							broadcast(new InProgressUpdated([
								'action' => 'splice',
								'took_over' => true,
								"chat_id" => $request->chat['chat_id'],
								'company_id' => session('companyselected')['id'],
								'user_agent_id' => $user_agent->id,
							]));
						}
						/** realtime para atendentes lista completa/ admins */

                        $realtime = new SendRealtime(Crypt::decrypt($request->chat['chat_id']), 'took-over');
                        $realtime->updateTableInProgress();

						/** realtime para clients */
						$user_client = UserClientChat::select('user_client_id as id')->where('chat_id', Crypt::decrypt($request->chat['chat_id']))->first();

						if ($user_client) {
							broadcast(new ClientQueueStatus([
								'chat_id' => $request->chat['chat_id'],
								'number' => Crypt::decrypt($request->chat['chat_id']),
								'user_client_id' => Crypt::encrypt($user_client->id),
								'comp_user_comp_depart_id_current' => Crypt::encrypt($company_user_company_department_id),
								'company_id' => session('companyselected')['id'],
								'sent_by' => Auth::user()->name,
								'agent' => Auth::user()->name,
								'agent_email' => Auth::user()->email,
								'agent_id' => Crypt::encrypt(Auth::user()->id),
								'agent_answered' => 1,
								'status' => 'IN_PROGRESS',
                                'content' => $chat_history->content
							]));
						}
					}

					return response()->json([
						'status' => true,
						'flag' => 1,
						'is_admin' => $request->is_admin,
						'session_user_cucd' => $session,
					]);
				} else {
					return response()->json([
						'status' => false,
						'flag' => 0,
					]);
				}
			} else {
				return response()->json([
					'status' => false,
					'flag' => 2,
				]);
			}
		} catch (\Exception $e) {
			Logger::reportException($e, [], ['chat-controller', 'takeTheChat'], false);
		}
	}

	public function teste()
	{
		broadcast(new GlobalNotification([
			// título da notificação
			'title' => 'bs-chat',
			// O corpo(mensagem) da notificação.
			'body' => 'bs-new-message-received',
			// A URL da imagem usada como um ícone da notificação.
			'icon' => 'https://builderall.com//franquias/2/73748/editor-html/3484811.png',
			// (Opcional) URL para qual a notificação deve redirecionar
			'url' => '',
			//
			'number' => 1232,
			// identifica se notificação é de chat ou ticket
			'type' => 'chat',
			// identifica o status do chat/ticket para fazer a lógica do disparo
			'status' => 'IN_PROGRESS',
			// identifica o id do departamento para qual a notificação deve ser dispara
			'company_department_id' => Crypt::encrypt(8),
			// envia o company user id do atendente que precisa receber a notificação (caso seja individual)
			'company_user_id' => session('companyselected')['company_user_id'],
			// Paramêtro de conexão no canal global de notificação, sempre deve ser passado
			'company_id' => session('companyselected')['id']
		]));
	}

    public function tabs(Request $request, $next_attendant = null) {
        $item = $request->chat;
        $item['type'] = $next_attendant == null ? $request->type : 'chat';
        $item['user_id'] = $next_attendant == null ? Auth::id() : $next_attendant->user_auth_id;
        $item['company_id'] = $next_attendant == null ? session('companyselected')['id'] : session('companyselected')['company_id'];
        broadcast(new Tabs($item));
    }

    public function delete(Request $request) {
        $result['success'] = false;
        try {
			if(session('restriction')[0]->chat_delete == 1){
				$id = Crypt::decrypt($request->chat_id);
				$status = Chat::select('status')->where('id', $id)->first();
				$delete = Chat::find($id)->delete();

				if ($delete) {

                    ChatWorkingTimes::Update($id);

					broadcast(new ChatTicketDelete([
						'company_id'    => session('companyselected')['id'],
						'type'          => 'CHAT',
						'id'            => $request->chat_id,
						'status'        => $status->status
					]));

                    broadcast(new ClientQueueStatus([
                        'chat_id' => $request->chat_id,
                        'user_client_id' => Crypt::encrypt(UserClientChat::select('user_client_id')->where('chat_id', $id)->first()->user_client_id),
                        'company_id' => session('companyselected')['id'],
                        'action' => 'splice',
                    ]));

					$result['success'] = true;
				}
			}else{
				$result['success'] = false;
			}

        } catch (\Exception $e) {
            Logger::reportException($e, [], ['chat-controller', 'delete'], false);
        }
        return $result;
    }

    public function setInfoToTurnIntoticket (Request $request) {
        $chat_id                = Crypt::decrypt($request->chat_id);
        $company_department     = Crypt::decrypt($request->company_department);
        $cucd_id                = is_null($request->cucd_id) ? null : Crypt::decrypt($request->cucd_id);
		$description            = $request->description;

		if (!is_null($request->images)) {
			$images = $request->images;
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

        return Chat::setInfoToTurnIntoticket($chat_id, $company_department, $description, $cucd_id);
    }

    public function getInfoToTurnIntoTicket (Request $request) {

        $chat_id = Crypt::decrypt($request->chat_id);

        $info = Chat::getInfoToTurnIntoTicket($chat_id);

        if ($info && isset($info->company_department)) {

            $info->company_department = Crypt::encrypt($info->company_department);
            isset($info->cucd_id) ? $info->cucd_id = Crypt::encrypt($info->cucd_id) : $info->cucd_id = null;

            return [
                'success'   => true,
                'info'      => $info
            ];
        } else {
            return [
                'success'   => false
            ];
        }
    }

    public function deleteInfoToTurnIntoticket (Request $request) {

        $chat_id = Crypt::decrypt($request->chat_id);

        $delete = Chat::deleteInfoToTurnIntoticket($chat_id);

        if ($delete) {
            return [
                'success'   => true,
            ];
        } else {
            return [
                'success'   => false
            ];
        }
    }

    public function getChatInfoClient(Request $request) {
        $result['success'] = false;
        try {
            $company_id = Crypt::decrypt(session('companyselected')['company_id']);
            $chat_id = Crypt::decrypt($request->id);
            $chat = UserClientChat::select(
                    'chat.id',
                    'chat.created_at',
                    'chat.status', 
                    'chat.is_robot',
                    'chat.comp_user_comp_depart_id_current as cucd_id',
                    'company_department.id as department_id',
                    'company_department.name as department_name'
                )
                ->join('user_client', 'user_client_chat.user_client_id', 'user_client.id')
                ->join('user_auth', 'user_client.user_auth_id', 'user_auth.id')
                ->join('chat', 'user_client_chat.chat_id', 'chat.id')
                ->join('company_department', 'chat.company_department_id', 'company_department.id')
                ->where('chat.company_id', $company_id)
                ->where('chat.id', $chat_id)
                ->where('user_auth.id', Auth::id())
                ->whereNull('ticket_id')
                ->whereNull('chat.deleted_at')
                ->first();

            if (isset($chat)) {

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
                        $chat->attendant_id = Crypt::encrypt($cucd->attendant_id);
                        $chat->attendant_name = $cucd->attendant_name;
                        $chat->attendant_email = $cucd->attendant_email;
                    }

                    $chat->cucd_id = Crypt::encrypt($chat->cucd_id);

                } else {
                    $chat->attendant_id = null;
                    $chat->attendant_name = null;
                    $chat->attendant_email = null;
                }

                $chat->department_id = Crypt::encrypt($chat->department_id);
                $chat->hash_id = Crypt::encrypt($chat->id);

                $result['success'] = true;
                $result['chat'] = $chat;
            }

            
        } catch (\Exception $e) {
            Logger::reportException($e, [], ['chat-controller', 'getChatInfo'], false);
        }
        return $result;
    }

    public function cancelChat(Request $request) {
        $result['success'] = false;
        $chat_id = Crypt::decrypt($request->id);
        try {
            if (Chat::checkTurnIntoTicketAtClosing($chat_id)){
                $info = Chat::getInfoToTurnIntoTicket($chat_id);
                $result['success'] = Chat::turnIntoTicket($chat_id, $info->cucd_id, $info->company_department, $info->description);
            } else {
                $result['success'] = Chat::cancel($chat_id);
            }
        } catch (\Exception $e) {
            Logger::reportException($e, [], ['chat-controller', 'cancelChat'], false);
        }
        return $result;
    }

    public function resolveChat(Request $request) {
        $result['success'] = false;
        $chat_id = Crypt::decrypt($request->id);
        try {
            if (Chat::checkTurnIntoTicketAtClosing($chat_id)){
                $info = Chat::getInfoToTurnIntoTicket($chat_id);
                $result['success'] = Chat::turnIntoTicket($chat_id, $info->cucd_id, $info->company_department, $info->description);
            } else {
                $result['success'] = Chat::resolve($chat_id);
            }
        } catch (\Exception $e) {
            Logger::reportException($e, [], ['chat-controller', 'resolveChat'], false);
        }
        return $result;
    }

    public function sendRobotsFirstMessage (Request $request) {
        $result['success'] = false;
        $chat_id = Crypt::decrypt($request->chat_id);
        $first_message = $request->first_message;
        try {
            $chat = Chat::find($chat_id);
            $store = $chat->chat_histories()->create([
                'type'  => 'ROBOT',
                'content'  => json_encode($first_message),
                'created_by' => -1,
            ]);
            $store->ch_id = Crypt::encrypt($store->id);
            $store->user_id = null;
            $store->user_name = null;
            $store->user_email = null;
            $result['success'] = true;
            $result['message'] = $store;
        } catch (\Exception $e) {
            Logger::reportException($e, [], ['chat-controller', 'sendRobotsFirstMessage'], false);
        }
        return $result;
    }
}
