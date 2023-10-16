<?php

namespace App\Http\Controllers;

use App\Chat;
use App\Ticket;
use Illuminate\Support\Facades\DB;
use App\Tools\Crypt;
use App\Tools\Builderall\Logger;
use App\Tools\ClearEmail;
use App\Tools\Client;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Swift;

class FullTicketController2 extends Controller
{
    public function counter(Request $request)
    {
        $my_tickets = $request->my_chats;
        $not_category = $request->not_category;
        $request_departments = [];
        $departments = [];

        $selected = $request->selected;
        $searchQuery = $request->searchQuery;

        foreach ($request->departments as $row) {
            array_push($request_departments, json_decode($row, true));
        }

        foreach ($request_departments as $dep) {
            array_push($departments, Crypt::decrypt($dep['id']));
        }

        // Tickets em atraso
        $query0 = Ticket::selectRaw("'OVERDUE' AS 'status', COUNT(*) as 'qtd', '0 as category_chat_id'")
                        ->join('chat', 'ticket.id', 'chat.ticket_id')
                        ->join('user_ticket', 'ticket.id', 'user_ticket.ticket_id')
                        ->join('company_user', 'user_ticket.company_user_id', 'company_user.id')
                        ->join('company_department', 'ticket.company_department_id', 'company_department.id')
                        ->join('company_department_settings', 'company_department.id', 'company_department_settings.company_department_id')
                        ->where('ticket.company_id', Crypt::decrypt(session('companyselected')['id']))
                        ->where('ticket.status', 'IN_PROGRESS')
                        ->whereIn('ticket.company_department_id', $departments)
                        ->whereRaw("DATE_ADD(ticket.updated_at, INTERVAL JSON_EXTRACT(company_department_settings.settings, '$.quant_limity.lateTime') MINUTE)  < UTC_TIMESTAMP()")
                        ->groupBy(DB::raw(1));

        // Tickets em aberto
        $query1 = Ticket::select('ticket.status', DB::raw('COUNT(*) as qtd'), 'cc.chat_id as category_chat_id')
                        ->join('chat', 'ticket.id', 'chat.ticket_id')
                        ->leftJoin('chat_category AS cc', 'chat.id', 'cc.chat_id')
                        ->where('ticket.company_id', Crypt::decrypt(session('companyselected')['id']))
                        ->where('ticket.status', 'OPENED')
                        ->whereIn('ticket.company_department_id', $departments)
                        ->groupBy(DB::raw(1))
                        ->unionAll($query0);

        // Tickets cancelados e não atribuídos a ninguém
        $query2 = Ticket::select('ticket.status', DB::raw('COUNT(*) as qtd'), 'cc.chat_id as category_chat_id')
                        ->join('chat', 'ticket.id', 'chat.ticket_id')
                        ->leftJoin('user_ticket', 'ticket.id', 'user_ticket.ticket_id')
                        ->leftJoin('chat_category AS cc', 'chat.id', 'cc.chat_id')
                        ->where('ticket.company_id', Crypt::decrypt(session('companyselected')['id']))
                        ->where('ticket.status', 'CANCELED')
                        ->whereIn('ticket.company_department_id', $departments)
                        ->whereNull('user_ticket.id')
                        ->groupBy(DB::raw(1))
                        ->unionAll($query1);

        // Tickets 'CLOSED', 'RESOLVED', 'CANCELED'
        $query3 = Ticket::select('ticket.status', DB::raw('COUNT(*) as qtd'), DB::raw('null as category_chat_id'))
                        ->join('user_ticket', 'ticket.id', 'user_ticket.ticket_id')
                        ->join('company_user', 'user_ticket.company_user_id', 'company_user.id')
                        ->join('chat', 'chat.ticket_id', 'ticket.id')
                        ->whereNull('chat.deleted_at')
                        ->where('ticket.company_id', Crypt::decrypt(session('companyselected')['id']))
                        ->whereIn('ticket.status', ['CLOSED', 'RESOLVED', 'CANCELED', 'IN_PROGRESS'])
                        ->whereIn('ticket.company_department_id', $departments)
                        ->groupBy(DB::raw(1));


        if ($searchQuery != "") {
            switch ($selected) {
                case 'filter-nameComplete':
                    $query0->join('user_client_ticket', 'user_client_ticket.ticket_id', 'ticket.id')
                    ->join('user_client', 'user_client.id', 'user_client_ticket.user_client_id')
                    ->join('user_auth', 'user_auth.id', 'user_client.user_auth_id')
                    ->whereRaw('user_auth.name like "%'.$searchQuery.'%"');

                    $query1->join('user_client_ticket', 'user_client_ticket.ticket_id', 'ticket.id')
                    ->join('user_client', 'user_client.id', 'user_client_ticket.user_client_id')
                    ->join('user_auth', 'user_auth.id', 'user_client.user_auth_id')
                    ->whereRaw('user_auth.name like "%'.$searchQuery.'%"');

                    $query2->join('user_client_ticket', 'user_client_ticket.ticket_id', 'ticket.id')
                    ->join('user_client', 'user_client.id', 'user_client_ticket.user_client_id')
                    ->join('user_auth', 'user_auth.id', 'user_client.user_auth_id')
                    ->whereRaw('user_auth.name like "%'.$searchQuery.'%"');

                    $query3->join('user_client_ticket', 'user_client_ticket.ticket_id', 'ticket.id')
                    ->join('user_client', 'user_client.id', 'user_client_ticket.user_client_id')
                    ->join('user_auth', 'user_auth.id', 'user_client.user_auth_id')
                    ->whereRaw('user_auth.name like "%'.$searchQuery.'%"');

                    break;
                case 'filter-id':
                    $query0->whereRaw("ticket.id = $searchQuery");

                    $query1->whereRaw("ticket.id = $searchQuery");

                    $query2->whereRaw("ticket.id = $searchQuery");

                    $query3->whereRaw("ticket.id = $searchQuery");

                    break;
                case 'filter-description':
                    $query0->whereRaw('ticket.description like "%'.$searchQuery.'%"');

                    $query1->whereRaw('ticket.description like "%'.$searchQuery.'%"');

                    $query2->whereRaw('ticket.description like "%'.$searchQuery.'%"');

                    $query3->whereRaw('ticket.description like "%'.$searchQuery.'%"');

                    break;
                case 'filter-email':
                    $query0->join('user_client_ticket', 'user_client_ticket.ticket_id', 'ticket.id')
                    ->join('user_client', 'user_client.id', 'user_client_ticket.user_client_id')
                    ->join('user_auth', 'user_auth.id', 'user_client.user_auth_id')
                    ->whereRaw('user_auth.email like "%'.$searchQuery.'%"');

                    $query1->join('user_client_ticket', 'user_client_ticket.ticket_id', 'ticket.id')
                    ->join('user_client', 'user_client.id', 'user_client_ticket.user_client_id')
                    ->join('user_auth', 'user_auth.id', 'user_client.user_auth_id')
                    ->whereRaw('user_auth.email like "%'.$searchQuery.'%"');

                    $query2->join('user_client_ticket', 'user_client_ticket.ticket_id', 'ticket.id')
                    ->join('user_client', 'user_client.id', 'user_client_ticket.user_client_id')
                    ->join('user_auth', 'user_auth.id', 'user_client.user_auth_id')
                    ->whereRaw('user_auth.email like "%'.$searchQuery.'%"');

                    $query3->join('user_client_ticket', 'user_client_ticket.ticket_id', 'ticket.id')
                    ->join('user_client', 'user_client.id', 'user_client_ticket.user_client_id')
                    ->join('user_auth', 'user_auth.id', 'user_client.user_auth_id')
                    ->whereRaw('user_auth.email like "%'.$searchQuery.'%"');
                    break;

                case 'filter-operator':
                    $query0->join('user_auth', 'company_user.user_auth_id', 'user_auth.id')
                    ->whereRaw('user_auth.name like "%'.$searchQuery.'%"');

                    $query1->leftJoin('user_ticket', 'ticket.id', 'user_ticket.ticket_id')
                    ->join('company_user', 'user_ticket.company_user_id', 'company_user.id')
                    ->join('user_auth', 'company_user.user_auth_id', 'user_auth.id')
                    ->whereRaw('user_auth.name like "%'.$searchQuery.'%"');

                    $query2->join('company_user', 'user_ticket.company_user_id', 'company_user.id')
                    ->join('user_auth', 'company_user.user_auth_id', 'user_auth.id')
                    ->whereRaw('user_auth.name like "%'.$searchQuery.'%"');

                    $query3->join('user_auth', 'company_user.user_auth_id', 'user_auth.id')
                    ->whereRaw('user_auth.name like "%'.$searchQuery.'%"');
                    break;

                case 'filter-comment':
                    $query0->whereRaw('ticket.comments like "%'.$searchQuery.'%"');

                    $query1->whereRaw('ticket.comments like "%'.$searchQuery.'%"');

                    $query2->whereRaw('ticket.comments like "%'.$searchQuery.'%"');

                    $query3->whereRaw('ticket.comments like "%'.$searchQuery.'%"');

                    break;
            }
        }
        
        if ($my_tickets) {
            $query0->whereRaw("company_user.user_auth_id = ".Auth::id()."");
            $query3->where('company_user.user_auth_id', Auth::id());
            $query3->unionAll($query1);
        } else {
            $query3->unionAll($query2);
        }

        if ($not_category) {
            if ($not_category) {
                $query1->where('cc.chat_id', null);
                $query2->where('cc.chat_id', null);
                $query3->where('cc.chat_id', null);
            }
        }

        $result = $query3->get();

        $response = [];
        $canceled = 0;

        foreach ($result as $row) {
            if ($row->status == 'CANCELED') {
                $canceled = $canceled + $row->qtd;
            }
        }

        foreach ($result as $row) {
            if ($row->status == 'CANCELED') {
                $response[strtolower($row->status)] = $canceled;
            } else {
                $response[strtolower($row->status)] = $row->qtd;
            }
        }

        return response()->json($response);
    }

