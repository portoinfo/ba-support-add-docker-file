<?php

namespace App\Jobs;

use App\Chat;
use App\ChatHistory;
use App\Events\ChatStatusChanger;
use App\Events\ClientQueueStatus;
use App\Events\ClosedUpdated;
use App\Events\FullChatClosed;
use App\Events\FullChatProgress;
use App\Events\GlobalNotification;
use App\Events\InProgressUpdated;
use App\Events\MessageSent;
use App\Http\Controllers\ChatController;
use App\Tools\Chats\SendRealtime;
use App\Tools\ChatWorkingTimes;
use App\Tools\Crypt;
use App\UserClientChat;
use Carbon\Carbon;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\DB;

class closeUnansweredChat implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $result;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($result)
    {
        $this->result = $result;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $chat_id = Crypt::decrypt($this->result['chat_id']);
        $chat_history_id = $this->result['chat_history_id'];
        $chat = $this->result['chat'];
        $company_id = $this->result['company_id'];
        $user = $this->result['user'];
        $user_agent_id = $this->result['created_by'];
        $company_department_id = $this->result['company_department_id'];
        $company_user_id = $this->result['company_user_id'];

        if (Chat::where('chat.id', $chat_id)->where('status', 'IN_PROGRESS')->exists()) {

            $turn_into_ticket_at_closing = Chat::checkTurnIntoTicketAtClosing($chat_id);
            $last_id = ChatHistory::where('chat_id', $chat_id)->where('hidden_for_client', 0)->max('id');

            if ($turn_into_ticket_at_closing && $chat_history_id == $last_id) {
                $info = Chat::getInfoToTurnIntoTicket($chat_id);
                if ($info) {
                    $company_department = $info->company_department;
                    $cucd_id = $info->cucd_id;
                    $description = $info->description;
                    Chat::turnIntoTicket($chat_id, $cucd_id, $company_department, $description);
                }
            } else {
                if ($chat_history_id == $last_id) {

                    $result = Chat::where('id', $chat_id)->update([
                        'status' => 'CLOSED',
                        'update_status_closed_resolved' => Carbon::now()->toDateTimeString(),
                        'service_time' => Chat::calculatorTimeQueue(null, "CLOSED", $chat_id)
                    ]);

                    broadcast(new InProgressUpdated([
                        'chat_id' => Crypt::encrypt($chat_id),
                        'number' => $chat_id,
                        'company_id' => $company_id,
                        'user_agent_id' => $user_agent_id,
                        'action' => 'splice',
                    ]));

                    broadcast(new FullChatProgress([
                        'chat_id' => Crypt::encrypt($chat_id),
                        'number' => $chat_id,
                        'company_id' => $company_id,
                        'user_agent_id' => $user_agent_id,
                        'action' => 'splice',
                    ]));

                    $realtime = new SendRealtime($chat_id, 'push');
                    $realtime->updateTableClosed();

                    $comp_user_comp_depart_id_current = Chat::select('comp_user_comp_depart_id_current')
                        ->where('id', $chat_id)
                        ->first()
                        ->comp_user_comp_depart_id_current;

                    if ($comp_user_comp_depart_id_current) {
                        $company_user_company_department_id = $comp_user_comp_depart_id_current;
                    } else {
                        $company_user_company_department_id = NULL;
                    }

                    if ($result) {
                        $create = ChatHistory::create([
                            'chat_id' => $chat_id,
                            'type' => 'EVENT',
                            'content' => 'bs-the-chat-ended-due-to-inactivity',
                            'company_user_company_department_id' => $company_user_company_department_id,
                            'created_by' => $user_agent_id,
                        ]);

                        $user_client = UserClientChat::select('user_client_id as id')->where('chat_id', $chat_id)->first();

                        if (isset($user_client->id)) {
                            broadcast(new ClientQueueStatus([
                                'chat_id' => Crypt::encrypt($chat_id),
                                'company_id' => $company_id,
                                'user_client_id' => Crypt::encrypt($user_client->id),
                                'status' => 'CLOSED',
                                'agent_answered' => 1,
                                'content' => $create->content,
                                'sent_by' => null
                            ]));
                        }

                        ChatWorkingTimes::Update($chat_id);

                        $gravatar = [
                            "user_email" => $user['email'],
                            "user_name" => $user['name'],
                            "user_id" => Crypt::encrypt($user['id)']),
                        ];

                        $other_items_to_trigger = [
                            'transferred_to_department' => false,
                            'transferred_to_agent' => false,
                            'deparment_name' => "",
                            'agent_name' => "",
                            'company_user_id' => "",
                        ];

                        $create->chat_id = Crypt::encrypt($chat_id);
                        $msg = json_decode(json_encode($create), true);
                        $user2 = json_decode(json_encode($user), true);
                        $result =  array_merge($msg, $user2, $other_items_to_trigger, $gravatar);

                        broadcast(new MessageSent($result));

                        $arrayStatusChanger = [
                            'chat_id' =>Crypt::encrypt($chat_id),
                            'status'  => 'CLOSED',
                        ];

                        if (isset($chat['companyDepartmentId'])) {
                            $arrayStatusChanger = array_merge($arrayStatusChanger, ['company_department_id' => $chat['companyDepartmentId']]);
                        }

                        broadcast(new ChatStatusChanger($arrayStatusChanger));

                        $arrayGlobalNotification = [
                            'title' => 'bs-chat',
                            'body' => 'bs-the-chat-was-closed',
                            'icon' => 'https://builderall.com//franquias/2/73748/editor-html/3484811.png',
                            'url' => '',
                            'silent' => false,
                            'number' => $chat_id,
                            'type' => 'chat',
                            'status' => 'CLOSED',
                            'company_id' => $company_id,
                            'company_department_id' => $company_department_id,
                            'company_user_id' => $company_user_id,
                        ];

                        broadcast(new GlobalNotification($arrayGlobalNotification));

                        $settings = DB::table('company_department_settings')
                            ->select('settings')
                            ->where('company_department_id', Crypt::decrypt($chat['companyDepartmentId']))
                            ->first()
                            ->settings;

                        $obj = json_decode($settings, true);

                        if (isset($obj['chat']['msgClose'])) {
                            if ($obj['chat']['msgClose'] !== null) {
                                $create = ChatHistory::create([
                                    'chat_id' => $chat_id,
                                    'type' => 'CLOSE',
                                    'company_user_company_department_id' => $company_user_company_department_id,
                                    'content' => $obj['chat']['msgClose'],
                                    'created_by' => $user_agent_id,
                                ]);
                            }
                        }
                    }
                }
            }
        }
    }
}
