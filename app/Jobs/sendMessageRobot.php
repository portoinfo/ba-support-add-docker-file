<?php

namespace App\Jobs;

use App\ChatHistory;
use App\Events\MessageSent;
use App\Tools\Crypt;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class sendMessageRobot implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $message;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($message)
    {
        $this->message = $message;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {


        $query = ChatHistory::join('user_client_chat', 'user_client_chat.chat_id', 'chat_history.chat_id')
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
            ->where('chat_history.id', $this->message->id)
            ->orderBy('chat_history.id')
            ->get();



        if ($query) {

            foreach ($query as $row) {
                $row->chat_id = Crypt::encrypt($row->chat_id);
                $row->ch_id = Crypt::encrypt($row->ch_id);
                $row->client_id = Crypt::encrypt($row->client_id);
                $row->user_id = Crypt::encrypt($row->user_id);
                if ($row->company_user_company_department_id) {
                    $row->company_user_company_department_id = Crypt::encrypt($row->company_user_company_department_id);
                }
            }


            $realtime = [
                'chat_id'                               => $query[0]['chat_id'],
                'id'                                    => $query[0]['ch_id'],
                'client_id'                             => $query[0]['client_id'],
                'user_id'                               => $query[0]['user_id'],
                'company_user_company_department_id'    => $query[0]['company_user_company_department_id'],
                'type'                                  => $query[0]['type'],
                'content'                               => $query[0]['content'],
                'hidden_for_client'                     => $query[0]['hidden_for_client'],
                'time'                                  => $query[0]['time'],
                'created_at'                            => $query[0]['created_at'],
                'client_name'                           => $query[0]['client_name'],
                'client_email'                          => $query[0]['client_email'],
                'client_id'                             => $query[0]['client_id'],
                'builderall_account_data'               => $query[0]['builderall_account_data'],
                'user_name'                             => $query[0]['user_name'],
                'user_email'                            => $query[0]['user_email'],
                'user_id'                               => $query[0]['user_id'],
            ];

            broadcast(new MessageSent($realtime));
        }
    }
}
