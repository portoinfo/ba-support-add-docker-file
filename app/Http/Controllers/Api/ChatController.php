<?php

namespace App\Http\Controllers\Api;

use App\Chat;
use App\ChatHistory;
use App\CompanyUserCompanyDepartment;
use App\Events\CanceledUpdated;
use App\Events\ChatStatusChanger;
use App\Events\ClientNotification;
use App\Events\ClientQueueStatus;
use App\Events\ClosedUpdated;
use App\Events\FullChatCanceled;
use App\Events\FullChatClosed;
use App\Events\FullChatProgress;
use App\Events\FullChatResolved;
use App\Events\GlobalNotification;
use App\Events\InProgressUpdated;
use App\Events\MessageSent;
use App\Events\QueueUpdated;
use App\Events\ResolvedUpdated;
use App\Events\TicketsAgentListUpdate;
use App\Events\TransferredUpdated;
use App\Http\Controllers\Controller;
use App\Http\Controllers\TicketController;
use App\Jobs\alertEndOfChat;
use App\Ticket;
use App\TicketChatAnswer;
use App\Tools\Client;
use App\Tools\Crypt;
use App\Tools\SystemState;
use App\UserClientChat;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;
use App\Tools\Builderall\Logger;
use App\Avaliation;
use App\Tools\Chats\SendRealtime;
use App\Tools\Tickets\MailAdmin;

class ChatController extends Controller
{
    private $company_selected;

    public function __construct()
    {
        $robot['success'] = false;
        try {
            $robot['result'] = SystemState::getCacheForApi(auth('api')->user()->id, 'companyselected', null);
            if ($robot['result']) {
                $this->company_selected =  SystemState::getCacheForApi(auth('api')->user()->id, 'companyselected', null);
            }
        } catch (\Exception $e) {
            Logger::reportException($e, [], ['chat-controller', '__construct'], false);
            $robot['success'] = false;
        }
    }

