<?php

namespace App\Http\Controllers;

use App\ChatHistory;
use App\Chat;
use App\CompanyUserCompanyDepartment;
use App\Events\ClientNotification;
use App\Events\ClientQueueStatus;
use App\Events\FullChatProgress;
use App\Events\GlobalNotification;
use App\Events\InProgressUpdated;
use App\Events\MessageSent;
use App\Events\MessageSentTicket;
use App\Events\TicketsListUpdate;
use App\Jobs\alertEndOfChat;
use App\Jobs\closeUnansweredChat;
use App\Jobs\sendMessageRobot;
use App\Ticket;
use App\Tools\Builderall\Logger;
use App\Tools\Chats\SendRealtime;
use App\Tools\ClearEmail;
use App\Tools\Client;
use App\Tools\Crypt;
use App\Tools\Tickets\Feedback;
use App\Tools\Upload;
use App\UserClientChat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use stdClass;

class ChatHistoryController extends Controller
{
    public function getChatHistory(Request $request)
    {
        $id = intval(Crypt::decrypt($request->id));

        $conditions = [['chat_history.chat_id', $id]];

        if (isset(session('companyselected')['user_client_id'])) {
            array_push($conditions, ['chat_history.hidden_for_client', 0]);
        }

        $result = ChatHistory::join('user_client_chat', 'user_client_chat.chat_id', 'chat_history.chat_id')
            ->join('user_client', 'user_client.id', 'user_client_chat.user_client_id')
            ->join('user_auth AS ua_client', 'user_client.user_auth_id', 'ua_client.id')
            ->leftJoin('company_user_company_department', 'chat_history.company_user_company_department_id', 'company_user_company_department.id')
            ->leftJoin('company_user', 'company_user_company_department.company_user_id', 'company_user.id')
            ->leftJoin('user_auth AS ua_agent', 'company_user.user_auth_id', 'ua_agent.id')
            ->select(
                'chat_history.chat_id as chat_id',
                'chat_history.company_user_company_department_id',
                'chat_history.type',
                'chat_history.content',
                'chat_history.content_translated',
                'chat_history.hidden_for_client',
                'chat_history.id as ch_id',
                DB::raw('DATE_FORMAT(chat_history.created_at,"%H:%i") as time'),
                'chat_history.created_at',
                'ua_client.name as client_name',
                'ua_client.email as client_email',
                'ua_client.id as client_id',
                'ua_client.builderall_account_data',
                'ua_agent.name as user_name',
                'ua_agent.email as user_email',
                'ua_agent.id as user_id'
            )
            ->where($conditions)
            ->orderBy('chat_history.id')
            ->groupBy('chat_history.id')
            ->get();

        foreach ($result as $row) {
            $row->chat_id = Crypt::encrypt($row->chat_id);
            $row->ch_id = Crypt::encrypt($row->ch_id);
            $row->client_id = Crypt::encrypt($row->client_id);
            $row->user_id = Crypt::encrypt($row->user_id);
            if ($row->company_user_company_department_id) {
                $row->company_user_company_department_id = Crypt::encrypt($row->company_user_company_department_id);
            }

            if ($row->type == 'ROBOT') {
                $row->content = json_decode($row->content, true);
            }
        }

        return response()->json($result);
    }

