<?php

namespace App\Http\Controllers;

use App\CompanyUserCompanyDepartment;
use App\Language;
use App\Models\Company_user;
use App\Tools\Builderall\Logger;
use App\Tools\Crypt;
use App\Tools\Gravatar;
use App\User;
use App\UserAuth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserAuthController extends Controller
{
    public function getAgentsByDepartment(Request $request){
        $result = Company_user::join('user_auth', 'user_auth.id', 'company_user.user_auth_id')
            ->join('company_user_company_department', 'company_user.id', 'company_user_company_department.company_user_id')
            ->select(
                'user_auth.id',
                'user_auth.name',
                'company_user_company_department.id as company_user_company_department_id'
            )
            ->where('company_user_company_department.is_active', 1)
            ->where('company_user.company_id', Crypt::decrypt(session('companyselected')['id']))
            ->where('company_user_company_department.company_department_id', intval(Crypt::decrypt($request->company_department_id)))
            ->where('user_auth.id', '!=' ,Auth::id())
            ->orderBy('user_auth.name')
            ->get();

        foreach($result as $row) {
            $row->id = Crypt::encrypt($row->id);
            $row->company_user_company_department_id = Crypt::encrypt($row->company_user_company_department_id);
        }

        if($result) {
            return response()->json([
                'agents' => $result,
                'status' => true
            ]);
        } else {
            return response()->json([
                'status' => false
            ]);
        }

    }

    public function getAllAgentsByDepartment(Request $request){
        $result = Company_user::join('user_auth', 'user_auth.id', 'company_user.user_auth_id')
            ->join('company_user_company_department', 'company_user.id', 'company_user_company_department.company_user_id')
            ->select(
                'user_auth.id',
                'user_auth.name',
                'company_user_company_department.id as company_user_company_department_id'
            )
            ->where('company_user_company_department.is_active', 1)
            ->where('company_user.company_id', Crypt::decrypt(session('companyselected')['id']))
            ->where('company_user_company_department.company_department_id', intval(Crypt::decrypt($request->company_department_id)))
            ->orderBy('user_auth.name')
            ->get();

        foreach($result as $row) {
            $row->id = Crypt::encrypt($row->id);
            $row->company_user_company_department_id = Crypt::encrypt($row->company_user_company_department_id);
        }

        if($result) {
            return response()->json([
                'agents' => $result,
                'status' => true
            ]);
        } else {
            return response()->json([
                'status' => false
            ]);
        }

    }

    public function changeStatus(Request $request) {
        $status = $request->status;

        DB::table('user_auth_status')->insertGetId([
            'user_auth_id' => auth()->user()->id,
            'status' => $status,
            'created_at' => \Carbon\Carbon::now()->toDateTimeString(),
        ]);

        if ($status === "online") {
            session(['status' => 'online']);
        } else if ($status === "busy") {
            session(['status' => 'busy']);
        } else if ($status === "appear_away") {
            session(['status' => 'appear_away']);
        }
        
        return response()->json(['status' => $status]);
    }

    public function gravatar(Request $request) {
        return Gravatar::getGravatar($request->email);
    }

    public function update(Request $request) {
        try {
            if($request['user_uuid'] == null){
                $email = $request['email'];
            }else{
                $email = Auth::user()->email;
            }

            $name                   = $request['name'];
            $subsidiary_id          = $request['subsidiary_id'];
            $current_password       = $request['current_password'];
            $new_password           = $request['new_password'];
            $selectConfigTelegram   = json_encode($request['selectConfigTelegram']);

            if(isset(session('companyselected')['company_user_id'])){
                DB::table('company_user')
                ->where('id', Crypt::decrypt(session('companyselected')['company_user_id']))
                ->update([
                    'config' => $selectConfigTelegram,
                ]);
            }
            
            if ($current_password == "") {
                UserAuth::find(Auth::id())->update([
                    "name"          => $name,
                    "email"         => $email,
                    "subsidiary_id" => $subsidiary_id,
                ]);
            } else {
                if (Hash::check($current_password, Auth::user()->password)) {
                    UserAuth::find(Auth::id())->update([
                        "name"          => $name,
                        "email"         => $email,
                        "subsidiary_id" => $subsidiary_id,
                        "password"      => bcrypt($new_password),
                    ]);
                } else {
                    return response()->json(array(
                        'code'      =>  203,
                        'message'   =>  "Invalid Password"
                    ), 203);
                }
            }
        } catch (\Exception $e) {
            echo $e;
            Logger::reportException($e, [], ['UserAuthController', 'update'], false);
            $group['success'] = false;
        }
    }

    public function getUserUuid() {
        return auth()->user();
    }

    public function acceptCookies(Request $request) {
        $id = $request->id;

        return UserAuth::find($id)->update([
                "cookies_accepted"  =>  '1',
            ]);

    }

    public function privacyPolicy(Request $request) {
        $locale = $request->locale;
        return Language::getRow($locale, true);
    }

    public function getConfigTelegram() {

        $result = DB::table('company_user')
        ->where('id', Crypt::decrypt(session('companyselected')['company_user_id']))
        ->where('user_auth_id', auth()->user()->id)
        ->select('config', 'telegram_chat_id')
        ->first();

        $result->telegram_chat_id = $result->telegram_chat_id == null ? null : Crypt::encrypt($result->telegram_chat_id);

        if($result->telegram_chat_id == null){
            $result->value = 'not_link';
        }else{
            $result->value = '';
        }

        $departments = DB::table('company_user_company_department')->join('company_department', 'company_user_company_department.company_department_id', 'company_department.id')
        ->join('company_user', 'company_user_company_department.company_user_id', 'company_user.id')
        ->select('company_department.id', 'company_department.name')
        ->where('company_user.user_auth_id', Auth::id())
        ->where('company_user.company_id', Crypt::decrypt(session('companyselected')['id']))
        ->where('company_user_company_department.is_active', 1)
        ->get();

        foreach ($departments as $key) {
            $key->id = Crypt::encrypt($key->id);
        }

        return json_encode([$result, $departments]);
    }

    public function getLanguage(Request $request) {
        try {
            $client_id = $request->client_id;

            return UserAuth::select('language')->where('id', Crypt::decrypt($client_id))->first();

        } catch (\Exception $e) {
			Logger::reportException($e, [], ['user-auth-controller', 'getLanguage'], false);
		}
    }

    public function setUserDarkMode(Request $request) {
        try {
            UserAuth::where('id', Auth::id())->update([
                'dark_mode' => $request->dark_mode
            ]);
        } catch (\Exception $e) {
            Logger::reportException($e, [], ['user-auth-controller', 'setUserDarkMode'], false);
        }
    }
    
    public function setUserFontSize(Request $request) {
        $response['success'] = false; 
        try {
            $current_config = UserAuth::select('config')->where('id', Auth::id())->first()->config;
            if (is_null($current_config)) {
                $update = UserAuth::where('id', Auth::id())->update([
                    'config' => json_encode(UserAuth::getDefaultConfig())
                ]);
            } else {
                $update = UserAuth::where('id', Auth::id())->update([
                    'config->fontSize' => $request->size
                ]);
            }

            if ($update) {
                $response['success'] = true; 
                $response['config'] = UserAuth::select('config')->where('id', Auth::id())->first()->config;
            }
        } catch (\Exception $e) {
            Logger::reportException($e, [], ['user-auth-controller', 'setUserFontSize'], false);
        }  
        return $response;
    }
}