    public function store(Request $request)
    {
        // armazeno todo o request na variavel
        $data =  $request->all();

        // crio o chat
        $result = Chat::create([
            'company_id' => Crypt::decrypt($this->company_selected['company_id']),
            'company_department_id' => Crypt::decrypt($data['company_department']['id']),
            'type' => 'DEFAULT',
            'status' => 'OPENED',
            'priority' => 'NORMAL',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'],
            'created_by' => auth('api')->user()->id
        ]);

        // Email
		$send_mail = new MailAdmin(Crypt::decrypt($this->company_selected['company_id']), auth('api')->user()->id, Crypt::decrypt($data['company_department']['id']));
		$send_mail->ticketchatOpened('CHAT', request('onlineUsers'));

        // insiro o user client id no user client chat
        if ($result) {
            $user_client_id = UserClientChat::create([
                'user_client_id' => Crypt::decrypt($this->company_selected['user_client_id']),
                'chat_id' => $result->id
            ]);

            if ($user_client_id) {
                $i = 0;
                foreach ($data['questions'] as $row) {
                    if (isset($data['answers'][$i]) && trim($data['answers'][$i]) !== '') {
                        TicketChatAnswer::create([
                            'company_depart_settings_question_id' => Crypt::decrypt($row['id']),
                            'chat_id' => $result->id,
                            'answer' => $data['answers'][$i]
                        ]);
                    }
                    $i++;
                }

                if ($i === count($data['questions'])) {

                    // crio uma mensagem type event contendo uma chave de tradução que indica que o cliente inciou o chat
                    $event = $result->chat_histories()->create([
                        'type'  => 'EVENT',
                        'content'  => 'bs-started-the-chat',
                        'created_by' => auth('api')->user()->id,
                    ]);

                    if ($event) {
                        $settings = DB::table('company_department_settings')
                            ->select('settings')
                            ->where('company_department_id', Crypt::decrypt($data['company_department']['id']))
                            ->first()
                            ->settings;

                        $obj = json_decode($settings, true);

                        if (isset($obj['chat']['arrayTranslate'])) {
                            $msg_open = $obj['chat']['arrayTranslate']['msgOpen'];
                            $user_lang =  explode("_", auth('api')->user()->language)[1];
                            foreach ($msg_open as $row) {
                                if ($row['code'] == $user_lang) {
                                    if($row['text'] !== '') {
                                        $result->chat_histories()->create([
                                            'type'  => 'OPEN',
                                            'content'  => $row['text'],
                                            'created_by' => auth('api')->user()->id,
                                        ]);
                                    }
                                }
                            }
                        } else if ($obj['chat']['msgOpen'] !== null) {
                            $result->chat_histories()->create([
                                'type'  => 'OPEN',
                                'content'  => $obj['chat']['msgOpen'],
                                'created_by' => auth('api')->user()->id,
                            ]);
                        }

                        if ($settings) {
                            $item = [
                                'chat_id' => Crypt::encrypt($result->id),
                                'number'  => $result->id,
                                'company_id' => Crypt::encrypt($result->company_id),
                                'company_department_id' => Crypt::encrypt($result->company_department_id),
                                'comp_user_comp_depart_id_current' => null,
                                'status' => $result->status,
                                'type' => $result->type,
                                'priority' => $result->priority,
                                'date' => date('d/m/Y'),
                                'time' => date('H:i:s'),
                                'created_at' => $result->created_at,
                                'department' => $data['company_department']['name'],
                                'dep_type' => "",
                                'client_id' => Crypt::encrypt($data['client']['id']),
                                'name' => $data['client']['name'],
                                'builderall_account_data' => null,
                                'email' => Client::getCleanEmail($data['client']['email'], $result->company_id),
                                'content' => $data['content'],
                                'agent' => null,
                                'user_agent' => $result->user_agent,
                                'answered' => 0,
                                'settings' => $settings,
                                'action' => 'push',
                                'user_client_id' => $this->company_selected['user_client_id']

                            ];

                            $realtime = new SendRealtime($result->id, 'push');
                            $realtime->updateTableQueue();

                            broadcast(new ClientQueueStatus($item));

                            broadcast(new GlobalNotification([
                                // título da notificação
                                'title' => 'bs-chat',
                                // O corpo(mensagem) da notificação.
                                'body' => 'bs-new-chat-added-to-the-queue',
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
                                'status' => 'OPENED',
                                // identifica o id do departamento para qual a notificação deve ser dispara
                                'company_department_id' => Crypt::encrypt($result->company_department_id),
                                // envia o company user id do atendente que precisa receber a notificação (caso seja individual)
                                //'company_user_id' => $company_user_id,
                                // Paramêtro de conexão no canal global de notificação, sempre deve ser passado
                                'company_id' => $this->company_selected['company_id']
                            ]));
                            // retorno o id do chat criado para que o mesmo possa ser aberto no front
                            return response()->json([
                                'chat_id' => Crypt::encrypt($result->id),
                                'number'  => $result->id,
                                'company_id' => Crypt::encrypt($result->company_id),
                                'company_department_id' => Crypt::encrypt($result->company_department_id),
                                'comp_user_comp_depart_id_current' => NULL,
                                'status' => 'OPENED',
                                'type' => 'DEFAULT',
                                'priority' => 'NORMAL',
                                'date' => date('d/m/Y'),
                                'time' => date('H:i:s'),
                                'created_at' => $result->created_at,
                                'department_name' => $data['company_department']['name'],
                                'settings' => $settings,
                                'agent' => NULL,
                                'answered' => 0,
                                'inactivityMessage' => 0,
                                'timewait' => 0
                            ]);
                        }
                    }
                }
            }
        }
    }

    public function chatHistoryStore(Request $request)
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