    public function store(Request $request)
    {

        //return $request;
        $id = intval(Crypt::decrypt($request->id));

        // atualiza o user_agent toda vez que o cliente responder o chat.
        if ($request->is_client) {
            $checkStatus = Chat::select('status')->where('id', Crypt::decrypt($request->id))->first();
            if ($checkStatus->status !== "IN_PROGRESS" && $checkStatus->status !== "OPENED") {
                return response()->json([
                    "error" => 1,
                    "status" => $checkStatus->status
                ]);
            }
            Chat::where('id', Crypt::decrypt($request->id))->update(['user_agent' => $_SERVER['HTTP_USER_AGENT'], 'updated_at' => DB::raw('updated_at')]);
        }

        if (isset(session('companyselected')['user_client_id'])) {
            $company_user_company_department = NULL;
            $company_id = session('companyselected')['company_id'];
            $user_client_id = session('companyselected')['user_client_id'];
            $agent_answered = 0;
            $answered = 1;
            $hidden_for_client = 0;

            if (isset($request->company_user_company_department_id)) {
                $query = CompanyUserCompanyDepartment::join('company_user', 'company_user.id', 'company_user_company_department.company_user_id')
                    ->join('chat', 'chat.comp_user_comp_depart_id_current', 'company_user_company_department.id')
                    ->select('company_user.user_auth_id', 'company_user.id as company_user_id')
                    ->where('chat.comp_user_comp_depart_id_current', Crypt::decrypt($request->company_user_company_department_id))
                    ->whereNull('chat.deleted_at')
                    ->first();

                $user_agent_id = $query->user_auth_id;
                $company_user_id = Crypt::encrypt($query->company_user_id);
            } else {
                $user_agent_id = null;
                $company_user_id = null;
            }
        } else {
            $company_user_company_department = Crypt::decrypt($request->company_user_company_department_id);
            $company_id = session('companyselected')['id'];
            $user_client_id = Crypt::encrypt(UserClientChat::select('user_client_id')->where('chat_id', $id)->first()->user_client_id);
            $agent_answered = 1;
            $answered = 0;
            $user_agent_id = Auth::id();
            if ($request->is_incognito) {
                $hidden_for_client = 1;
            } else {
                $hidden_for_client = 0;
            }
            //$company_user_id = session('companyselected')['company_user_id'];


        }

        $type =  trim(strip_tags($request->type));
        // $content =  trim(strip_tags($request->content));


        $content =  $request->content;
        $content_translated = $request->content_translated;

        if (isset($request['images'])) {
            $images = $request['images'];
            foreach ($images as $row) {
                // Define the Base64 value you need to save as an image
                $b64 = explode(',', $row)[1];

                $image_name = Crypt::encrypt(uniqid(md5(uniqid() . microtime())));
                $data = base64_decode($b64);
                $dir = '..' . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'company' . DIRECTORY_SEPARATOR . Crypt::decrypt(session('companyselected')['id']) . DIRECTORY_SEPARATOR . 'chat' . DIRECTORY_SEPARATOR . $id . DIRECTORY_SEPARATOR;
                $filename = $image_name . '.png';

                // Caso já exista o diretório, apenas movo o arquivo pra dentro dele, se não, crio a pasta e movo o arquivo em seguida...
                if (is_dir($dir)) {
                    $success = file_put_contents($dir.$filename, $data);
                } else {
                    mkdir($dir, 0755, true);
                    $success = file_put_contents($dir.$filename, $data);
                }

                if ($success) {
                    $content = str_replace($row, 'chat/files/'. $request['id'] .'/'.$filename, $content);
                    $content_translated != null ? $content_translated[0]['content'] = str_replace($row, 'chat/files/'. $request['id'] .'/'.$filename, $content_translated[0]['content']) : null;
                }

            }
        }
        
        if ($content_translated != null) {
            $ct = [
                'language' => $content_translated[0]['language'],
                'content' => $content_translated[0]['content']
            ];
            $ct = json_encode([$ct]);
        } else {
            $ct = null;
        }

        $create = ChatHistory::create([
            'chat_id' => $id,
            'company_user_company_department_id' => $company_user_company_department,
            'type' => $type,
            'content' => $content,
            'content_translated' => $ct,
            'hidden_for_client' => $hidden_for_client,
            'created_by' => Auth::id()
        ]);

        $user = Auth::user();

        $create->chat_id = $request->id;
        if (!isset($request->company_user_company_department_id)) {
            $create->company_user_company_department_id = $request->company_user_company_department_id;
        }
        $msg = json_decode(json_encode($create), true);
        $user = json_decode(json_encode($user), true);
        if ($company_user_company_department == NULL) {
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

        $result =  array_merge($msg, $user, $gravatar, [
            'action' => 'update',
            'company_id' => $company_id,
            'user_agent_id' => $user_agent_id,
            'agent_answered' => $agent_answered,
            'number' => $id,
            'answered' =>  1,
            'comp_user_comp_depart_id_current' => $request->company_user_company_department_id,
            'company_department_id' => $request->company_department_id,
            'department' => $request->department,
            'status' => "IN_PROGRESS",
            'user_agent' =>  $_SERVER['HTTP_USER_AGENT'],
        ]);

        $result['created_at'] = $create->created_at;
        $result['ch_id'] = Crypt::encrypt($create->id);
        $result['email'] = Client::getCleanEmail($result['email'], Crypt::decrypt($company_id));

        broadcast(new MessageSent($result));
        if (isset(session('companyselected')['user_client_id'])) {
            $result['client_id'] = Crypt::encrypt($result['id']);
        }
        if (isset($user_agent_id)) {
            $realtime = new SendRealtime($id, 'update');
            $realtime->updateTableInProgress();
        }
        if (!$hidden_for_client) {
            broadcast(new ClientQueueStatus([
                'chat_id' => $request->id,
                'company_id' => $company_id,
                'user_client_id' => $user_client_id,
                'agent_answered' => $agent_answered,
                'content' => $content,
                'sent_by' => $agent_answered ? Auth::user()->name : null,
            ]));
        }
        // notificação do atendente para o cliente
        if (isset(session('companyselected')['company_user_id']) && isset($user_client_id) && !$hidden_for_client) {
            broadcast(new ClientNotification([
                // título da notificação
                'title' => 'bs-chat',
                // O corpo(mensagem) da notificação.
                'body' => 'bs-new-message-received',
                // A URL da imagem usada como um ícone da notificação.
                'icon' => 'https://builderall.com//franquias/2/73748/editor-html/3484811.png',
                // (Opcional) URL para qual a notificação deve redirecionar
                'url' => '',
                //se for true, a notificação fica sem som (PS: está sendo disparado som no browser, então pode ficar true aqui que vai sair som igual)
                'silent' => false,
                // numero de identificação do chat / ticket
                'number' => $id,
                // envia o company user id do atendente que precisa receber a notificação (caso seja individual)
                'user_client_id' => $user_client_id,
                // Paramêtro de conexão no canal global de notificação, sempre deve ser passado
                'company_id' => session('companyselected')['id']
            ]));
        }
        // notificação do cliente para o atendente
        if (isset(session('companyselected')['user_client_id']) && isset($company_user_id)) {
            broadcast(new GlobalNotification([
                // título da notificação
                'title' => 'bs-chat',
                // O corpo(mensagem) da notificação.
                'body' => 'bs-new-message-received',
                // A URL da imagem usada como um ícone da notificação.
                'icon' => 'https://builderall.com//franquias/2/73748/editor-html/3484811.png',
                // (Opcional) URL para qual a notificação deve redirecionar
                'url' => '',
                //se for true, a notificação fica sem som (PS: está sendo disparado som no browser, então pode ficar true aqui que vai sair som igual)
                'silent' => false,
                // numero de identificação do chat / ticket
                'number' => $id,
                // identifica se a notificação é de chat ou ticket
                'type' => 'chat',
                // identifica o status do chat/ticket para fazer a lógica do disparo
                'status' => 'IN_PROGRESS',
                // identifica o id do departamento para qual a notificação deve ser dispara
                'company_department_id' => $request->company_department_id,
                // envia o company user id do atendente que precisa receber a notificação (caso seja individual)
                'company_user_id' => $company_user_id,
                // Paramêtro de conexão no canal global de notificação, sempre deve ser passado
                'company_id' => session('companyselected')['company_id']
            ]));
        }

        if (!isset(session('companyselected')['user_client_id'])) {
            if ($request->time_for_inactivity_message > 0) {
                if (!$hidden_for_client) {
                    $chat = [
                        "chat_id" => $id,
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

    public function storeRobot(Request $request) {

        $message['success'] = false;
        $message['message'] = [];

		try {
            if (isset(session('companyselected')['user_client_id'])) {
                $data = $request->all();

                if($data['is_bot']) {
                    $by = -1;
                } else {
                    $by = Auth::id();
                }

                $store = ChatHistory::create([
                    'chat_id' => $data['id'],
                    'company_user_company_department_id' => NULL,
                    'type' => 'ROBOT',
                    'content' => json_encode($data['message']),
                    'hidden_for_client' => 0,
                    'created_by' => $by
                ]);

                $store->ch_id = Crypt::encrypt($store->id);
                $store->user_id = null;
                $store->user_name = null;
                $store->user_email = null;  
                if (isset($data['old_module'])) {
                    $store->content = json_decode($store->content, true);
                }
                $message['message'] = $store;
                
                

                if ($data['time'] && $data['time'] > 0) {
                    sendMessageRobot::dispatch($store)->delay(now()->addSeconds($data['time']));
                }

                $message['success'] = true;

            }
		} catch (\Exception $e) {   
			Logger::reportException($e, [], ['chat-controller', 'storeRobot'], false);
			$message['success'] = false;
		}

        return $message;
    }

    public function clientStoreTicket(Request $request) {
        $response['success'] = false;
        try {
            $data = $request->All();

            $chat_id = intval(Crypt::decrypt($data['chat_id']));
            $company_id = intval(Crypt::decrypt(session('companyselected')['company_id']));
            $content = $data['content'];
            $cucd_id = intval(Crypt::decrypt($data['cucd_id']));
            $department_id = Crypt::decrypt($data['department_id']);
            $ticket_id = intval(Crypt::decrypt($data['ticket_id']));
            $user_id = auth()->user()->id;

            $status = Ticket::where("id", $ticket_id)->first()->status;

            Chat::setUserAgent($chat_id);

            if ($status == 'CLOSED') {
                Ticket::reopen($cucd_id, $ticket_id, $chat_id, $_SERVER['HTTP_USER_AGENT']);
            }

            if (isset($data['images'])) {
                $content = Upload::replaceImagesInContent($content, $data['images'], $company_id, $chat_id);
            }

            if ($request->hasFile('files')) {
                $content = Upload::ticketFiles($content, $_FILES, $company_id, $chat_id);
                $type = 'FILE';
            } else {
                $type = 'TEXT';
            }

            $create = ChatHistory::create([
                'chat_id' => $chat_id,
                'company_user_company_department_id' => NULL,
                'type' => $type,
                'created_by' => $user_id,
                'content' => $content
            ]);

            if ($create) {

                // REALTIMES
                if ($cucd_id != 0) {
                    $cu_id = CompanyUserCompanyDepartment::where('id', $cucd_id)->first()->company_user_id;
                    broadcast(new GlobalNotification([
                        'title' => 'bs-ticket',
                        'body' => $content,
                        'icon' => 'https://builderall.com//franquias/2/73748/editor-html/3484811.png',
                        'url' => '',
                        'silent' => true,
                        'number' => $ticket_id,
                        'type' => 'ticket',
                        'status' => 'OPENED',
                        'company_department_id' => $data['department_id'],
                        'company_user_id' => Crypt::encrypt($cu_id),
                        'company_id' => session('companyselected')['company_id']
                    ]));
                }

                if ($status == 'IN_PROGRESS') {
                    $realtime = new \App\Tools\Tickets\SendRealtime($ticket_id, 'splice');
                    $realtime->updateTableInProgress();
    
                    $realtime2 = new \App\Tools\Tickets\SendRealtime($ticket_id, 'push');
                    $realtime2->updateTableInProgress();
                }

                $user = Auth::user();

                $user->client_id = $user->id;
                $user->id_creator = $user->id;
                $user->client_email = Client::getCleanEmail($user->email, Crypt::decrypt(session('companyselected')['company_id']));
                $user->client_name = $user->name;

                $create->chat_id = $data['chat_id'];
                $create->ch_id = Crypt::encrypt($create->id);
                $msg = json_decode(json_encode($create), true);
                $user = json_decode(json_encode($user), true);
                $result =  array_merge($user, $msg);

                $result['created_at'] = $create->created_at;
                $result['id'] = $data['chat_id'];
                $result['email'] = Client::getCleanEmail($result['email'], Crypt::decrypt(session('companyselected')['company_id']));
                broadcast(new MessageSentTicket($result));


                $response['success'] = true;
            }

        } catch (\Exception $e) {  
			Logger::reportException($e, [], ['chat-history-controller', 'clientStoreTicket'], false);
		}
        return $response;
    }

    public function storeTicketHistory(Request $request)
    {
        $comp_department = Crypt::decrypt(request('id_department'));
        $comp_user = request('company_user_id');
        
        $check_cucd = CompanyUserCompanyDepartment::where('company_department_id', $comp_department)
            ->join('company_user', 'company_user_company_department.company_user_id', 'company_user.id')
            ->where('company_user_id', $comp_user)
            ->whereNull('company_user.deleted_at')
            ->where('company_user.is_active', 1)
            ->where('company_user_company_department.is_active', 1)
            ->exists();


        $id = intval(Crypt::decrypt($request->id));
        $company_id = intval(Crypt::decrypt(session('companyselected')['company_id']));
        $status_return = 'IN_PROGRESS';
        // atualiza o user_agent toda vez que o cliente responder o chat.
        if ($request->is_client) {

            $status = Ticket::where("id", $request->id_ticket)->first()->status;
            if ($request->is_ticket) {
                if($status == 'CLOSED'){

                    if (!$check_cucd) {
                        $ticket_id = $request->id_ticket;
                        try {
                            DB::table('user_ticket')
                            ->where('ticket_id', $ticket_id)
                            ->delete();
                        
                            DB::table('ticket')
                            ->where('id', $ticket_id)
                            ->update([
                                'status' => 'OPENED',
                                'user_agent' => $_SERVER['HTTP_USER_AGENT']
                            ]);

                            Chat::where('ticket_id', $ticket_id)->update([
                                'comp_user_comp_depart_id_current' => null
                            ]);
            
                            DB::table('chat_history')->insertGetId([
                                'chat_id' => $id,
                                'company_user_company_department_id' => null,
                                'type' => 'EVENT',
                                'content' => 'bs-reopened-the-ticket',
                                'created_by' => auth()->user()->id,
                                'created_at' => \Carbon\Carbon::now()->toDateTimeString(),
                            ]);
                        } catch (\Exception $e) {
                            echo $e;
                            Logger::reportException($e, [], ['ChatHistoryController', 'storeTicketHistory'], false);
                            $message['success'] = false;
                        }
                    }else{
                        DB::table('chat_history')->insertGetId([
                            'chat_id' => $id,
                            'company_user_company_department_id' => null,
                            'type' => 'EVENT',
                            'content' => 'bs-reopened-the-ticket',
                            'created_by' => auth()->user()->id,
                            'created_at' => \Carbon\Carbon::now()->toDateTimeString(),
                        ]);

                        $status_return = 'IN_PROGRESS';
                        DB::table('ticket')
                        ->where('id', $request->id_ticket)
                        ->update([
                            'status' => $status_return,
                            'user_agent' => $_SERVER['HTTP_USER_AGENT'],
                            'update_status_closed_resolved'     => null
                        ]);
                    }
                }else{
                    DB::table('ticket')
                    ->where('id', $request->id_ticket)
                    ->update([
                        // 'updated_at' => DB::raw('updated_at'),
                        'user_agent' => $_SERVER['HTTP_USER_AGENT']
                    ]);
                }
            }
           
        }

        $text = $request->content;

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

        // verifico se existe algum arquivo enviado no request...
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
            'company_user_company_department_id' => NULL,
            'type' => $type,
            'created_by' => Auth::id(),
            'content' => $content
        ]);

        //SE CASO TIVER ATENDENTE NO TICKET
        if (!is_null(request('company_user_id'))) {
            broadcast(new GlobalNotification([
                // título da notificação
                'title' => 'bs-ticket',
                // O corpo(mensagem) da notificação.
                'body' => request('content'),
                // A URL da imagem usada como um ícone da notificação.
                'icon' => 'https://builderall.com//franquias/2/73748/editor-html/3484811.png',
                // (Opcional) URL para qual a notificação deve redirecionar
                'url' => '',
                //se for true, a notificação fica sem som (PS: está sendo disparado som no browser, então pode ficar true aqui que vai sair som igual)
                'silent' => true,
                // numero de identificação do chat / ticket
                'number' => request('id_ticket'),
                // identifica se a notificação é de chat ou ticket
                'type' => 'ticket',
                // identifica o status do chat/ticket para fazer a lógica do disparo
                'status' => 'OPENED',
                // identifica o id do departamento para qual a notificação deve ser dispara
                'company_department_id' => request('id_department'),
                // envia o company user id do atendente que precisa receber a notificação (caso seja individual)
                'company_user_id' => request('company_user_id'),
                // Paramêtro de conexão no canal global de notificação, sempre deve ser passado
                'company_id' => session('companyselected')['company_id']
            ]));
        }



        //ALERTA PARA O ATENDENTE CASO SEJA ENVIADO UMA MENSAGEM PARA O ATENDENTE
        $realtime = new \App\Tools\Tickets\SendRealtime(request('id_ticket'), 'splice');
        $realtime->updateTableInProgress();

        $realtime2 = new \App\Tools\Tickets\SendRealtime(request('id_ticket'), 'push');
        $realtime2->updateTableInProgress();

        $user = Auth::user();

        $user->client_id = $user->id;
        $user->id_creator = $user->id;
        $user->client_email = Client::getCleanEmail($user->email, Crypt::decrypt(session('companyselected')['company_id']));
        $user->client_name = $user->name;

        $create->chat_id = $request->id;
        $msg = json_decode(json_encode($create), true);
        $user = json_decode(json_encode($user), true);
        $result =  array_merge($user, $msg);

        $result['created_at'] = $create->created_at;
        $result['id'] = Crypt::encrypt($id);
        $result['email'] = Client::getCleanEmail($result['email'], Crypt::decrypt(session('companyselected')['company_id']));
        broadcast(new MessageSentTicket($result));

        $ticket['status'] = $status_return;
        $ticket['success'] = true;
        $ticket['created_by'] = \Carbon\Carbon::now()->toDateTimeString();

        return $ticket;
    }

    public function update(Request $request) {
        $update['success'] = false;

		try {
            if (isset(session('companyselected')['user_client_id'])) {

                $data = $request->all();

                ChatHistory::find(Crypt::decrypt($data['ch_id']))->update([
                    'content' => json_encode($data['content'])
                ]);

                $update['success'] = true;

            }
		} catch (\Exception $e) {
			Logger::reportException($e, [], ['chat-history-controller', 'update'], false);
			$update['success'] = false;
		}

        return $update;
    }

    public function setTranslatedMessage(Request $request) {
        $result['success'] = false;

        try {

            $message = $request->message;
            $language = $request->language;
            $ch_id = Crypt::decrypt($request->ch_id);

            $ct_current_is_null = ChatHistory::where('id', $ch_id)->whereNull('content_translated')->exists();

            if ($ct_current_is_null) {

                $content_translated = [
                    'language' => $language,
                    'content' => $message
                ];

                ChatHistory::find($ch_id)->update([
                    'content_translated' => [$content_translated]
                ]);

                $result['content_translated'] = [$content_translated];

            } else {

                $q = ChatHistory::where('id', $ch_id)->value('content_translated');
                $ct_current = json_decode($q);
                $content_translated = [
                    'language' => $language,
                    'content' => $message
                ];

                array_push($ct_current, json_decode(json_encode($content_translated)));

                ChatHistory::find($ch_id)->update([
                    'content_translated' => $ct_current
                ]);

                $result['content_translated'] = $ct_current;
            }

            $result['success'] = true;


        } catch (\Exception $e) {
            Logger::reportException($e, [], ['chat-history-controller', 'setTranslatedMessage'], false);
			$result['success'] = false;
        }

        return $result;
    }

    public function checkTranslation(Request $request) {
        $result['success'] = false;

        try {

            $ch_id = Crypt::decrypt($request->ch_id);
            $language = $request->language;

            $result['exists'] = false;
            $result['content_translated'] = [];

            if (ChatHistory::where('id', $ch_id)->whereNotNull('content_translated')->exists()) {
                $q = ChatHistory::where('id', $ch_id)->value('content_translated');
                $ct_current = json_decode($q);

                foreach ($ct_current as $row) {
                    if ($row->language == $language) {
                        $result['exists'] = true;
                        $result['content_translated'] = $row->content;
                    }
                }
            }

            $result['success'] = true;
        } catch (\Exception $e) {
            Logger::reportException($e, [], ['chat-history-controller', 'checkTranslation'], false);
			$result['success'] = false;
        }

        return $result;
    }

    public function getChatHistoryClient(Request $request) {
        $result['success'] = false;
        try {
            $chat_id = $request->id;
            $merged_status_ticket = request('status');
            $merged_ticket_id = request('ticket_id');

            if($merged_status_ticket == 'MERGED'){
                $tm = DB::table('ticket_merge')
                        ->join('chat', 'ticket_merge.ticket_id_origin', 'chat.ticket_id')
                        ->where('ticket_id_merge', $merged_ticket_id)
                        ->select('chat.id','ticket_id_origin')
                        ->first();

                if($tm != null){
                    $chat_id = $tm->id;
                    $ticket_id_origin = $tm->ticket_id_origin;
                }   
            }

            $check = UserClientChat::join('user_client', 'user_client_chat.user_client_id', 'user_client.id')
            ->join('user_auth', 'user_client.user_auth_id', 'user_auth.id')
            ->where('user_auth.id', Auth::id())
            ->exists();

            if ($check) {

                $first_id = isset($request->first) ? Crypt::decrypt($request->first) : 99999999999999;
                $perPage = 15;
                $page = $request->page;
                $startAt = $perPage * ($page - 1);

                $ticket_id = Chat::select('ticket_id')
                        ->where('id', $chat_id)
                        ->whereNotNull('ticket_id')
                        ->whereNull('changed_to_ticket_at')
                        ->first();

                $ticket_id = is_null($ticket_id) ? 0 : $ticket_id->ticket_id;

                if ($ticket_id == 0) {
                    $select_answers = "
                        SELECT 0 AS question, company_depart_settings_question_id, NULL AS chat_history_id, NULL AS company_user_company_department_id, 'TEXT' AS type, answer as content,
                            tca.created_at, uc.user_auth_id as user_id, ua.name AS user_name, ua.email AS user_email,  (tca.created_at > c.changed_to_ticket_at) AS is_ticket_msg
                        FROM company_depart_settings_question cdsq
                        JOIN ticket_chat_answer tca ON cdsq.id = tca.company_depart_settings_question_id
                        JOIN user_client_chat ucc ON tca.chat_id = ucc.chat_id
                        JOIN user_client uc ON ucc.user_client_id = uc.id
                        JOIN user_auth ua ON uc.user_auth_id = ua.id
                        LEFT JOIN chat as c ON tca.chat_id = c.id
                        WHERE (!(c.ticket_id IS NOT NULL AND c.changed_to_ticket_at IS NULL) AND tca.chat_id = :id3) OR tca.ticket_id = :id4
                    ";
                } else {
                    $select_answers = "
                        SELECT 0 AS question, company_depart_settings_question_id, NULL AS chat_history_id, NULL AS company_user_company_department_id, 'TEXT' AS type, answer as content,
                            tca.created_at, uc.user_auth_id as user_id, ua.name AS user_name, ua.email AS user_email,  0 AS is_ticket_msg
                        FROM company_depart_settings_question cdsq
                        JOIN ticket_chat_answer tca ON cdsq.id = tca.company_depart_settings_question_id
                        JOIN user_client_ticket uct ON tca.ticket_id = uct.ticket_id
                        JOIN user_client uc ON uct.user_client_id = uc.id
                        JOIN user_auth ua ON uc.user_auth_id = ua.id
                        LEFT JOIN chat as c ON tca.chat_id = c.id
                        WHERE (!(c.ticket_id IS NOT NULL AND c.changed_to_ticket_at IS NULL) AND tca.chat_id = :id3) OR tca.ticket_id = :id4
                    ";
                }

                $sql = 
                "SELECT seq, COALESCE(chat_history_id, seq) as id, company_user_company_department_id, type, content, created_at, user_id, user_name, user_email, is_ticket_msg FROM (
                    SELECT ROW_NUMBER() OVER (ORDER BY IF(question IS NOT NULL, 0, 1), company_depart_settings_question_id, chat_history_id) AS seq, chat_history_id, company_user_company_department_id, type, content, created_at, user_id, user_name, user_email, is_ticket_msg
                    FROM (
                        -- Pergunta
                        SELECT 1 AS question, company_depart_settings_question_id, NULL AS chat_history_id, NULL AS company_user_company_department_id, 'ROBOT_QUESTION' AS type, question as content,
                            tca.created_at, NULL as user_id, NULL AS user_name, NULL AS user_email, (tca.created_at > c.changed_to_ticket_at) AS is_ticket_msg
                        FROM company_depart_settings_question cdsq
                        JOIN ticket_chat_answer tca ON cdsq.id = tca.company_depart_settings_question_id
                        LEFT JOIN chat as c ON tca.chat_id = c.id
                        WHERE (!(c.ticket_id IS NOT NULL AND c.changed_to_ticket_at IS NULL) AND tca.chat_id = :id1) OR tca.ticket_id = :id2
                        -- Resposta
                        UNION ALL" . $select_answers . "
                        UNION ALL
                        -- chat_history
                        SELECT NULL AS question, NULL AS company_depart_settings_question_id, ch.id as chat_history_id, ch.company_user_company_department_id, ch.type, ch.content, ch.created_at, ua.id as user_id,
                            ua.name as user_name, ua.email as user_email, (ch.created_at > c.changed_to_ticket_at) AS is_ticket_msg
                        FROM chat_history ch
                        LEFT JOIN user_auth ua ON ch.created_by = ua.id
                        JOIN chat as c ON ch.chat_id = c.id
                        WHERE ch.chat_id = :id5
                        AND ch.hidden_for_client = 0
                    ) sub_interno
                    ORDER BY IF(question IS NOT NULL, 0, 1), company_depart_settings_question_id, chat_history_id LIMIT 99999999999999
                ) sub
                HAVING id < :first_id
                ORDER BY 1 DESC
                LIMIT 15;";

                $data = DB::select($sql, [
                    'id1' => $chat_id,
                    'id2' => $ticket_id,
                    'id3' => $chat_id,
                    'id4' => $ticket_id,
                    'id5' => $chat_id,
                    'first_id' => $first_id,
                ]);

                foreach ($data as $message) {
                    $message->ch_id = is_null($message->id) ? null : Crypt::encrypt($message->id);
                    $message->user_id = is_null($message->user_id) ? null : Crypt::encrypt($message->user_id);
                    $message->company_user_company_department_id = is_null($message->company_user_company_department_id) ? null : Crypt::encrypt($message->company_user_company_department_id);
                    $message->user_email = is_null($message->user_email) ? null : ClearEmail::clear($message->user_email); 
                    $message->is_ticket_msg = is_null($message->is_ticket_msg) ? ($ticket_id == 0 ? 0 : 1) : $message->is_ticket_msg; 
                }
                
                
            if($merged_status_ticket == 'MERGED'){
                    $newItem = [
                        'ch_id' => 'cDRPeDdGQ1hwSVJwa3NEMUhJZUR1QT09',
                        'company_user_company_department_id' => null,
                        'content' => Feedback::t('bs-merged'). ': '.$ticket_id_origin,
                        'created_at' => \Carbon\Carbon::now()->toDateTimeString(),
                        'id' => '1',
                        'is_ticket_msg' => 1,
                        'type' => 'EVENT',
                        'user_email' => 'Aviso',
                        'user_id' => 'user',
                        'user_name' => Feedback::t('bs-ticket').' ',
                    ];

                    array_unshift($data, $newItem);
                }
                
                $result['success'] = true;
                $result['messages'] = $data;
            }

        } catch (\Exception $e) {
            dd($e);
            Logger::reportException($e, [], ['chat-history-controller', 'getChatHistoryClient'], false);
        }
        return $result;
    }
}
