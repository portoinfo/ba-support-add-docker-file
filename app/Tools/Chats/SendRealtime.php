<?php

namespace App\Tools\Chats;

use App\Chat;
use App\Events\CanceledUpdated;
use App\Events\ClosedUpdated;
use App\Events\FullChatCanceled;
use App\Events\FullChatClosed;
use App\Events\FullChatProgress;
use App\Events\FullChatResolved;
use App\Events\InProgressUpdated;
use App\Events\QueueUpdated;
use App\Events\ResolvedUpdated;
use App\Tools\Client;
use App\Tools\Crypt;
use Carbon\Carbon;

class SendRealtime
{

    private $chat;
    private $response;

    public function __construct($chat_id, $action = null)
    {
        $this->chat = Chat::select(
            'chat.id                                        as chat_id',
            'chat.id                                        as number',
            'chat.is_robot                                  as is_robot',
            'chat.company_id                                as company_id',
            'chat.company_department_id                     as company_department_id',
            'chat.created_at                                as created_at',
            'chat.user_agent                                as user_agent',
            'chat.status                                    as status',
            'chat.description                               as description',
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

        if ($this->chat) {

            if ($this->chat->last_chat_history_created_by == $this->chat->client_id) {
                $this->chat->answered = 1;
                $this->chat->agent_answered = 0;
            } else {
                $this->chat->answered = 0;
                $this->chat->agent_answered = 1;
            }

            if ($this->chat->update_status_in_progress !== null && $this->chat->status == 'OPENED') {
                $this->chat->transferred = true;
            } else {
                $this->chat->transferred = false;
            }

            if($action == 'took-over') {
                $this->chat->took_over = true;
            } else {
                $this->chat->took_over = false;
            }

            $this->chat->chat_id                            = Crypt::encrypt($this->chat->chat_id);
            $this->chat->company_id                         = Crypt::encrypt($this->chat->company_id);
            $this->chat->company_department_id              = Crypt::encrypt($this->chat->company_department_id);
            $this->chat->comp_user_comp_depart_id_current   = Crypt::encrypt($this->chat->comp_user_comp_depart_id_current);
            $this->chat->client_id                          = Crypt::encrypt($this->chat->client_id);
            $this->chat->user_agent_id                      = Crypt::encrypt($this->chat->user_agent_id);
            $this->chat->operator_id                        = Crypt::encrypt($this->chat->operator_id);
            $this->chat->email                              = Client::getCleanEmail($this->chat->email, Crypt::decrypt($this->chat->company_id));

            $this->response = [
                'chat_id'                                   => $this->chat->chat_id,
                'number'                                    => $this->chat->number,
                'is_robot'                                  => $this->chat->is_robot,
                'company_id'                                => $this->chat->company_id,
                'company_department_id'                     => $this->chat->company_department_id,
                'created_at'                                => $this->chat->created_at,
                'user_agent'                                => $this->chat->user_agent,
                'status'                                    => $this->chat->status,
                'description'                               => $this->chat->description,
                'comp_user_comp_depart_id_current'          => $this->chat->comp_user_comp_depart_id_current,
                'type'                                      => $this->chat->type,
                'department'                                => $this->chat->department,
                'dep_type'                                  => $this->chat->dep_type,
                'name'                                      => $this->chat->name,
                'email'                                     => $this->chat->email,
                'client_id'                                 => $this->chat->client_id,
                'user_client_id'                            => $this->chat->client_id,
                'builderall_account_data'                   => $this->chat->builderall_account_data,
                'operator'                                  => $this->chat->operator,
                'user_agent_id'                             => $this->chat->user_agent_id,
                'operator_id'                               => $this->chat->operator_id,
                'operator_email'                            => $this->chat->operator_email,
                'answered'                                  => $this->chat->answered,
                'content'                                   => $this->chat->content,
                'priority'                                  => $this->chat->priority,
                'transferred'                               => $this->chat->transferred,
                'agent_answered'                            => $this->chat->agent_answered,
                'settings'                                  => $this->chat->settings,
                'took_over'                                 => $this->chat->took_over,
                'turn_into_ticket_at_closing'               => $this->chat->turn_into_ticket_at_closing,
                'action'                                    => $action,
                'date'                                      => date('d/m/Y'),
                'time'                                      => date('H:i:s'),
                'category_chat_id'                          => $this->chat->category_chat_id,
                'end'                                       => Carbon::now()->toDateTimeString(),
            ];
        }
    }

    public function updateTableQueue()
    {
        broadcast(new QueueUpdated($this->response));
    }

    public function updateTableInProgress()
    {
        broadcast(new InProgressUpdated($this->response));
        broadcast(new FullChatProgress($this->response));
    }

    public function updateTableResolved()
    {
        broadcast(new ResolvedUpdated($this->response));
        broadcast(new FullChatResolved($this->response));
    }

    public function updateTableClosed()
    {
        broadcast(new ClosedUpdated($this->response));
        broadcast(new FullChatClosed($this->response));
    }

    public function updateTableCanceled()
    {
        broadcast(new CanceledUpdated($this->response));
        broadcast(new FullChatCanceled($this->response));
    }
}