    public function getTicketsOnQueue(Request $request) {
        /** begin */
		try {
			$data =  $request->all();

            $selected = $request->selected;
            $searchQuery = $request->searchQuery;
            $not_category = $request->not_category;

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

            $query = Ticket::select(
                    'ticket.id',
                    'ticket.id as number',
                    'ticket.id as ticket_id',
                    'ticket.status',
                    'ticket.description',
                    'ticket.created_at',
                    'ticket.updated_at',
                    'ticket.created_by',
                    'c.created_by AS chat_created_by',
                    'ticket.priority',
                    'ticket.type',
                    'ticket.user_agent',
                    'ticket.comments',
                    'ticket.company_id AS company_id',
                    'ticket.update_status_in_progress',
                    DB::raw('null as name'),
                    DB::raw('null as email'),
                    'cd.name AS department',
                    'cd.type AS department_type',
                    'cd.id AS department_id',
                    'cucd.id AS company_user_company_department_id',
                    'cds.settings',
                    DB::raw('COALESCE(ua_client.id, ua_create.id) AS id_created'),
                    DB::raw('COALESCE(ua_client.name, ua_create.name)  AS name_created'),
                    DB::raw('COALESCE(ua_client.email, ua_create.email) AS email_created'),
                    'ua_client.builderall_account_data AS builderall_account_data',
                    'c.service_time',
                    'c.update_status_in_progress AS last_update_status',
                    'c.id AS chat_id',
                    'cucd.company_user_id',
                    'c.type AS chat_type',
                    DB::raw('IF((SELECT company_user_company_department_id FROM chat_history WHERE chat_id = c.id AND type != "EVENT" ORDER BY id DESC LIMIT 1) IS NULL,1,0) AS answered'),
                    DB::raw('IF((SELECT ticket_id_origin FROM ticket_merge as tm WHERE ticket.id = tm.ticket_id_origin LIMIT 1) IS NOT NULL,1,0) AS is_merged'),
                    DB::raw('DATE_FORMAT(ticket.created_at,"%d/%m/%Y") as date'),
					DB::raw('DATE_FORMAT(ticket.created_at,"%H:%i:%s") as time'),
                    'cc.chat_id as category_chat_id'
                )
                ->join('company_department AS cd', 'ticket.company_department_id', 'cd.id')
                ->join('company_department_settings AS cds', 'cd.id', 'cds.company_department_id')
                ->join('company_user_company_department AS cucd', 'cds.company_department_id', 'cucd.company_department_id')
                ->join('company_user AS cu', 'cucd.company_user_id', 'cu.id')
                ->join('user_auth AS ua_create', 'ticket.created_by', 'ua_create.id')
                ->leftJoin('chat AS c', 'ticket.id', 'c.ticket_id')
                ->leftJoin('user_client_ticket AS uct', 'ticket.id', 'uct.ticket_id')
                ->leftJoin('user_client AS uc', 'uct.user_client_id', 'uc.id')
                ->leftJoin('user_auth AS ua_client', 'uc.user_auth_id', 'ua_client.id')
                ->leftJoin('chat_category AS cc', 'c.id', 'cc.chat_id')
                ->where('cu.company_id', Crypt::decrypt(session('companyselected')['id']))
                ->where('cucd.is_active', 1)
                ->where('ticket.status', "OPENED")
                ->whereIn('cd.id', $departments)
                ->whereNull('cd.deleted_at');

                if ($searchQuery != "") {
                    switch ($selected) {
                        case 'filter-nameComplete':
                            $query->whereRaw('ua_create.name like "%'.$searchQuery.'%"');
                            break;

                        case 'filter-id':
                            $query->whereRaw("ticket.id = $searchQuery");
                            break;

                        case 'filter-description':
                            $query->whereRaw('ticket.description like "%'.$searchQuery.'%"');
                            break;

                        case 'filter-email':
                            $query->whereRaw('ua_create.email like "%'.$searchQuery.'%"');
                            break;

                        case 'filter-operator':
                            $query->whereRaw('1 = 0');
                            break;

                        case 'filter-comment':
                            $query->whereRaw('ticket.comments like "%'.$searchQuery.'%"');
                            break;
                    }
                }

                if ($not_category) {
                    $query->where('cc.chat_id', null);
                }

                switch(request('order')){
                    case 'id':
                        if(request('isActive') == 'true'){
                            $query->orderBy('id', 'DESC');
                        }else{
                            $query->orderBy('id', 'ASC');
                        }
                        break;
                    case 'created_at':
                        if(request('isActive') == 'true'){
                            $query->orderBy('ticket.created_at', 'ASC');
                        }else{
                            $query->orderBy('ticket.created_at', 'DESC');
                        }
                        break;
                    case 'name_created':
                        if(request('isActive') == 'true'){
                            $query->orderBy('name_created', 'DESC');
                        }else{
                            $query->orderBy('name_created', 'ASC');
                        }
                        break;
                    case 'email_created':
                        if(request('isActive') == 'true'){
                            $query->orderBy('email_created', 'DESC');
                        }else{
                            $query->orderBy('email_created', 'ASC');
                        }
                        break;
                    case 'department':
                        if(request('isActive') == 'true'){
                            $query->orderBy('department', 'DESC');
                        }else{
                            $query->orderBy('department', 'ASC');
                        }
                        break;
                    default:
                        $query->orderBy('ticket.created_at', 'ASC');
                    break;
                }

                $result = $query->groupBy('cd.id', 'ticket.id')
                ->paginate($data['per_page']);

			foreach ($result as $row) {
				$row->ticket_id = Crypt::encrypt($row->ticket_id);
                $row->email_created = Client::getCleanEmail($row->email_created, $row->company_id);
				$row->company_id = Crypt::encrypt($row->company_id);
				$row->company_department_id = Crypt::encrypt($row->company_department_id);
				$row->client_id = Crypt::encrypt($row->id_created);
				$row->company_user_company_department_id = Crypt::encrypt($row->company_user_company_department_id);
                $row->chat_id_crypt = Crypt::encrypt($row->chat_id);
                $row->department_id = Crypt::encrypt($row->department_id);
			}

            return response()->json($result);

		} catch (\Exception $e) {
            echo $e;
			Logger::reportException($e, [], ['chat-controller', 'getChatsOnQueue'], false);
		}
    }

