<?php

namespace App\Http\Controllers;

use App\Avaliation;
use App\Chat;
use App\CompanyUserCompanyDepartment;
use App\Models\Company_department;
use App\Models\Company_user;
use App\Ticket;
use App\Tools\Builderall\Logger;
use App\Tools\Crypt;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MonitoringController extends Controller
{
    public function index(){
		return view('functions.admin.monitoring.monitoring');
 	}

    public function countTicketsChatsOnQueue(Request $request) {

        $result['success'] = false;

        try {

            $type = $request->type;
            $departments = $request->departments;

            $is_chat = $type == 'CHAT';
            $is_ticket = $type == 'TICKET';

            if ($is_chat) {
                $query = Chat::where('chat.company_id', Crypt::decrypt(session('companyselected')['id']))
                ->join('user_client_chat', 'user_client_chat.chat_id', 'chat.id')
                ->join('user_client', 'user_client.id', 'user_client_chat.user_client_id')
                ->join('user_auth', 'user_auth.id', 'user_client.user_auth_id')
                ->whereNull('chat.ticket_id')
                ->where('chat.status', 'OPENED')
                ->where('chat.type', 'DEFAULT');
            } else if ($is_ticket) {
                $query = Ticket::where('ticket.company_id', Crypt::decrypt(session('companyselected')['id']))
                ->where('ticket.status', 'OPENED');
            }

            if ($departments !== null) {

                $department = Crypt::decrypt($departments);

                if ($is_chat) {
                    $query->join('company_department AS cd', 'cd.id', 'chat.company_department_id')->groupBy('chat.id');

                } else if ($is_ticket) {
                    $query->join('company_department AS cd', 'cd.id', 'ticket.company_department_id');
                }

                $query->where('company_department_id', $department);
            }

            $result['count'] = $query->count();
            $result['success'] = true;

        } catch (\Exception $e) {
            Logger::reportException($e, [], ['monitoring-controller', 'countChatsOnQueue'], false);
        }

        return $result;

    }

    public function calcAVGWaitingTime(Request $request) {

        $result['success'] = false;

        try {

            $type = $request->type;
            $departments = $request->departments;
            $timezone = $request->timezone;

            $is_chat = $type == 'CHAT';
            $is_ticket = $type == 'TICKET';

            $date = new \DateTime("now", new \DateTimeZone($timezone));
            $date = $date->format('d/m/Y');

            $date1 = $date . " 00:00:00";
            $date1 = Carbon::createFromFormat('d/m/Y H:i:s', $date1, $timezone)->setTimezone('UTC');

            $date2 = $date . " 23:59:59";
            $date2 = Carbon::createFromFormat('d/m/Y H:i:s', $date2, $timezone)->setTimezone('UTC');

            if ($is_ticket) {
                $sql = "CALL pro_real_time_company_tickets_average_time_queue_and_service(?, ?, ?, ?);";
            } else if ($is_chat) {
                $sql = "CALL pro_real_time_company_chats_average_time_queue_and_service(?, ?, ?, ?);";
            }

            if ($departments !== null) {
                $department = Crypt::decrypt($departments);
            } else {
                $department = $departments;
            }

            $res = DB::select($sql, [
                Crypt::decrypt(session('companyselected')['id']),
                $department,
                $date1,
                $date2
            ]);

            $result['value'] = $res[0];
            $result['success'] = true;

        } catch (\Exception $e) {
            Logger::reportException($e, [], ['monitoring-controller', 'calcAVGWaitingTime'], false);
        }

        return $result;
    }

    public function getChatsTicketsCancelled(Request $request) {

        $result['success'] = false;

        try {

            $type = $request->type;
            $department = $request->departments;
            $timezone = $request->timezone;

            $is_chat = $type == 'CHAT';
            $is_ticket = $type == 'TICKET';

            $date = new \DateTime("now", new \DateTimeZone($timezone));
            $date = $date->format('d/m/Y');

            $date1 = $date . " 00:00:00";
            $date1 = Carbon::createFromFormat('d/m/Y H:i:s', $date1, $timezone)->setTimezone('UTC');

            $date2 = $date . " 23:59:59";
            $date2 = Carbon::createFromFormat('d/m/Y H:i:s', $date2, $timezone)->setTimezone('UTC');

            if ($is_ticket) {
                $sql = Ticket::where('company_id', Crypt::decrypt(session('companyselected')['id']))
                    ->where('status', 'CANCELED')
                    ->whereBetween('created_at', [$date1, $date2]);
            } else if ($is_chat) {
                $sql = Chat::where('company_id', Crypt::decrypt(session('companyselected')['id']))
                    ->where('status', 'CANCELED')
                    ->where('type', 'DEFAULT')
                    ->whereBetween('created_at', [$date1, $date2]);
            }

            if ($department !== null) {
                $sql->where('company_department_id', Crypt::decrypt($department));
            }

            $query = $sql->count();

            $result['count'] = $query;
            $result['success'] = true;
        } catch (\Exception $e) {
            Logger::reportException($e, [], ['monitoring-controller', 'getChatsTicketsCancelled'], false);
        }

        return $result;
    }

    public function countAgentsByCompany(Request $request) {

        $result['success'] = false;

        try {

            $sql = User::Join('company_user', 'user_auth.id', 'company_user.user_auth_id')
                ->join('company_user_company_department AS cucd', 'company_user.id', 'cucd.company_user_id')
                ->where('company_user.company_id', Crypt::decrypt(session('companyselected')['id']))
                ->where('cucd.is_active', 1)
                ->whereNull('company_user.deleted_at')
                ->groupBy('user_auth.id');

            if ($request->department !== null) {
                $sql->where('cucd.company_department_id', Crypt::decrypt($request->department));
            }

            $count = $sql->get();
            $count = $count->count();

            $result['count'] = $count;
            $result['success'] = true;

        } catch (\Exception $e) {
            Logger::reportException($e, [], ['monitoring-controller', 'countAgentsByCompany'], false);
        }

        return $result;
    }

    public function sumDurationTicketChats(Request $request) {

        $result['success'] = false;

        try {

            $type = $request->type;
            $department = $request->departments;
            $timezone = $request->timezone;

            $is_chat = $type == 'CHAT';
            $is_ticket = $type == 'TICKET';

            $date = new \DateTime("now", new \DateTimeZone($timezone));
            $date = $date->format('d/m/Y');

            $date1 = $date . " 00:00:00";
            $date1 = Carbon::createFromFormat('d/m/Y H:i:s', $date1, $timezone)->setTimezone('UTC');

            $date2 = $date . " 23:59:59";
            $date2 = Carbon::createFromFormat('d/m/Y H:i:s', $date2, $timezone)->setTimezone('UTC');

            if ($is_ticket) {
                // .....
            } else if ($is_chat) {
                $query1 = Chat::select('service_time')
                    ->where('company_id', Crypt::decrypt(session('companyselected')['id']))
                    ->where('type', '!=', 'TICKET')
                    ->whereIn('status', ['CLOSED', 'RESOLVED'])
                    ->whereBetween('created_at', [$date1, $date2])
                    ->whereNotNull('service_time');

                $query2 = Chat::select(DB::raw('ROUND(AVG(service_time)) AS avg, SUM(service_time) AS sum_st_finished'))
                    ->where('company_id', Crypt::decrypt(session('companyselected')['id']))
                    ->where('type', '!=', 'TICKET')
                    ->whereIn('status', ['CLOSED', 'RESOLVED'])
                    ->whereBetween('created_at', [$date1, $date2])
                    ->whereNotNull('service_time');

                $query3 = Chat::select(DB::raw('TIME_TO_SEC(TIMEDIFF(UTC_TIMESTAMP(), update_status_in_progress)) AS diff, SUM(TIME_TO_SEC(TIMEDIFF(UTC_TIMESTAMP(), update_status_in_progress))) AS sum_st_now'))
                    ->where('company_id', Crypt::decrypt(session('companyselected')['id']))
                    ->where('status', 'IN_PROGRESS')
                    ->where('type', 'DEFAULT')
                    ->orderBy('diff', 'DESC');
            }

            if ($department !== null) {
                $query1->where('company_department_id', Crypt::decrypt($department));
                $query2->where('company_department_id', Crypt::decrypt($department));
                $query3->where('company_department_id', Crypt::decrypt($department));
            }

            $result1 = $query1->orderBy('service_time', 'DESC')->first();
            $result2 = $query2->get();
            $service_time_now = $query3->get();

            $avg = 0;
            $sum_st_finished = 0;
            $sum_st_now = 0;
            $longer_closed_resolved = 0;
            $longer_now = 0;

            if (count($result2) > 0) {
                $avg = intval($result2[0]['avg']);
                $sum_st_finished = intval($result2[0]['sum_st_finished']);
            }

            if($result1 != null) {
                $longer_closed_resolved = $result1->service_time;
            }

            if (count($service_time_now) > 0) {
                $longer_now = $service_time_now[0]['diff'];
                $sum_st_now = intval($service_time_now[0]['sum_st_now']);
            }

            if ($longer_closed_resolved > $longer_now) {
                $longer = $longer_closed_resolved ;
            } else {
                $longer = $longer_now;
            }

            $sum_total_service_time = $sum_st_finished + $sum_st_now;

            $result['success'] = true;
            $result['counters'] = [
                'longer_closed_resolved'    => $longer_closed_resolved,
                'longer_in_progress_now'    => $longer_now,
                'longer'                    => $longer,
                'avg'                       => $avg,
                'diff'                      => $service_time_now,
                'sum_total_service_time'    => $sum_total_service_time
            ];

        } catch (\Exception $e) {
            Logger::reportException($e, [], ['monitoring-controller', 'sumDurationTicketChats'], false);
        }

        return $result;

    }

    public function getEvaluationsToday(Request $request) {

        $result['success'] = false;

        try {

            $type = $request->type;
            $department = $request->departments;
            $timezone = $request->timezone;

            $is_chat = $type == 'CHAT';
            $is_ticket = $type == 'TICKET';

            $date = new \DateTime("now", new \DateTimeZone($timezone));
            $date = $date->format('d/m/Y');

            $date1 = $date . " 00:00:00";
            $date1 = Carbon::createFromFormat('d/m/Y H:i:s', $date1, $timezone)->setTimezone('UTC');

            $date2 = $date . " 23:59:59";
            $date2 = Carbon::createFromFormat('d/m/Y H:i:s', $date2, $timezone)->setTimezone('UTC');

            if ($is_ticket) {
                // .....
            } else if ($is_chat) {

                $query1 = Avaliation::join('chat', 'chat.id', 'avaliation.chat_id')
                            ->join('company_department', 'chat.company_department_id', 'company_department.id')
                            ->whereNull('chat.deleted_at')
                            ->whereNull('avaliation.ticket_id')
                            ->whereNotNull('stars_atendent')
                            ->whereBetween('avaliation.created_at', [$date1, $date2]);
                            

                $query2 = Avaliation::join('chat', 'chat.id', 'avaliation.chat_id')
                            ->join('company_department', 'chat.company_department_id', 'company_department.id')
                            ->whereNull('chat.deleted_at')
                            ->whereNull('avaliation.ticket_id')
                            ->whereNotNull('stars_service')
                            ->whereBetween('avaliation.created_at', [$date1, $date2]);
                            
            }

            if ($department !== null) {
                $query1->where('company_department.id', Crypt::decrypt($department));
                $query2->where('company_department.id', Crypt::decrypt($department));
            }


            $atendent_satisfaction = $query1->avg('stars_atendent');
            $service_satisfaction = $query2->avg('stars_service');


            $result['success'] = true;
            $result['evaluations'] = [
                'service_satisfaction'  => number_format(doubleval($service_satisfaction), 2),
                'atendent_satisfaction' => number_format(doubleval($atendent_satisfaction), 2)
            ];

        } catch (\Exception $e) {
            Logger::reportException($e, [], ['monitoring-controller', 'getEvaluationsToday'], false);
        }

        return $result;
    }

    public function getDepartmentByChatId(Request $request) {

        $result['success'] = false;

        try {

            $chat_id = Crypt::decrypt($request->chat_id);

            $query = Chat::select('company_department_id')->where('id', $chat_id)->get();

            if ($query[0]['company_department_id']) {
                $result['success'] = true;
                $result['department_id'] = Crypt::encrypt($query[0]['company_department_id']);
            }

        } catch (\Exception $e) {
            Logger::reportException($e, [], ['monitoring-controller', 'getDepartmentByChatId'], false);
        }

        return $result;
    }

    public function countOnlineAgentsByDeparment(Request $request) {

        $result['success'] = false;

        try {

            $online_agents = $request->online_agents;
            $department_id = Crypt::decrypt($request->department_id);

            if (count($online_agents)) {

                $count = 0;

                foreach ($online_agents as $id) {
                    $company_user = Company_user::select('id')->where('user_auth_id', Crypt::decrypt($id))->get();

                    if (count($company_user)) {
                        foreach ($company_user as $row) {
                            $cucd = CompanyUserCompanyDepartment::where('company_user_id', $row->id)
                                    ->where('company_department_id', $department_id)
                                    ->where('is_active', 1)
                                    ->first();
                            if ($cucd) {
                                $count++;
                            }
                        }
                    }
                }

                $result['count'] = $count;
                $result['success'] = true;
            } else {
                $result['success'] = false;
            }

        } catch (\Exception $e) {
            Logger::reportException($e, [], ['monitoring-controller', 'countOnlineAgentsByDeparment'], false);
        }

        return $result;
    }
}
