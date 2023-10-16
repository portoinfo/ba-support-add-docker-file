<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Tools\Crypt;
use App\Models\User_group;
use App\Tools\Builderall\Logger;
use App\Tools\Builderall\ConfigBasic;

class GroupController extends Controller
{
	public function index(Request $request){
		if($request->isMethod('post')){

			$group['success'] = false;

			try {

				$config = new ConfigBasic;
			    $settingsBasic = $config->setConfigGrouptBasic();

				$id = DB::table('user_group')->insertGetId([
					'company_id' => Crypt::decrypt(session('companyselected')['id']),
					'name' => request('name'),
					'description' => request('description'),
					'settings' => $settingsBasic,
					'is_active' => 1,
					'created_by' => auth()->user()->id,
				]);
				
				$group['settings'] = $settingsBasic;
				$group['id'] = Crypt::encrypt($id);	
				$group['success'] = true;
				$group['created'] = \Carbon\Carbon::now()->toDateTimeString();

			} catch (\Exception $e) {
				echo $e;
				Logger::reportException($e, [], ['group-controller', 'index'], false);
				$group['success'] = false;
			}

			return json_encode($group); 

		}else{
			return view('functions.admin.group.group');
		}
 	}

 	public function showGroup(){

		$group['success'] = false;

		try {

			$group['result'] = User_group::select('user_group.id', 'user_group.name', 'user_group.description', 'user_group.is_active', 'user_group.settings')
			->where('user_group.company_id', Crypt::decrypt(session('companyselected')['id']))
			->get();



			foreach ($group['result'] as $key) {
				$key->id = Crypt::encrypt($key->id);
				
				if($key->settings == '{"permissions":[]}'){
					$key->settings = null;
				}else{
					$key->settings = json_decode($key->settings);	
				}
			}

			$group['success'] = true;

		} catch (\Exception $e) {
			echo $e;
			Logger::reportException($e, [], ['group-controller', 'show'], false);
			$group['success'] = false;
		}

		return json_encode($group);
	 }
	 
	public function showGroupById($id){

		$group['success'] = false;

		try {

			$group['result'] = User_group::select('user_group.id', 'user_group.name', 'user_group.description', 'user_group.is_active', 'user_group.settings')
			->where('user_group.company_id', intval(Crypt::decrypt(session('companyselected')['id'])))
			->where('id', Crypt::decrypt($id))
			->get();

			foreach ($group['result'] as $key) {
				$key->id = Crypt::encrypt($key->id);
				$key->settings = json_decode($key->settings);
			}

			$group['success'] = true;

		} catch (\Exception $e) {
			echo $e;
			Logger::reportException($e, [], ['group-controller', 'show-group-by-id'], false);
			$group['success'] = false;
		}

		return json_encode($group);
 	}
 	
 	public function saveGroupPermission(){

		$permission['success'] = false;

		try {

			$id = DB::table('permission_module')->insertGetId([
				'name' => request('name'),
				'description' => request('description'),
				'is_active' => 1,
				'created_by' => auth()->user()->id,
			]);

			$permission['success'] = true;
			$permission['created'] = \Carbon\Carbon::now()->toDateTimeString();

		} catch (\Exception $e) {
			echo $e;
			Logger::reportException($e, [], ['group-controller', 'save-group-permission'], false);
			$permission['success'] = false;
		}

		return json_encode($permission); 

 	}
 	
 	public function storeGroupUser(){
 		$group['success'] = false;

		try {

			$id = DB::table('company_user_user_group')->insertGetId([
				'company_user_id' => Crypt::decrypt(request('company_user_id')),
				'user_group_id' => Crypt::decrypt(request('group')),
			]);

			$group['success'] = true;
			$group['id'] = Crypt::encrypt($id);
			$group['created'] = \Carbon\Carbon::now()->toDateTimeString();

		} catch (\Exception $e) {
			echo $e;
			Logger::reportException($e, [], ['group-controller', 'store-group-user'], false);
			$group['success'] = false;
		}

		return json_encode($group); 
 	}

 	public function showGroupUserAgents($id, $company_id){

 		$group['success'] = false;

		try {

			$group['result'] = DB::table('company_user_user_group')
			->leftjoin('company_user', 'company_user_user_group.company_user_id', 'company_user.id')
			->leftjoin('user_group', 'company_user_user_group.user_group_id', 'user_group.id')
			->select('user_group.id','user_group.name', 'user_group.description')
			->where('company_user.id', Crypt::decrypt($company_id))
			->where('company_user.user_auth_id', Crypt::decrypt($id))
			->get();

			foreach ($group['result'] as $key) {
				$key->id = Crypt::encrypt($key->id);
			}

			$group['success'] = true;

		} catch (\Exception $e) {
			echo $e;
			Logger::reportException($e, [], ['group-controller', 'show-group-user-agents'], false);
			$group['success'] = false;
		}

		return json_encode($group);
 	}


