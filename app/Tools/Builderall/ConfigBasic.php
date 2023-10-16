<?php

namespace App\Tools\Builderall;

use Exception;
use Illuminate\Support\Facades\DB;
use App\Models\CompanySettings;
use App\Models\CompanyDepartmentSettings;

class ConfigBasic
{
    /**
     * Create All Configs
     */
    public function createAllConfigs()
    {

    }

    /**
     * Configuração padrão do admin
     */
    static function createCompanyConfig($company_id, $base_url, $hash_code ){
        DB::table('company_settings')->insert([
            'company_id' => $company_id,
            'blocked_domain' => json_encode([]),
            'released_domain' => json_encode([]),
            'settings_chat' => '1', //NUMERO DE CHATS ABERTOS SIMULTANEAMENTE
            'settings_ticket' => '1', //NUMERO DE TICKET ABERTOS SIMULTANEAMENTE
            'general' => json_encode(CompanySettings::getDefaultGeneral($base_url, $hash_code)),
            'created_by' => auth()->user()->id,
        ]);
    }

    static function createDepartmentConfig($company_id){
        // CRIAR UM DEPARTAMENTO?
    }

    static function createUserGroupConfig($company_id){
        // CRIAR UM GRUPO?
    }

    static function setConfigDepartmentBasic($department_id, $tz, $language){
        DB::table('company_department_settings')->insert([
            'company_department_id' => $department_id,
            'created_by' => auth()->user()->id,
            'settings'   => json_encode(CompanyDepartmentSettings::getDefaultSettings($tz, $language))
        ]);
    }

    static function setConfigDepartmentQuestion($department_id, $language){

        $question = __('bs-what-is-your-problem-doubt');
        DB::table('company_depart_settings_question')->insert([
            'company_department_id' => $department_id,
            'question' => $question,
            'type' => 'TEXT',
            'mandatory' => 0,
            'active' => 1,
            'type_question' => 'CHAT',
            'created_by' => auth()->user()->id,
        ]);
    }

    static function setConfigGrouptBasic(){
        return '{"permissions":{
            "company":{"view":false,"insert":false,"edit":false,"delete":false},
            "department":{"view":false,"insert":false,"edit":false,"delete":false,
                "config":{"general":{"view":false,"insert":false,"edit":false,"delete":false},
                "management":{"view":false,"insert":false,"edit":false,"delete":false},
                "autoAnswer":{"view":false,"insert":false,"edit":false,"delete":false},
                "quantLimitation":{"view":false,"insert":false,"edit":false,"delete":false},
                "robot":{"view":false,"insert":false,"edit":false,"delete":false},
                "evalution":{"view":false,"insert":false,"edit":false,"delete":false},
                "chat":{"view":false,"insert":false,"edit":false,"delete":false},
                "ticket":{"view":false,"insert":false,"edit":false,"delete":false}}},
            "group":{"view":false,"insert":false,"edit":false,"delete":false},
            "agents":{"view":false,"insert":false,"edit":false,"delete":false},
            "chat":{"transform":true,"alllist":false,"admin":false,"open":true,"resolved":false,"close":true,"moved":true,"blocked":false,"delete":false,"queue_full_control":true},
            "ticket":{"transform":true,"alllist":false,"admin":false,"open":true,"resolved":true,"close":true,"moved":true,"blocked":false,"delete":false,"returnQueue":false,"reopenTicket":true,"create":true},
            "integration":{"view":false,"insert":false,"edit":false,"delete":false},
            "client":{"view":false,"insert":false,"edit":false,"delete":false},
            "monitoring":{"view":false,"insert":false,"edit":false,"delete":false},
            "analyze":{"view":false,"insert":false,"edit":false,"delete":false}
        }}';
    }

}
