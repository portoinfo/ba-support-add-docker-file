<?php

namespace App\Http\Controllers;

use App\Chat;
use App\Ticket;
use App\Tools\Builderall\Logger;
use App\Tools\Client;
use App\Tools\Crypt;
use App\Tools\exportExcel;
use App\Tools\Tickets\Feedback;
use Carbon\Carbon;
use DOMDocument;
use DOMXPath;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FilterController extends Controller
{
    public function index()
    {
        return view('functions.filter.index');
    }

    public function indexCategory()
    {
        return view('functions.filter.category');
    }

    public function filter(Request $request)
    {
        $query = Chat::select(
            "ua_agent.name",
            "ua_agent.name as operator_id",
            "ua_agent.email",
            "chat.id as chat_number",
            "chat.id as chat_id",
            "company_department.name as department",
            "company_department.type as department_type",
            "company_department.id as department_id",
            "chat.status",
            "chat.is_robot",
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
            "ua_client.name as name_client",
            "ua_client.id as id_client",
            "ua_client.email as email_client",
            "ua_client.builderall_account_data",
            DB::raw('IF(ticket.id IS NULL, chat.id, ticket.id) AS number_order'),
            DB::raw('IF((SELECT ticket_id_origin FROM ticket_merge as tm WHERE ticket.id = tm.ticket_id_origin LIMIT 1) IS NOT NULL,1,0) AS is_merged'),
        )
            ->join("user_client_chat", "chat.id", "user_client_chat.chat_id")
            ->join("user_client as uc", "uc.id", "user_client_chat.user_client_id")
            ->join("user_auth as ua_client", "ua_client.id", "uc.user_auth_id")
            ->join("company_department", "company_department.id", "chat.company_department_id")
            ->leftJoin("company_user_company_department", "company_user_company_department.id", "chat.comp_user_comp_depart_id_current")
            ->leftJoin("company_user", "company_user.id", "company_user_company_department.company_user_id")
            ->leftJoin("user_auth as ua_agent", "ua_agent.id", "company_user.user_auth_id")
            ->leftJoin("ticket", "ticket.id", "chat.ticket_id")
            ->where('company_user.company_id', Crypt::decrypt(session('companyselected')['id']))
            ->orderBy("number_order", "desc");

        if ($request->filter_date === "true") {

            $date=  gmdate('d/m/Y', strtotime($request->date1));
            $date = $date . ' 00:00:00';
            $from = Carbon::createFromFormat('d/m/Y H:i:s', $date, $request->tz)->setTimezone('UTC');

            $date2=  gmdate('d/m/Y', strtotime($request->date2));
            $date2 = $date2 . ' 23:59:59';
            $to = Carbon::createFromFormat('d/m/Y H:i:s', $date2, $request->tz)->setTimezone('UTC');
  
            $from = $from->format('Y/m/d H:i:s');
            $to = $to->format('Y/m/d H:i:s');

            $query->whereRaw('COALESCE(ticket.created_at, chat.created_at) BETWEEN "' . $from . '" AND "' . $to . '"');
        }

        if ($request->type === 'TICKET') {

            $query->where('ticket_id', '!=', NULL);
        } else if ($request->type === 'CHAT') {
            $query->where('ticket_id', NULL);
        }

        if ($request->status  !== 'ALL') {
            if ($request->type === 'TICKET') {
                $query->where('ticket.status', $request->status);
            } else if ($request->type === 'CHAT') {
                $query->where('chat.status', $request->status);
            } else if ($request->type === 'ALL') {
                if($request->status  === 'IN_PROGRESS'){
                    $query->whereRaw("(( chat.ticket_id IS NOT NULL AND ticket.status IN ('IN_PROGRESS') ) OR (chat.ticket_id IS NULL AND chat.status IN ('IN_PROGRESS')))");
                }else{
                    $query->whereRaw("(chat.status IN ('" . $request->status . "') or ticket.status IN ('" . $request->status . "'))");
                }
            }
        } else {
            // NÃO É POSSIVEL FILTRAR TICKETS QUE FORAM MERGED QUANDO ESTAVAM NA FILA.
            // POIS NÃO TINHAM ATENDENTES, ESSA QUERY SÓ TRAZ COM ATENDENTE.
            $query->whereRaw("(chat.status IN ('CLOSED', 'RESOLVED', 'CANCELED', 'IN_PROGRESS')
            or ticket.status IN ('CLOSED', 'RESOLVED', 'CANCELED', 'IN_PROGRESS', 'MERGED'))");
        }

        if ($request->department  !== 'ALL') {
            $query->where('company_department.id', Crypt::decrypt($request->department));
        }

        if ($request->search_query !== null) {

            switch ($request->option) {
                case 'filter-nameComplete':
                    $query->where('ua_client.name', 'like', '%' . strip_tags($request->search_query) . '%');
                    break;

                case 'filter-id':
                    if ($request->type === 'TICKET') {
                        $query->where('ticket.id', 'like', '%' . $request->search_query . '%');
                    } else if ($request->type === 'CHAT') {
                        $query->where('chat.id', 'like', '%' . $request->search_query . '%');
                    } else {
                        $query->where('ticket.id', 'like', '%' . $request->search_query . '%')
                            ->orWhere('chat.id', 'like', '%' . $request->search_query . '%');
                    }
                    break;

                case 'filter-description':
                    $query->join('chat_history', 'chat_history.chat_id', 'chat.id')
                        ->where('chat_history.content', 'like', '%' . $request->search_query . '%')
                        ->groupBy('chat.id', 'ticket.id');
                    break;

                case 'filter-email':
                    $query->where('ua_client.email', 'like', '%' . strip_tags($request->search_query) . '%');
                    break;

                case 'filter-operator':
                    $query->where('ua_agent.name', 'like', '%' . strip_tags($request->search_query) . '%');
                    break;

                case 'filter-comment':
                    $query->where('ticket.comments', 'like', '%' . strip_tags($request->search_query) . '%');
                    break;
            }
        }


        $result = $query->paginate($request->per_page);



        foreach ($result as $item) {
            $item->chat_number = Crypt::encrypt($item->chat_id);
            $item->ticket_number = Crypt::encrypt($item->ticket_id);
            $item->id_client = Crypt::encrypt($item->id_client);
            $item->operator_id = Crypt::encrypt($item->operator_id);
            $item->email_client = Client::forceCleanEmail($item->email_client);
            $item->is_robot = isset($item->comp_user_comp_depart_id_current) ? false : $item->is_robot;
        }

        return response()->json([
            'list' => $result
        ]);

    }


    public function filterTickets(Request $request){

        $sub = Ticket::select(
            'ticket.id',
            'ticket.status',
            'ticket.description',
            'ticket.created_at',
            'ticket.updated_at',
            'ticket.created_by',
            "c.created_by as chat_created_by",
            'ticket.priority',
            'ticket.type',
            'ticket.user_agent',
            'ticket.comments',
            'ticket.update_status_in_progress',
            'ua.name',
            'ua.email',
            'cd.name AS department',
            "cd.type as department_type",
            'cd.id AS department_id',
            'cucd.id as company_user_company_department_id',
            'cds.settings',
            DB::raw('COALESCE(ua_client.name, ua_create.name) AS name_created'),
            DB::raw('COALESCE(ua_client.email, ua_create.email) AS email_created'),
            'ua_client.builderall_account_data',
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
            ->where('c.company_id', Crypt::decrypt(session('companyselected')['id']))
            ->whereNull('cd.deleted_at')
            ->groupBy('cd.id', 'ticket.id');


            //CASO NÃO FOR ADMIN NEM ATENDENTE NORMAL = (ALLLIST ATIVADO) - TRAZER TODOS OS TICKETS
            if(!$request->alllist){
                $sub->whereRaw("(cu.user_auth_id = ".auth()->user()->id." OR ".session('restriction')[0]->ticket_admin." = 1)");
            }

            //CASO ELE SELECIONE UM STATUS
            if ($request->status  !== 'ALL') {
                $sub->where('ticket.status', $request->status);
            }

            //CASO ELE SELECIONE UM DEPARTAMENTO
            if ($request->department  !== 'ALL') {
                $sub->where('cd.id', Crypt::decrypt($request->department));
            }

            //CASO ELE DIGITE ALGO NO INPUT "BUSCAR"
            if ($request->search_query !== null) {
                switch ($request->option) {
                    case 'filter-nameComplete':
                        $sub->where('ua_client.name', 'like', '%' . strip_tags($request->search_query) . '%');
                        break;
                    case 'filter-id':
                        $sub->where('ticket.id', 'like', '%' . $request->search_query . '%');
                        break;
                    case 'filter-description':
                        $sub->join('chat_history', 'chat_history.chat_id', 'c.id')
                            ->where('chat_history.content', 'like', '%' . $request->search_query . '%')
                            ->groupBy('c.id', 'ticket.id');
                        break;

                    case 'filter-email':
                        $sub->where('ua_client.email', 'like', '%' . strip_tags($request->search_query) . '%');
                        break;

                    case 'filter-operator':
                        $sub->where('ua.name', 'like', '%' . strip_tags($request->search_query) . '%');
                        break;

                    case 'filter-comment':
                        $sub->where('ticket.comments', 'like', '%' . strip_tags($request->search_query) . '%');
                        break;
                }
            }

        $result = DB::table( DB::raw("({$sub->toSql()}) as sub") )
            ->mergeBindings($sub->getQuery())
            ->orderBy('answered', 'DESC')->orderBy('sub.updated_at')->get();


        foreach ($result as $key) {
            $key->department_id = Crypt::encrypt($key->department_id);
            $key->company_user_company_department_id = Crypt::encrypt($key->company_user_company_department_id);
            $key->overdue = json_decode($key->settings)->quant_limity->lateTime;
            $key->chat_id_decry = $key->chat_id;
            $key->chat_id = Crypt::encrypt($key->chat_id);
            $key->chat_created_by_encrypted = Crypt::encrypt($key->chat_created_by);
            //$key->company_id = Crypt::encrypt($key->company_id);
        }

        return json_encode($result);
    }

    public function getGenerateExcel(Request $request){
        
        $result['success'] = false;

        try {
            $tz = request('tz');
            $ids = request('ct_ids');
            $array_aux = [];

            foreach ($ids as $key){

                $chat_history = DB::table('chat_history as ch')
                ->join('chat', 'ch.chat_id', 'chat.id')
                ->leftjoin('ticket', 'chat.ticket_id', 'ticket.id')
                ->join('user_client_chat', 'user_client_chat.chat_id', 'chat.id')
                ->join('user_client', 'user_client.id', 'user_client_chat.user_client_id')
                ->join('user_auth as client', 'client.id', 'user_client.user_auth_id')
                ->leftjoin('company_user_company_department', 'company_user_company_department.id', 'ch.company_user_company_department_id')
                ->leftjoin('company_user', 'company_user.id', 'company_user_company_department.company_user_id')
                ->leftjoin('user_auth as attendant', 'attendant.id', 'company_user.user_auth_id')
                ->select('client.name as client_name', 'attendant.name as attendant_name', 'chat.id as chat_id', 'ticket.id as ticket_id', 'ch.id as chat_history_id', 'ch.type', 'ch.content', 'ch.content_translated',
                'ch.created_at')
                ->where('hidden_for_client', 0)
                ->where('ch.chat_id', $key)
                ->orderBy('ch.chat_id', 'asc')
                ->orderBy('ch.created_at', 'asc')
                ->get();
    
                $ticket_chat_answer = DB::table('ticket_chat_answer as tt')
                ->join('company_depart_settings_question as cc', 'tt.company_depart_settings_question_id', 'cc.id')
                ->select('tt.id', 'tt.chat_id', 'question', 'answer', 'tt.created_at')
                ->where('tt.chat_id', $key)
                ->get();
              
                foreach($ticket_chat_answer as $key){

                    array_push($array_aux, (object) array(
                        "client_name" => null,
                        "attendant_name" => Feedback::t('bs-department'),
                        "chat_id" => $key->chat_id,
                        "chat_history_id" => null,
                        "type" => "TEXT",
                        "content" => $key->question,
                        "content_translated" => null,
                        "created_at" => Carbon::createFromFormat('Y-m-d H:i:s', $key->created_at, 'UTC')->setTimezone($tz)
                    ));
                    
    
                    array_push($array_aux,  (object) array(
                        "client_name" => Feedback::t('bs-client'),
                        "attendant_name" => null,
                        "chat_id" => $key->chat_id,
                        "chat_history_id" => null,
                        "type" => "TEXT",
                        "content" => $key->answer,
                        "content_translated" => null,
                        "created_at" => Carbon::createFromFormat('Y-m-d H:i:s', $key->created_at, 'UTC')->setTimezone($tz)
                    ));
                }
    
                foreach($chat_history as $key){
                    array_push($array_aux, $key);
                }

                // array_push($listen, $array_aux); 
            }

            $result['chat_history'] = $array_aux;
        

            foreach($result['chat_history'] as $key){
                //CONVERTE IMAGEM EM LINK
                if($key->type == 'TEXT'){
                    if (str_contains($key->content, '<img')) {

                        try {
                            $html = $key->content;
                            $doc = new DOMDocument();
                            $doc->loadHTML($html);
                        } catch (\Throwable $th) {
                            $html = $key->content;
                            $html = preg_replace('/<\/span\s*[^>]*>/', '</span>', $html);
                            $doc = new DOMDocument();
                            $doc->loadHTML($html);
                        }
                        $xpath = new DOMXPath($doc);
                        $src = $xpath->evaluate("string(//img/@src)"); # "/images/image.jpg"

                        if(config('app.env') == 'sandbox'){ // IO
                            $key->content = 'https://ba-support.builderall.io/public/'.$src;
                            $key->content = 'http://localhost:8000/public/'.$src;
                        }else if(config('app.is_helpdesk')){ //.HS
                            $key->content = 'https://hs.builderall.com/public/'.$src;
                        }else{ //.COM
                            $key->content = 'https://ba-support.builderall.com/public/'.$src;
                        }
                    }
                }

                // TRADUZ OS EVENTOS E REMOVE AS TAGS
                if($key->type == 'EVENT'){
                    $key->content = Feedback::t($key->content);
                }else{
                    $key->content = strip_tags($key->content);
                }

                // {
                //     "message":"awdawdwadwadwad\r\n\r\nAtenciosamente,\r\nfuncionario admin",
                //     "files":[{"unique_name":"bWM1dU9pZFNUb2xIaEgvQjBBN0JIcEhSS1FVWWJnb2x2R2txQXpTNTF4THVaWlVkWUtPektTSmRHUzhWSFlXaA==.png",
                //         "original_name":"5ede4a3fb760540004f2c5e9.png",
                //         "type":"FILE"}]
                // }
                if($key->type == 'FILE'){

                    if($key->ticket_id){
                        $id = $key->chat_id;
                    }else{
                        $id = $key->chat_id;
                    }

                    if(json_decode($key->content) != null){
                        foreach(json_decode($key->content)->files as $image){
                            if(config('app.env') == 'sandbox'){ // IO
                                // $key->content = json_decode($key->content)->message.' '.'https://ba-support.builderall.io/public/'.Crypt::encrypt($key->chat_id).'/'.$image->unique_name;
                                $key->content = json_decode($key->content)->message.' '.'http://localhost:8000/public/chat/files/'.Crypt::encrypt($id).'/'.$image->unique_name;
                            }else if(config('app.is_helpdesk')){ //.HS
                                $key->content = json_decode($key->content)->message.' '.'https://hs.builderall.com/public/chat/files/'.Crypt::encrypt($id).'/'.$image->unique_name;
                            }else{ //.COM
                                $key->content = json_decode($key->content)->message.' '.'https://ba-support.builderall.com/public/chat/files/'.Crypt::encrypt($id).'/'.$image->unique_name;
                            }
                        }
                    }

                }
                

                // NOME DO ATENDENTE OU CLIENTE
                if($key->attendant_name == null){
                    $key->name = $key->client_name;
                }else{
                    $key->name = $key->attendant_name;
                }
                $key->created_at = Carbon::createFromFormat('Y-m-d H:i:s', $key->created_at, 'UTC')->setTimezone($tz);
            }

            $result['success'] = true;
        } catch (\Exception $e) {
            echo $e;
            Logger::reportException($e, [], ['filter-controller', 'getGenerateExcel'], false);
            $result['success'] = false;
        }

        return json_encode($result);
    }

    public function generateExcelChatTicket(){
		$agentsUsers['success'] = false;

		try {
            if(Crypt::decrypt(request('chat_id'))){
                $chat_id = intval(Crypt::decrypt(request('chat_id')));
                $type = 'CHAT';
            }else{
                $chat_id = request('chat_id');
                $type = 'TICKET';
            }

			$exportChat = new exportExcel;
            $link = $exportChat->generateListCT($chat_id,$type);

			$agentsUsers['data'] = $link;
			$agentsUsers['success'] = true;
		} catch (\Exception $e) {
			echo $e;
			Logger::reportException($e, [], ['AnalyzeController', 'generateFileExcel'], false);
			$agentsUsers['success'] = false;
		}
		return $agentsUsers;
	}

    public function historyCT(){
	
			$path = '..' . DIRECTORY_SEPARATOR . 'storage' . 
            DIRECTORY_SEPARATOR . 'app' . 
            DIRECTORY_SEPARATOR . 'public' . 
            DIRECTORY_SEPARATOR . 'document' . 
			DIRECTORY_SEPARATOR . 'history.xlsx';

			if (!File::exists($path)) {
				abort(404);
			}

			$file = File::get($path);
			$type = File::mimeType($path);

			ob_end_clean();

			$response = Response::make($file, 200);
			$response->header("Content-Type", $type);
			return $response;
	}
    
}
