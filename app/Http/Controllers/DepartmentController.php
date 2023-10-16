<?php

namespace App\Http\Controllers;

use App\Chat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Tools\Crypt;
use App\Models\Company_department;
use App\Models\CompanyDepartmentSettings;
use App\Tools\Builderall\Logger;
use App\Tools\ConfigsCompanyReleased;
use Auth;
use App\User;
use Carbon\Carbon;
use App\Tools\Builderall\ConfigBasic;

class DepartmentController extends Controller
{
    public function index(Request $request)
    {
        if ($request->isMethod('post')) {

            $department['success'] = false;

            try {

                $id = DB::table('company_department')->insertGetId([
                    'company_id' => Crypt::decrypt(session('companyselected')['id']),
                    'company_user_id' => Crypt::decrypt(session('companyselected')['company_user_id']),
                    'name' => request('name'),
                    'description' => request('description'),
                    'module' => request('module'),
                    'has_robot' => 1,
                    'is_active' => 1,
                    'created_by' => auth()->user()->id,
                ]);

                $config = new ConfigBasic;
                $config->setConfigDepartmentBasic($id, $request->timezone, auth()->user()->language);

                //VINCULAR ADMIN AO DEPARTAMENTOS
                $auxDepartments = DB::table('company_user')
                ->join('company', 'company_user.company_id', 'company.id')
                ->join('user_auth', 'company_user.user_auth_id', 'user_auth.id')
                ->where('company.id', Crypt::decrypt(session('companyselected')['id']))
                ->select('company_user.id','company_user.is_admin', 'user_auth.name', )
                ->get();

                foreach ($auxDepartments as $key){
                    if($key->is_admin == 1){
                        DB::table('company_user_company_department')->insertGetId([
                            'company_user_id' => $key->id,
                            'company_department_id' => $id,
                            'is_active' => 1,
                        ]);
                    }
                }

                $department['success'] = true;
                $department['id'] = Crypt::encrypt($id);
                $department['created'] = \Carbon\Carbon::now()->toDateTimeString();
            } catch (\Exception $e) {
                echo $e;
                Logger::reportException($e, [], ['department-controller', 'index'], false);
                $department['success'] = false;
            }

            return json_encode($department);
        } else {
            return view('functions.admin.department.department');
        }
    }

    public function showDepartment()
    {

        $department['success'] = false;

        try {

            $department['result'] = Company_department::select('company_department.id', 'company_department.name', 'company_department.description', 'company_department.module', 'company_department.company_user_id', 'company_department.is_active')
                ->where('company_department.company_id', Crypt::decrypt(session('companyselected')['id']))
                ->get();

            foreach ($department['result'] as $key) {
                $key->id = Crypt::encrypt($key->id);
                $key->company_user_id = Crypt::encrypt($key->company_user_id);
            }

            $department['success'] = true;
        } catch (\Exception $e) {
            echo $e;
            Logger::reportException($e, [], ['department-controller', 'show'], false);
            $department['success'] = false;
        }

        return json_encode($department);
    }

    public function getMyDepartments()
    {
        $department['success'] = false;

        try {

            if (session('restriction')[0]->ticket_admin || session('is_admin')) {
                $department['result'] = Company_department::select(
                    'company_department.id',
                    'company_department.name',
                    'company_department.description',
                    'company_department.module',
                    'company_department.company_user_id',
                    'company_department.is_active'
                )
                    ->where('company_department.company_id', Crypt::decrypt(session('companyselected')['id']))
                    ->get();
            } else {
                $department['result'] = Company_department::join('company_user_company_department', 'company_department.id', 'company_user_company_department.company_department_id')
                    ->select(
                        'company_department.id',
                        'company_department.name',
                        'company_department.description',
                        'company_department.module',
                        'company_department.company_user_id',
                        'company_department.is_active'
                    )
                    ->where('company_department.company_id', Crypt::decrypt(session('companyselected')['id']))
                    ->where('company_user_company_department.company_user_id', Crypt::decrypt(session('companyselected')['company_user_id']))
                    ->where('company_user_company_department.is_active', 1)
                    ->get();
            }

            foreach ($department['result'] as $key) {
                $key->id = Crypt::encrypt($key->id);
                $key->company_user_id = Crypt::encrypt($key->company_user_id);
            }

            $department['success'] = true;
        } catch (\Exception $e) {
            echo $e;
            Logger::reportException($e, [], ['department-controller', 'show'], false);
            $department['success'] = false;
        }

        return json_encode($department);
    }

    public function showSettings(Request $request)
    {

        $id = Crypt::decrypt($request->id);
        $tz = Crypt::decrypt($request->timezone);
        $settings['success'] = false;

        try {

            $settings['result'] = DB::table('company_department_settings')
                ->join('company_department', 'company_department_settings.company_department_id', 'company_department.id')
                ->select(
                    'company_department_settings.id as company_department_settings_id',
                    'company_department.id as company_department_id',
                    'settings'
                )
                ->where('company_department_id', $id)
                ->get();

            foreach ($settings['result'] as $key) {
                $key->company_department_settings_id = Crypt::encrypt($key->company_department_settings_id);
                $key->company_department_id = Crypt::encrypt($key->company_department_id);
            }

            if ($settings['result'][0]->settings == null) {
                $settings['result'][0]->settings = json_encode(CompanyDepartmentSettings::getDefaultSettings($tz, auth()->user()->language));
            }
            $settings['value'] = json_decode($settings['result'][0]->settings);
            $settings['company_department_settings_id'] = $settings['result'][0]->company_department_settings_id;
            $settings['company_department_id'] = $settings['result'][0]->company_department_id;
            $settings['success'] = true;


        } catch (\Exception $e) {
            echo $e;
            Logger::reportException($e, [], ['department-controller', 'show-settings'], false);
            $settings['success'] = false;
        }

        return json_encode($settings);
    }

    public function storeSettings()
    {

        $settings['success'] = false;

        try {

            $json = json_encode(request('settings'));

            $settings['result'] = DB::table('company_department_settings')
                ->where('id', Crypt::decrypt(request('id')))
                ->update([
                    'settings' => $json,
                    'updated_by' => auth()->user()->id,
                ]);

            DB::table('company_department')
                ->where('id', Crypt::decrypt(request('id_cd')))
                ->update([
                    'module' => request('settings')['general']['module']['code'],
                    'updated_by' => auth()->user()->id,
                ]);

            $settings['success'] = true;
        } catch (\Exception $e) {
            echo $e;
            Logger::reportException($e, [], ['department-controller', 'store-settings'], false);
            $settings['success'] = false;
        }

        return json_encode($settings);
    }