        if (isset($this->company_selected['user_client_id'])) {
            $company_user_company_department = NULL;
            $company_id = $this->company_selected['company_id'];
            $user_client_id = $this->company_selected['user_client_id'];
            $agent_answered = 0;
            $answered = 1;
            $hidden_for_client = 0;

            if (isset($request->company_user_company_department_id)) {

                $query = CompanyUserCompanyDepartment::join('company_user', 'company_user.id', 'company_user_company_department.company_user_id')
                    ->whereNull('chat.deleted_at')
                    ->join('chat', 'chat.comp_user_comp_depart_id_current', 'company_user_company_department.id')
                    ->select('company_user.user_auth_id', 'company_user.id as company_user_id')
                    ->where('chat.comp_user_comp_depart_id_current', Crypt::decrypt($request->company_user_company_department_id))
                    ->first();

                $user_agent_id = $query->user_auth_id;
                $company_user_id = Crypt::encrypt($query->company_user_id);
            } else {
                $user_agent_id = null;
                $company_user_id = null;
            }
        } else {
            $company_user_company_department = Crypt::decrypt($request->company_user_company_department_id);
            $company_id = $this->company_selected['id'];
            $user_client_id = Crypt::encrypt(UserClientChat::select('user_client_id')->where('chat_id', $id)->first()->user_client_id);
            $agent_answered = 1;
            $answered = 0;
            $user_agent_id = auth('api')->user()->id;
            if ($request->is_incognito) {
                $hidden_for_client = 1;
            } else {
                $hidden_for_client = 0;
            }
        }

        $type =  trim(strip_tags($request->type));
        $content =  trim(strip_tags($request->content));

        $create = ChatHistory::create([
            'chat_id' => $id,
            'company_user_company_department_id' => $company_user_company_department,
            'type' => $type,
            'content' => $content,
            'hidden_for_client' => $hidden_for_client,
            'created_by' => auth('api')->user()->id
        ]);

        $user = auth('api')->user();

        $create->chat_id = $request->id;
        if (!isset($request->company_user_company_department_id)) {
            $create->company_user_company_department_id = $request->company_user_company_department_id;
        }
        $msg = json_decode(json_encode($create), true);
        $user = json_decode(json_encode($user), true);
        if ($company_user_company_department == NULL) {
            $gravatar = [
                "client_email" => auth('api')->user()->email,
                "client_name" => auth('api')->user()->name,
                "client_id" => Crypt::encrypt(auth('api')->user()->id),
            ];
        } else {
            $gravatar = [
                "user_email" => auth('api')->user()->email,
                "user_name" => auth('api')->user()->name,
                "user_id" => Crypt::encrypt(auth('api')->user()->id),
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
            'user_agent' =>  $_SERVER['HTTP_USER_AGENT']
        ]);

        $result['created_at'] = $create->created_at;
        $result['ch_id'] = Crypt::encrypt($create->id);
        $result['email'] = Client::getCleanEmail($result['email'], Crypt::decrypt($company_id));