 	public function showGroupUser($groups){

 		$group['success'] = false;

		try {
			$arrayName = array(
				'groups' => intval(Crypt::decrypt($groups)),
				'company' => intval(Crypt::decrypt(session('companyselected')['id'])),
				'company2' => intval(Crypt::decrypt(session('companyselected')['id']))
			);

			$query = "SELECT * FROM (
						SELECT * FROM (
							SELECT 	
								user_auth.id, 
								user_auth.name, 
								user_group.id as user_group_id, 
								company_user.id as company_user_id,
								(SELECT GROUP_CONCAT(user_group.name) FROM user_group
									JOIN company_user_user_group ON user_group.id = company_user_user_group.user_group_id
									JOIN company_user ON company_user.id = company_user_user_group.company_user_id
									WHERE company_user.user_auth_id = user_auth.id AND company_user.company_id = :company 
									AND user_group.is_active = 1 AND user_group.deleted_at is null) grupos
							FROM user_auth 
							JOIN company_user as company_user ON company_user.user_auth_id = user_auth.id
							LEFT JOIN company_user_user_group AS cuug ON company_user.id = cuug.company_user_id
							LEFT JOIN user_group AS user_group ON user_group.id = cuug.user_group_id
							AND user_group.id = :groups
							WHERE company_user.company_id = :company2
							AND company_user.is_admin = 0
							and company_user.deleted_at is null
							ORDER BY user_auth.id, user_group.id DESC
							LIMIT 99999999
						) sub 
					) sub 
					GROUP BY sub.id";

			$group['result'] = DB::select($query, $arrayName);

			$count = 0;			
			foreach ($group['result'] as $key) {
				$key->id = Crypt::encrypt($key->id);
				$key->company_user_id = Crypt::encrypt($key->company_user_id);
				if($key->user_group_id != null){
					$count++;
					$key->user_group_id = Crypt::encrypt($key->user_group_id);
				}
				$key->grupos = str_replace(',', ', ', $key->grupos);
			}

			$group['contador'] = $count;
			$group['success'] = true;

		} catch (\Exception $e) {
			echo $e;
			Logger::reportException($e, [], ['group-controller', 'show-group-user'], false);
			$group['success'] = false;
		}

		return json_encode($group);
 	}

 	public function destroyGroupUser(){
 		
 		$group['success'] = false;

 		try {

			$settings['result'] = DB::table('company_user_user_group')
			->where('user_group_id', Crypt::decrypt(request('user_group_id')) )
			->where('company_user_id', Crypt::decrypt(request('company_user_id')) )
			->delete();

			$group['success'] = true;

		} catch (\Exception $e) {
			echo $e;
			Logger::reportException($e, [], ['group-controller', 'destroy-group-user'], false);
			$group['success'] = false;
		}

		echo json_encode($group); 
 	}

 	public function destroyGroup(){

		$group['success'] = false;

 		try {		

			if(request('type') == 'delete'){
				$group['result'] = DB::table('user_group')
				->where('id', Crypt::decrypt(request('id')))
				->update([
					'deleted_at' => \Carbon\Carbon::now()->toDateTimeString(),
					'deleted_by' => auth()->user()->id,
					'updated_by' => auth()->user()->id,
				]);
			}else if(request('type') == 'disable'){
				$group['result'] = DB::table('user_group')
				->where('id', Crypt::decrypt(request('id')))
				->update([
					'updated_by' => auth()->user()->id,
					'is_active' => 0,
				]);
			}else if(request('type') == 'restore'){
				$group['result'] = DB::table('user_group')
				->where('id', Crypt::decrypt(request('id')))
				->update([
					'updated_by' => auth()->user()->id,
					'is_active' => 1,
				]);	
			}

			$group['success'] = true;

		} catch (\Exception $e) {
			echo $e;
			Logger::reportException($e, [], ['group-controller', 'destroy'], false);
			$group['success'] = false;
		}

		echo json_encode($group);
 	}

 	public function updateGroup(){
		$group['success'] = false;

 		try {

			$group['result'] = DB::table('user_group')
				->where('id', Crypt::decrypt(request('id')))
				->update([
					'name' => request('name'),
					'description' => request('description'),
					'updated_by' => auth()->user()->id,
				]);

			$group['success'] = true;

		} catch (\Exception $e) {
			echo $e;
			Logger::reportException($e, [], ['group-controller', 'update'], false);
			$group['success'] = false;
		}

		echo json_encode($group);
 		
 	}

 	public function addUserGroup(){
		$group['success'] = false;

		try {

			$id = DB::table('company_user_user_group')->insertGetId([
				'company_user_id' => Crypt::decrypt(request('id_company_user')),
				'user_group_id' => Crypt::decrypt(request('id_group')),
			]);

			$group['success'] = true;
			$group['id'] = Crypt::encrypt($id);
			$group['created'] = \Carbon\Carbon::now()->toDateTimeString();

		} catch (\Exception $e) {
			echo $e;
			Logger::reportException($e, [], ['group-controller', 'add-user-group'], false);
			$group['success'] = false;
		}

		return json_encode($group); 
 	}

 	public function removeUserGroup(){
 		$group['success'] = false;

 		try {	

			$settings['result'] = DB::table('company_user_user_group')
			->where('company_user_id', Crypt::decrypt(request('id_company_user')) )
			->where('user_group_id', Crypt::decrypt(request('id_group')) )
			->delete();

			$group['success'] = true;

		} catch (\Exception $e) {
			echo $e;
			Logger::reportException($e, [], ['group-controller', 'remove-user-group'], false);
			$group['success'] = false;
		}

		echo json_encode($group); 
 	}

 	public function savePermissionGroup(){

		$group['success'] = false;

		try {

			$aux['permissions'] = request('permission'); 
			
			$group['result'] = DB::table('user_group')
				->where('id', Crypt::decrypt(request('id_group')))
				->update([
					'settings' => json_encode($aux),
					'updated_by' => auth()->user()->id,
				]);

			$group['success'] = true;

		} catch (\Exception $e) {
			echo $e;
			Logger::reportException($e, [], ['group-controller', 'save-permission-group'], false);
			$group['success'] = false;
		}

		echo json_encode($group); 
 	}
}
