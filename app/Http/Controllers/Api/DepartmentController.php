<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Company_department;
use App\Tools\Builderall\Logger;
use App\Tools\ConfigsCompanyReleased;
use App\Tools\Crypt;
use App\Tools\SystemState;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DepartmentController extends Controller
{
    public function getOpenDepartments(Request $request)
    {
        $company_selected = SystemState::getCacheForApi(auth('api')->user()->id, 'companyselected', null);

        if (isset($company_selected['user_client_id']))
        {
            $company_id = Crypt::decrypt($company_selected['company_id']);
        }
        else
        {
            $company_id = Crypt::decrypt($company_selected['id']);
        }

        $query = Company_department::join('company_department_settings', 'company_department_settings.company_department_id', 'company_department.id')
            ->join('company', 'company_department.company_id', 'company.id')
            ->select('company_department.id', 'company_department.name', 'company_department_settings.settings')
            ->where('company.id', $company_id)
            ->where('company_department.is_active', 1)
            ->where('company_department.type', "")
            ->orderBy('company_department.name');

        $departments = $query->get();

        $country_bw = $request->country_bw; // país do browser Ex: pt_BR
        $country_sys = $request->country_sys; // país do sistema Ex: pt_BR
        $response = [];
        $index = 0;

        foreach ($departments as $department) {
            $settings = json_decode($department->settings); // configurações do departamento.

            $departmentTimezone = $settings->general->language; // Timezone do departamento.

            $find = 0;

            $dateTime = new \DateTime(
                'now',
                new \DateTimeZone($departmentTimezone)
            );
            $dayOfTheWeek = $dateTime->format('N');

            if ($dayOfTheWeek === (int) 7) {
                $dayOfTheWeek = 0;
            }

            $settings_module = $settings->general->module->code; //pega o modulo

            // verifica se o departamento tem o modulo 'chat' ativo
            if ($settings_module == "ALL" || $settings_module == "CHAT") {

                $settings_country = $settings->general->userLang; //pega os paises do departamento
                $found_contry = 0;

                foreach ($settings_country as $row) {
                    //verifica se esta liberado para todos os paises ou se o país do browser ou sistema está incluso.
                    //$row->code ou é "ALL" ou retorna o país assim neste formato = 'BR', 'US' etc...
                    if ($row->code == "ALL") {
                        $found_contry = 1;
                    } else if ($country_bw == $row->code || $country_sys == $row->code) {
                        $found_contry = 1;
                    }
                }

                if ($found_contry) {

                    if (property_exists($settings->chat, 'openChatOnline')) {
                        $openChatOnline = $settings->chat->openChatOnline;
                    } else {
                        $openChatOnline = false;
                    }

                    $open_chat = $settings->chat->openChat;

                    // verifica se o departamento aceita a abertura de chats por clientes, passa soh os que aceitam
                    if ($open_chat) {
                        // Tenho que adicionar o ":00" no final para deixar a data no formato d/m/Y H:i:s.
                        $openHour = $settings->chat->openDepartment[$dayOfTheWeek]->am . ":00";
                        $closeHour = $settings->chat->openDepartment[$dayOfTheWeek]->ap . ":00";

                        if ($openHour === "00:00:00" && $closeHour === "00:00:00") {
                            // Se ambas as horas forem 00:00:00 significa que o departamento nao trabalha nesse dia
                        } else {
                            $serverDate = date('d/m/Y H:i:s');

                            $date = new \DateTime("now", new \DateTimeZone($departmentTimezone));
                            $date = $date->format('d/m/Y');

                            $openDate = $date . " " . $openHour;
                            $closeDate = $date . " " . $closeHour;

                            $serverDateToUTC = Carbon::createFromFormat('d/m/Y H:i:s', $serverDate, "UTC"); // Criação da data do server para UTC
                            if ($departmentTimezone == 'UTC') {
                                $openDateToUTC = Carbon::createFromFormat('d/m/Y H:i:s', $openDate, $departmentTimezone);
                                $closeDateToUTC = Carbon::createFromFormat('d/m/Y H:i:s', $closeDate, $departmentTimezone);
                            } else {
                                $openDateToUTC = Carbon::createFromFormat('d/m/Y H:i:s', $openDate, $departmentTimezone)->setTimezone('UTC'); // Conversão da data de abertura para UTC
                                $closeDateToUTC = Carbon::createFromFormat('d/m/Y H:i:s', $closeDate, $departmentTimezone)->setTimezone('UTC'); // Conversão da data de fechamento para UTC
                            }

                            if (($serverDateToUTC >= $openDateToUTC) && ($serverDateToUTC <= $closeDateToUTC)) {
                                // Se a data do server estiver entre a data de abertura e data de fechamento, o departamento está aberto.
                                $response[$index]['online'] = 1;
                                $find = 1;
                            } else {
                                // Se a data do server não estiver entre a data de abertura e data de fechamento
                                $response[$index]['online'] = 0;
                                $find = 1;
                            }
                            $response[$index]['id'] = Crypt::encrypt($department->id);
                            $response[$index]['name'] = $department->name;
                            $response[$index]['openDateToUTC'] = $openDateToUTC;
                            $response[$index]['closeDateToUTC'] = $closeDateToUTC;
                            $response[$index]['openChatOnline'] = $openChatOnline;
                        }
                    }
                }
            }
            if ($find) {
                $index++;
            }
        }

        return response()->json($response);
    }

    public function getQuestions($id)
    {
        $robot['success'] = false;

        try {
            //CÓDIGO PARA SANEAR AS QUESTÕES COMO type_question NULL
            $robot['result'] = DB::table('company_depart_settings_question')
                ->select('id', 'question as quest', 'company_department_id', 'type', 'mandatory', 'language', 'active', 'type_question', 'created_by', 'created_at')
                ->where('company_department_id', Crypt::decrypt($id))
                ->where('active', 1)
                ->where('deleted_at', null)
                ->get();

            foreach ($robot['result'] as $key) {
                if ($key->type_question == null) {
                    DB::table('company_depart_settings_question')->insertGetId([
                        'company_department_id' => $key->company_department_id,
                        'question' => $key->quest,
                        'type' => $key->type,
                        'mandatory' => $key->mandatory,
                        'active' => $key->active,
                        'type_question' => 'CHAT',
                        'created_by' => $key->created_by,
                        'created_at' => $key->created_at,
                    ]);

                    DB::table('company_depart_settings_question')
                    ->where('id', $key->id)
                    ->update([
                        'type_question' => 'TICKET',
                    ]);
                }
            }

            //CÓDIGO PARA SANEAR AS QUESTÕES COMO type_question NULL
            if(request('type') == 'TICKET'){
                $robot['result'] = DB::table('company_depart_settings_question')
                ->select('id', 'question as quest', 'type', 'mandatory', 'language', 'active')
                ->where('company_department_id', Crypt::decrypt($id))
                ->where('deleted_at', null)
                ->where('active', 1)
                ->where('type_question', 'TICKET')
                ->get();
            }else{
                $robot['result'] = DB::table('company_depart_settings_question')
                    ->select('id', 'question as quest', 'type', 'mandatory', 'language', 'active')
                    ->where('company_department_id', Crypt::decrypt($id))
                    ->where('deleted_at', null)
                    ->where('active', 1)
                    ->where('type_question', 'CHAT')
                    ->get();
            }

            foreach ($robot['result'] as $key) {
                $key->id = Crypt::encrypt($key->id);
            }

            $robot['success'] = true;
        } catch (\Exception $e) {
            Logger::reportException($e, [], ['department-controller', 'show-robot'], false);
            $robot['success'] = false;
        }

        $robot['master'] = ConfigsCompanyReleased::get(); // Crypt::encrypt(11);
        echo json_encode($robot);
    }
}