    public function deleteDepartment()
    {

        $department['success'] = false;
        try {

            //FAZER FUNÇÃO PARA NAO DESATIVAR DEPARTAMENTO COM CHATS NA FILA

            // $select = DB::table('ticket')
            // 	->where('company_department_id', Crypt::decrypt(request('id')))
            // 	->get();

            // if($select->count() > 0){
            // 	$department['success'] = false;
            // 	$department['value'] = 'not_disable_or_delete';
            // 	return json_encode($department);
            // }

            //FAZER FUNÇÃO PARA NAO DESATIVAR DEPARTAMENTO COM CHATS NA FILA


            if (request('type') == 'delete') {
                $department['result'] = DB::table('company_department')
                    ->where('id', Crypt::decrypt(request('id')))
                    ->update([
                        'deleted_at' => \Carbon\Carbon::now()->toDateTimeString(),
                        'deleted_by' => auth()->user()->id,
                        'updated_by' => auth()->user()->id,
                    ]);
            } else if (request('type') == 'disable') {
                $department['result'] = DB::table('company_department')
                    ->where('id', Crypt::decrypt(request('id')))
                    ->update([
                        'updated_by' => auth()->user()->id,
                        'is_active' => 0,
                    ]);
            } else if (request('type') == 'restore') {

                $department['result'] = DB::table('company_department')
                    ->where('id', Crypt::decrypt(request('id')))
                    ->update([
                        'updated_by' => auth()->user()->id,
                        'is_active' => 1,
                    ]);
            }

            $department['success'] = true;
        } catch (\Exception $e) {
            echo $e;
            Logger::reportException($e, [], ['department-controller', 'delete'], false);
            $department['success'] = false;
        }

        echo json_encode($department);
    }

    public function updateDepartment()
    {
        $department['success'] = false;

        try {

            $department['result'] = DB::table('company_department')
                ->where('id', Crypt::decrypt(request('id')))
                ->update([
                    'name' => request('name'),
                    'description' => request('description'),
                    'updated_by' => auth()->user()->id,
                ]);

            $department['success'] = true;
        } catch (\Exception $e) {
            echo $e;
            Logger::reportException($e, [], ['department-controller', 'update'], false);
            $department['success'] = false;
        }

        echo json_encode($department);
    }

    public function addUserDepartment()
    {
        $department['success'] = false;

        try {

            $department['result'] = DB::table('company_user_company_department')
                ->where('company_user_id', Crypt::decrypt(request('id_user')))
                ->where('company_department_id', Crypt::decrypt(request('id_department')))
                ->get();

            if ($department['result'] == '[]') {
                //INSERINDO REGISTRO CASO NÃO EXISTA AINDA.
                $department['result'] = DB::table('company_user_company_department')->insert([
                    'company_user_id' => Crypt::decrypt(request('id_user')),
                    'company_department_id' => Crypt::decrypt(request('id_department')),
                    'is_active' => 1,
                ]);
            } else {
                //ATUALIZANDO O REGISTRO CASO EXISTA.
                $department['result'] = DB::table('company_user_company_department')
                    ->where('company_user_id', Crypt::decrypt(request('id_user')))
                    ->where('company_department_id', Crypt::decrypt(request('id_department')))
                    ->update([
                        'is_active' => 1,
                    ]);
            }

            $department['success'] = true;
        } catch (\Exception $e) {
            echo $e;
            Logger::reportException($e, [], ['department-controller', 'add-user'], false);
            $department['success'] = false;
        }

        echo json_encode($department);


        //	MODELO ANTIGO!
        // $department['success'] = false;

        // try {

        // 	$department['result'] = DB::table('company_user_company_department')->insert([
        // 		'company_user_id' => Crypt::decrypt(request('id_user')),
        // 		'company_department_id' => Crypt::decrypt(request('id_department')),
        // 	]);

        // 	$department['success'] = true;
        // } catch (\Exception $e) {
        // 	echo $e;
        // 	Logger::reportException($e, [], ['department-controller', 'add-user'], false);
        // 	$department['success'] = false;
        // }

        // echo json_encode($department);
    }

    public function removeUserDepartment()
    {
        $department['success'] = false;

        try {
            $cucd = DB::table('company_user_company_department')
            ->where('company_user_id', Crypt::decrypt(request('id_user')))
            ->where('company_department_id', Crypt::decrypt(request('id_department')))
            ->first();

            $check_chat_ticket = DB::table('chat')
            ->where('comp_user_comp_depart_id_current', $cucd->id)
            ->where('status', 'IN_PROGRESS')
            ->select('id')
            ->count();

            if($check_chat_ticket == 0){
                $department['result'] = DB::table('company_user_company_department')
                    ->where('company_user_id', Crypt::decrypt(request('id_user')))
                    ->where('company_department_id', Crypt::decrypt(request('id_department')))
                    ->update([
                        'is_active' => 0,
                    ]);

                $department['success'] = true;
            }else{
                $department['error'] = 'bs-unable-to-remove-agents-with-chat-or-ticke';
                $department['success'] = false;
            }
         
        } catch (\Exception $e) {
            echo $e;
            Logger::reportException($e, [], ['department-controller', 'remove-user'], false);
            $department['success'] = false;
        }


        echo json_encode($department);
    }

    public function getAgentsDepartment($department)
    {
        $agentsUsers['success'] = false;

        try {
            $arrayName = array(
                'departments' => intval(Crypt::decrypt($department)),
                'departments2' => intval(Crypt::decrypt($department)),
                'company' => intval(Crypt::decrypt(session('companyselected')['id'])),
                'company2' => intval(Crypt::decrypt(session('companyselected')['id']))
            );

            $query = 'SELECT * FROM (
                SELECT * FROM (
                    SELECT
                        user_auth.id,
                        user_auth.name,
                        cd.id as company_department_id,
                        cu.id as company_user_id,
                        cuug.is_active,

                            (
                                SELECT GROUP_CONCAT(cd.name) FROM company_department AS cd
                                JOIN company_user_company_department AS cuug ON cd.id = cuug.company_department_id
                                JOIN company_user cu ON cu.id = cuug.company_user_id
                                WHERE cu.user_auth_id = user_auth.id AND cd.company_id = :company
                                AND cuug.is_active = 1 AND cd.is_active = 1  AND cd.deleted_at is null
                            ) departamentos
                            FROM user_auth
                            JOIN company_user AS cu ON cu.user_auth_id = user_auth.id
                            LEFT JOIN company_user_company_department AS cuug ON cu.id = cuug.company_user_id
                            AND cuug.company_department_id = :departments
                            LEFT JOIN company_department AS cd ON cd.id = cuug.company_department_id
                            AND cd.id = :departments2
                            WHERE cu.company_id = :company2
                            and cu.is_admin = 0
                            and cu.deleted_at is null
                            ORDER BY user_auth.id, cd.id DESC
                            LIMIT 99999999
                        ) sub
                ) sub
			GROUP BY sub.id';

            $agentsUsers['result'] = DB::select($query, $arrayName);

            foreach ($agentsUsers['result'] as $key) {
                $key->id = Crypt::encrypt($key->id);
                $key->company_user_id = Crypt::encrypt($key->company_user_id);
                if ($key->company_department_id != null) {
                    $key->company_department_id = Crypt::encrypt($key->company_department_id);
                }
                $key->departamentos = str_replace(',', ', ', $key->departamentos);
            }

            $count = 0;
            foreach ($agentsUsers['result'] as $key) {
                if ($key->is_active == 0) {
                    $count++;
                }
            }

            $agentsUsers['contador'] = true;
            $agentsUsers['success'] = true;
        } catch (\Exception $e) {
            echo $e;
            Logger::reportException($e, [], ['department-controller', 'get-agents'], false);
            $agentsUsers['success'] = false;
        }