    public function getTicketsInProgress(Request $request) {
        /** begin */
		try {
			$data =  $request->all();

            $selected = $request->selected;
            $searchQuery = $request->searchQuery;

            $my_tickets = $request->my_tickets;
            $not_category = $request->not_category;
            $overdue = $request->overdue;
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

            $query = Ticket::select(
                    'ticket.id',
                    'ticket.id as number',
                    'ticket.id as ticket_id',
                    'ticket.status',
                    'ticket.description',
                    'ticket.created_at',
                    'ticket.updated_at',
                    'ticket.created_by',
                    'c.created_by AS chat_created_by',
                    'ticket.priority',
                    'ticket.type',
                    'ticket.user_agent',
                    'ticket.comments',
                    'ticket.company_id AS company_id',
                    'ticket.update_status_in_progress',
                    'ua.name as name',
                    'ua.email as email',
                    'ua.id as operator_id',
                    'cd.name AS department',
                    'cd.type AS department_type',
                    'cd.id AS department_id',
                    'cucd.id AS company_user_company_department_id',
                    DB::raw('COALESCE(ua_client.id, ua_create.id) AS id_created'),
                    DB::raw('COALESCE(ua_client.name, ua_create.name)  AS name_created'),
                    DB::raw('COALESCE(ua_client.email, ua_create.email) AS email_created'),
                    'ua_client.builderall_account_data AS builderall_account_data',
                    'c.service_time',
                    'c.update_status_in_progress AS last_update_status',
                    'c.id AS chat_id',
                    'cucd.company_user_id',
                    'c.type AS chat_type',
                    'cds.settings',
                    DB::raw('IF((SELECT company_user_company_department_id FROM chat_history WHERE chat_id = c.id AND type != "EVENT" ORDER BY id DESC LIMIT 1) IS NULL,1,0) AS answered'),
                    DB::raw('DATE_FORMAT(ticket.created_at,"%d/%m/%Y") as date'),
					DB::raw('DATE_FORMAT(ticket.created_at,"%H:%i:%s") as time')
                )
                ->join('user_ticket AS ut', 'ticket.id', 'ut.ticket_id')
                ->join('company_department AS cd', 'ticket.company_department_id', 'cd.id')
                ->join('company_department_settings AS cds', 'cd.id', 'cds.company_department_id')
                ->join('chat AS c', 'ticket.id', 'c.ticket_id')
                ->leftjoin('user_auth AS ua_create', 'ticket.created_by', 'ua_create.id')
                ->leftjoin('company_user_company_department AS cucd', 'c.comp_user_comp_depart_id_current', 'cucd.id')
                ->leftjoin('company_user AS cu', 'cucd.company_user_id', 'cu.id')
                ->leftjoin('user_auth AS ua', 'cu.user_auth_id', 'ua.id')
                ->leftJoin('user_client_ticket AS uct', 'ticket.id', 'uct.ticket_id')
                ->leftJoin('user_client AS uc', 'uct.user_client_id', 'uc.id')
                ->leftJoin('user_auth AS ua_client', 'uc.user_auth_id', 'ua_client.id')
                ->where('ticket.company_id', Crypt::decrypt(session('companyselected')['id']))
                ->where('ticket.status', 'IN_PROGRESS')
                ->whereIn('cd.id', $departments)
                ->whereNull('cd.deleted_at')
                ->whereNull('ticket.deleted_at');

            if ($searchQuery != "") {
                switch ($selected) {
                    case 'filter-nameComplete':
                        $query->whereRaw('ua_create.name like "%'.$searchQuery.'%"');
                        break;

                    case 'filter-id':
                        $query->whereRaw("ticket.id = $searchQuery");
                        break;

                    case 'filter-description':
                        $query->whereRaw('ticket.description like "%'.$searchQuery.'%"');
                        break;

                    case 'filter-email':
                        $query->whereRaw('ua_create.email like "%'.$searchQuery.'%"');
                        break;

                    case 'filter-operator':
                        $query->whereRaw('ua.name like "%'.$searchQuery.'%"');
                        break;

                    case 'filter-comment':
                        $query->whereRaw('ticket.comments like "%'.$searchQuery.'%"');
                        break;
                }
            }

            if ($my_tickets) {
                $query->where('cucd.company_user_id', intval(Crypt::decrypt(session('companyselected')['company_user_id'])))
                    ->whereRaw('cucd.company_department_id IN
                        (SELECT cucd2.company_department_id FROM company_user_company_department cucd2
                        JOIN company_user cu2 ON cucd2.company_user_id = cu2.id
                        WHERE cu2.user_auth_id = '.Auth::id().'
                        AND cucd2.is_active = 1)'
                );
            }
            
            if ($not_category) {
                // AND NOT EXISTS(SELECT id FROM chat_category WHERE chat_id = c.id)
                $query->whereNotExists(function ($query) {
                    $query->select(DB::raw(1))
                          ->from('chat_category')
                          ->whereRaw('chat_category.chat_id = c.id');
                });
            }

            if ($overdue === 'true' || ($overdue && $overdue !== 'false')) {
                $query->whereRaw("DATE_ADD(ticket.updated_at, INTERVAL JSON_EXTRACT(cds.settings, '$.quant_limity.lateTime') MINUTE)  < UTC_TIMESTAMP()");
            }

            switch(request('order')){
                case 'id':
                    if(request('isActive') == 'true'){
                        $query->orderBy('id', 'DESC');
                    }else{
                        $query->orderBy('id', 'ASC');
                    }
                    break;
                case 'created_at':
                    if(request('isActive') == 'true'){
                        $query->orderBy('ticket.created_at', 'ASC');
                    }else{
                        $query->orderBy('ticket.created_at', 'DESC');
                    }
                    break;
                case 'name_created':
                    if(request('isActive') == 'true'){
                        $query->orderBy('name_created', 'DESC');
                    }else{
                        $query->orderBy('name_created', 'ASC');
                    }
                    break;
                case 'answered':
                    if(request('isActive') == 'true'){
                        $query->orderBy('answered', 'DESC' ,'ticket.id', 'ASC');
                    }else{
                        $query->orderBy('answered', 'ASC' ,'ticket.id', 'ASC');
                    }
                    break;
                case 'department':
                    if(request('isActive') == 'true'){
                        $query->orderBy('department', 'DESC');
                    }else{
                        $query->orderBy('department', 'ASC');
                    }
                    break;
                case 'email_created':
                    if(request('isActive') == 'true'){
                        $query->orderBy('email_created', 'DESC');
                    }else{
                        $query->orderBy('email_created', 'ASC');
                    }
                    break;
                case 'name':
                    if(request('isActive') == 'true'){
                        $query->orderBy('name', 'DESC');
                    }else{
                        $query->orderBy('name', 'ASC');
                    }
                    break;
                default:
                    $query->orderBy('answered', 'DESC' ,'ticket.id', 'ASC');
                    break;
            }


            $result = $query->groupBy('cd.id', 'ticket.id')->paginate($data['per_page']);

			foreach ($result as $row) {
				$row->ticket_id = Crypt::encrypt($row->ticket_id);
                $row->email_created = Client::getCleanEmail($row->email_created, $row->company_id);
				$row->company_id = Crypt::encrypt($row->company_id);
				$row->company_department_id = Crypt::encrypt($row->company_department_id);
				$row->client_id = Crypt::encrypt($row->id_created);
                $row->company_user_company_department_id = Crypt::encrypt($row->company_user_company_department_id);
                $row->chat_id_crypt = Crypt::encrypt($row->chat_id);
                $row->department_id = Crypt::encrypt($row->department_id);
			}

            return response()->json($result);

		} catch (\Exception $e) {
            echo $e;
			Logger::reportException($e, [], ['chat-controller', 'getTicketsInProgress'], false);
		}
    }

