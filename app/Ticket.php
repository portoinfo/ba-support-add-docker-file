<?php

namespace App;

use App\Events\ClientTicketsList;
use App\Events\MessageSentTicket;
use App\Models\UserClientTicket;
use App\Tools\Builderall\Logger;
use App\Tools\Crypt;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Ticket extends Model
{
    use SoftDeletes;

    protected $table = 'ticket';

    protected $guarded = [];

    protected $hidden = [
        'updated_at', 'deleted_at', 'created_by', 'updated_by', 'deleted_at'
    ];


    public function userClientTicket()
    {
        return $this->belongsTo(UserClientTicket::class, 'id', 'ticket_id');
    }

    public static function calculatorTimeQueueTicket($id_ticket, $type)
	{

		if($type == 'IN_PROGRESS' || $type == 'CANCELED'){
            $result = self::where('id', $id_ticket)->select('created_at', 'update_status_in_progress')->first();
            $datetime1 = strtotime($result->created_at);
            $datetime2 = strtotime(\Carbon\Carbon::now()->toDateTimeString());
            $secs = $datetime2 - $datetime1; // == <seconds between the two times>
            return $secs;
		}

		if($type == 'CLOSED' || $type == 'RESOLVED'){
			$result = self::where('id', $id_ticket)->select('update_status_in_progress')->first();

			if($result->update_status_in_progress != null){
				$datetime1 = strtotime($result->update_status_in_progress);
				$datetime2 = strtotime(\Carbon\Carbon::now()->toDateTimeString());
				$secs = $datetime2 - $datetime1; // == <seconds between the two times>
				return $secs;
			}
		}


	}

    public static function reopen($cucd_id, $ticket_id, $chat_id, $user_agent) {
        $success = false;
        try {

            $check_cucd = CompanyUserCompanyDepartment::join('company_user', 'company_user_company_department.company_user_id', 'company_user.id')
                ->where('company_user_company_department.id', $cucd_id)
                ->whereNull('company_user.deleted_at')
                ->where('company_user.is_active', 1)
                ->where('company_user_company_department.is_active', 1)
                ->exists();

            if (!$check_cucd) {
                DB::table('user_ticket')
                ->where('ticket_id', $ticket_id)
                ->delete();

                $status = 'OPENED';

                Ticket::where('id', $ticket_id)
                ->update([
                    'status' => $status,
                    'user_agent' => $user_agent,
                    'update_status_closed_resolved' => null,
                ]);
    
                Chat::where('ticket_id', $ticket_id)->update([
                    'comp_user_comp_depart_id_current' => null
                ]);
            } else {
                $status = 'IN_PROGRESS';
                Ticket::where('id', $ticket_id)
                ->update([
                    'status' => $status,
                    'user_agent' => $_SERVER['HTTP_USER_AGENT'],
                    'update_status_closed_resolved' => null,
                ]);
            }


            $create = ChatHistory::create([
                'chat_id' => $chat_id,
                'company_user_company_department_id' => null,
                'type' => 'EVENT',
                'content' => 'bs-reopened-the-ticket',
                'created_by' => auth()->user()->id,
            ]);

            // MessageSentTicket
            $user = auth()->user();
			$user->user_id = $user->id;
			$user->id_creator = $user->id;
			$user->user_email = $user->email;
			$user->user_name = $user->name;
	        $create->chat_id = Crypt::encrypt($chat_id);
	        $create->ch_id = Crypt::encrypt($create->id);
	        $msg = json_decode(json_encode($create), true);
	        $user = json_decode(json_encode($user), true);
	        $result =  array_merge($user, $msg);
	        $result['created_at'] = $create->created_at;
	        $result['id'] = Crypt::encrypt($chat_id);
	        broadcast(new MessageSentTicket($result));

            // ClientTicketsList
            $user_client_ticket = DB::table('user_client_ticket')
            ->select('user_client_id')
            ->where('ticket_id', $ticket_id)
            ->first();
            $client_ticket_list = [
                'id' => $ticket_id,
                'company_id' => session('companyselected')['id'],
                'user_client_id' => Crypt::encrypt($user_client_ticket->user_client_id),
                'status' => $status,
                'agent_answered' => false,
                'hash_id' => Crypt::encrypt($ticket_id),  
            ];
            if ($status == 'OPENED') {
                $remove_agent_data = [
                    'agent' => null,
                    'agent_email' => null,
                    'agent_id' => null,
                    'cucd_id' => null,
                ];
                $client_ticket_list = array_merge($client_ticket_list, $remove_agent_data);
            }
            broadcast(new ClientTicketsList($client_ticket_list));

            // AGENTS LIST
            $splice_closed = new \App\Tools\Tickets\SendRealtime($ticket_id, 'splice');
            $splice_closed->updateTableClosed();

            if ($status == 'OPENED') {
                $push = new \App\Tools\Tickets\SendRealtime($ticket_id, 'push');
                $push->updateTableQueue();
            } else {
                $push = new \App\Tools\Tickets\SendRealtime($ticket_id, 'push');
                $push->updateTableInProgress();
            }

            $success = true;
        } catch (\Exception $e) {
            dd($e);
            Logger::reportException($e, [], ['ticket', 'reopen'], false);
        }
        return $success;
    }

}
