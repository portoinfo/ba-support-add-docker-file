<?php

namespace App\Tools\Tickets;

use App\Events\FullTicketCanceled;
use App\Events\FullTicketClosed;
use App\Events\FullTicketInProgress;
use App\Events\FullTicketResolved;
use App\Events\TicketQueueUpdated;
use App\Ticket;
use App\Tools\Client;
use App\Tools\Crypt;
use App\Tools\SystemState;
use Illuminate\Support\Facades\DB;

class SendRealtime
{

    private $ticket;
    private $response;

    public function __construct($ticket_id, $action)
    {
        $ticket = Ticket::where('id',$ticket_id)->first();
        $company_id = $ticket->company_id;

        $this->ticket = Ticket::select(
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
            DB::raw('DATE_FORMAT(ticket.created_at,"%H:%i:%s") as time'),
            'cc.chat_id as category_chat_id'
        )
        ->join('company_department AS cd', 'ticket.company_department_id', 'cd.id')
        ->join('company_department_settings AS cds', 'cd.id', 'cds.company_department_id')
        ->join('chat AS c', 'ticket.id', 'c.ticket_id')
        ->join('user_auth AS ua_create', 'ticket.created_by', 'ua_create.id')
        ->leftjoin('company_user_company_department AS cucd', 'c.comp_user_comp_depart_id_current', 'cucd.id')
        ->leftjoin('company_user AS cu', 'cucd.company_user_id', 'cu.id')
        ->leftjoin('user_auth AS ua', 'cu.user_auth_id', 'ua.id')
        ->leftJoin('user_client_ticket AS uct', 'ticket.id', 'uct.ticket_id')
        ->leftJoin('user_client AS uc', 'uct.user_client_id', 'uc.id')
        ->leftJoin('user_auth AS ua_client', 'uc.user_auth_id', 'ua_client.id')
        ->leftJoin('chat_category AS cc', 'c.id', 'cc.chat_id')
        ->where('ticket.company_id', $company_id)
        ->where('ticket.id', $ticket_id)
        ->first();

        if ($this->ticket) {
            $this->ticket->ticket_id                            = Crypt::encrypt($this->ticket->ticket_id);
            $this->ticket->email_created                        = Client::getCleanEmail($this->ticket->email_created, $this->ticket->company_id);
            $this->ticket->company_id                           = Crypt::encrypt($this->ticket->company_id);
            $this->ticket->client_id                            = Crypt::encrypt($this->ticket->id_created);
            $this->ticket->company_user_company_department_id   = Crypt::encrypt($this->ticket->company_user_company_department_id);
            $this->ticket->chat_id_crypt                        = Crypt::encrypt($this->ticket->chat_id);
            $this->ticket->department_id                        = Crypt::encrypt($this->ticket->department_id);

            $this->response = [
                'id'                                            => $this->ticket->id,
                'number'                                        => $this->ticket->number,
                'ticket_id'                                     => $this->ticket->ticket_id,
                'status'                                        => $this->ticket->status,
                'description'                                   => $this->ticket->description,
                'created_at'                                    => $this->ticket->created_at,
                'updated_at'                                    => $this->ticket->updated_at,
                'created_by'                                    => $this->ticket->created_by,
                'chat_created_by'                               => $this->ticket->chat_created_by,
                'priority'                                      => $this->ticket->priority,
                'type'                                          => $this->ticket->type,
                'user_agent'                                    => $this->ticket->user_agent,
                'comments'                                      => $this->ticket->comments,
                'company_id'                                    => $this->ticket->company_id,
                'update_status_in_progress'                     => $this->ticket->update_status_in_progress,
                'name'                                          => $this->ticket->name,
                'email'                                         => $this->ticket->email,
                'operator_id'                                   => $this->ticket->operator_id,
                'department'                                    => $this->ticket->department,
                'department_type'                               => $this->ticket->department_type,
                'department_id'                                 => $this->ticket->department_id,
                'company_user_company_department_id'            => $this->ticket->company_user_company_department_id,
                'id_created'                                    => $this->ticket->id_created,
                'client_id'                                     => $this->ticket->client_id,
                'name_created'                                  => $this->ticket->name_created,
                'email_created'                                 => $this->ticket->email_created,
                'builderall_account_data'                       => $this->ticket->builderall_account_data,
                'service_time'                                  => $this->ticket->service_time,
                'last_update_status'                            => $this->ticket->last_update_status,
                'chat_id'                                       => $this->ticket->chat_id,
                'chat_id_crypt'                                 => $this->ticket->chat_id_crypt,
                'company_user_id'                               => $this->ticket->company_user_id,
                'chat_type'                                     => $this->ticket->chat_type,
                'settings'                                      => $this->ticket->settings,
                'answered'                                      => $this->ticket->answered,
                'date'                                          => $this->ticket->date,
                'time'                                          => $this->ticket->time,
                'category_chat_id'                              => $this->ticket->category_chat_id,
                'action'                                        => $action
            ];
        }
    }

    public function updateTableQueue()
    {
        broadcast(new TicketQueueUpdated($this->response));
    }

    public function updateTableInProgress()
    {
        broadcast(new FullTicketInProgress($this->response));
    }

    public function updateTableResolved()
    {
        broadcast(new FullTicketResolved($this->response));
    }

    public function updateTableClosed()
    {
        broadcast(new FullTicketClosed($this->response));
    }

    public function updateTableCanceled()
    {
        broadcast(new FullTicketCanceled($this->response));
    }

    public function getTicket() {
        return $this->response;
    }
}