    public function getTicketsFinished(Request $request) {
         /** begin */
		try {
			$data =  $request->all();

            $my_tickets = $request->my_tickets;
            $not_category = $request->not_category;
            $selected = $request->selected;
            $searchQuery = $request->searchQuery;

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

            $query = Ticket::select(
                    'ticket.id',
                    'ticket.id as number',
                    'ticket.id as ticket_id',
                    'ticket.status',
                    'ticket.description',
                    'ticket.created_at',
                    'ticket.updated_at',
                    'ticket.updated_at as end',
                    'ticket.created_by',
                    'c.created_by AS chat_created_by',
                    'ticket.priority',
                    'ticket.type',
                    'ticket.user_agent',
                    'ticket.comments',
                    'ticket.company_id AS company_id',
                    'ticket.update_status_in_progress',
                    'ua.name as name',
                    'ua.email as email',
                    'ua.id as operator_id',
                    'cd.name AS department',
                    'cd.type AS department_type',
                    'cd.id AS department_id',
                    'cucd.id AS company_user_company_department_id',
                    DB::raw('COALESCE(ua_client.id, ua_create.id) AS id_created'),
                    DB::raw('COALESCE(ua_client.name, ua_create.name)  AS name_created'),
                    DB::raw('COALESCE(ua_client.email, ua_create.email) AS email_created'),
                    'ua_client.builderall_account_data AS builderall_account_data',
                    'c.service_time',
                    'c.update_status_in_progress AS last_update_status',
                    'c.id AS chat_id',
                    'cucd.company_user_id',
                    'c.type AS chat_type',
                    'cds.settings',
                    DB::raw('IF((SELECT company_user_company_department_id FROM chat_history WHERE chat_id = c.id AND type != "EVENT" ORDER BY id DESC LIMIT 1) IS NULL,1,0) AS answered'),
                    DB::raw('DATE_FORMAT(ticket.created_at,"%d/%m/%Y") as date'),
					DB::raw('DATE_FORMAT(ticket.created_at,"%H:%i:%s") as time'),
                    DB::raw('IF((SELECT ticket_id_origin FROM ticket_merge as tm WHERE ticket.id = tm.ticket_id_origin LIMIT 1) IS NOT NULL,1,0) AS is_merged'),
                    'cc.chat_id as category_chat_id'
                )
                ->join('user_ticket AS ut', 'ticket.id', 'ut.ticket_id')
                ->join('company_department AS cd', 'ticket.company_department_id', 'cd.id')
                ->join('company_department_settings AS cds', 'cd.id', 'cds.company_department_id')
                ->join('chat AS c', 'ticket.id', 'c.ticket_id')
                ->leftJoin('user_auth AS ua_create', 'ticket.created_by', 'ua_create.id')
                ->leftJoin('company_user_company_department AS cucd', 'c.comp_user_comp_depart_id_current', 'cucd.id')
                ->leftJoin('company_user AS cu', 'cucd.company_user_id', 'cu.id')
                ->leftJoin('user_auth AS ua', 'cu.user_auth_id', 'ua.id')
                ->leftJoin('user_client_ticket AS uct', 'ticket.id', 'uct.ticket_id')
                ->leftJoin('user_client AS uc', 'uct.user_client_id', 'uc.id')
                ->leftJoin('user_auth AS ua_client', 'uc.user_auth_id', 'ua_client.id')
                ->leftJoin('chat_category AS cc', 'c.id', 'cc.chat_id')
                ->where('ticket.company_id', Crypt::decrypt(session('companyselected')['id']))
                ->whereIn('ticket.status', ['CLOSED', 'RESOLVED'])
                ->whereIn('cd.id', $departments)
                ->whereNull('cd.deleted_at');

            if ($searchQuery != "") {
                switch ($selected) {
                    case 'filter-nameComplete':
                        $query->whereRaw('ua_create.name like "%'.$searchQuery.'%"');
                        break;

                    case 'filter-id':
                        $query->whereRaw("ticket.id = $searchQuery");
                        break;

                    case 'filter-description':
                        $query->whereRaw('ticket.description like "%'.$searchQuery.'%"');
                        break;

                    case 'filter-email':
                        $query->whereRaw('ua_create.email like "%'.$searchQuery.'%"');
                        break;

                    case 'filter-operator':
                        $query->whereRaw('ua.name like "%'.$searchQuery.'%"');
                        break;

                    case 'filter-comment':
                        $query->whereRaw('ticket.comments like "%'.$searchQuery.'%"');
                        break;
                }
            }

            if ($my_tickets) {
                $query->where('cucd.company_user_id', intval(Crypt::decrypt(session('companyselected')['company_user_id'])))
                    ->whereRaw('cucd.company_department_id IN
                        (SELECT cucd2.company_department_id FROM company_user_company_department cucd2
                        JOIN company_user cu2 ON cucd2.company_user_id = cu2.id
                        WHERE cu2.user_auth_id = '.Auth::id().'
                        AND cucd2.is_active = 1)'
                );
            }

            if ($not_category) {
                $query->where('cc.chat_id', null);
            }

            switch(request('order')){
                case 'id':
                    if(request('isActive') == 'true'){
                        $query->orderBy('id', 'DESC');
                    }else{
                        $query->orderBy('id', 'ASC');
                    }
                    break;
                case 'created_at':
                    if(request('isActive') == 'true'){
                        $query->orderBy('ticket.created_at', 'DESC');
                    }else{
                        $query->orderBy('ticket.created_at', 'ASC');
                    }
                    break;
                case 'end':
                    if(request('isActive') == 'true'){
                        $query->orderBy('end', 'DESC');
                    }else{
                        $query->orderBy('end', 'ASC');
                    }
                    break;
                case 'name_created':
                    if(request('isActive') == 'true'){
                        $query->orderBy('name_created', 'DESC');
                    }else{
                        $query->orderBy('name_created', 'ASC');
                    }
                    break;
                case 'email_created':
                    if(request('isActive') == 'true'){
                        $query->orderBy('email_created', 'DESC');
                    }else{
                        $query->orderBy('email_created', 'ASC');
                    }
                    break;
                case 'name':
                    if(request('isActive') == 'true'){
                        $query->orderBy('name', 'DESC');
                    }else{
                        $query->orderBy('name', 'ASC');
                    }
                    break;
                case 'department':
                    if(request('isActive') == 'true'){
                        $query->orderBy('department', 'DESC');
                    }else{
                        $query->orderBy('department', 'ASC');
                    }
                    break;
                default:
                    $query->orderBy('id', 'DESC');
                    break;    
            }

            $result = $query->groupBy('cd.id', 'ticket.id')->paginate($data['per_page']);

			foreach ($result as $row) {
				$row->ticket_id = Crypt::encrypt($row->ticket_id);
                $row->email_created = Client::getCleanEmail($row->email_created, $row->company_id);
				$row->company_id = Crypt::encrypt($row->company_id);
				$row->company_department_id = Crypt::encrypt($row->company_department_id);
				$row->client_id = Crypt::encrypt($row->id_created);
                $row->company_user_company_department_id = Crypt::encrypt($row->company_user_company_department_id);
                $row->chat_id_crypt = Crypt::encrypt($row->chat_id);
                $row->department_id = Crypt::encrypt($row->department_id);
			}

            return response()->json($result);

		} catch (\Exception $e) {
            echo $e;
			Logger::reportException($e, [], ['chat-controller', 'getTicketsFinished'], false);
		}
    }