        broadcast(new MessageSent($result));
        if (isset($this->company_selected['user_client_id'])) {
            $result['client_id'] = Crypt::encrypt($result['id']);
        }
        if (isset($user_agent_id)) {
            $realtime = new SendRealtime($id, 'update');
            $realtime->updateTableInProgress();
        }
        broadcast(new ClientQueueStatus([
            'chat_id' => $request->id,
            'company_id' => $company_id,
            'user_client_id' => $user_client_id,
            'agent_answered' => $agent_answered,
            'content' => $content,
        ]));
        // notificação do atendente para o cliente
        if (isset($this->company_selected['company_user_id']) && isset($user_client_id) && !$hidden_for_client) {
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
                'company_id' => $this->company_selected['id']
            ]));
        }
        // notificação do cliente para o atendente
        if (isset($this->company_selected['user_client_id']) && isset($company_user_id)) {
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
                'company_id' => $this->company_selected['company_id']
            ]));
        }

        if (!isset($this->company_selected['user_client_id'])) {
            if ($request->time_for_inactivity_message > 0) {
                if (!$hidden_for_client) {
                    $chat = [
                        "chat_id" => $id,
                        "chat_history_id" => $create->id,
                        "chat" => $request->chat,
                        "company_id" => $request->company_id,
                        "user" => auth('api')->user(),
                        "company_user_id" => $this->company_selected['company_user_id'],
                        "company_department_id" => $request->company_department_id,
                        "user_client_id" => $user_client_id
                    ];

                    if ($request->time_for_inactivity_message == 1) {
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

        if (isset($this->company_selected['user_client_id'])) {
            $company_id = Crypt::decrypt($this->company_selected['company_id']);
            $company_user_company_department_id = NULL;
        } else {
            $company_id = Crypt::decrypt($this->company_selected['id']);
            $company_user_company_department_id = CompanyUserCompanyDepartment::join('company_user', 'company_user.id', 'company_user_company_department.company_user_id')
                ->select('company_user_company_department.id')
                ->where('company_user.user_auth_id', auth('api')->user()->id)
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
                    'created_by' => auth('api')->user()->id,
                    'content' => json_encode([
                        'unique_name' => $unique_name,
                        'original_name' => $original_name . $extension,

                    ]),
                    'hidden_for_client' => $hidden_for_client,
                ]);

                $user = auth('api')->user();

                if ($company_user_company_department_id == NULL) {
                    $gravatar = [
                        "client_email" => auth('api')->user()->email,
                        "client_name" => auth('api')->user()->name,
                        "client_id" => Crypt::encrypt(auth('api')->user()->id),
                    ];
                } else {
                    $gravatar = [
                        "user_email" => auth('api')->user()->email,
                        "user_name" => auth('api')->user()->name,
                        "user_id" => Crypt::encrypt(auth('api')->user()->id),
                    ];
                }

                $create->chat_id = $data['chat_id'];
                $msg = json_decode(json_encode($create), true);
                $user = json_decode(json_encode($user), true);
                $result =  array_merge($msg, $user, $gravatar);

                $result['created_at'] = $create->created_at;

                broadcast(new MessageSent($result));

                if (!isset($this->company_selected['user_client_id'])) {
                    if ($request->time_for_inactivity_message > 0) {
                        if (!$hidden_for_client) {
                            $user_client_id = Crypt::encrypt(UserClientChat::select('user_client_id')->where('chat_id', Crypt::decrypt($data['chat_id']))->first()->user_client_id);
                            $chat = [
                                "chat_id" => Crypt::decrypt($data['chat_id']),
                                "chat_history_id" => $create->id,
                                "chat" => $request->chat,
                                "company_id" => $request->company_id,
                                "user" => auth('api')->user(),
                                "company_user_id" => $this->company_selected['company_user_id'],
                                "company_department_id" => $request->company_department_id,
                                "user_client_id" => $user_client_id
                            ];

                            if ($request->time_for_inactivity_message == 1) {
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

    public function getChatHistory(Request $request)
    {
        $id = intval(Crypt::decrypt($request->id));

        $conditions = [['chat_history.chat_id', $id]];

        if (isset($this->company_selected['user_client_id'])) {
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
                'chat_history.hidden_for_client',
                DB::raw('DATE_FORMAT(chat_history.created_at,"%H:%i") as time'),
                'chat_history.created_at',
                'ua_client.name as client_name',
                'ua_client.email as client_email',
                'ua_client.id as client_id',
                'ua_agent.name as user_name',
                'ua_agent.email as user_email',
                'ua_agent.id as user_id'
            )
            ->where($conditions)
            ->orderBy('chat_history.id')
            ->get();

        foreach ($result as $row) {
            $row->chat_id = Crypt::encrypt($row->chat_id);
            $row->client_id = Crypt::encrypt($row->client_id);
            $row->user_id = Crypt::encrypt($row->user_id);
            if ($row->company_user_company_department_id) {
                $row->company_user_company_department_id = Crypt::encrypt($row->company_user_company_department_id);
            }
        }

        return response()->json($result);
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

        if (isset($data['status']) && $data['status'] != 'OPENED' && $data['action'] == 'CANCELED') {
            $data['action'] = 'RESOLVED';
        }

        $chat_id = Crypt::decrypt($data['id']);
        $turn_into_ticket_at_closing = Chat::checkTurnIntoTicketAtClosing($chat_id);
        $close_action = $data['action'] == 'CANCELED' || $data['action'] == 'CLOSED' || $data['action'] == 'RESOLVED';

        if ($turn_into_ticket_at_closing && $close_action) {

            $info = Chat::where('id', $chat_id)->first();

            if (isset($info->information_to_turn_into_ticket)) {
                $info = $info->information_to_turn_into_ticket;
                $info = json_decode($info);
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
                    $result = Chat::where('id', Crypt::decrypt($data['chat']['id']))->update(['status' => 'CANCELED']);
                    $content = 'bs-canceled-the-chat';
                    $status = 'CANCELED';
                    $body_notification = 'bs-the-chat-was-canceled';

                    broadcast(new ClientQueueStatus([
                        'chat_id' => $data['chat']['id'],
                        'company_id' => $this->company_selected['company_id'],
                        'user_client_id' => $this->company_selected['user_client_id'],
                        'status' => $status,
                        'agent_answered' => 0
                    ]));

                    if ($data['status'] === 'OPENED') {
                        broadcast(new QueueUpdated([
                            'chat_id' => $data['chat']['id'],
                            'company_id' => $this->company_selected['company_id'],
                            'action' => 'splice'
                        ]));
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
                                    'company_id' => $this->company_selected['id'],
                                    'user_agent_id' => $query->user_auth_id,
                                    'action' => 'splice',
                                ]));
                                // trigger realtime to admin / see complete list
                                broadcast(new FullChatProgress([
                                    "chat_id" => $data['chat']['id'],
                                    'company_id' => $this->company_selected['id'],
                                    'user_agent_id' => $query->user_auth_id,
                                    'action' => 'splice',
                                ]));

                                $realtime = new SendRealtime(Crypt::decrypt($data['chat']['id']), 'push');
                                $realtime->updateTableCanceled();

                            }
                        } else if ($chat->type === 'TRANSFERED') {
                            broadcast(new TransferredUpdated([
                                "chat_id" => $data['chat']['id'],
                                'company_id' => $this->company_selected['id'],
                                'action' => 'splice',
                            ]));
                        }
                    }
                    break;
                case 'RESOLVED':
                    if (isset($this->company_selected['user_client_id'])) {
                        $comp_user_comp_depart_id_current = false;
                        $company_id = $this->company_selected['company_id'];
                        $user_client_id = $this->company_selected['user_client_id'];
                        $name = auth('api')->user()->name;
                        $email = Client::getCleanEmail(auth('api')->user()->email, Crypt::decrypt($company_id));
                        $client_id = Crypt::encrypt(auth('api')->user()->id);
                        $agent_name = $request->agent;
                        $department_name = $request->department;
                        $chat = $request->chat;
                        $chat_id = $request->chat['id'];
                        $result = Chat::where('id', Crypt::decrypt($chat_id))->update(['status' => 'CLOSED']);
                        $content = 'bs-closed-the-chat';
                        $status = 'CLOSED';
                        $body_notification = 'bs-the-chat-was-closed';
                        $ba_acct_data = null;
                    } else {
                        $comp_user_comp_depart_id_current = Chat::select('comp_user_comp_depart_id_current')
                            ->where('id', Crypt::decrypt($data['id']))
                            ->first()
                            ->comp_user_comp_depart_id_current;

                        $company_id = $this->company_selected['id'];
                        $user_client_id = UserClientChat::select('user_client_id as id')->where('chat_id', Crypt::decrypt($data['id']))->first()->id;
                        $name = $request->chat['client']['name'];
                        $email = $request->chat['client']['email'];
                        $client_id = $request->chat['client']['id'];
                        $agent_name = auth('api')->user()->name;
                        $department_name = $request->chat['department'];
                        $chat = $request->chat;
                        $chat_id = $request->chat['chat_id'];
                        $result = Chat::where('id', Crypt::decrypt($chat_id))->update(['status' => 'RESOLVED']);
                        $content = 'bs-marked-as-resolved';
                        $status = 'RESOLVED';
                        $body_notification = 'bs-the-chat-was-resolved';
                        $ba_acct_data = null;
                    }

                    $query = Chat::join('company_user_company_department', 'company_user_company_department.id', 'chat.comp_user_comp_depart_id_current')
                        ->join('company_user', 'company_user.id', 'company_user_company_department.company_user_id')
                        ->select('company_user.user_auth_id', 'chat.status')
                        ->where('chat.id', Crypt::decrypt($chat_id))
                        ->first();


                    broadcast(new ClientQueueStatus([
                        'chat_id' => $chat_id,
                        'company_id' => $company_id,
                        'user_client_id' => $user_client_id,
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

                        $realtime = new SendRealtime(Crypt::decrypt($chat_id), 'push');
                        $realtime->updateTableResolved();

                    } else {
                        broadcast(new TransferredUpdated([
                            "chat_id" => $chat->id,
                            'company_id' => $company_id,
                            'action' => 'splice',
                        ]));
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
                    'created_by' => auth('api')->user()->id,
                ]);

                $user = auth('api')->user();

                if ($company_user_company_department_id == NULL) {
                    $gravatar = [
                        "client_email" => auth('api')->user()->email,
                        "client_name" => auth('api')->user()->name,
                        "client_id" => Crypt::encrypt(auth('api')->user()->id),
                    ];
                } else {
                    $gravatar = [
                        "user_email" => auth('api')->user()->email,
                        "user_name" => auth('api')->user()->name,
                        "user_id" => Crypt::encrypt(auth('api')->user()->id),
                    ];
                }

                $create->chat_id = $data['id'];
                $msg = json_decode(json_encode($create), true);
                $user = json_decode(json_encode($user), true);
                $result =  array_merge($msg, $user, $other_items_to_trigger, $gravatar);

                broadcast(new MessageSent($result));

                $arrayStatusChanger = [
                    'chat_id' => $data['id'],
                    'status'  => $status,
                ];

                if (isset($data['company_department'])) {
                    $arrayStatusChanger = array_merge($arrayStatusChanger, ['company_department_id' => $data['company_department']]);
                }

                broadcast(new ChatStatusChanger($arrayStatusChanger));

                $arrayGlobalNotificationItems = [];
                if (isset($this->company_selected['user_client_id'])) {
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
                    $arrayGlobalNotificationItems['company_id'] = $this->company_selected['company_id'];
                } else {
                    if (isset($other_items_to_trigger['transferred_to_agent']) && $other_items_to_trigger['transferred_to_agent'] === true) {
                        $arrayGlobalNotificationItems['company_department_id'] = $data['company_department']['id'];
                        $arrayGlobalNotificationItems['company_user_id'] = $other_items_to_trigger['company_user_id'];
                    } else if ($status !== 'OPENED') {
                        $arrayGlobalNotificationItems['company_department_id'] = $data['company_department'];
                        $arrayGlobalNotificationItems['company_user_id'] = $this->company_selected['company_user_id'];
                    }
                    $arrayGlobalNotificationItems['company_id'] = $this->company_selected['id'];
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
                                if($row['text'] !== '') {
                                    $create = ChatHistory::create([
                                        'chat_id' => Crypt::decrypt($data['id']),
                                        'type' => 'CLOSE',
                                        'company_user_company_department_id' => $company_user_company_department_id,
                                        'content' => $row['text'],
                                        'created_by' => auth('api')->id(),
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
                                'created_by' => auth('api')->id(),
                            ]);
                        }
                    }

                    if ($create) {
                        if ($company_user_company_department_id == NULL) {
                            $gravatar = [
                                "client_email" => auth('api')->user()->email,
                                "client_name" => auth('api')->user()->name,
                                "client_id" => Crypt::encrypt(auth('api')->user()->id),
                            ];
                        } else {
                            $gravatar = [
                                "user_email" => auth('api')->user()->email,
                                "user_name" => auth('api')->user()->name,
                                "user_id" => Crypt::encrypt(auth('api')->user()->id),
                            ];
                        }

                        $create->chat_id = $data['id'];
                        $msg = json_decode(json_encode($create), true);
                        $user = json_decode(json_encode($user), true);
                        $result =  array_merge($msg, $user, $gravatar);

                        broadcast(new MessageSent($result));
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

    public function chatEvaluation(Request $request)
    {
        $data = $request->all();

        $result = Avaliation::create([
            'chat_id'        => Crypt::decrypt($data['chat_id']),
            'stars_atendent' => $data['stars_atendent'],
            'stars_service'  => $data['stars_service'],
            'comment'        => trim($data['comment'])
        ]);

        if ($result) {
            $create = ChatHistory::create([
                'chat_id' => Crypt::decrypt($data['chat_id']),
                'type' => 'EVENT',
                'content' => 'bs-rated-the-chat',
                'created_by' => auth('api')->user()->id,
            ]);

            $create->chat_id = $data['chat_id'];
            $msg = json_decode(json_encode($create), true);
            $user = json_decode(json_encode(auth('api')->user()), true);
            $result =  array_merge($msg, $user);

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


    public function getLastChat(Request $request)
    {
        $user_id = $request->user_id;

        $chat = Chat::where('chat.created_by', $user_id)
            ->join('company_department', 'company_department.id', 'chat.company_department_id')
            ->select('chat.*', 'company_department.name as department_name')
            ->whereIn('chat.status', ['OPENED', 'IN_PROGRESS'])
            ->where('chat.type', 'DEFAULT')
            ->orderBy('chat.id', 'desc')
            ->first();

        if ($chat) {
            $chat->chat_id                          = Crypt::encrypt($chat->id);
            $chat->company_department_id            = Crypt::encrypt($chat->company_department_id);
            $chat->company_id                       = Crypt::encrypt($chat->company_id);
            $chat->created_by                       = Crypt::encrypt($chat->created_by);
            if ($chat->comp_user_comp_depart_id_current == null) {
                $chat->comp_user_comp_depart_id_current = null;
            } else {
                $chat->comp_user_comp_depart_id_current = Crypt::encrypt($chat->comp_user_comp_depart_id_current);
            }

            return  response()->json([
                'flag'   => '1',
                'status' => 'Success',
                'chat'   => $chat
            ]);
        } else {
            return response()->json([
                'flag'   => '2',
                'status' => 'Not Found'
            ]);
        }
    }

    public function files($chat_id, $filename)
    {
        $company_id = Crypt::decrypt($this->company_selected['company_id']);

        if (!isset($company_id)) {
            abort(404);
        } else {
            $path = '..' . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'company' . DIRECTORY_SEPARATOR . $company_id . DIRECTORY_SEPARATOR . 'chat' . DIRECTORY_SEPARATOR . Crypt::decrypt($chat_id) . DIRECTORY_SEPARATOR . $filename;

            if (!File::exists($path)) {
                abort(404);
            }

            $imageData = base64_encode(file_get_contents($path));
            $src = 'data: ' . mime_content_type($path) . ';base64,' . $imageData;
            return $src;
        }
    }
}
