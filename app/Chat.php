<?php

namespace App;

use App\Events\ChatStatusChanger;
use App\Events\ClientQueueStatus;
use App\Events\ClientTicketsList;
use App\Events\GlobalNotification;
use App\Events\MessageSent;
use App\Http\Controllers\TicketController;
use App\Models\Company_department;
use App\Tools\Builderall\Logger;
use App\Tools\Chats\SendRealtime as SendRealtimeChat;
use App\Tools\ChatWorkingTimes;
use App\Tools\Client;
use App\Tools\Crypt;
use App\Tools\SystemState;
use App\Tools\Tickets\SendRealtime as SendRealtimeTicket;
use Carbon\Carbon;
use Dotenv\Result\Success;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Chat extends Model
{
    use SoftDeletes;

    protected $table = 'chat';

    protected $fillable = [
        'company_id',
        'company_department_id',
        'company_user_company_department_id',
        'comp_user_comp_depart_id_current',
        'ticket_id',
        'created_at',
        'user_agent',
        'created_by',
        'status',
        'is_robot',
        'type',
        'update_status_closed_resolved',
        'update_status_in_progress',
        'update_status_canceled',
        'queue_time',
        'service_time',
        'changed_to_ticket_at',
        'turn_into_ticket_at_closing',
        'information_to_turn_into_ticket'
    ];

    protected $hidden = [
        'updated_at', 'deleted_at', 'updated_by', 'deleted_at'
    ];

    public function chat_histories(){
        return $this->hasMany(ChatHistory::class);
    }

    public function company_user_company_department(){
        return $this->hasOne(CompanyUserCompanyDepartment::class);
    }

    public static function calculatorTimeQueue($created, $type, $id_chat)
	{
		if($type == 'IN_PROGRESS' || $type == 'CANCELED'){
			$datetime1 = strtotime($created);
			$datetime2 = strtotime(\Carbon\Carbon::now()->toDateTimeString());
			$secs = $datetime2 - $datetime1; // == <seconds between the two times>
			return $secs;
		}

		if($type == 'CLOSED'){
			$result = self::where('id', $id_chat)->select('update_status_in_progress')->first();

			if($result->update_status_in_progress != null){
				$datetime1 = strtotime($result->update_status_in_progress);
				$datetime2 = strtotime(\Carbon\Carbon::now()->toDateTimeString());
				$secs = $datetime2 - $datetime1; // == <seconds between the two times>
				return $secs;
			}
		}
	}

    public static function checkTurnIntoTicketAtClosing ($chat_id) {
        return Chat::where('id', $chat_id)->where('turn_into_ticket_at_closing', 1)->exists();
    }

    public static function getInfoToTurnIntoTicket ($chat_id) {
        $chat = Chat::select('information_to_turn_into_ticket')->where('id', $chat_id)->first();
        if (isset($chat)) {
            return json_decode($chat->information_to_turn_into_ticket);
        } else {
            return false;
        }
    }

    public static function setInfoToTurnIntoticket ($chat_id, $company_department, $description, $cucd_id = null) {
        $result['success'] = false;

        try {
            $info = [
                'company_department'    => $company_department,
                'cucd_id'               => $cucd_id,
                'description'           => $description
            ];

            $update = Chat::where('id', $chat_id)->update([
                'turn_into_ticket_at_closing'       => 1,
                'information_to_turn_into_ticket'   => json_encode($info)
            ]);

            if ($update) {
                broadcast(new ChatStatusChanger([
                    'chat_id'   => Crypt::encrypt($chat_id),
                    'number'    => $chat_id,
                    'status'    => 'TURN_INTO_TICKET_AT_CLOSING'
                ]));
                $chat_splice_and_push = new SendRealtimeChat($chat_id, 'splice_and_push');
                $chat_splice_and_push->updateTableInProgress();
                $result['success'] = true;
            }
        } catch (\Exception $e) {
            Logger::reportException($e, [], ['chat', 'turnIntoTicketAtClosing'], false);
        }
        return $result;
    }

    public static function deleteInfoToTurnIntoticket ($chat_id) {
        $success = false;

        try {
            $update = Chat::where('id', $chat_id)->update([
                'turn_into_ticket_at_closing'       => 0,
                'information_to_turn_into_ticket'   => null
            ]);

            if ($update) {
                $chat_splice_and_push = new SendRealtimeChat($chat_id, 'splice_and_push');
                $chat_splice_and_push->updateTableInProgress();
                $success = true;
            }

        } catch (\Exception $e) {
            Logger::reportException($e, [], ['chat', 'deleteInfoToTurnIntoticket'], false);
        }

        return $success;
    }

    public static function turnIntoTicket($chat_id, $cucd_id = null, $cd_id, $description)
    {
        $success = false;
            
        $chat = Chat::where('id',$chat_id)->first();
        $cd_id = intval($cd_id);
        $cd_name = Company_department::select('name')->where('id', $cd_id)->first()->name;
        $company_id = $chat->company_id;
        $cucd_id_creator = null;

        if (is_null($chat->comp_user_comp_depart_id_current)) {
            $cwt = DB::table('chat_working_times')->where('chat_id', $chat_id)->orderBy('id', 'desc')->first();
            if(isset($cwt)) {
                $cucd_id_creator = $cwt->company_user_company_department_id;
            }
        } else {
            $cucd_id_creator = $chat->comp_user_comp_depart_id_current;
        }

        if (isset($cucd_id_creator)) {
            $user_auth = CompanyUserCompanyDepartment::select('user_auth.id')
                ->join('company_user', 'company_user_company_department.company_user_id', 'company_user.id')
                ->join('user_auth', 'company_user.user_auth_id', 'user_auth.id')
                ->where('company_user_company_department.id', $cucd_id_creator)
                ->first();
        } else {
            $user_auth = (object) ['id' => $chat->created_by];
        }

        

        if (isset($user_auth)) {
            
            $auth_id = $user_auth->id;
            $cu_id = null;

            if (isset($chat) && is_null($chat->ticket_id)) {
                $ticket_status = is_null($cucd_id) ? 'OPENED' : 'IN_PROGRESS';

                $ticket = Ticket::create([
                    'company_id'                => $company_id,
                    'company_department_id'     => $cd_id,
                    'description'               => $description,
                    'status'                    => $ticket_status,
                    'type'                      => 'DEFAULT',
                    'priority'                  => 'NORMAL',
                    'user_agent'                => isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : "",
                    'comments'                  => null,
                    'created_by'                => $auth_id,
                    'update_status_in_progress' => Carbon::now()->toDateTimeString(),
                    'queue_time'                => $ticket_status == 'OPENED' ? null : 0,
                ]);

                if ($ticket) {
                    $chat_update = Chat::where('id', $chat->id)->update([
                        'type'                              => 'CHANGED_TO_TICKET',
                        'changed_to_ticket_at'              => Carbon::now()->toDateTimeString(),
                        'status'                            => 'CLOSED',
                        'company_department_id'             => $cd_id,
                        'comp_user_comp_depart_id_current'  => $cucd_id,
                        'ticket_id'                         => $ticket->id,
                        'updated_by'                        => $auth_id,
                        'update_status_closed_resolved'     => Carbon::now()->toDateTimeString(),
                        'service_time'                      => Chat::calculatorTimeQueue($chat->created_at, 'CLOSED', $chat->id),
                        'turn_into_ticket_at_closing'       => 0,
                        'information_to_turn_into_ticket'   => null
                    ]);

                    if ($chat_update) {
                        $user_client = UserClientChat::select('user_client_id as id')->where('chat_id', $chat->id)->first();

                        if (isset($user_client)) {
                            $user_client_ticket = DB::table('user_client_ticket')->insert([
                                'user_client_id'    => $user_client->id,
                                'ticket_id'         => $ticket->id,
                                'created_by'        => $auth_id,
                            ]);

                            if ($user_client_ticket) {
                                if ($ticket_status == 'IN_PROGRESS') {

                                    $cucd = CompanyUserCompanyDepartment::where('id', $cucd_id)->first();

                                    if (isset($cucd)) {

                                        $cu_id = $cucd->company_user_id;

                                        if (isset($cu_id)) {
                                            $user_ticket = DB::table('user_ticket')->insert([
                                                'company_user_id'   => $cu_id,
                                                'ticket_id'         => $ticket->id,
                                                'created_by'        => $auth_id,
                                            ]);

                                            if ($user_ticket) {
                                                $success = true;
                                            }
                                        }
                                    }
                                } else {
                                    $success = true;
                                }
                            }
                        }
                    }
                }
            }

            // WebSockets
            if ($success) {
                broadcast(new ClientQueueStatus([
                    'chat_id'               => Crypt::encrypt($chat->id),
                    'company_id'            => Crypt::encrypt($company_id),
                    'user_client_id'        => Crypt::encrypt($user_client->id),
                    'action'                => 'splice',
                    'turned_into_ticket'    => true,
                    'id'                    => $chat->id,
                    'ticket_id'             => $ticket->id
                ]));

                broadcast(new ChatStatusChanger([
                    'chat_id'   => Crypt::encrypt($chat->id),
                    'id'        => $chat->id,
                    'status'    => 'TICKET',
                    'ticket_id' => $ticket->id
                ]));

                $client_ticket_list = [
                    'company_id'        => Crypt::encrypt($company_id),
                    'user_client_id'    => Crypt::encrypt($user_client->id),
                    'action'            => 'push',
                    'hash_id'           => Crypt::encrypt($ticket->id),
                    'chat_id'           => $chat->id,
                    'ticket_id'         => $ticket->id,
                    'description'       => $ticket->description,
                    'status'            => $ticket->status,
                    'created_at'        => $ticket->created_at,
                    'department'        => $cd_name
                ];

                if (isset($cu_id)) {
                    $attendant = UserAuth::select('user_auth.id', 'user_auth.name', 'user_auth.email')
                        ->join('company_user', 'company_user.user_auth_id', 'user_auth.id')
                        ->where('company_user.id', $cu_id)
                        ->first();

                    if (isset($attendant)) {
                        $attendant_data = [
                            'attendant_name' => $attendant->name,
                            'attendant_email' => $attendant->email,
                            'attendant_id' => Crypt::encrypt($attendant->id),
                            'cucd_id' => Crypt::encrypt($cucd_id),
                        ];

                        $client_ticket_list = array_merge($attendant_data, $client_ticket_list);
                    }
                }

                broadcast(new ClientTicketsList($client_ticket_list));

                $chat_ws = new SendRealtimeChat($chat->id, 'splice');
                $chat_ws->updateTableInProgress();

                $ticket_ws = new SendRealtimeTicket($ticket->id, 'push');
                $ticket_status == 'IN_PROGRESS' ? $ticket_ws->updateTableInProgress() : $ticket_ws->updateTableQueue();

                $ch_event = ChatHistory::createEvent($chat->id, 'bs-turned-chat-into-ticket', $cucd_id_creator, $auth_id);

                $gn_items = [];
                $gn_items['company_department_id'] = Crypt::encrypt($cd_id);
                $gn_items['company_user_id'] = isset($cu_id) ? Crypt::encrypt($cu_id) : null;
                $gn_items['company_id'] = Crypt::encrypt($company_id);
                $arrayGlobalNotification = array_merge([
                    'title' => 'bs-chat',
                    'body' => 'bs-the-chat-was-turned-into-ticket',
                    'icon' => 'https://builderall.com//franquias/2/73748/editor-html/3484811.png',
                    'url' => '',
                    'silent' => false,
                    'number' => $chat->id,
                    'type' => 'chat',
                    'status' => 'TICKET',
                    'item' => [],
                ], $gn_items);

                broadcast(new GlobalNotification($arrayGlobalNotification));

                if (isset($cucd_id_creator)) {
                    if ($ch_event) {
                        $end_cwt    = ChatWorkingTimes::Update($chat->id);
                        if (isset($cucd_id)) {
                            $start_cwt  = ChatWorkingTimes::Insert($chat->id,  $cucd_id, $ch_event->id, $ticket->id);
                        }
                        if (!isset($start_cwt) && $end_cwt || $end_cwt && $start_cwt) {
                            $response = $ticket;
                        } else {
                            $response = false;
                        }
                    } else {
                        $response = false;
                    }
                } else {
                    $response = $ticket;
                }

            } else {
                $response = false;
            }
        } else {
            $response = false;
        }
        return $response;
    }

    public static function cancel($chat_id) {
        $success = false;
        $user = Auth::user();
        $chat_hash_id = Crypt::encrypt($chat_id);
        $chat = Chat::where('id', $chat_id)->first();
        if ($chat && ($chat->status == 'OPENED' || $chat->status == 'ROBOT')) {
            $update = Chat::where('id', $chat->id)->update([
                'status'                    => 'CANCELED',
                'update_status_canceled'    => Carbon::now()->toDateTimeString(),
                'queue_time'                => Chat::calculatorTimeQueue($chat->created_at, 'CANCELED', $chat->id),
            ]);
            if ($update) {
                ChatWorkingTimes::Update($chat->id);
                $ch = ChatHistory::create([
                    'chat_id' => $chat->id,
                    'type' => 'EVENT',
                    'content' => 'bs-canceled-the-chat',
                    'company_user_company_department_id' => $chat->comp_user_comp_depart_id_current,
                    'created_by' => $user->id,
                ]);

                //webSockets
                $rt1 = new SendRealtimeChat($chat->id, 'push');
                $rt1->updateTableCanceled();
                $rt2 = new SendRealtimeChat($chat->id, 'splice');
                $rt2->updateTableQueue();
                $rt2->updateTableInProgress();
                $client_info = [
                    "client_email" => Auth::user()->email,
                    "client_name" => Auth::user()->name,
                    "client_id" => Crypt::encrypt(Auth::user()->id),
                ];
                $ch->chat_id = $chat_hash_id;
                $msg = json_decode(json_encode($ch), true);
                $user = json_decode(json_encode($user), true);
                $message_sent_info =  array_merge($msg, $user, $client_info);
                broadcast(new MessageSent($message_sent_info));
                broadcast(new ChatStatusChanger([
                    'chat_id' => $chat_hash_id,
                    'status'  => 'CANCELED',
                    'company_department_id' => Crypt::encrypt($chat->company_department_id),
                ]));
                $ucc = UserClientChat::where('chat_id', $chat->id)->first();
                $user_client_id = $ucc->user_client_id;
                broadcast(new ClientQueueStatus([
                    'chat_id' => $chat_hash_id,
                    'company_id' => Crypt::encrypt($chat->company_id),
                    'user_client_id' => Crypt::encrypt($user_client_id),
                    'status' => 'CANCELED',
                    'agent_answered' => 0,
                    'content' => $ch->content,
                ]));
                $success = true;
            }
        }

        return $success;
    }

    public static function resolve($chat_id) {
        $success = false;
        $is_client = isset(session('companyselected')['user_client_id']);
        $user = Auth::user();
        $chat_hash_id = Crypt::encrypt($chat_id);
        $chat = Chat::where('id', $chat_id)->first();

        $company_user = Chat::select('company_user.id as company_user_id')
        ->join('company_user_company_department', 'company_user_company_department.id', 'chat.comp_user_comp_depart_id_current')
        ->join('company_user', 'company_user.id', 'company_user_company_department.company_user_id')
        ->where('chat.id', $chat_id)
        ->first();

        $company_user_id = isset($company_user->company_user_id) ? $company_user->company_user_id : null;

        if ($is_client) {
            $company_id = session('companyselected')['company_id'];
            $user_client_id = session('companyselected')['user_client_id'];
            $company_user_company_department_id = null;
            $user_ws_info = [
                "client_email" => $user->email,
                "client_name" => $user->name,
                "client_id" => Crypt::encrypt($user->id),
            ];
        } else {
            $company_id = session('companyselected')['id'];
            $user_client_id = UserClientChat::select('user_client_id')->where('chat_id', $chat_id)->first()->user_client_id;
            $user_client_id = Crypt::encrypt($user_client_id);
            $company_user_company_department_id = $chat->comp_user_comp_depart_id_current;
            $user_ws_info = [
                "user_email" => $user->email,
                "user_name" => $user->name,
                "user_id" => Crypt::encrypt($user->id),
            ];
        }

        $update = Chat::where('id', $chat_id)->update([
            'status' => 'RESOLVED',
            'update_status_closed_resolved' => Carbon::now()->toDateTimeString(),
            'service_time' => Chat::calculatorTimeQueue($chat->created_at, 'CLOSED', $chat_id),
        ]);

        if ($update) {
            $splice_realtime = new SendRealtimeChat($chat->id, 'splice');
            $splice_realtime->updateTableQueue();
            $splice_realtime->updateTableInProgress();
            $splice_realtime->updateTableResolved();
            $splice_realtime->updateTableClosed();
            $splice_realtime->updateTableCanceled();

            ChatWorkingTimes::Update($chat_id);

            $ch = ChatHistory::create([
                'chat_id' => $chat->id,
                'type' => 'EVENT',
                'content' => 'bs-marked-as-resolved',
                'company_user_company_department_id' => $company_user_company_department_id,
                'created_by' => $user->id,
            ]);

            $ch->chat_id = $chat_hash_id;
            $msg = json_decode(json_encode($ch), true);
            $user = json_decode(json_encode($user), true);
            $message_sent_info =  array_merge($msg, $user, $user_ws_info);
            broadcast(new MessageSent($message_sent_info));

            broadcast(new GlobalNotification([
                'title' => 'bs-chat',
                'body' => $ch->content,
                'icon' => 'https://builderall.com//franquias/2/73748/editor-html/3484811.png',
                'url' => '',
                'silent' => false,
                'number' => $chat_id,
                'type' => 'chat',
                'status' => 'RESOLVED',
                'company_user_id' => Crypt::encrypt($company_user_id),
                'company_department_id' => Crypt::encrypt($chat->company_department_id),
                'company_id' => $company_id
            ]));

            broadcast(new ChatStatusChanger([
                'chat_id'   => $chat_hash_id,
                'status'    =>  'RESOLVED',
                'company_department_id' => Crypt::encrypt($chat->company_department_id),
            ]));

            broadcast(new ClientQueueStatus([
                'chat_id' => $chat_hash_id,
                'company_id' => $company_id,
                'user_client_id' => $user_client_id,
                'agent_answered' => $is_client ? 0 : 1,
                'content' => $ch->content,
                'sent_by' => $is_client ? null : $user->name,
            ]));

            $success = true;
        }

        return $success;
    }

    public static function setUserAgent($id) {
        Chat::where('id', Crypt::decrypt($id))
        ->update([
            'user_agent' => $_SERVER['HTTP_USER_AGENT'], 
            'updated_at' => DB::raw('updated_at')
        ]);
    }


    public static function get($chat_id, $action = null)
    {
        $chat = Chat::select(
            'chat.id                                        as chat_id',
            'chat.id                                        as number',
            'chat.is_robot                                  as is_robot',
            'chat.company_id                                as company_id',
            'chat.company_department_id                     as company_department_id',
            'chat.created_at                                as created_at',
            'chat.user_agent                                as user_agent',
            'chat.status                                    as status', 
            'chat.description                               as chat_description', 
            'chat.comp_user_comp_depart_id_current          as comp_user_comp_depart_id_current',
            'chat.type                                      as type',
            'chat.priority                                  as priority',
            'chat.update_status_in_progress                 as update_status_in_progress',
            'chat.turn_into_ticket_at_closing               as turn_into_ticket_at_closing',
            'company_department.name                        as department',
            'company_department.type                        as dep_type',
            'ua_client.name                                 as name',
            'ua_client.email                                as email',
            'ua_client.id                                   as client_id',
            'ua_client.builderall_account_data              as builderall_account_data',
            'ua_agent.name                                  as operator',
            'ua_agent.id                                    as user_agent_id',
            'ua_agent.id                                    as operator_id',
            'ua_agent.email                                 as operator_email',
            'company_department_settings.settings           as settings',
            'chat_history.id                                as chat_history_id',
            'chat_history.created_by                        as last_chat_history_created_by',
            'chat_history.content                           as content',
            'cc.chat_id                                     as category_chat_id'
        )
            ->join('chat_history',                          'chat_history.chat_id', '=', 'chat.id')
            ->join('company_department',                    'company_department.id', 'chat.company_department_id')
            ->join('company_department_settings',           'company_department.id', 'company_department_settings.company_department_id')
            ->join('user_client_chat',                      'user_client_chat.chat_id', 'chat.id')
            ->join('user_client',                           'user_client.id', 'user_client_chat.user_client_id')
            ->join('user_auth AS ua_client',                'user_client.user_auth_id', 'ua_client.id')
            ->leftjoin('company_user_company_department',   'chat.comp_user_comp_depart_id_current', 'company_user_company_department.id')
            ->leftjoin('company_user',                      'company_user_company_department.company_user_id', 'company_user.id')
            ->leftjoin('user_auth AS ua_agent',             'company_user.user_auth_id', 'ua_agent.id')
            ->leftJoin('chat_category AS cc',               'chat.id', 'cc.chat_id')
            ->where('chat.id', $chat_id)
            ->orderBy('chat_history_id', 'desc')
            ->first();

        if ($chat) {

            if ($chat->last_chat_history_created_by == $chat->client_id) {
                $chat->answered = 1;
                $chat->agent_answered = 0;
            } else {
                $chat->answered = 0;
                $chat->agent_answered = 1;
            }

            if ($chat->update_status_in_progress !== null && $chat->status == 'OPENED') {
                $chat->transferred = true;
            } else {
                $chat->transferred = false;
            }

            if($action == 'took-over') {
                $chat->took_over = true;
            } else {
                $chat->took_over = false;
            }

            $chat->chat_id                            = Crypt::encrypt($chat->chat_id);
            $chat->company_id                         = Crypt::encrypt($chat->company_id);
            $chat->company_department_id              = Crypt::encrypt($chat->company_department_id);
            $chat->comp_user_comp_depart_id_current   = Crypt::encrypt($chat->comp_user_comp_depart_id_current);
            $chat->client_id                          = Crypt::encrypt($chat->client_id);
            $chat->user_agent_id                      = Crypt::encrypt($chat->user_agent_id);
            $chat->operator_id                        = Crypt::encrypt($chat->operator_id);
            $chat->email                              = Client::getCleanEmail($chat->email, Crypt::decrypt($chat->company_id));

            $response = [
                'chat_id'                                   => $chat->chat_id,
                'number'                                    => $chat->number,
                'is_robot'                                  => $chat->is_robot,
                'company_id'                                => $chat->company_id,
                'company_department_id'                     => $chat->company_department_id,
                'created_at'                                => $chat->created_at,
                'user_agent'                                => $chat->user_agent,
                'status'                                    => $chat->status,
                'comp_user_comp_depart_id_current'          => $chat->comp_user_comp_depart_id_current,
                'type'                                      => $chat->type,
                'department'                                => $chat->department,
                'dep_type'                                  => $chat->dep_type,
                'name'                                      => $chat->name,
                'email'                                     => $chat->email,
                'client_id'                                 => $chat->client_id,
                'user_client_id'                            => $chat->client_id,
                'builderall_account_data'                   => $chat->builderall_account_data,
                'operator'                                  => $chat->operator,
                'user_agent_id'                             => $chat->user_agent_id,
                'operator_id'                               => $chat->operator_id,
                'operator_email'                            => $chat->operator_email,
                'answered'                                  => $chat->answered,
                'content'                                   => $chat->content,
                'priority'                                  => $chat->priority,
                'transferred'                               => $chat->transferred,
                'agent_answered'                            => $chat->agent_answered,
                'settings'                                  => $chat->settings,
                'took_over'                                 => $chat->took_over,
                'turn_into_ticket_at_closing'               => $chat->turn_into_ticket_at_closing,
                'action'                                    => $action,
                'date'                                      => date('d/m/Y'),
                'time'                                      => date('H:i:s'),
                'category_chat_id'                          => $chat->category_chat_id,
                'end'                                       => Carbon::now()->toDateTimeString(),
            ];
        }

        return $response;
    }



}