        echo json_encode($agentsUsers);
    }

    public function getSubsidiary()
    {
        $subsidiary['success'] = false;

        try {

            $subsidiary['result'] = DB::table('subsidiary')
                ->select('id as key', 'name as label', 'iso_code as code')
                ->get();

            $subsidiary['success'] = true;
        } catch (\Exception $e) {
            echo $e;
            Logger::reportException($e, [], ['department-controller', 'subsidiary'], false);
            $subsidiary['success'] = false;
        }

        return json_encode($subsidiary);
    }

    public function getSchedule(){
        // Adicione "America/Sao_Paulo" à lista de fusos horários
        $timezones = \App\Tools\Timezone::all;

        $infoWorking = DB::table('company_user')
        ->where('id', Crypt::decrypt(request('company_user_id')))
        ->select('opening_hours', 'is_working', 'time_zone')
        ->first();

        $infoWorking->opening_hours = json_decode($infoWorking->opening_hours);

        return [json_encode($timezones), $infoWorking];
    }

    public function postSchedule()
    {
        $result['success'] = false;
        try {

            $result['value'] = DB::table('company_user')
            ->where('id', Crypt::decrypt(request('company_user_id')))
            ->update([
                'is_working' => request('workingStatus') ? 1 : 0,
            ]);

            $result['success'] = true;
        } catch (\Exception $e) {
            echo $e;
            Logger::reportException($e, [], ['department-controller', 'getSchedule'], false);
            $result['success'] = false;
        }

        return $result;
    }
    
    public function storeRobot()
    {
        $robot['success'] = false;

        try {

            $result = DB::table('department_robot')
                ->select('department_robot.id', 'department_robot.question as quest')
                ->where('department_robot.company_department_id', Crypt::decrypt(request('id')))
                ->get();

            if(count($result) > 1){
                $result = DB::table('department_robot')
                ->where('department_robot.company_department_id', Crypt::decrypt(request('id')))
                ->delete();

                DB::table('department_robot')->insertGetId([
                    'company_department_id' => Crypt::decrypt(request('id')),
                    'question' => json_encode(request('ds')),
                    'created_by' => auth()->user()->id,
                ]);
            }else if(count($result) < 1){
                DB::table('department_robot')->insertGetId([
                    'company_department_id' => Crypt::decrypt(request('id')),
                    'question' => json_encode(request('ds')),
                    'created_by' => auth()->user()->id,
                ]);
            }else{
                $request = DB::table('department_robot')
                ->where('id', $result[0]->id)
                ->update([
                    'question' => json_encode(request('ds')),
                    'updated_by' => auth()->user()->id,
                ]);
                $robot['value'] = $request;
            }

            $robot['success'] = true;
        } catch (\Exception $e) {
            echo $e;
            Logger::reportException($e, [], ['department-controller', 'store-robot'], false);
            $robot['success'] = false;
        }

        return json_encode($robot);
    }

    public function showRobot($department)
    {
        $robot['success'] = false;

        try {

            $robot['result'] = DB::table('department_robot')
                ->select('department_robot.id', 'department_robot.question as quest')
                ->where('department_robot.company_department_id', Crypt::decrypt($department))
                ->first();

            $robot['result']->id = Crypt::encrypt($robot['result']->id);
            $robot['result']->question = json_decode($robot['result']->quest);
            
            $robot['success'] = true;
        } catch (\Exception $e) {
            echo $e;
            Logger::reportException($e, [], ['department-controller', 'show-robot'], false);
            $robot['success'] = false;
        }

        echo json_encode($robot);
    }

    public function deleteRobot()
    {
        $robot['success'] = false;

        try {

            $robot['result'] = DB::table('department_robot')
                ->where('id', Crypt::decrypt(request('id_robot')))
                ->where('company_department_id', Crypt::decrypt(request('id_department')))
                ->delete();

            $robot['success'] = true;
        } catch (\Exception $e) {
            echo $e;
            Logger::reportException($e, [], ['department-controller', 'delete-robot'], false);
            $robot['success'] = false;
        }

        echo json_encode($robot);
    }

    public function setQuestions()
    {
        $department['success'] = false;

        try {

            $id = DB::table('company_depart_settings_question')->insertGetId([
                'company_department_id' => Crypt::decrypt(request('id_department')),
                'question' => request('quest'),
                'type' => request('type'),
                'mandatory' => intval(request('mandatory')),
                'language' => request('language'),
                'active' => 1,
                'type_question' => request('type_question'),
                'created_by' => auth()->user()->id,
            ]);

            $department['success'] = true;
            $department['id'] = Crypt::encrypt($id);
            $department['question'] = request('quest');
            $department['type'] = request('type');
            $department['mandatory'] = request('mandatory');
            $department['language'] = request('language');
        } catch (\Exception $e) {
            echo $e;
            Logger::reportException($e, [], ['department-controller', 'index'], false);
            $department['success'] = false;
        }

        return json_encode($department);
    }
    public function getQuestions($id)
    {
        $robot['success'] = false;

        try {
            //CÓDIGO PARA SANEAR AS QUESTÕES COMO type_question NULL
            $robot['result'] = DB::table('company_depart_settings_question')
                ->select('id', 'question as quest', 'company_department_id', 'type', 'mandatory', 'language', 'active', 'type_question', 'created_by', 'created_at')
                ->where('company_department_id', Crypt::decrypt($id))
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
            if (request('type') == 'TICKET') {
                $robot['result'] = DB::table('company_depart_settings_question')
                    ->select('id', 'question as quest', 'type', 'mandatory', 'language', 'active')
                    ->where('company_department_id', Crypt::decrypt($id))
                    ->where('deleted_at', null)
                    ->where('type_question', 'TICKET')
                    ->get();
            } else {
                $robot['result'] = DB::table('company_depart_settings_question')
                    ->select('id', 'question as quest', 'type', 'mandatory', 'language', 'active')
                    ->where('company_department_id', Crypt::decrypt($id))
                    ->where('deleted_at', null)
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

    public function deleteQuestions()
    {
        $robot['success'] = false;


        try {
            if (request('type') == 'delete') {

                try {

                    $robot['result'] = DB::table('company_depart_settings_question')
                        ->where('id', Crypt::decrypt(request('id')))
                        ->delete();
                    $robot['success'] = true;
                    return json_encode($robot);

                } catch (\Exception $e) {

                    $robot['result'] = DB::table('company_depart_settings_question')
                    ->where('id', Crypt::decrypt(request('id')))
                    ->update([
                        'deleted_at' => \Carbon\Carbon::now()->toDateTimeString(),
                        'updated_by' => auth()->user()->id,
                    ]);

                }
            } else if (request('type') == 'disable') {
                $robot['result'] = DB::table('company_depart_settings_question')
                    ->where('id', Crypt::decrypt(request('id')))
                    ->update([
                        'updated_by' => auth()->user()->id,
                        'active' => 0,
                    ]);
            } else if (request('type') == 'restore') {
                $robot['result'] = DB::table('company_depart_settings_question')
                    ->where('id', Crypt::decrypt(request('id')))
                    ->update([
                        'updated_by' => auth()->user()->id,
                        'active' => 1,
                    ]);
            }


            $robot['success'] = true;
        } catch (\Exception $e) {
            echo $e;
            Logger::reportException($e, [], ['department-controller', 'delete-robot'], false);
            $robot['success'] = false;
        }

        echo json_encode($robot);
    }

    public function updateQuestions()
    {

        $robot['success'] = false;

        try {

            $robot['result'] = DB::table('company_depart_settings_question')
                ->where('id', Crypt::decrypt(request('item')['id']))
                ->update([
                    'mandatory' => request('item')['mandatory'],
                    'updated_by' => auth()->user()->id,
                ]);

            $robot['success'] = true;
        } catch (\Exception $e) {
            echo $e;
            Logger::reportException($e, [], ['department-controller', 'delete-robot'], false);
            $robot['success'] = false;
        }

        echo json_encode($robot);
    }


    public function getDepartmentSettings(Request $request)
    {
        $id =   Crypt::decrypt($request['id']);

        $q = CompanyDepartmentSettings::select('settings')->where('company_department_id', $id)->get();

        return $q;
    }

    public function getDepartmentsByClientChats()
    {
        $result['success'] = false;
        try {
            $company_id = Crypt::decrypt(session('companyselected')['company_id']);
            $user_client_id = intval(Crypt::decrypt(session('companyselected')['user_client_id']));

            $departments = DB::table('chat')->select('company_department.id as id', 'company_department.name', 'company_department.type')
                ->join('user_client_chat', 'chat.id', 'user_client_chat.chat_id')
                ->join('company_department', 'chat.company_department_id', 'company_department.id')
                ->where('user_client_chat.user_client_id', $user_client_id)
                ->where('chat.company_id', $company_id)
                ->where('chat.status', '!=', 'CANCELED')
                ->whereNull('chat.ticket_id')
                ->whereNull('chat.deleted_at')
                ->groupBy('chat.company_department_id')
                ->get();

            foreach ($departments as $key) {
                $key->id = Crypt::encrypt($key->id);
                $key->value = $key->id;
                $key->text = $key->name;
            }

            $result['success'] = true;
            $result['departments'] = $departments;
        } catch (\Exception $e) {
            Logger::reportException($e, [], ['department-controller', 'getDepartmentsByClientChats'], false);
        }

        return $result;
    }

    public function getDepartmentsByClientTickets()
    {
        $result['success'] = false;
        try {
            $company_id = Crypt::decrypt(session('companyselected')['company_id']);
            $user_client_id = intval(Crypt::decrypt(session('companyselected')['user_client_id']));

            $departments = DB::table('chat')->select('company_department.id as id', 'company_department.name', 'company_department.type')
                ->join('user_client_chat', 'chat.id', 'user_client_chat.chat_id')
                ->join('company_department', 'chat.company_department_id', 'company_department.id')
                ->join('ticket', 'ticket.id', 'chat.ticket_id')
                ->where('chat.company_id', $company_id)
                ->whereNull('ticket.deleted_at')
                ->where('user_client_chat.user_client_id', $user_client_id)
                ->where('ticket.status', '!=', 'MERGED')
                ->where('ticket.status', '!=', 'CANCELED')
                ->groupBy('chat.company_department_id')
                ->get();

            foreach ($departments as $key) {
                $key->id = Crypt::encrypt($key->id);
                $key->value = $key->id;
                $key->text = $key->name;
            }

            $result['success'] = true;
            $result['departments'] = $departments;
        } catch (\Exception $e) {
            Logger::reportException($e, [], ['department-controller', 'getDepartmentsByClientChats'], false);
        }

        return $result;
    }

    public function getDepartmentsByCompany()
    {
        if (isset(session('companyselected')['user_client_id'])) {
            $company_id = Crypt::decrypt(session('companyselected')['company_id']);
            // se não, faço a lógica pro atendente
        } else {
            $company_id = Crypt::decrypt(session('companyselected')['id']);
        }
        //tem que recuperar a company da sessao do cliente... por enquanto esta estatico a company 11
        //Crypt::decrypt(session('companyselected')['id'])

        $department = Company_department::join('company_department_settings', 'company_department_settings.company_department_id', 'company_department.id')
            ->join('company', 'company_department.company_id', 'company.id')
            ->select('company_department.id', 'company_department.name', 'company_department_settings.settings', 'company_department.type')
            ->where('company.id', $company_id)
            //só traz departamentos ativos
            ->where('company_department.is_active', 1)
            ->orderBy('company_department.name')
            ->get();

        foreach ($department as $key) {
            $key->id = Crypt::encrypt($key->id);
            $key->value = $key->id;
            $key->text = $key->name;
        }

        return $department;
    }

    public function getDepartmentsOfAgent()
    {

        $department = Company_department::join('company_user_company_department', 'company_department.id', 'company_user_company_department.company_department_id')
            ->select('company_department.id', 'company_department.name', 'company_department.type')
            ->where('company_user_company_department.company_user_id', Crypt::decrypt(session('companyselected')['company_user_id']))
            ->where('company_department.is_active', 1)
            ->orderBy('company_department.name')
            ->get();

        foreach ($department as $key) {
            $key->id = Crypt::encrypt($key->id);
        }

        return $department;
    }

    public function registerNewDepartment(Request $request)
    {
        return view('functions.admin.department.register-department', [
            'base_url' => url('/'),
            'save_redirect_url' => url('/department/list-departments'),
            'go_back_redirect_url' => url('/department/list-departments'),
        ]);
    }

    public function listDepartments(Request $request)
    {
        return view('functions.admin.department.list-departments', [
            'usuario' => Auth::user()->toJson(),
            'timezones' => json_encode(\App\Tools\Timezone::all),
            'base_url' => url('/'),
            'go_back_url' => url('/department')
        ]);
    }

    public function isValidEmail()
    {
        $user = User::where('email', request('email'))
            ->first();

        if ($user) {
            $check['success'] = true;
            $check['value'] = $user->name;
            return $check;
        } else {
            $check['success'] = false;
            return $check;
        }
    }

    public function checkSuggestionCreateChat(Request $request) {
        try {

            $dep_id = Crypt::decrypt($request->department_id);

            $robot_finale = CompanyDepartmentSettings::where('company_department_id', $dep_id)
            ->whereRaw("JSON_EXTRACT(settings, '$.robot.robot_finale') = true")
            ->count();

            if ($robot_finale) {
                return 1;
            } else {
                return 0;
            }

        } catch (\Exception $e) {
            Logger::reportException($e, [], ['department-controller', 'checkSuggestionCreateChat'], false);
        }
    }

    public function isRobot(Request $request) {
        try {

            $dep_id = Crypt::decrypt($request->company_department_id);

            $robot = CompanyDepartmentSettings::where('company_department_id', $dep_id)
            ->whereRaw("JSON_EXTRACT(settings, '$.robot.is_active') = true")
            ->count();

            if ($robot) {
                return 1;
            } else {
                return 0;
            }

        } catch (\Exception $e) {
            Logger::reportException($e, [], ['department-controller', 'isRobot'], false);
        }
    }

    public function isOnlineAgent(Request $request) {
        try {

            $dep_id = Crypt::decrypt($request->company_department_id);

            $openChatOnline = CompanyDepartmentSettings::where('company_department_id', $dep_id)
            ->whereRaw("JSON_EXTRACT(settings, '$.chat.openChatOnline') = true")
            ->count();

            if ($openChatOnline) {
                return 1;
            } else {
                return 0;
            }

        } catch (\Exception $e) {
            Logger::reportException($e, [], ['department-controller', 'isOnlineAgent'], false);
        }
    }


    public function getDepartmentsDepartmentDashboard(Request $request)
    {
        try {
            $sql = "
			SELECT id AS value, name AS text, (deleted_at IS NOT NULL) AS deleted
			FROM company_department
			WHERE company_id = ?
			ORDER BY 3, 2;";

            $res = DB::select($sql, [
                Crypt::decrypt(request('company_id'))
            ]);

            if (!empty($res)) {
                foreach ($res as $key => $value) {
                    $value->value = Crypt::encrypt($value->value);
                }
            }

            $res['success'] = true;

            return json_encode($res);
        } catch (\Exception $e) {
            echo $e;
            Logger::reportException($e, [], ['department-controller', 'getDepartmentsDepartmentDashboard'], false);
            $res['success'] = false;
        }
    }

    public function getTotalsAndPercentagesDepartmentDashboard(Request $request)
    {
        try {

            $sql = "call pro_dashboard_department_totals_and_percentages(?, ?, ?)";

            $res = DB::select($sql, [
                Crypt::decrypt(request('company_id')),
                request('department_id') === 0 ? 0 : Crypt::decrypt(request('department_id')),
                0
            ]);

            if (empty($res)) {
                $res[0] = [
                    'count' => 0,
                    'perc_em_atendimento' => "0",
                    'perc_fechados' => "0",
                    'type' => "Chat"
                ];
                $res[1] = [
                    'count' => 0,
                    'perc_em_atendimento' => "0",
                    'perc_fechados' => "0",
                    'type' => "Ticket"
                ];
            }

            $res['success'] = true;

            return json_encode($res);
        } catch (\Exception $e) {
            echo $e;
            Logger::reportException($e, [], ['department-controller', 'getTotalsAndPercentagesDepartmentDashboard'], false);
            $res['success'] = false;
        }
    }

    public function getGeneralAvgForAttendancesDepartmentDashboard(Request $request)
    {
        try {

            $sql = "call pro_dashboard_department_general_average_for_attendances(?, ?, ?)";

            $res = DB::select($sql, [
                Crypt::decrypt(request('company_id')),
                request('department_id') === 0 ? 0 : Crypt::decrypt(request('department_id')),
                0
            ]);

            if (empty($res)) {
                $res[0] = [
                    'media_stars_atendent' => "0",
                    'media_stars_service' => "0"
                ];
            }

            $res['success'] = true;

            return json_encode($res);
        } catch (\Exception $e) {
            echo $e;
            Logger::reportException($e, [], ['department-controller', 'getGeneralAvgForAttendancesDepartmentDashboard'], false);
            $res['success'] = false;
        }
    }

    public function getBarChartDepartmentDashboard(Request $request)
    {
        try {

            $sql = "CALL pro_dashboard_department_tickets_chats_opened_x_closed(?, ?, ?, ?)";

            $res = DB::select($sql, [
                Crypt::decrypt(request('company_id')),
                request('department_id') === 0 ? 0 : Crypt::decrypt(request('department_id')),
                strtoupper(request('period')),
                0
            ]);

            if (request('period') == 'week') {
                $aux = [
                    'labels' => [],
                    'chats_opened' => [],
                    'chats_closed' => [],
                    'tickets_opened' => [],
                    'tickets_closed' => [],
                    'success' => false
                ];

                $first_day_of_week = date_sub(date_create(), date_interval_create_from_date_string(date('w', date_timestamp_get(date_create())) . " days"));
                $interations = intval(date('w', date_timestamp_get(date_create())));
                for ($i = 0; $i <= $interations; $i++) {
                    $aux['labels'][date_format($first_day_of_week, 'Ymd')] = [
                        'dia' => date_format($first_day_of_week, 'd'),
                        'mes' => date_format($first_day_of_week, 'm'),
                        'ano' => date_format($first_day_of_week, 'Y'),
                    ];
                    $aux['chats_opened'][date_format($first_day_of_week, 'Ymd')] = 0;
                    $aux['chats_closed'][date_format($first_day_of_week, 'Ymd')] = 0;
                    $aux['tickets_opened'][date_format($first_day_of_week, 'Ymd')] = 0;
                    $aux['tickets_closed'][date_format($first_day_of_week, 'Ymd')] = 0;

                    $first_day_of_week = date_add($first_day_of_week, date_interval_create_from_date_string("1 days"));
                }

                if (!empty($res)) {
                    for ($i = 0; $i < count($res); $i++) {
                        $dt = $res[$i]->year . ($res[$i]->month < 10 ? '0' . $res[$i]->month :  $res[$i]->month) . ($res[$i]->day < 10 ? '0' . $res[$i]->day : $res[$i]->day);
                        switch ($res[$i]->type) {
                            case 'Chats_Opened':
                                $aux['chats_opened'][$dt] = intval($res[$i]->count);
                                break;
                            case 'Chats_Closed':
                                $aux['chats_closed'][$dt] = intval($res[$i]->count);
                                break;
                            case 'Tickets_Opened':
                                $aux['tickets_opened'][$dt] = intval($res[$i]->count);
                                break;
                            case 'Tickets_Closed':
                                $aux['tickets_closed'][$dt] = intval($res[$i]->count);
                                break;
                        }
                    }
                }

                $aux['labels'] = array_values($aux['labels']);
                $aux['chats_opened'] = array_values($aux['chats_opened']);
                $aux['chats_closed'] = array_values($aux['chats_closed']);
                $aux['tickets_opened'] = array_values($aux['tickets_opened']);
                $aux['tickets_closed'] = array_values($aux['tickets_closed']);

                $res = $aux;
            } else if (request('period') == 'month') {
                $aux = [
                    'labels' => [1, 2, 3, 4],
                    'chats_opened' => [0, 0, 0, 0],
                    'chats_closed' => [0, 0, 0, 0],
                    'tickets_opened' => [0, 0, 0, 0],
                    'tickets_closed' => [0, 0, 0, 0],
                    'success' => false
                ];

                if (!empty($res)) {
                    for ($i = 0; $i < count($res); $i++) {
                        $index = intval($res[$i]->week) - 1;
                        switch ($res[$i]->type) {
                            case 'Chats_Opened':
                                $aux['chats_opened'][$index] = intval($res[$i]->count);
                                break;
                            case 'Chats_Closed':
                                $aux['chats_closed'][$index] = intval($res[$i]->count);
                                break;
                            case 'Tickets_Opened':
                                $aux['tickets_opened'][$index] = intval($res[$i]->count);
                                break;
                            case 'Tickets_Closed':
                                $aux['tickets_closed'][$index] = intval($res[$i]->count);
                                break;
                        }
                    }
                }
                $res = $aux;
            } else {
                $aux = [
                    'labels' => [],
                    'chats_opened' => [],
                    'chats_closed' => [],
                    'tickets_opened' => [],
                    'tickets_closed' => [],
                    'success' => false
                ];

                $current_month = intval(date_format(date_create(), 'n'));
                $current_year = date_format(date_create(), 'Y');
                for ($i = 1; $i <= $current_month; $i++) {
                    $mes = $i < 10 ? '0' . $i : $i;
                    $aux['labels'][$current_year . $mes] = [
                        'mes' => $mes,
                        'ano' => $current_year,
                    ];
                    $aux['chats_opened'][$current_year . $mes] = 0;
                    $aux['chats_closed'][$current_year . $mes] = 0;
                    $aux['tickets_opened'][$current_year . $mes] = 0;
                    $aux['tickets_closed'][$current_year . $mes] = 0;
                }

                if (!empty($res)) {
                    for ($i = 0; $i < count($res); $i++) {
                        $dt = $res[$i]->year . ($res[$i]->month < 10 ? '0' . $res[$i]->month : $res[$i]->month);
                        switch ($res[$i]->type) {
                            case 'Chats_Opened':
                                $aux['chats_opened'][$dt] = intval($res[$i]->count);
                                break;
                            case 'Chats_Closed':
                                $aux['chats_closed'][$dt] = intval($res[$i]->count);
                                break;
                            case 'Tickets_Opened':
                                $aux['tickets_opened'][$dt] = intval($res[$i]->count);
                                break;
                            case 'Tickets_Closed':
                                $aux['tickets_closed'][$dt] = intval($res[$i]->count);
                                break;
                        }
                    }
                }

                $aux['labels'] = array_values($aux['labels']);
                $aux['chats_opened'] = array_values($aux['chats_opened']);
                $aux['chats_closed'] = array_values($aux['chats_closed']);
                $aux['tickets_opened'] = array_values($aux['tickets_opened']);
                $aux['tickets_closed'] = array_values($aux['tickets_closed']);

                $res = $aux;
            }

            $res['success'] = true;

            return json_encode($res);
        } catch (\Exception $e) {
            echo $e;
            Logger::reportException($e, [], ['department-controller', 'getBarChartDepartmentDashboard'], false);
            $res['success'] = false;
        }
    }

    public function getLineChartDepartmentDashboard(Request $request)
    {
        // get-line-chart-department-dashboard
        try {

            $sql = "CALL pro_dashboard_department_tickets_chats_resolved_by_attendant(?, ?, ?, ?, ?)";

            $attendants_ids = request('attendant_ids');
            $decrypted_attendants = [];
            $attendants_len = count($attendants_ids);
            for ($i = 0; $i < $attendants_len; $i++) {
                $decrypted_attendants[] = Crypt::decrypt($attendants_ids[$i]);
            }
            // unset($attendants_ids);

            $res = DB::select($sql, [
                intval(Crypt::decrypt(request('company_id'))),
                request('department_id') === 0 ? 0 : intval(Crypt::decrypt(request('department_id'))),
                strtoupper(request('period')),
                implode(', ', $decrypted_attendants),
                0
            ]);

            if (request('period') == 'week') {
                $aux = [
                    'labels' => [],
                    'attendants' => [],
                    'success' => false
                ];

                $first_day_of_week = date_sub(date_create(), date_interval_create_from_date_string(date('w', date_timestamp_get(date_create())) . " days"));
                $interations = intval(date('w', date_timestamp_get(date_create())));

                for ($i = 0; $i <= $interations; $i++) {
                    $aux['labels'][date_format($first_day_of_week, 'Ymd')] = [
                        'dia' => date_format($first_day_of_week, 'd'),
                        'mes' => date_format($first_day_of_week, 'm'),
                        'ano' => date_format($first_day_of_week, 'Y'),
                    ];


                    for ($j = 0; $j < $attendants_len; $j++) {
                        $aux['attendants'][$attendants_ids[$j]][date_format($first_day_of_week, 'Ymd')] = 0;
                    }

                    $first_day_of_week = date_add($first_day_of_week, date_interval_create_from_date_string("1 days"));
                }

                if (!empty($res)) {
                    for ($i = 0; $i < count($res); $i++) {
                        $dt = $res[$i]->year . ($res[$i]->month < 10 ? '0' . $res[$i]->month :  $res[$i]->month) . ($res[$i]->day < 10 ? '0' . $res[$i]->day : $res[$i]->day);

                        $aux['attendants'][Crypt::encrypt($res[$i]->user_auth_id)][$dt] = intval($res[$i]->count);
                    }
                }

                $aux['labels'] = array_values($aux['labels']);
                for ($j = 0; $j < $attendants_len; $j++) {
                    $aux['attendants'][$attendants_ids[$j]] = array_values($aux['attendants'][$attendants_ids[$j]]);
                }

                $res = $aux;
            } else if (request('period') == 'month') {
                $aux = [
                    'labels' => [1, 2, 3, 4],
                    'attendants' => [],
                    'success' => false
                ];

                for ($j = 0; $j < $attendants_len; $j++) {
                    $aux['attendants'][$attendants_ids[$j]] = [0, 0, 0, 0];
                }

                if (!empty($res)) {
                    for ($i = 0; $i < count($res); $i++) {
                        $index = intval($res[$i]->week) - 1;
                        $aux['attendants'][Crypt::encrypt($res[$i]->user_auth_id)][$index] = intval($res[$i]->count);
                    }
                }

                $res = $aux;
            } else {
                $aux = [
                    'labels' => [],
                    'attendants' => [],
                    'success' => false
                ];

                $current_month = intval(date_format(date_create(), 'n'));
                $current_year = date_format(date_create(), 'Y');
                for ($i = 1; $i <= $current_month; $i++) {
                    $mes = $i < 10 ? '0' . $i : $i;
                    $aux['labels'][$current_year . $mes] = [
                        'mes' => $mes,
                        'ano' => $current_year,
                    ];

                    for ($j = 0; $j < $attendants_len; $j++) {
                        $aux['attendants'][$attendants_ids[$j]][$current_year . $mes] = 0;
                    }
                }

                if (!empty($res)) {
                    for ($i = 0; $i < count($res); $i++) {
                        $dt = $res[$i]->year . ($res[$i]->month < 10 ? '0' . $res[$i]->month : $res[$i]->month);
                        $aux['attendants'][Crypt::encrypt($res[$i]->user_auth_id)][$dt] = intval($res[$i]->count);
                    }
                }

                $aux['labels'] = array_values($aux['labels']);
                for ($j = 0; $j < $attendants_len; $j++) {
                    $aux['attendants'][$attendants_ids[$j]] = array_values($aux['attendants'][$attendants_ids[$j]]);
                }

                $res = $aux;
            }

            $res['success'] = true;

            return json_encode($res);
        } catch (\Exception $e) {
            echo $e;
            Logger::reportException($e, [], ['department-controller', 'getLineChartDepartmentDashboard'], false);
            $res['success'] = false;
        }
    }

    public function getAttendantsDepartmentDashboard(Request $request)
    {
        try {
            $sql = "
			SELECT cu.user_auth_id as value, u.name as text, (cu.deleted_at IS NOT NULL) AS deleted
			FROM user_auth u
			JOIN company_user cu ON u.id = cu.user_auth_id
			JOIN company_user_company_department cucd ON cu.id = cucd.company_user_id
			WHERE cu.is_admin = 0
			AND cu.company_id = ?
			";

            $params = [
                Crypt::decrypt(request('company_id'))
            ];

            if (request('department_id') !== 0) {
                $sql .= ' AND cucd.company_department_id = ?';
                array_push($params, Crypt::decrypt(request('department_id')));
            }

            $sql .= " GROUP BY 1 ORDER BY 3, 2";

            $res = DB::select($sql, $params);

            if (!empty($res)) {
                foreach ($res as $key => $value) {
                    $value->value = Crypt::encrypt($value->value);
                }
            }

            $res['success'] = true;

            return json_encode($res);
        } catch (\Exception $e) {
            echo $e;
            Logger::reportException($e, [], ['department-controller', 'getDepartmentsDepartmentDashboard'], false);
            $res['success'] = false;
        }
    }

    public function getTicketTimeCardsDepartmentDashboard(Request $request)
    {
        try {
            $sql = "CALL pro_dashboard_company_tickets_average_time_queue_and_service(?, ?, ?, ?);";

            $res = DB::select($sql, [
                Crypt::decrypt(request('company_id')),
                request('department_id') === 0 ? 0 : intval(Crypt::decrypt(request('department_id'))),
                strtoupper(request('period')),
                0
            ]);

            if (!empty($res) && isset($res[0])) {
                foreach ($res[0] as $key => &$val) {
                    $val = $val != null ? intval($val) : $val;
                }
            } else {
                $res[0] = [
                    'avg_queue_time' => 0,
                    'avg_service_time' => 0
                ];
            }

            $res['success'] = true;

            return json_encode($res);
        } catch (\Exception $e) {

            Logger::reportException($e, [], ['department-controller', 'getTicketTimeCardsDepartmentDashboard'], false);
            $res['success'] = false;
        }
    }

    public function getChatTimeCardsDepartmentDashboard(Request $request)
    {
        try {
            $sql = "CALL pro_dashboard_company_chats_average_time_queue_and_service(?, ?, ?, ?);";

            $res = DB::select($sql, [
                Crypt::decrypt(request('company_id')),
                request('department_id') === 0 ? 0 : intval(Crypt::decrypt(request('department_id'))),
                strtoupper(request('period')),
                0
            ]);

            if (!empty($res) && isset($res[0])) {
                foreach ($res[0] as $key => &$val) {
                    $val = $val != null ? intval($val) : $val;
                }
            } else {
                $res[0] = [
                    'avg_queue_time' => 0,
                    'avg_service_time' => 0
                ];
            }

            $res['success'] = true;

            return json_encode($res);
        } catch (\Exception $e) {

            Logger::reportException($e, [], ['department-controller', 'getChatTimeCardsDepartmentDashboard'], false);
            $res['success'] = false;
        }
    }
    /*
	public function getDepartmentsByCompany()
    {
		try {
			$result = DB::table('company_department')
				->select('id', 'name')
				->where('company_id', Crypt::decrypt(session('companyselected')['id']))
				->orderBy('name')
				->get();
			foreach ($result as $row){
				$row->id = Crypt::encrypt($row->id);
			}
			return response()->json($result);
		} catch (\Exception $e) {
            Logger::reportException($e, [], ['department-controller', 'getDepartments'], false);
        }
    }
	*/
    public function destroy()
    {
    }

    public function getOpenDepartments(Request $request)
    {

        if (isset(session('companyselected')['user_client_id'])) {
            $company_id = Crypt::decrypt(session('companyselected')['company_id']);
            $is_employee = false;
        } else {
            $company_id = Crypt::decrypt(session('companyselected')['id']);
            $is_employee = true;
        }

        $query = Company_department::join('company_department_settings', 'company_department_settings.company_department_id', 'company_department.id')
            ->join('company', 'company_department.company_id', 'company.id')
            ->select('company_department.id', 'company_department.name', 'company_department_settings.settings', 'company_department.type')
            ->where('company.id', $company_id)
            ->where('company_department.is_active', 1)
            ->orderBy('company_department.name');

        if ($is_employee) {
            if ($request->except_this !== 0) {
                $query->where('company_department.id', '!=', Crypt::decrypt($request->except_this));
            }
        } else {
            $dtype = session('dtype');
            if ($dtype) {
                if(session('show_only_dtype')){
                    $query = $query->whereIn("type", $dtype);
                } else {
                    array_push($dtype, '');
                    $query->whereIn("type", $dtype);
                }
            } else {
                $query->where('type', '=', '');
            }
        }

        $departments = $query->get();
        $country = $request->country; // país do sistema Ex: BR
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

            if ($dayOfTheWeek == 7) {
                $dayOfTheWeek = 0;
            }

            $settings_module = $settings->general->module->code; //pega o modulo

            // verifica se o departamento tem o modulo 'chat' ativo
            if ($settings_module == "ALL" || $settings_module == "CHAT") {

                $settings_country = $settings->general->userLang; //pega os paises do departamento
                if ($country == 'ALL') {
                    $found_contry = 1;
                } else {
                    $found_contry = 0;
                    foreach ($settings_country as $row) {
                        //verifica se esta liberado para todos os paises ou se o país do browser ou sistema está incluso.
                        //$row->code ou é "ALL" ou retorna o país assim neste formato = 'BR', 'US' etc...
                        if ($row->code == "ALL") {
                            $found_contry = 1;
                        } else if ($country == $row->code) {
                            $found_contry = 1;
                        }
                    }
                }

                if ($found_contry) {

                    if (property_exists($settings->chat, 'openChatOnline')) {
                        $openChatOnline = $settings->chat->openChatOnline;
                    } else {
                        $openChatOnline = false;
                    }

                    if (property_exists($settings, 'robot')) {
                        $hasActiveRobot = $settings->robot->is_active;
                    } else {
                        $hasActiveRobot = false;
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
                            $response[$index]['type'] = $department->type;
                            $response[$index]['openDateToUTC'] = $openDateToUTC;
                            $response[$index]['closeDateToUTC'] = $closeDateToUTC;
                            $response[$index]['openHour'] = $openHour;
                            $response[$index]['closeHour'] = $closeHour;
                            $response[$index]['openChatOnline'] = $openChatOnline;
                            $response[$index]['hasActiveRobot'] = $hasActiveRobot;
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

    public function getDepartmentsByTimezone(Request $request)
    {
        $id = Crypt::decrypt($request->id);

        try {
            //Busco o departamento.
            $department = CompanyDepartmentSettings::join('company_department', 'company_department.id', 'company_department_settings.company_department_id')
                ->select('company_department_settings.settings')
                ->where('company_department.id', $id)
                ->first();

            if ($department) {
                $settings = json_decode($department->settings); // configurações do departamento.
                $departmentTimezone = $settings->general->language; // Timezone do departamento.

                $dateTime = new \DateTime(
                    'now',
                    new \DateTimeZone($departmentTimezone)
                );

                $dayOfTheWeek = $dateTime->format('N');

                if ($dayOfTheWeek === (int) 7) {
                    $dayOfTheWeek = 0;
                    /*
        		- Se for domingo (7), eu faço a varíavel receber 0.
        		- Tenho que fazer isso porque no array do banco, a posição do horário de domingo é 0 e não 7.
        		- Desse modo consigo usar a variável como posição no array de horários pra setar as variáveis de horário de abertura e fechamento.
        		*/
                }

                // Tenho que adicionar o ":00" no final para deixar a data no formato d/m/Y H:i:s.
                $openHour = $settings->chat->openDepartment[$dayOfTheWeek]->am . ":00";
                $closeHour = $settings->chat->openDepartment[$dayOfTheWeek]->ap . ":00";

                if ($openHour === "00:00:00" && $closeHour === "00:00:00") {
                    // Se ambas as horas forem 00:00:00 significa que o departamento está fechado. Devido a isso, é retornado um erro.
                    return response()->json([
                        'message' => 'offline',
                        'e' => true,
                        'online' => 0,
                        'flag' => 1,
                    ]);
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
                        return response()->json([
                            'message' => 'online',
                            'online' => 1,
                            'flag' => 2,
                            //'settings' => $settings,
                            //'departmentTimezone' => $departmentTimezone,
                            //'dayOfTheWeek' => $dayOfTheWeek,
                            //'openHour' => $openHour,
                            //'closeHour' => $closeHour,
                            //'openDate' => $openDate,
                            //'closeDate' => $closeDate,
                            //'openDateToUTC' => $openDateToUTC,
                            //'closeDateToUTC' => $closeDateToUTC,
                            //'serverDate' => $serverDate,
                            //'serverDateToUTC' => $serverDateToUTC,
                        ]);
                    } else {
                        // Se a data do server não estiver entre a data de abertura e data de fechamento, é retornado um erro.
                        return response()->json([
                            'message' => 'offline',
                            'e' => true,
                            'online' => 0,
                            'flag' => 3,
                        ]);
                    }
                }
            } else {
                // Se o departamento com type checkout não for encontrado, é retornado um erro.
                return response()->json([
                    'message' => 'offline',
                    'e' => true,
                    'online' => 0,
                    'flag' => 4,
                ]);
            }
        } catch (\Exception $e) {
            Logger::reportException($e, [], ['department-controller', 'getDepartmentsByTimezone'], false);
        }
    }
}
