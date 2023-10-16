<?php

namespace App\Http\Controllers;

use App\Chat;
use App\CompanyUserCompanyDepartment;
use App\Tools\Builderall\Logger;
use App\Tools\Client;
use App\Tools\Crypt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class FullChatController extends Controller
{
    public function index()
    {
        return view('functions.full-chat.index');
    }

    public function getChatsInProgress(Request $request)
    {
        /** begin */
        try {
            /** validator */
            $validator = Validator::make($request->all(), [
                'my_chats' => 'required|int',
                'departments' => 'required|array',
            ]);

            if ($validator->fails()) {
                return response($validator->errors(), 400);
            }
            /** filter by department */
            $filter_departments = [];

            foreach ($request->departments as $row) {
                array_push($filter_departments, (int) Crypt::decrypt(json_decode($row)->id));
            }

            $conditions = [
                ['chat.company_id', Crypt::decrypt(session('companyselected')['id'])],
                ['chat.type', 'DEFAULT'],
                ['chat.status', 'IN_PROGRESS']
            ];
            /** filter my chats */
            if ($request->my_chats) {
                array_push($conditions, ['ua_agent.id', Auth::id()]);
            }
            /** query */
            $result = Chat::join('company_department', 'company_department.id', 'chat.company_department_id')
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
                    'ua_client.id as client_id',
                    'ua_client.builderall_account_data',
                    'ua_client.name',
                    'ua_client.email',
                    'ua_agent.name AS operator',
                    'ua_agent.id AS operator_id',
                    'ua_agent.email AS operator_email',
                    DB::raw('IF((SELECT company_user_company_department_id FROM chat_history WHERE chat_id = chat.id ORDER BY id DESC LIMIT 1) IS NULL, 1, 0) AS answered'),
                    'cc.chat_id as category_chat_id'
                )
                ->whereIn('chat.company_department_id', $filter_departments)
                ->whereNull('chat.ticket_id')
                ->where($conditions);

                if (request('filter_not_category') == 'true' || request('filter_not_category') == 1) {
                    $result->where('cc.chat_id', null);
                }

                $result->groupBy('chat.id');

                $query = $result->get();

            /** encryption */
            foreach ($query as $row) {
                $row->chat_id = Crypt::encrypt($row->chat_id);
                $row->client_id = Crypt::encrypt($row->client_id);
                $row->company_id = Crypt::encrypt($row->company_id);
                $row->company_department_id = Crypt::encrypt($row->company_department_id);
                $row->comp_user_comp_depart_id_current = Crypt::encrypt($row->comp_user_comp_depart_id_current);
                $row->email = Client::getCleanEmail($row->email, Crypt::decrypt($row->company_id));
                $row->operator_id = Crypt::encrypt($row->operator_id);
            }
            /** response */
            return response()->json([
                'chats' => $query,
                'company_id' => session('companyselected')['id'],
            ]);
            /** send exception to logger */
        } catch (\Exception $e) {
            echo $e;
            Logger::reportException($e, [], ['full-chat-controller', 'getChatsInProgress'], false);
        }
    }

    public function getChatsClosed(Request $request)
    {
        /** begin */
        try {
            /** validator */
            $validator = Validator::make($request->all(), [
                'take' => 'required|int',
                'skip' => 'required|int',
                'my_chats' => 'required|int',
                'departments' => 'required|array',
            ]);

            if ($validator->fails()) {
                return response($validator->errors(), 400);
            }
            /** filter by department */
            $filter_departments = [];

            foreach ($request->departments as $row) {
                array_push($filter_departments, (int) Crypt::decrypt(json_decode($row)->id));
            }

            $conditions = [
                ['chat.company_id', Crypt::decrypt(session('companyselected')['id'])],
                ['chat.type', 'DEFAULT'],
                ['chat.status', 'CLOSED']
            ];
            /** filter my chats */
            if ($request->my_chats) {
                array_push($conditions, ['ua_agent.id', Auth::id()]);
            }
            /** query */
            $result = Chat::join('company_department', 'company_department.id', '=', 'chat.company_department_id')
                ->join('chat_history', 'chat_history.chat_id', '=', 'chat.id')
                ->join('user_client_chat', 'user_client_chat.chat_id', '=', 'chat.id')
                ->join('user_client', 'user_client.id', '=', 'user_client_chat.user_client_id')
                ->join('user_auth AS ua_client', 'user_client.user_auth_id', 'ua_client.id')
                ->join('company_user_company_department', 'chat.comp_user_comp_depart_id_current', 'company_user_company_department.id')
                ->join('company_user', 'company_user_company_department.company_user_id', 'company_user.id')
                ->join('user_auth AS ua_agent', 'company_user.user_auth_id', 'ua_agent.id')
                ->leftJoin('chat_category AS cc', 'chat.id', 'cc.chat_id')
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
                    'chat.updated_at as end',
                    'chat.user_agent',
                    'chat.turn_into_ticket_at_closing',
                    DB::raw('DATE_FORMAT(chat.created_at,"%d/%m/%Y") as date'),
                    DB::raw('DATE_FORMAT(chat.created_at,"%H:%i:%s") as time'),
                    'company_department.name as department',
                    'company_department.type as dep_type',
                    'ua_client.id as client_id',
                    'ua_client.builderall_account_data',
                    'ua_client.name',
                    'ua_client.email',
                    'ua_agent.name AS operator',
                    'ua_agent.id AS operator_id',
                    'ua_agent.email AS operator_email',
                    'chat_history.content',
                    'cc.chat_id as category_chat_id'
                )
                ->where($conditions)
                ->whereIn('chat.company_department_id', $filter_departments)
                ->whereNull('chat.ticket_id');

                if (request('filter_not_category') == 'true' || request('filter_not_category') == 1) {
                    $result->where('cc.chat_id', null);
                }

                $result->groupBy('chat.id')
                ->orderByDesc('chat.updated_at')
                ->skip($request->skip)
                ->take($request->take);

                $query = $result->get();
            //** encryption */
            foreach ($query as $row) {
                $row->chat_id = Crypt::encrypt($row->chat_id);
                $row->client_id = Crypt::encrypt($row->client_id);
                $row->company_id = Crypt::encrypt($row->company_id);
                $row->company_department_id = Crypt::encrypt($row->company_department_id);
                $row->comp_user_comp_depart_id_current = Crypt::encrypt($row->comp_user_comp_depart_id_current);
                $row->email = Client::getCleanEmail($row->email, Crypt::decrypt($row->company_id));
                $row->operator_id = Crypt::encrypt($row->operator_id);
            }
            /** response */
            return response()->json([
                'chats' => $query,
                'company_id' => session('companyselected')['id'],
                'skip' => $request->skip + $request->take,
            ]);
            /** send exception to logger */
        } catch (\Exception $e) {
            Logger::reportException($e, [], ['full-chat-controller', 'getChatsClosed'], false);
        }
    }

    public function getChatsResolved(Request $request)
    {
        /** begin */
        try {
            /** validator */
            $validator = Validator::make($request->all(), [
                'take' => 'required|int',
                'skip' => 'required|int',
                'my_chats' => 'required|int',
                'departments' => 'required|array',
            ]);

            if ($validator->fails()) {
                return response($validator->errors(), 400);
            }
            /** filter by department */
            $filter_departments = [];

            foreach ($request->departments as $row) {
                array_push($filter_departments, (int) Crypt::decrypt(json_decode($row)->id));
            }

            $conditions = [
                ['chat.company_id', Crypt::decrypt(session('companyselected')['id'])],
                ['chat.type', 'DEFAULT'],
                ['chat.status', 'RESOLVED']
            ];
            /** filter my chats */
            if ($request->my_chats) {
                array_push($conditions, ['ua_agent.id', Auth::id()]);
            }
            /** query */
            $result = Chat::join('company_department', 'company_department.id', '=', 'chat.company_department_id')
                ->join('chat_history', 'chat_history.chat_id', '=', 'chat.id')
                ->join('user_client_chat', 'user_client_chat.chat_id', '=', 'chat.id')
                ->join('user_client', 'user_client.id', '=', 'user_client_chat.user_client_id')
                ->join('user_auth AS ua_client', 'user_client.user_auth_id', 'ua_client.id')
                ->join('company_user_company_department', 'chat.comp_user_comp_depart_id_current', 'company_user_company_department.id')
                ->join('company_user', 'company_user_company_department.company_user_id', 'company_user.id')
                ->join('user_auth AS ua_agent', 'company_user.user_auth_id', 'ua_agent.id')
                ->leftJoin('chat_category AS cc', 'chat.id', 'cc.chat_id')
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
                    'chat.updated_at as end',
                    'chat.user_agent',
                    'chat.turn_into_ticket_at_closing',
                    DB::raw('DATE_FORMAT(chat.created_at,"%d/%m/%Y") as date'),
                    DB::raw('DATE_FORMAT(chat.created_at,"%H:%i:%s") as time'),
                    'company_department.name as department',
                    'company_department.type as dep_type',
                    'ua_client.id as client_id',
                    'ua_client.builderall_account_data',
                    'ua_client.name',
                    'ua_client.email',
                    'ua_agent.name AS operator',
                    'ua_agent.email AS operator_email',
                    'ua_agent.id AS operator_id',
                    'chat_history.content',
                    'cc.chat_id as category_chat_id'
                )
                ->where($conditions)
                ->whereIn('chat.company_department_id', $filter_departments)
                ->whereNull('chat.ticket_id');

                if (request('filter_not_category') == 'true' || request('filter_not_category') == 1) {
                    $result->where('cc.chat_id', null);
                }

                $result->groupBy('chat.id')
                ->orderByDesc('chat.updated_at')
                ->skip($request->skip)
                ->take($request->take);

                $query = $result->get();
            //** encryption */
            foreach ($query as $row) {
                $row->chat_id = Crypt::encrypt($row->chat_id);
                $row->client_id = Crypt::encrypt($row->client_id);
                $row->company_id = Crypt::encrypt($row->company_id);
                $row->company_department_id = Crypt::encrypt($row->company_department_id);
                $row->comp_user_comp_depart_id_current = Crypt::encrypt($row->comp_user_comp_depart_id_current);
                $row->email = Client::getCleanEmail($row->email, Crypt::decrypt($row->company_id));
                $row->operator_id = Crypt::encrypt($row->operator_id);
            }
            /** response */
            return response()->json([
                'chats' => $query,
                'company_id' => session('companyselected')['id'],
                'skip' => $request->skip + $request->take,
            ]);
        } catch (\Exception $e) {
            Logger::reportException($e, [], ['full-chat-controller', 'getChatsResolved'], false);
        }
    }

    public function getChatsFinished(Request $request)
    {
        /** begin */
        try {
            $data =  $request->all();
            /** validator */
            $validator = Validator::make($request->all(), [
                'my_chats' => 'required|int',
                'departments' => 'required|array',
            ]);

            if ($validator->fails()) {
                return response($validator->errors(), 400);
            }
            /** filter by department */
            $filter_departments = [];

            foreach ($request->departments as $row) {
                array_push($filter_departments, (int) Crypt::decrypt(json_decode($row)->id));
            }

            $conditions = [
                ['chat.company_id', Crypt::decrypt(session('companyselected')['id'])],
                ['chat.type', 'DEFAULT'],
            ];
            /** filter my chats */
            if ($request->my_chats) {
                array_push($conditions, ['ua_agent.id', Auth::id()]);
            }
            /** query */
            $result = Chat::join('company_department', 'company_department.id', '=', 'chat.company_department_id')
                ->join('user_client_chat', 'user_client_chat.chat_id', '=', 'chat.id')
                ->join('user_client', 'user_client.id', '=', 'user_client_chat.user_client_id')
                ->join('user_auth AS ua_client', 'user_client.user_auth_id', 'ua_client.id')
                ->leftjoin('company_user_company_department', 'chat.comp_user_comp_depart_id_current', 'company_user_company_department.id')
                ->leftjoin('company_user', 'company_user_company_department.company_user_id', 'company_user.id')
                ->leftjoin('user_auth AS ua_agent', 'company_user.user_auth_id', 'ua_agent.id')
                ->leftJoin('chat_category AS cc', 'chat.id', 'cc.chat_id')
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
                    'ua_client.id as client_id',
                    'ua_client.builderall_account_data',
                    'ua_client.name',
                    'ua_client.email',
                    'ua_agent.name AS operator',
                    'ua_agent.id AS operator_id',
                    'ua_agent.email AS operator_email',
                    DB::raw('NULL as content'),
                    'cc.chat_id as category_chat_id'
                )
                ->where($conditions)
                ->where(function ($f) {
                    $f->where([['chat.status', 'RESOLVED']])
                        ->orWhere([['chat.status', 'CLOSED']]);
                })
                ->whereIn('chat.company_department_id', $filter_departments)
                ->whereNull('chat.ticket_id');

                if (request('filter_not_category') == 'true' || request('filter_not_category') == 1) {
                    $result->where('cc.chat_id', null);
                }
                
                $result->groupBy('chat.id')
                ->orderByDesc('chat.updated_at')
                ->limit(1);

                $query = $result->simplePaginate($data['per_page']);

            //** encryption */
            foreach ($query as $row) {
                $row->chat_id = Crypt::encrypt($row->chat_id);
                $row->client_id = Crypt::encrypt($row->client_id);
                $row->company_id = Crypt::encrypt($row->company_id);
                $row->company_department_id = Crypt::encrypt($row->company_department_id);
                $row->comp_user_comp_depart_id_current = isset($row->comp_user_comp_depart_id_current) ? Crypt::encrypt($row->comp_user_comp_depart_id_current) : null;
                $row->email = Client::getCleanEmail($row->email, Crypt::decrypt($row->company_id));
                $row->operator_id = Crypt::encrypt($row->operator_id);
            }

            return response()->json($query);


        } catch (\Exception $e) {
            Logger::reportException($e, [], ['full-chat-controller', 'getChatsFinished'], false);
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
                'my_chats' => 'required|int',
                'departments' => 'required|array',
            ]);

            if ($validator->fails()) {
                return response($validator->errors(), 400);
            }
            /** filter by department */
            $filter_departments = [];

            foreach ($request->departments as $row) {
                array_push($filter_departments, (int) Crypt::decrypt(json_decode($row)->id));
            }

            $conditions = [
                ['chat.company_id', Crypt::decrypt(session('companyselected')['id'])],
                ['chat.type', 'DEFAULT'],
                ['chat.status', 'CANCELED']
            ];
            /** filter my chats */
            if ($request->my_chats) {
                array_push($conditions, ['ua_agent.id', Auth::id()]);
            }
            /** query */
            $query = Chat::join('company_department', 'company_department.id', '=', 'chat.company_department_id')
                ->join('chat_history', 'chat_history.chat_id', '=', 'chat.id')
                ->join('user_client_chat', 'user_client_chat.chat_id', '=', 'chat.id')
                ->join('user_client', 'user_client.id', '=', 'user_client_chat.user_client_id')
                ->join('user_auth AS ua_client', 'user_client.user_auth_id', 'ua_client.id')
                ->leftjoin('company_user_company_department', 'chat.comp_user_comp_depart_id_current', 'company_user_company_department.id')
                ->leftjoin('company_user', 'company_user_company_department.company_user_id', 'company_user.id')
                ->leftjoin('user_auth AS ua_agent', 'company_user.user_auth_id', 'ua_agent.id')
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
                    'ua_client.id as client_id',
                    'ua_client.builderall_account_data',
                    'ua_client.name',
                    'ua_client.email',
                    'ua_agent.name AS operator',
                    'ua_agent.id AS operator_id',
                    'ua_agent.email AS operator_email',
                    'chat_history.content'
                )
                ->where($conditions)
                ->whereIn('chat.company_department_id', $filter_departments)
                ->whereNull('chat.ticket_id')
                ->groupBy('chat.id')
                ->orderByDesc('chat.updated_at');

            if (!empty($request->take)) {
                $query->skip($data['skip'])->take($data['take']);
                $result = $query->get();
            } else {
                $result = $query->paginate($data['per_page']);
            }

            //** encryption */
            foreach ($result as $row) {
                $row->chat_id = Crypt::encrypt($row->chat_id);
                $row->client_id = Crypt::encrypt($row->client_id);
                $row->company_id = Crypt::encrypt($row->company_id);
                $row->company_department_id = Crypt::encrypt($row->company_department_id);
                $row->comp_user_comp_depart_id_current = Crypt::encrypt($row->comp_user_comp_depart_id_current);
                $row->email = Client::getCleanEmail($row->email, Crypt::decrypt($row->company_id));
                $row->operator_id = Crypt::encrypt($row->operator_id);
            }
            /** response */
            if (!empty($request->take)) {
                return response()->json([
                    'chats' => $result,
                    'company_id' => session('companyselected')['id'],
                    'skip' => $data['skip'] + $data['take'],
                ]);
            } else {
                return response()->json($result);
            }
            /** send exception to logger */
        } catch (\Exception $e) {
            Logger::reportException($e, [], ['full-chat-controller', 'getChatsCanceled'], false);
        }
    }

    public function getAgentTablesCount(Request $request)
    {
        /** begin */
        // try {
        /** user departments */
        $request_departments = [];
        $departments = [];

        foreach ($request->departments as $row) {
            array_push($request_departments, json_decode($row, true));
        }

        foreach ($request_departments as $dep) {
            array_push($departments, Crypt::decrypt($dep['id']));
        }
        /** query */
        $firstCount    = Chat::join('company_department', 'company_department.id', 'chat.company_department_id')
            ->join('chat_history', 'chat_history.chat_id', 'chat.id')
            ->join('user_client_chat', 'user_client_chat.chat_id', 'chat.id')
            ->join('user_client', 'user_client.id', 'user_client_chat.user_client_id')
            ->join('user_auth', 'user_auth.id', 'user_client.user_auth_id')
            ->leftJoin('chat_category AS cc', 'chat.id', 'cc.chat_id')
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
            });

            if (request('filter_not_category') == 'true' || request('filter_not_category') == 1) {
                $firstCount->where('cc.chat_id', null);
            }
            
            $firstCount->groupBy(DB::raw(1));

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
            ->leftJoin('chat_category AS cc', 'chat.id', 'cc.chat_id')
            ->select(
                DB::raw("
					'in_progress' AS type,
					COUNT(DISTINCT `chat`.`id`) as count
				")
            )
            ->whereIn('chat.company_department_id', $departments)
            ->whereNull('chat.ticket_id')
            ->where([
                ['chat.company_id', Crypt::decrypt(session('companyselected')['id'])],
                ['chat.status', 'IN_PROGRESS'],
                ['chat.type', 'DEFAULT'],
            ]);

            if (request('filter_not_category') == 'true' || request('filter_not_category') == 1) {
                $secondCount->where('cc.chat_id', null);
            }

        $lastCount = Chat::join('company_department', 'company_department.id', 'chat.company_department_id')
            ->join('chat_history', 'chat_history.chat_id', 'chat.id')
            ->join('user_client_chat', 'user_client_chat.chat_id', 'chat.id')
            ->join('user_client', 'user_client.id', 'user_client_chat.user_client_id')
            ->join('user_auth AS ua_client', 'user_client.user_auth_id', 'ua_client.id')
            ->leftjoin('company_user_company_department', 'chat.comp_user_comp_depart_id_current', 'company_user_company_department.id')
            ->leftjoin('company_user', 'company_user_company_department.company_user_id', 'company_user.id')
            ->leftjoin('user_auth AS ua_agent', 'company_user.user_auth_id', 'ua_agent.id')
            ->leftJoin('chat_category AS cc', 'chat.id', 'cc.chat_id')
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
                ['chat.company_id', Crypt::decrypt(session('companyselected')['id'])],
                ['chat.type', 'DEFAULT']
            ])
            ->whereIn('chat.company_department_id', $departments)
            ->whereIn('chat.status', ['CLOSED', 'RESOLVED', 'CANCELED'])
            ->whereNull('chat.ticket_id');

            if (request('filter_not_category') == 'true' || request('filter_not_category') == 1) {
                $lastCount->where('cc.chat_id', null);
            }

            $lastCount->groupBy(DB::raw(1));

        if ($request->my_chats) {
            $cucd = CompanyUserCompanyDepartment::join('company_user', 'company_user.id', 'company_user_company_department.company_user_id')
                ->select('company_user_company_department.id')
                ->where('company_user.user_auth_id', Auth::id())
                ->whereIn('company_user_company_department.company_department_id', $departments)
                ->get();

            $secondCount->whereIn('chat.comp_user_comp_depart_id_current', $cucd);
            $lastCount
                ->whereIn('chat.comp_user_comp_depart_id_current', $cucd)
                ->union($firstCount)
                ->union($secondCount);
        } else {
            $lastCount->union($firstCount)
                ->union($secondCount);
        }

        $result = $lastCount->get();

        // ** add each count to array
        $count = [];
        foreach ($result as $row) {
            $count[$row->type] = $row->count;
        }
        /** response */
        return response()->json($count);
        /** send exception to logger */
        // } catch (\Exception $e) {
        //     Logger::reportException($e, [], ['full-chat-controller', 'getAgentTablesCount'], false);
        // }
    }
}
