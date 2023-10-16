<?php

namespace App\Http\Controllers;

use App\Tools\Builderall\Logger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Tools\Crypt;
use Auth;
use Illuminate\Support\Str;

class SettingsController extends Controller
{
	public function index(Request $request){
		if($request->isMethod('post')){

			$setting['success'] = false;

			try {

				$result = DB::table('user')->select('user.id', 'user.email')
				->where('email', request('email'))
				->get();

				//verificar se o email já está em alguma empresa
				if ($result == '[]') {

					$id = DB::table('user')->insertGetId([
						'subsidiary_id' => auth()->user()->subsidiary_id,
						'language' => auth()->user()->language,
						'name' => request('name'),
						'email' => request('email'),
						'is_active' => 1,
						'hash_code' => Crypt::encrypt(request('email')),  
						'password' => bcrypt(request('password')),
						'created_by' => auth()->user()->id,
					]);

					DB::table('company_user')->insertGetId([
						'company_id' => Crypt::decrypt(session('companyselected')['id']),
						'user_id' => $id,
						'is_admin' => 0,
						'created_by' => auth()->user()->id,
					]);
					
					$setting['id'] =  Crypt::encrypt($id);
				}else{

					DB::table('company_user')->insertGetId([
						'company_id' => Crypt::decrypt(session('companyselected')['id']),
						'user_id' => $result[0]->id,
						'is_admin' => 0,
						'created_by' => auth()->user()->id,
					]);

					$setting['id'] = Crypt::encrypt($result[0]->id);
				}

				$setting['success'] = true;
				$setting['created'] = \Carbon\Carbon::now()->toDateTimeString();

			} catch (\Exception $e) {
				echo $e;
				Logger::reportException($e, [], ['settings-controller', 'index'], false);
				$setting['success'] = false;
			}

			echo json_encode($setting); 

		}else{

			return view('functions.admin.setting.setting');
		}
		
	}

	public function showUsers(){


 		$setting['success'] = false;

 		try {

			$setting['result'] = DB::table('user')
 			->leftjoin('company_user', 'company_user.user_id', 'user.id')
			->select('user.id', 'user.name', 'user.email', 'user.created_at', 'company_user.company_id', 'user.is_active')
			->where('user.created_by', auth()->user()->id)
			->where('company_user.company_id', Crypt::decrypt(session('companyselected')['id']))
			->get();

			foreach ($setting['result'] as $key) {
                $key->id = Crypt::encrypt($key->id);
                $key->company_id = Crypt::encrypt($key->company_id);
            }

			$setting['success'] = true;

		} catch (\Exception $e) {
			Logger::reportException($e, [], ['settings-controller', 'show-users'], false);
			$setting['success'] = false;
		}

		echo json_encode($setting); 

	}

	public function permissionsave(){

		$department['success'] = false;

		try {

			// $id = DB::table('permission_module_funcionallity')->insertGetId([
			// 	'company_id' => Crypt::decrypt(session('companyselected')['id']),
			// 	'company_user_id' => Crypt::decrypt(session('companyselected')['company_user_id']),
			// 	'name' => request('name'),
			// 	'description' => request('description'),
			// 	'module' => request('module'),
			// 	'has_robot' => 1,
			// 	'is_active' => 1,
			// 	'created_by' => auth()->user()->id,
			// ]);

			// DB::table('company_department_settings')->insert([
			// 	'company_department_id' => $id,
			// 	'created_by' => auth()->user()->id,
			// ]);

			// DB::table('company_user_company_department')->insert([
			// 	'company_user_id' => Crypt::decrypt(session('companyselected')['company_user_id']),
			// 	'company_department_id' => $id, 
			// ]);

			$department['success'] = true;
			$department['created'] = \Carbon\Carbon::now()->toDateTimeString();

		} catch (\Exception $e) {
			echo $e;
			Logger::reportException($e, [], ['settings-controller', 'permission-save'], false);
			$department['success'] = false;
		}

		return json_encode($department);
	}

	public function permissiongroupsave(){
			$department['success'] = false;

		try {

			$id = DB::table('permission_module')->insertGetId([
				'name' => request('name'),
				'description' => request('description'),
				'is_active' => 1,
				'created_by' => auth()->user()->id,
			]);

			$department['success'] = true;
			$department['created'] = \Carbon\Carbon::now()->toDateTimeString();

		} catch (\Exception $e) {
			echo $e;
			Logger::reportException($e, [], ['settings-controller', 'permission-group-save'], false);
			$department['success'] = false;
		}

		return json_encode($department);
	}

	public function showPermissionGroup(){

		$setting['success'] = false;

 		try {

			$setting['result'] = DB::table('permission_module')
			->select('permission_module.id', 'permission_module.name', 'permission_module.description','permission_module.is_active')
			->get();

			foreach ($setting['result'] as $key) {
                $key->id = Crypt::encrypt($key->id);
            }

			$setting['success'] = true;

		} catch (\Exception $e) {
			echo $e;
			Logger::reportException($e, [], ['settings-controller', 'show-permission-group'], false);
			$setting['success'] = false;
		}

		echo json_encode($setting); 
	}

	public function showPermission(){

	}

	public function create(){

	}

	public function store(){

	}

	public function edit(){

	}

	public function update(){

	}

	public function destroy(){

	}
}