    public function getTicketsCanceled(Request $request) {
         /** begin */
		try {
			$data =  $request->all();

            $my_tickets = $request->my_tickets;
            $selected = $request->selected;
            $searchQuery = $request->searchQuery;
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

            $query = Ticket::select(
                'ticket.id',
                'ticket.id as number',
                'ticket.id as ticket_id',
                'ticket.status',
                'ticket.description',
                'ticket.created_at',
                'ticket.updated_at',
                'ticket.updated_at as end',
                'ticket.created_by',
                'c.created_by AS chat_created_by',
                'ticket.priority',
                'ticket.type',
                'ticket.user_agent',
                'ticket.comments',
                'ticket.company_id AS company_id',
                'ticket.update_status_in_progress',
                'ua.name as name',
                'ua.email as email',
                'ua.id as operator_id',
                'cd.name AS department',
                'cd.type AS department_type',
                'cd.id AS department_id',
                'cucd.id AS company_user_company_department_id',
                DB::raw('COALESCE(ua_client.id, ua_create.id) AS id_created'),
                DB::raw('COALESCE(ua_client.name, ua_create.name)  AS name_created'),
                DB::raw('COALESCE(ua_client.email, ua_create.email) AS email_created'),
                'ua_client.builderall_account_data AS builderall_account_data',
                'c.service_time',
                'c.update_status_in_progress AS last_update_status',
                'c.id AS chat_id',
                'cucd.company_user_id',
                'c.type AS chat_type',
                'cds.settings',
                DB::raw('IF((SELECT company_user_company_department_id FROM chat_history WHERE chat_id = c.id AND type != "EVENT" ORDER BY id DESC LIMIT 1) IS NULL,1,0) AS answered'),
                DB::raw('DATE_FORMAT(ticket.created_at,"%d/%m/%Y") as date'),
				DB::raw('DATE_FORMAT(ticket.created_at,"%H:%i:%s") as time'),
            )
            ->leftJoin('user_ticket AS ut', 'ticket.id', 'ut.ticket_id')
            ->leftJoin('company_department AS cd', 'ticket.company_department_id', 'cd.id')
            ->leftJoin('company_department_settings AS cds', 'cd.id', 'cds.company_department_id')
            ->leftJoin('chat AS c', 'ticket.id', 'c.ticket_id')
            ->leftJoin('user_auth AS ua_create', 'ticket.created_by', 'ua_create.id')
            ->leftJoin('company_user_company_department AS cucd', 'c.comp_user_comp_depart_id_current', 'cucd.id')
            ->leftJoin('company_user AS cu', 'cucd.company_user_id', 'cu.id')
            ->leftJoin('user_auth AS ua', 'cu.user_auth_id', 'ua.id')
            ->leftJoin('user_client_ticket AS uct', 'ticket.id', 'uct.ticket_id')
            ->leftJoin('user_client AS uc', 'uct.user_client_id', 'uc.id')
            ->leftJoin('user_auth AS ua_client', 'uc.user_auth_id', 'ua_client.id')
            ->where('ticket.company_id', Crypt::decrypt(session('companyselected')['id']))
            ->where('ticket.status', 'CANCELED')
            ->whereIn('cd.id', $departments)
            ->whereNull('cd.deleted_at');

            if ($searchQuery != "") {
                switch ($selected) {
                    case 'filter-nameComplete':
                        $query->whereRaw('ua_create.name like "%'.$searchQuery.'%"');
                        break;

                    case 'filter-id':
                        $query->whereRaw("ticket.id = $searchQuery");
                        break;

                    case 'filter-description':
                        $query->whereRaw('ticket.description like "%'.$searchQuery.'%"');
                        break;

                    case 'filter-email':
                        $query->whereRaw('ua_create.email like "%'.$searchQuery.'%"');
                        break;

                    case 'filter-operator':
                        $query->whereRaw('ua.name like "%'.$searchQuery.'%"');
                        break;

                    case 'filter-comment':
                        $query->whereRaw('ticket.comments like "%'.$searchQuery.'%"');
                        break;
                }
            }

            if ($my_tickets) {
                $query->where('cucd.company_user_id', intval(Crypt::decrypt(session('companyselected')['company_user_id'])))
                    ->whereRaw('cucd.company_department_id IN
                        (SELECT cucd2.company_department_id FROM company_user_company_department cucd2
                        JOIN company_user cu2 ON cucd2.company_user_id = cu2.id
                        WHERE cu2.user_auth_id = '.Auth::id().'
                        AND cucd2.is_active = 1)'
                );
            }


            switch(request('order')){
                case 'id':
                    if(request('isActive') == 'true'){
                        $query->orderBy('id', 'DESC');
                    }else{
                        $query->orderBy('id', 'ASC');
                    }
                    break;
                case 'created_at':
                    if(request('isActive') == 'true'){
                        $query->orderBy('ticket.created_at', 'DESC');
                    }else{
                        $query->orderBy('ticket.created_at', 'ASC');
                    }
                    break;
                case 'end':
                    if(request('isActive') == 'true'){
                        $query->orderBy('end', 'DESC');
                    }else{
                        $query->orderBy('end', 'ASC');
                    }
                    break;
                case 'name_created':
                    if(request('isActive') == 'true'){
                        $query->orderBy('name_created', 'DESC');
                    }else{
                        $query->orderBy('name_created', 'ASC');
                    }
                    break;
                case 'email_created':
                    if(request('isActive') == 'true'){
                        $query->orderBy('email_created', 'DESC');
                    }else{
                        $query->orderBy('email_created', 'ASC');
                    }
                    break;
                case 'name':
                    if(request('isActive') == 'true'){
                        $query->orderBy('name', 'DESC');
                    }else{
                        $query->orderBy('name', 'ASC');
                    }
                    break;
                case 'department':
                    if(request('isActive') == 'true'){
                        $query->orderBy('department', 'DESC');
                    }else{
                        $query->orderBy('department', 'ASC');
                    }
                    break;
                default:
                    $query->orderBy('id', 'DESC');
                    break;    
            }

            $result = $query->groupBy('cd.id', 'ticket.id')->paginate($data['per_page']);

			foreach ($result as $row) {
				$row->ticket_id = Crypt::encrypt($row->ticket_id);
                $row->email_created = Client::getCleanEmail($row->email_created, $row->company_id);
				$row->company_id = Crypt::encrypt($row->company_id);
				$row->company_department_id = Crypt::encrypt($row->company_department_id);
				$row->client_id = Crypt::encrypt($row->id_created);
                $row->company_user_company_department_id = Crypt::encrypt($row->company_user_company_department_id);
                $row->chat_id_crypt = Crypt::encrypt($row->chat_id);
                $row->department_id = Crypt::encrypt($row->department_id);
			}

            return response()->json($result);

		} catch (\Exception $e) {
			Logger::reportException($e, [], ['chat-controller', 'getTicketsCanceled'], false);
		}

    }

    public function getCounterPerClient(Request $request) {
        try {

            $client_id = Crypt::decrypt($request->client_id);

            $in_progress = Ticket::join('user_client_ticket', 'user_client_ticket.ticket_id', 'ticket.id')
            ->join('user_client', 'user_client_ticket.user_client_id', 'user_client.id')
            ->where('user_client.user_auth_id', $client_id)
            ->where('ticket.company_id', Crypt::decrypt(session('companyselected')['id']))
            ->where('ticket.status', 'IN_PROGRESS')
            ->count();

            $queue = Ticket::join('user_client_ticket', 'user_client_ticket.ticket_id', 'ticket.id')
            ->join('user_client', 'user_client_ticket.user_client_id', 'user_client.id')
            ->where('user_client.user_auth_id', $client_id)
            ->where('ticket.company_id', Crypt::decrypt(session('companyselected')['id']))
            ->where('ticket.status', 'OPENED')
            ->count();

            $show = $in_progress > 0 || $queue > 0;

            return response()->json([
                'queue' => $queue,
                'in_progress' => $in_progress,
                'success' => true,
                'show' => $show
            ]);

        } catch (\Exception $e) {
            Logger::reportException($e, [], ['full-ticket-controller-2', 'getCounterPerClient'], false);
        }
    }
}
