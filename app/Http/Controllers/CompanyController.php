<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\Company;
use App\Models\Company_user;
use App\Models\Company_contact;
use App\Models\CompanySettings;
use App\Tools\Builderall\Logger;
use App\Tools\Builderall\ConfigBasic;
use App\Tools\Crypt;
use Carbon\Carbon;
use App\Tools\ConfigsCompanyReleased;
use App\Tools\CompanyAllowedDomains;
use Illuminate\Support\Facades\Auth;
use App\Tools\BlockUsers;
use Exception;
use App\User;
use App\CompanyUserCompanyDepartment;
use App\Models\Company_department;
use App\Tools\Builderall\MailCentral;
use App\Tools\Tickets\Feedback;
use Builderall\Authenticator\BuilderallAuth;
use App\Tools\ClearEmail;
use DOMDocument;
use DOMXPath;
use InvalidArgumentException;
use Psy\Shell;

class CompanyController extends Controller
{
	public function index(){

		return view('functions.admin.company.company');
	}

	public function showCompany(){

		$company['success'] = false;

		try {

			$company['result'] = DB::table('company_user')
			->leftJoin('company', 'company_user.company_id', 'company.id')
			->select('company.id', 'company.name', 'company.description', 'company.address', 'company.logo',
			'company.created_at', 'company_user.id as company_user_id', 'company_user.is_admin', 'company.status', 'company.hash_code', 'company_user.telegram_chat_id')
			->where('company_user.user_auth_id', auth()->user()->id)
			->where('company.deleted_at', null)
			->where('company_user.deleted_at', null)
			->where('company_user.is_active', 1)
			->get();


			$user = User::select('can_create_company')->where('email', auth()->user()->email)
			->first();

			if($user->can_create_company == 0 && $company['result'] == '[]'){
				return redirect('logout');
			}else if($user->can_create_company == 1){
				// session(['is_admin' => '1']);
				// ADICIONADO PARA ATENDENTE COMUM NÃO RECEBER PERMISSÃO DE ADMIN
				if(session('companyselected') == null){
					session(['is_admin' => 0]);
				}else{
					session(['is_admin' => intval(session('companyselected')['is_admin'])]);
				}
			}

			// //REMOVER COMPANHIAS DESATIVADAS
			foreach($company['result'] as $key=>$value){
				if($company['result'][$key]->is_admin == 0){
					if($company['result'][$key]->status == 'INACTIVE'){
						unset($company['result'][$key]);
					}
				}
			}

			foreach ($company['result'] as $key) {
				$key->id = Crypt::encrypt($key->id);
				$key->company_user_id = Crypt::encrypt($key->company_user_id);
				$key->telegram_chat_id = $key->telegram_chat_id == null ? null : Crypt::encrypt($key->telegram_chat_id);
			}
			
			$company['success'] = true;

		} catch (\Exception $e) {
			echo $e;
			Logger::reportException($e, [], ['company-controller', 'show'], false);
			$company['success'] = false;
		}

		echo json_encode($company);
	}

	public function selectedCompany($id){

		$company['success'] = false;

		try {

			$company['result'] = DB::table('company')
			->select('company.id', 'company.name', 'company.description', 'company.address', 'company.logo', 'company.created_at')
			->where('id',  Crypt::decrypt($id))
			->get();

			foreach ($company['result'] as $key) {
				$key->id = Crypt::encrypt($key->id);
			}

			$company['success'] = true;

		} catch (\Exception $e) {

			Logger::reportException($e, [], ['company-controller', 'selected-company'], false);
			$company['success'] = false;
		}

		echo json_encode($company);
	}

	public function createCompany(){

		$company['success'] = false;

		try {
            $base_url = request('base_url');

            $hash_code = Crypt::encrypt(time() * rand());
            $api_token = Crypt::encrypt(time() + rand());

			$idCompany = DB::table('company')->insertGetId([
				'name' => request('name'),
				'description' => request('description'),
				// 'address' => request('address'),
				'logo' => request('logo')." ",
				'hash_code' => $hash_code,
				'api_token' => $api_token,
				'created_by' => auth()->user()->id,
			]);

			$company_user_id = DB::table('company_user')->insertGetId([
				'company_id' => $idCompany,
				'user_auth_id' => auth()->user()->id,
				'is_admin' => 1,
				'created_by' => auth()->user()->id,
			]);

			$config = new ConfigBasic;
			$config->createCompanyConfig($idCompany, $base_url, $hash_code);

			// SEM O APP É MELHOR. FICA SEM GAMBIARRA NO FRONT
			$translate = new Feedback();
			$department = $translate->t('bs-general-support');
			$group = $translate::t('bs-general');
			$descriptionGroup = $translate::t('bs-attendants-in-general');

			$idDepart = DB::table('company_department')->insertGetId([
				'company_id' => $idCompany,
				'company_user_id' => $company_user_id,
				'name' => $department,
				'description' => $department,
				'module' => 'ALL',
				'has_robot' => 1,
				'is_active' => 1,
				'created_by' => auth()->user()->id,
			]);

			$company_user_company_department_id = DB::table('company_user_company_department')->insertGetId([
				'company_user_id' => $company_user_id,
				'company_department_id' => $idDepart,
				'is_active' => 1
			]);

			$config->setConfigDepartmentBasic($idDepart, request('timezone'), auth()->user()->language);
			$config->setConfigDepartmentQuestion($idDepart, auth()->user()->language);

			$settingsBasic = $config->setConfigGrouptBasic();

			$idGroup = DB::table('user_group')->insertGetId([
				'company_id' => $idCompany,
				'name' => $group,
				'description' => $descriptionGroup,
				'settings' => $settingsBasic,
				'is_active' => 1,
				'created_by' => auth()->user()->id,
			]);

			CompanyAllowedDomains::addDefaultDomains($idCompany);
			$domains = request('domains');
			CompanyAllowedDomains::addDomainGroup($domains, $idCompany);

			// RETORNAR O SCRIPT

			$company['hash_code'] = $hash_code;
			$company['success'] = true;
			$company['id'] = Crypt::encrypt($idCompany);
			$company['company_user_id'] = Crypt::encrypt($company_user_id);
			$company['created'] = \Carbon\Carbon::now()->toDateTimeString();

		} catch (\Exception $e) {
			echo $e;
			Logger::reportException($e, [], ['company-controller', 'create'], false);
			$company['success'] = false;
		}

		echo json_encode($company);
	}

	public function storeContact(Request $request){

		$contact['success'] = false;

		try {

			$id = DB::table('company_contact')->insertGetId([
				'company_id' => Crypt::decrypt(request('itemselected')),
				'type' => request('type'),
				'description' => request('description'),
				'created_by' => auth()->user()->id,
			]);

			$contact['success'] = true;
			$contact['id'] = Crypt::encrypt($id);
			$contact['created'] = \Carbon\Carbon::now()->toDateTimeString();

		} catch (\Exception $e) {

			Logger::reportException($e, [], ['company-controller', 'store-contact'], false);
			$contact['success'] = false;
		}

		echo json_encode($contact);

	}

	public function showContact($id){
		$contact['success'] = false;

		try {

			$contact['result'] = Company_contact::select('id', 'type', 'description')
			->where('company_id', Crypt::decrypt($id))
				// ->where('deleted_at', null)
			->get();

			foreach ($contact['result'] as $key) {
				$key->id = Crypt::encrypt($key->id);
			}

			$contact['success'] = true;

		} catch (\Exception $e) {

			Logger::reportException($e, [], ['company-controller', 'show-contact'], false);
			$contact['success'] = false;
		}

		echo json_encode($contact);
	}

	public function deletContact(){

		$contact['success'] = false;

		try {

			$contact['result'] = DB::table('company_contact')
			->where('id', Crypt::decrypt(request('item')))
			->update([
				'deleted_at' => \Carbon\Carbon::now()->toDateTimeString(),
				'deleted_by' => auth()->user()->id,
				'updated_by' => auth()->user()->id,
			]);

			$contact['success'] = true;

		} catch (\Exception $e) {

			Logger::reportException($e, [], ['company-controller', 'delete-contact'], false);
			$contact['success'] = false;
		}

		echo json_encode($contact);
	}

	public function deleteCompany(){

		$contact['success'] = false;

		try {
			if (Hash::check(request('password'), auth()->user()->password) || BuilderallAuth::generateToken(auth()->user()->email, request('password'))){

				$company = DB::table('company')
				->join('company_user', 'company_user.company_id', 'company.id')
				->join('user_auth', 'company_user.user_auth_id', 'user_auth.id')
				->select('company.id', 'company_user.id as company_user_id')
				->where('company.id', Crypt::decrypt(request('id')))
				->where('user_auth.id', auth()->user()->id)
				->first();

				if(!is_null($company)){
					$contact['result'] = DB::table('company')
					->where('id', $company->id)
					->update([
						'deleted_at' => \Carbon\Carbon::now()->toDateTimeString(),
						'deleted_by' => auth()->user()->id,
						'updated_by' => auth()->user()->id,
					]);

					if(session('companyselected') == null){
						$contact['logout'] = false;
					}else if(Crypt::decrypt(session('companyselected')['id']) == $company->id){
						$contact['logout'] = true;
					}
				}else{
					$contact['success'] = false;
					$contact['error'] = 'password';
				}

				$contact['success'] = true;
			}else{
				$contact['success'] = false;
				$contact['error'] = 'password';
			}


		} catch (\Exception $e) {
			echo $e;
			Logger::reportException($e, [], ['company-controller', 'delete-company'], false);
			$contact['success'] = false;
		}

		echo json_encode($contact);
	}


	public function updateCompany(Request $request){
		$contact['success'] = false;

		try {

			$contact['result'] = DB::table('company')
			->where('id', Crypt::decrypt(request('id')))
			->update([
				'name' => request('name'),
				'description' => request('description'),
				// 'address' => request('address'),
				'logo' => request('logo'),
				'updated_by' => auth()->user()->id,
			]);

			$company = session('companyselected');
			$company['name'] = request('name');
			session(['companyselected' => $company]);

			$contact['success'] = true;

		} catch (\Exception $e) {

			Logger::reportException($e, [], ['company-controller', 'update-company'], false);

			$contact['success'] = false;
		}

		echo json_encode($contact);
	}

	public function domainreleased(){
		$contact['success'] = false;

		try {

			$domains = request('domainReleased');
			$csid = request('csid');

			$contact['result'] = DB::table('company_settings')
			->where('company_id', Crypt::decrypt(request('csid')))
			->update([
				'released_domain' => $domains,
				'updated_by' => auth()->user()->id,
			]);

			$contact['success'] = true;

		} catch (\Exception $e) {

			Logger::reportException($e, [], ['company-controller', 'domain-released'], false);

			$contact['success'] = false;
		}

		echo json_encode($contact);
	}

	public function domainssettings($cs){

		$contact['success'] = false;

		try {

			$contact['result'] = DB::table('company_settings')
			->select('id', 'general', 'company_id', 'blocked_domain', 'released_domain', 'settings_chat', 'settings_ticket')
			->where('company_id', Crypt::decrypt(request('cs')))
			->get();

			if($contact['result'] == '[]'){
				$id = DB::table('company_settings')->insertGetId([
					'company_id' => Crypt::decrypt(request('cs')) ,
					'blocked_domain' => json_encode([]),
					'released_domain' => json_encode([]),
					'settings_chat' => '0',
					'settings_ticket' => '0',
					'general' => json_encode(CompanySettings::getDefaultGeneral(null, null)),
					'created_by' => auth()->user()->id,
				]);

				$contact['result'] = DB::table('company_settings')
					->select('id', 'general', 'company_id', 'blocked_domain', 'released_domain', 'settings_chat', 'settings_ticket')
					->where('id', $id)
					->get();

			}

			foreach ($contact['result'] as $key) {
				$key->id = Crypt::encrypt($key->id);
				$key->company_id = Crypt::encrypt($key->company_id);
			}

			// $sender = DB::table('company')
			// ->select('name_sender', 'email_sender')
			// ->where('id', Crypt::decrypt(request('cs')))
			// ->first();

			$contact['released'] = json_decode($contact['result'][0]->released_domain);
			$contact['blocked'] = json_decode($contact['result'][0]->blocked_domain);
			$contact['settings_chat'] = json_decode($contact['result'][0]->settings_chat);
			$contact['settings_ticket'] = json_decode($contact['result'][0]->settings_ticket);
			$contact['general'] = !empty(json_decode($contact['result'][0]->general)) ? json_decode($contact['result'][0]->general) : CompanySettings::getDefaultGeneral(null, null);
			// $contact['sender'] = $sender;

			$contact['company'] = DB::table('company')
			->select('hash_code')
			->where('id', Crypt::decrypt(session('companyselected')['id']))
			->first();

			$contact['success'] = true;

		} catch (\Exception $e) {
			echo $e;
			Logger::reportException($e, [], ['company-controller', 'domain-settings'], false);

			$contact['success'] = false;
		}

		echo json_encode($contact);
	}

	public function domainreleasedDelete(){
		$contact['success'] = false;

		try {

			$domains = request('domainReleased');
			$csid = request('csid');

			$contact['result'] = DB::table('company_settings')
			->where('company_id', Crypt::decrypt(request('csid')))
			->update([
				'released_domain' => $domains,
				'updated_by' => auth()->user()->id,
			]);

			$contact['success'] = true;

		} catch (\Exception $e) {

			Logger::reportException($e, [], ['company-controller', 'domain-released-delete'], false);
			$contact['success'] = false;
		}

		echo json_encode($contact);
	}

	public function domainblocked(){
		$domain['success'] = false;

		try {

			$domains = request('domainBlocked');
			$csid = request('csid');

			$domain['result'] = DB::table('company_settings')
			->where('company_id', Crypt::decrypt(request('csid')))
			->update([
				'blocked_domain' => $domains,
				'updated_by' => auth()->user()->id,
			]);

			$domain['success'] = true;

		} catch (\Exception $e) {

			Logger::reportException($e, [], ['company-controller', 'domain-blocked'], false);
			$domain['success'] = false;
		}

		echo json_encode($domain);
	}

	public function domainblockedDelete(){
		$contact['success'] = false;

		try {

			$domains = request('domainBlocked');
			$csid = request('csid');

			$contact['result'] = DB::table('company_settings')
			->where('company_id', Crypt::decrypt(request('csid')))
			->update([
				'blocked_domain' => $domains,
				'updated_by' => auth()->user()->id,
			]);

			$contact['success'] = true;

		} catch (\Exception $e) {

			Logger::reportException($e, [], ['company-controller', 'domain-blocked-delete'], false);

			$contact['success'] = false;
		}

		echo json_encode($contact);
	}

	public function ChatTicket(){
		$contact['success'] = false;

		try {

			$settings_chat = request('settings_chat');
			$ticketSimCli =  request('ticketSimCli');
			
			$logout = [
				'client_logout' => request('logout'),
				'showChat' => request('showChat'),
				'showTicket' => request('showTicket'),
				'showAdmin' => request('showAdmin'),
				'editPerfilClient' => request('editPerfilClient'),
				'editPerfilAttendants' => request('editPerfilAttendants'),
				'acesso_anonymous' => request('acesso_anonymous'),
				'titleLogin' => request('titleLogin'),
				'subtitleLogin' => request('subtitleLogin'),
				'titlechatclient' => request('titlechatclient'),
				'titleticketclient' => request('titleticketclient'),
				'nameRobot' => request('nameRobot'),
				'selectedStatus' => request('selectedStatus'),
				'departmentsSelected' => request('departmentsSelected'),
				'departmentsSelectedUnique' => request('departmentsSelectedUnique'),
				'agentsSelected' => request('agentsSelected'),
				'modelDistribution' => request('modelDistribution'),
			];

			$setting = CompanySettings::where('company_id', Crypt::decrypt(request('csid')))->first();

			if (!$setting)
			{
				DB::table('company_settings')->insertGetId([
					'company_id' => Crypt::decrypt(request('csid')) ,
					'blocked_domain' => json_encode([]),
					'released_domain' => json_encode([]),
					'settings_chat' => $settings_chat,
					'settings_ticket' =>  (int) $ticketSimCli ,
					'general' => json_encode($logout),
					'created_by' => auth()->user()->id,
				]);
			}
			else
			{
				DB::table('company_settings')
				->where('company_id', Crypt::decrypt(request('csid')))
				->update([
					'settings_chat' => $settings_chat,
					'settings_ticket' =>  (int) $ticketSimCli ,
					'general' => json_encode($logout),
					'updated_by' => auth()->user()->id,
				]);
			}

			$contact['success'] = true;

		} catch (\Exception $e) {
			echo $e;
			Logger::reportException($e, [], ['company-controller', 'ChatTicket'], false);
			$contact['success'] = false;
		}

		echo json_encode($contact);
	}

	public function getCategory(){
		$category['success'] = false;
		
		try {
			
			$category['result'] = DB::table('category')
			->leftjoin('category as c2', 'c2.id', 'category.category_id')
			->select('category.id', 'category.category_id', 'category.description', 'c2.description as dad_description')
			->where('category.company_id', Crypt::decrypt(session('companyselected')['id']))
			->whereNull('category.deleted_at')
			->orderBy('category.description')
			->get();	

			//CODIGO PARA CONVERTER OS ANTIGOS NO NOVO FORMATO
			foreach ($category['result'] as $key) {
				if(json_decode($key->description) == null){
					$key->description = json_encode([
						(object) 
						["language" => 'pt_BR', "description" => $key->description],
						["language" => 'en_US', "description" => ''],
						["language" => 'fr_FR', "description" => ''],
						["language" => 'de_DE', "description" => ''],
						["language" => 'es_ES', "description" => ''],
						["language" => 'it_IT', "description" => ''],
						["language" => 'pt_PT', "description" => ''],
						["language" => 'he_IL', "description" => ''],
						["language" => 'hu_HU', "description" => ''],
						["language" => 'pl_PL', "description" => ''],
						["language" => 'cz_CZ', "description" => ''],
					]);

					$update['result'] = DB::table('category')
					->where('id', $key->id)
					->update([
						'description' => $key->description,
					]);

					$key->dad_description = json_encode([
						(object) 
						["language" => 'pt_BR', "description" => $key->dad_description],
						["language" => 'en_US', "description" => ''],
						["language" => 'fr_FR', "description" => ''],
						["language" => 'de_DE', "description" => ''],
						["language" => 'es_ES', "description" => ''],
						["language" => 'it_IT', "description" => ''],
						["language" => 'pt_PT', "description" => ''],
						["language" => 'he_IL', "description" => ''],
						["language" => 'hu_HU', "description" => ''],
						["language" => 'pl_PL', "description" => ''],
						["language" => 'cz_CZ', "description" => ''],
					]);
					
				}
			}

			foreach ($category['result']  as $key) {
				$key->active = false;
				$key->active2 = false;
			}
			// auth()->user()->language

			
			$category['result2'] = $category['result'];
			$category['success'] = true;

		} catch (\Exception $e) {
			echo $e;
			Logger::reportException($e, [], ['company-controller', 'getCategory'], false);
			$category['success'] = false;
		}
		
		return $category;
	}
	
	public function getCategoryId(){
		$category['success'] = false;
		
		try {

			if(request('cttype') == 'CHAT'){
				$chat_id = Crypt::decrypt((request('chat_id')));
			}else{
				$chat_id = request('chat_id');
			}

			$arrayName = array(
				'chat_id' => $chat_id,
			);

			$query = "WITH RECURSIVE categories 
						AS
						(
							SELECT cc.category_id, cc.id as chat_category_id, cat.description, cat.category_id as pai
							FROM chat_category cc
							JOIN chat c ON cc.chat_id = c.id
							JOIN category cat ON cc.category_id = cat.id
							WHERE c.id = :chat_id
							UNION
							SELECT cat.id as category_id, NULL as chat_category_id, cat.description, cat.category_id as pai
							FROM category cat
							JOIN categories f ON f.pai = cat.id
						)
						SELECT cat.*, (SELECT COUNT(*) FROM categories WHERE pai = cat.category_id) as count FROM categories cat;";

			$category['result'] = DB::select($query, $arrayName);

			$category['success'] = true;
			
		} catch (\Exception $e) {
			echo $e;
			Logger::reportException($e, [], ['company-controller', 'getCategoryId'], false);
			$category['success'] = false;
		}
		
		return $category;
	}

	public function createFaqRobotAll(){
		$faqrobot['success'] = false;
		try {
			$title = request('title');
			$description = request('description');
			$keywordAll = request('keywordAll');
			$itemselected = request('itemselected');

			if($itemselected == []){
				$company_faq_robot_tool_id = null;
				$language = request('language');
			}else{
				if($itemselected['company_faq_robot_tool_id'] == null){
					$company_faq_robot_tool_id = $itemselected['id'];
				}else{
					$company_faq_robot_tool_id = $itemselected['company_faq_robot_tool_id'];
				}
				$language = $itemselected['language'];
			}
			
			if($title == '' || $language == ''){
				$faqrobot['success'] = false;
			}else{

				$check = DB::table('company_faq_robot')
				->where('company_id', Crypt::decrypt(session('companyselected')['id']))
				->first();
				if($check == null){
					$id = DB::table('company_faq_robot')->insertGetId([
						'company_id'  => Crypt::decrypt(session('companyselected')['id']), 
						'is_active' => 0,
						'top_tools_show_count' => 0,
						'created_by' => auth()->user()->id,
					]);	
				}else{
					$id = $check->id; 
				}

				$id = DB::table('company_faq_robot_tools')->insertGetId([
					'company_faq_robot_id' => $id,
					'company_faq_robot_tool_id' => $company_faq_robot_tool_id,
					'title' => $title,
					'description' => $description,
					'keywords' => json_encode($keywordAll),
					'language' => $language,
					'click_count' => 0,
					'created_by' => auth()->user()->id,
					'created_at' => \Carbon\Carbon::now()->toDateTimeString(),
				]);
	
				$faqrobot['id'] = $id;
				$faqrobot['company_faq_robot_tool_id'] = $company_faq_robot_tool_id;
				$faqrobot['tool_status'] = 1;
				$faqrobot['success'] = true;
			}
		} catch (\Exception $e) {
			echo $e;
			Logger::reportException($e, [], ['company-controller', 'createFaqRobotAll'], false);
			$faqrobot['success'] = false;
		}
		
		return $faqrobot;
	}
	
	public function getFaqRobotAll(){
		$faqrobot['success'] = false;
		
		$language = request('language');
		try {

			$faqrobot['result'] = DB::table('company_faq_robot_tools as cfrt')
			->join('company_faq_robot', 'company_faq_robot.id', 'cfrt.company_faq_robot_id')
			->where('company_faq_robot.company_id', Crypt::decrypt(session('companyselected')['id']))
			->where('cfrt.language', $language)
			->whereNull('cfrt.deleted_at')
			// ->whereNull('cfrt.company_faq_robot_tool_id')
			->select('cfrt.id', 'cfrt.title', 'cfrt.description', 'cfrt.keywords as keywordAll', 
			'cfrt.tool_status', 'cfrt.language', 'cfrt.company_faq_robot_tool_id', 'cfrt.is_active as active')
			->get();

			$faqrobot['info'] = DB::table('company_faq_robot_to_train')
			->get();

			foreach ($faqrobot['result'] as $key) {
				$key->active = $key->active == 1 ? true : false;
				$key->keywordAll = json_decode($key->keywordAll) == null ? [] : json_decode($key->keywordAll);

				foreach ($faqrobot['info'] as $item) {
					if($key->id == $item->company_faq_robot_tool_id){
						$key->last_total = $item->last_total;
						$key->total = $item->total;
						$key->created_at = $item->created_at;
					}
				}
			}

			$faqrobot['result2'] = DB::table('company_faq_robot_info as cfri')
			->join('company_faq_robot', 'company_faq_robot.id', 'cfri.company_faq_robot_id')
			->where('company_faq_robot.company_id', Crypt::decrypt(session('companyselected')['id']))
			->select('cfri.initial_message', 'cfri.offline_tool_message', 'cfri.direct_message_to_attendant', 'cfri.confirm_keywords', 
			'cfri.change_tools_keywords', 'cfri.talk_to_attendant', 'cfri.name_robot')
			->where('cfri.language', $language)
			->first();
			
			$faqrobot['result3'] = DB::table('company_faq_robot')
			->select('is_active', 'top_tools_show_count as topNumberTools', 'top_subtools_show_count AS topSubNumberTools', 'language')
			->where('company_id',Crypt::decrypt(session('companyselected')['id']))
			->where('language', $language)
			->first();

			if($faqrobot['result3'] == null){
				
				$faqrobot['result3'] = DB::table('company_faq_robot')
				->select('id', 'is_active', 'top_tools_show_count as topNumberTools', 'top_subtools_show_count AS topSubNumberTools', 'language')
				->where('company_id',Crypt::decrypt(session('companyselected')['id']))
				->first();

				if($faqrobot['result3'] != null){
					if($faqrobot['result3']->language == null){
						DB::table('company_faq_robot')
						->where('id', $faqrobot['result3']->id)
						->update([
							'language' => $language,
						]);
						$faqrobot['result3']->language = $language;
					}else {
						DB::table('company_faq_robot')->insertGetId([
							'company_id'  => Crypt::decrypt(session('companyselected')['id']), 
							'is_active' => $faqrobot['result3']->is_active,
							'top_tools_show_count' => $faqrobot['result3']->topNumberTools,
							'created_by' => auth()->user()->id,
							'language' => $language,
						]);	

						$faqrobot['result3'] = DB::table('company_faq_robot')
						->select('id', 'is_active', 'top_tools_show_count as topNumberTools', 'top_subtools_show_count AS topSubNumberTools', 'language')
						->where('company_id',Crypt::decrypt(session('companyselected')['id']))
						->where('language', $language)
						->first();
					}
				}	
			}

			$faqrobot['success'] = true;

		} catch (\Exception $e) {
			echo $e;
			Logger::reportException($e, [], ['company-controller', 'getFaqRobotAll'], false);
			$faqrobot['success'] = false;
		}
		
		return $faqrobot;
	}
	
	public function setFaqRobotAll(Request $request){

		$content =  $request->content;

		$keywordAll = request('keywordAll');
		
        $content_translated = $request->content_translated;
		$id = request('itemselected')['id'];
        if (isset($request['images'])) {
            $images = $request['images'];
            foreach ($images as $row) {
                // Define the Base64 value you need to save as an image
                $b64 = explode(',', $row)[1];

                $image_name = Crypt::encrypt(uniqid(md5(uniqid() . microtime())));
                $data = base64_decode($b64);
                $dir = '..' . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'company' . DIRECTORY_SEPARATOR . Crypt::decrypt(session('companyselected')['id']) . DIRECTORY_SEPARATOR . 'faq' . DIRECTORY_SEPARATOR . $id . DIRECTORY_SEPARATOR;
                $filename = $image_name . '.png';

                // Caso já exista o diretório, apenas movo o arquivo pra dentro dele, se não, crio a pasta e movo o arquivo em seguida...
                if (is_dir($dir)) {
                    // $success = file_put_contents($dir.$filename, $data);
					
					// LOGICA PARA APAGAR IMAGENS CASO NÃO ESTEJAM MAIS SENDO USADAS NO CONTENT.
						// NÃO CONSIGO.
						// // The string with the URLs
						// $string = $content;

						// // Extract the URLs using preg_match_all
						// preg_match_all('/<img\s+.*?src=[\"\']?([^\"\' >]+)[\"\']?[^>]*>/i', $string, $matches);
						// $urls = $matches[1];

						// $urls = str_replace('public/faq/files/'.session('companyselected')['id'].'/'.Crypt::encrypt($id).'/', '', $urls);
						// // public/faq/files/UTJINmpiNG94U3dLYkhhaWJkWWNWUT09/WG1QK2hhQjA1dERmVTVJeDltMStFdz09/

						// // Get all the image files in the server folder
						// $images = glob($dir.'/*.*');
					
						// // Check each image file against the extracted URLs
						// foreach ($images as $image) {
						// 	$array = explode("/", $image);
						// 	if (!in_array($array[1], $urls)) {
						// 		unlink($image);
						// 	}
						// }
					// LOGICA PARA APAGAR IMAGENS CASO NÃO ESTEJAM MAIS SENDO USADAS NO CONTENT.
					$success = file_put_contents($dir.$filename, $data);
                } else {
                    mkdir($dir, 0755, true);
                    $success = file_put_contents($dir.$filename, $data);
                }
				

                if ($success) {
                    $content = str_replace($row, 'public/faq/files/'.session('companyselected')['id'].'/'.Crypt::encrypt($id).'/'.$filename, $content);
            	}
            }
        }

		$update['success'] = DB::table('company_faq_robot_tools')
		->where('id', $id)
		->update([
			'title' => request('title'),
			'description' => $content,
			'keywords' => json_encode($keywordAll),
		]);

		$update['content'] = $content;
		return $update;
	}

	public function updateStatusTool(){
		try {
			$id = request('id');
			$status = request('tool_status');
			
			$update['result'] = DB::table('company_faq_robot_tools')
			->where('id', $id)
			->update([
				'tool_status' => $status == 1 ? 0 : 1,
			]);
		} catch (\Exception $e) {
			Logger::reportException($e, [], ['company-controller', 'updateStatusTool'], false);
		}
		
		return json_encode($update);
	}
	
	public function updateRobot(){
		try {
			
			$check = DB::table('company_faq_robot')
			->where('company_id', Crypt::decrypt(session('companyselected')['id']))
			->where('language', request('defaultLanguage'))
			->select('id', 'is_finish')
			->first();

			if($check != null){
				if($check->is_finish == 1){
					return 'the_training_is_not_over';
				}else{
					DB::table('company_faq_robot')
					->where('id', $check->id)
					->update([
						'is_finish' => 1,
					]);
				}
			}

			$result = DB::table('company_faq_robot_tools')
			->join('company_faq_robot', 'company_faq_robot.id', 'company_faq_robot_tools.company_faq_robot_id')
			->where('company_faq_robot.company_id', Crypt::decrypt(session('companyselected')['id']))
			->whereNull('company_faq_robot_tools.company_faq_robot_tool_id')
			->select('company_faq_robot_tools.id', 'company_faq_robot_tools.title', 'company_faq_robot_tools.keywords', 'company_faq_robot_tools.language')
			->whereNull('company_faq_robot_tools.deleted_at')
			->where('company_faq_robot_tools.language', request('defaultLanguage'))
			->get();

			$result2 = DB::table('company_faq_robot_tools')
			->join('company_faq_robot', 'company_faq_robot.id', 'company_faq_robot_tools.company_faq_robot_id')
			->where('company_faq_robot.company_id', Crypt::decrypt(session('companyselected')['id']))
			->whereNotNull('company_faq_robot_tools.company_faq_robot_tool_id')
			->select('company_faq_robot_tools.id', 'company_faq_robot_tools.company_faq_robot_tool_id', 'company_faq_robot_tools.title', 'company_faq_robot_tools.keywords',)
			->whereNull('company_faq_robot_tools.deleted_at')
			->where('company_faq_robot_tools.language', request('defaultLanguage'))
			->get();

			foreach ($result as $key) {
				$string = $key->title;
				$key->title = strtolower(preg_replace('/[^a-zA-Z0-9]/', '',  iconv('UTF-8', 'ASCII//TRANSLIT', $string)));
				// $dir = '..' . DIRECTORY_SEPARATOR . 'chatbot' . 
				// DIRECTORY_SEPARATOR . 'Tools' . 
				// DIRECTORY_SEPARATOR . 'Company' . 
				// DIRECTORY_SEPARATOR . Crypt::decrypt(session('companyselected')['id']) . 
				// DIRECTORY_SEPARATOR . $key->title .
				// DIRECTORY_SEPARATOR . $key->language;
				
				// $dir = '../chatbot/Tools/Company/'.Crypt::decrypt(session('companyselected')['id']).'/'.'Alltools'.'/'.$key->language.'/';
				// $dir = '/srv/ba-support/chatbot/Tools/Company/'.Crypt::decrypt(session('companyselected')['id']).'/'.$key->title.'/'.$key->language.'/';
				$dir = '/srv/ba-support/chatbot/Tools/Company/'.Crypt::decrypt(session('companyselected')['id']).'/'.'Alltools'.'/'.$key->language.'/';
				// Tools/Company/"company_name"/"tool_name"/EN/
				if (!file_exists($dir)) {
					mkdir($dir, 0755, true);
					// echo 'Pasta criada com sucesso!';
				} else {
					// echo 'A pasta já existe!';
				}
			}

			$aux['intents'] = [];
			foreach ($result as $key) {
				foreach ($result2 as $key2) {
					// echo '<br>'.$key2->keywords;
					if($key2->keywords == null){
						$options = [$key2->title];
					}else{
						$titleConcat = json_decode($key2->keywords);
						$arra7 = [];
						foreach ($titleConcat as $tc) {
							array_push($arra7,str_replace(",", "", $tc));
						}
						$string = implode(', ', $arra7);
						array_unshift($arra7, $key2->title);
						$options = $arra7;
					}

					if($key->id == $key2->company_faq_robot_tool_id){
						array_push($aux['intents'], (object) [
							'tag' => (string) $key2->id,
							"patterns" => str_replace(['"', '“', '”'], "'", $options),
							"responses" => ['CHECKRESULTEXIST'.$key2->id],
						]);
					}
				}

				$string = $key->title;
				$key->title = strtolower(preg_replace('/[^a-zA-Z0-9]/', '',  iconv('UTF-8', 'ASCII//TRANSLIT', $string)));
				// $dir = '../chatbot/Tools/Company/'.Crypt::decrypt(session('companyselected')['id']).'/'.'Alltools'.'/'.$key->language.'/';
				// $dir = '/srv/ba-support/chatbot/Tools/Company/'.Crypt::decrypt(session('companyselected')['id']).'/'.$key->title.'/'.$key->language.'/';
				$dir = '/srv/ba-support/chatbot/Tools/Company/'.Crypt::decrypt(session('companyselected')['id']).'/'.'Alltools'.'/'.$key->language.'/';
			}

			$check = DB::table('company_faq_robot')
					->join('company_faq_robot_info', 'company_faq_robot.id', 'company_faq_robot_info.company_faq_robot_id')
					->where('company_id',Crypt::decrypt(session('companyselected')['id']))
					->where('is_active', 1)
					->where('company_faq_robot_info.language', auth()->user()->language)
					->select('talk_to_attendant')
					->first();

			if($check != null){
				$decodedData = json_decode($check->talk_to_attendant);
				if (!empty($decodedData) && is_object($decodedData)) {
					$array = json_decode($check->talk_to_attendant);
					array_push($aux['intents'], (object) [
						'tag' => 'talkattendant',
						"patterns" => $array,
						"responses" => ["CHECKRESULTEXISTAGENT"]
					]);
				}
			}

			array_unshift($aux['intents'], (object) [
				'tag' => 'alternative',
				"patterns" => [""],
				"responses" => ["CHECKRESULTUNDERSTAND"]
			]);

			$aux2 = (object) $aux;
			$json = json_encode($aux2, JSON_UNESCAPED_UNICODE);

			// Escrevendo o JSON em um arquivo
			$file = fopen($dir.'/intents.json', 'w');
			fwrite($file, $json);
			fclose($file);
			$var = 'vazio';
			$hostname = request('hostname');

			$file_path = $dir.'/intents.json';
			$file_size = filesize($file_path); // Obtém o tamanho do arquivo em bytes

			ignore_user_abort(true); // continua a execução mesmo se a conexão do cliente for interrompida
			set_time_limit(0); // desativa o limite de tempo de execução do script PHP
			$var = $this->setPython($hostname, 'Alltools', $key->language);
			echo $var;
			
		}catch (\Exception $e) {
			echo $e;
			$faqrobot['success'] = false;
			Logger::reportException($e, [], ['company-controller', 'updateRobot'], false);
		}

		$faqrobot['value'] = $var;
		$faqrobot['success'] = true;
		return $faqrobot;
	}

	public function setQuestionRobot(Request $request){
		$faqrobot['success'] = false;

		// if (isset($request['files'])) {
		// 	$nomeImagem = $request['files'][0]->getClientOriginalName();
		// 	$extensao = pathinfo($nomeImagem, PATHINFO_EXTENSION);

		// 	$dir = '..' . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'public' 
		// 	. DIRECTORY_SEPARATOR . 'company' . DIRECTORY_SEPARATOR . Crypt::decrypt(session('companyselected')['id']) 
		// 	. DIRECTORY_SEPARATOR . 'imageRobot' . DIRECTORY_SEPARATOR;
		// 	$filename = 'teste.'.$extensao;

		// 	$imagem = $request['files'];

		// 	// Caso já exista o diretório, apenas movo o arquivo pra dentro dele, se não, crio a pasta e movo o arquivo em seguida...
		// 	if (is_dir($dir)) {
		// 		$success = file_put_contents($dir.$filename, $imagem);
		// 	} else {
		// 		mkdir($dir, 0755, true);
		// 		$success = file_put_contents($dir.$filename, $imagem);
		// 	}
		// }
		
		// dd($success);

		try {
			$item = json_decode(request('item'));
			$language = request('language');
			$topNumberTools = request('topNumberTools');
			$topSubNumberTools = request('topSubNumberTools');
			$is_active = request('is_active');

			$check = DB::table('company_faq_robot_info')
			->join('company_faq_robot', 'company_faq_robot.id', 'company_faq_robot_info.company_faq_robot_id')
			->where('company_faq_robot.company_id', Crypt::decrypt(session('companyselected')['id']))
			->select('company_faq_robot_info.id')
			->where('company_faq_robot_info.language', $language)
			->first();

			if($check == null){

				$check2 = DB::table('company_faq_robot')
				->where('company_id', Crypt::decrypt(session('companyselected')['id']))
				->where('language', $language)
				->first();

				if($check2 == null){
					$id = DB::table('company_faq_robot')->insertGetId([
						'company_id'  => Crypt::decrypt(session('companyselected')['id']), 
						'is_active' => 0,
						'top_tools_show_count' => 0,
						'created_by' => auth()->user()->id,
					]);	
				}else{
					$id = $check2->id; 
				}

				DB::table('company_faq_robot_info')->insertGetId([
					'company_faq_robot_id'  => $id, 
					'initial_message' => $item->initial_message,
					'offline_tool_message' => $item->offline_tool_message,
					'direct_message_to_attendant' => $item->direct_message_to_attendant,
					'confirm_keywords' => json_encode($item->confirmations),
					'changeTools_keywords' => json_encode($item->changeTools),
					'talk_to_attendant' => json_encode($item->phrasesSpeakAttendants, JSON_UNESCAPED_UNICODE),
					'name_robot' => $item->name_robot,
					'language' => $language,
					'created_by' => auth()->user()->id,
				]);
			}else{
				$update['result'] = DB::table('company_faq_robot_info')
				->where('id', $check->id)
				->update([
					'initial_message' => $item->initial_message,
					'offline_tool_message' => $item->offline_tool_message,
					'direct_message_to_attendant' => $item->direct_message_to_attendant,
					'confirm_keywords' => json_encode($item->confirmations),
					'change_tools_keywords' => json_encode($item->changeTools),
					'talk_to_attendant' => json_encode($item->phrasesSpeakAttendants, JSON_UNESCAPED_UNICODE),
					'name_robot' => $item->name_robot,
					'updated_by' => auth()->user()->id,
				]);
			}

			$check2 = DB::table('company_faq_robot')
			->where('company_id', Crypt::decrypt(session('companyselected')['id']))
			->where('language', $language)
			->select('id')
			->first();
			if($check2 == null){
				$id = DB::table('company_faq_robot')->insertGetId([
					'company_id'  => Crypt::decrypt(session('companyselected')['id']), 
					'is_active' => $is_active == 'true' ? true : false,
					'top_tools_show_count' => $topNumberTools,
					'top_tools_show_count' => $topSubNumberTools,
					'created_by' => auth()->user()->id,
					'language' => $language
				]);	
			}else{
				$update['result'] = DB::table('company_faq_robot')
				->where('id', $check2->id)
				->update([
					'top_tools_show_count' => $topNumberTools,
					'top_subtools_show_count' => $topSubNumberTools,
					'is_active' => $is_active == 'true' ? true : false,
					'updated_by' => auth()->user()->id,
					'language' => $language
				]);
			}

			$faqrobot['success'] = true;
		} catch (\Exception $e) {
			echo $e;
			Logger::reportException($e, [], ['company-controller', 'setQuestionRobot'], false);
		}
		
		return json_encode($faqrobot);
	}

	public function updateFaqRobotAll(Request $request){
		$update['success'] = false;
		try {
			$id = request('itemselected')['id'];
			$title = request('title');
			$description = request('description');
			$keywordAll = request('keywordAll');
			$language = request('language');
			
			$update['result'] = DB::table('company_faq_robot_tools')
			->where('id', $id)
			->update([
				'title' => $title,
				'description' => $description,
				'keywords' => $keywordAll,
				'language' => $language,
				'updated_by' => auth()->user()->id,
			]);

			$update['success'] = true;
		} catch (\Exception $e) {
			echo $e;
			Logger::reportException($e, [], ['company-controller', 'updateFaqRobotAll'], false);
			$update['success'] = false;
		}
		
		return json_encode($update);
	}
	

	public static function activeFaqRobotAll() {
		$update['success'] = false;
		try {
			$id = request('id');
			$bool = request('bool');

			$update['result'] = DB::table('company_faq_robot_tools')
			->where('id', $id)
			->update([
				'is_active' => $bool == 'true' ? 0 : 1,
				'updated_by' => auth()->user()->id,
			]);

			$update['success'] = true;
		} catch (\Exception $e) {
			echo $e;
			Logger::reportException($e, [], ['company-controller', 'activeFaqRobotAll'], false);
			$update['success'] = false;
		}
		
		return json_encode($update);
	}

	public static function deleteFaqRobotAll() {
		$category['success'] = false;
		$id = request('id');
		try {
			DB::table('company_faq_robot_tools')
			->where('id', $id)
			->update([
				'deleted_at' => \Carbon\Carbon::now()->toDateTimeString(),
				'deleted_by' => auth()->user()->id,
			]);

			$check = DB::table('company_faq_robot_tools')
			->where('company_faq_robot_tool_id', $id)
			->select('id')
			->get();

			if($check == '[]'){
				// Get all the image files in the server folder
				$dir = '..' . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR 
				. 'public' . DIRECTORY_SEPARATOR . 'company' . DIRECTORY_SEPARATOR . Crypt::decrypt(session('companyselected')['id']) 
				. DIRECTORY_SEPARATOR . 'faq' . DIRECTORY_SEPARATOR . $id . DIRECTORY_SEPARATOR;
				$images = glob($dir.'/*.*');
			
				// Check each image file against the extracted URLs
				foreach ($images as $image) {
					unlink($image);
				}

				if (is_dir($dir)) {
					rmdir($dir);
				}
			}else{
				foreach ($check as $key) {
					$id = $key->id;
	
					// Get all the image files in the server folder
					$dir = '..' . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR 
					. 'public' . DIRECTORY_SEPARATOR . 'company' . DIRECTORY_SEPARATOR . Crypt::decrypt(session('companyselected')['id']) 
					. DIRECTORY_SEPARATOR . 'faq' . DIRECTORY_SEPARATOR . $id . DIRECTORY_SEPARATOR;
					$images = glob($dir.'/*.*');
				
					// Check each image file against the extracted URLs
					foreach ($images as $image) {
						unlink($image);
					}
	
					if (is_dir($dir)) {
						rmdir($dir);
					}
				}
			}
			
			

			$category['success'] = true;

		} catch (\Exception $e) {
			Logger::reportException($e, [], ['company-controller', 'deleteFaqRobotAll'], false);
			$category['success'] = false;
		}

		return $category;
	}

	public function storeCategory(){
		$category['success'] = false;
		
		try {
			$myObj = [
				["language" => 'pt_BR', "description" => ''],
				["language" => 'en_US', "description" => ''],
				["language" => 'fr_FR', "description" => ''],
				["language" => 'de_DE', "description" => ''],
				["language" => 'es_ES', "description" => ''],
				["language" => 'it_IT', "description" => ''],
				["language" => 'pt_PT', "description" => ''],
				["language" => 'he_IL', "description" => ''],
				["language" => 'hu_HU', "description" => ''],
				["language" => 'pl_PL', "description" => ''],
				["language" => 'cz_CZ', "description" => ''],
				["language" => 'es_CO', "description" => ''],
			];

			$array = ['language' => auth()->user()->language];
			$addcheck = false;
			for ($i=0; $i < 10; $i++) { 
				if($myObj[$i]['language'] == $array['language']){
					$myObj[$i]['description'] = request('description');
					$addcheck = true;
				}
			}

			if(!$addcheck) {
				array_push($myObj, ["language" => auth()->user()->language, "description" => request('description')]);
				$addcheck = false;
			}
			$myObj = json_encode($myObj);

			$id = DB::table('category')->insertGetId([
				'company_id'  => Crypt::decrypt(session('companyselected')['id']), 
				'category_id' => request('selected'),
				'description' => $myObj,
				'created_by' => auth()->user()->id,
				'created_at' => \Carbon\Carbon::now()->toDateTimeString(),
			]);
			
			$category['id'] = $id;
			$category['object'] = $myObj;
			$category['category_id'] = request('selected');
			$category['description'] = request('description');
			$category['success'] = true;
			
		} catch (\Exception $e) {
			echo $e;
			Logger::reportException($e, [], ['company-controller', 'storeCategory'], false);
			$category['success'] = false;
		}
		
		return $category;
	}
	
	public function updateCategory(){
		$update['success'] = false;
		try {
			$id = request('itemEditSelected')['id'];
			
			$update['result'] = DB::table('category')
			->where('id', $id)
			->update([
				'description' => request('description'),
			]);
			
			$update['id'] = $id;
			$update['object'] = request('description');
			$update['success'] = true;
		} catch (\Exception $e) {
			
			Logger::reportException($e, [], ['company-controller', 'updateCategory'], false);
			$update['success'] = false;
		}
		
		return json_encode($update);
	}
	
	public function updateMovedCategory(){
		$update['success'] = false;
		try {
			$selectedsItems = request('selectedsItems');
			$listCategory2 = request('listCategory2');
			$array = [];
			foreach ($listCategory2 as $key) {
				if($key['active2'] == true){
					array_push($array, $key);
				}
			}
			$id = $array[0]['id'];

			if(count($array) == 1){
				foreach ($selectedsItems as $key) {
					// update
					DB::table('category')
					->where('id', $key['id'])
					->update([
						'category_id' => $id,
					]);	
				}
			}

			$update['success'] = true;
		} catch (\Exception $e) {
			echo $e;
			Logger::reportException($e, [], ['company-controller', 'updateMovedCategory'], false);
			$update['success'] = false;
		}
		
		return json_encode($update);
	}

	public function deleteCategory(){
		$category['success'] = false;
		
		try {
			$check = DB::table('category')
				->join('chat_category', 'chat_category.category_id', 'category.id')
				->where('chat_category.category_id', request('category_id'))
				->first();

			if($check == null){
				DB::table('category')
				->where('id', request('category_id'))
				->delete();
				$category['success'] = true;
			}else{
				DB::table('category')
				->where('id', request('category_id'))
				->update([
					'deleted_at' => \Carbon\Carbon::now()->toDateTimeString(),
					'deleted_by' => auth()->user()->id,
				]);
				$category['success'] = true;
			}

		} catch (\Exception $e) {
			// echo $e;

			DB::table('category')
			->where('id', request('category_id'))
			->update([
				'deleted_at' => \Carbon\Carbon::now()->toDateTimeString(),
				'deleted_by' => auth()->user()->id,
			]);
			$category['success'] = true;


			// $category['error'] = 'already_linked';
			// $category['success'] = false;
		}

		return $category;	
	}

	public function anyCustomEmail(Request $request){
		$email['success'] = false;

		$type = request('type');
		$emailHtml = request('emailHtml');
		$defaultLanguage = request('defaultLanguage');
		$nameSender = request('nameSender');
		$emailSender = request('emailSender');
		$title = request('title');

		try {
			if($request->isMethod('post')){

				$query = DB::table('emails')
					->where('company_id', Crypt::decrypt(session('companyselected')['id']))
					->where('language', $defaultLanguage)
					->where('type', $type)
					->first();

				if($query == null){
					$id = DB::table('emails')->insertGetId([
						'company_id' => Crypt::decrypt(session('companyselected')['id']),
						'language' => $defaultLanguage,
						'type' => $type,
						'title' => $title,
						'email' => $emailHtml,
						'name_sender' => $nameSender,
						'email_sender' => $emailSender,
						'created_by' => auth()->user()->id,
						'created_at' => \Carbon\Carbon::now()->toDateTimeString(),
					]);
					$email['success'] = true;
				}else{
					$update = DB::table('emails')
					->where('id', $query->id)
					->where('type', $type)
					->where('language', $defaultLanguage)
					->update([
						'title' => $title,
						'email' => $emailHtml,
						'name_sender' => $nameSender,
						'email_sender' => $emailSender,
					]);

					// $email['value'] = $emailHtml;
					$email['success'] = $update;
				}
			}else if($request->isMethod('get')){
				$query = DB::table('emails')
					->where('company_id', Crypt::decrypt(session('companyselected')['id']))
					->where('language', $defaultLanguage)
					->where('type', $type)
					->first();
				if($query == null){
					$query = DB::table('emails')
					->where('company_id', null)
					->where('language', $defaultLanguage)
					->where('type', $type)
					->first();
				}
				if($query == null){
					return '';
				}

				return [$query->email, $query->title, $query->name_sender, $query->email_sender];
			}
		} catch (\Exception $e) {
			echo $e;
			Logger::reportException($e, [], ['company-controller', 'anyCustomEmail'], false);
			$email['success'] = false;
		}

		return $email;	
	}

	public function registerNewCompany(Request $request){
        return view('functions.admin.company.edit-company',[
			'usuario' => Auth::user()->toJson(),
			'csid' => null,
			'viewcontact' => "true",
			'save_href' => url('/select-company'),
			'cancel_href' => url('/company'),
			'update_href' => url('/select-company')
		]);
	}

	public function editCompany(Request $request){
        return view('functions.admin.company.edit-company',[
			'usuario' => Auth::user()->toJson(),
			'is_helpdesk' => ConfigsCompanyReleased::is_helpdesk(),
			'csid' => session('companyselected.id'),
			'viewcontact' => "false",
			'save_href' => url('/select-company'),
			'cancel_href' => url('/company'),
			'update_href' => url('/select-company'),

		]);
	}

	public function getSummaryCardsCompanyDashboard(Request $request){
		try {
			$sql = "CALL pro_dashboard_company_indicators(?, ?);";

			$res = DB::select($sql, [
				Crypt::decrypt(request('company_id')),
				0
			]);

			if(empty($res)){
				$res[0] = [ 'type' => "Departments", 'Count' => 0 ];
				$res[1] = [ 'type' => "Groups", 'Count' => 0 ];
				$res[2] = [ 'type' => "Atendents", 'Count' => 0 ];
				$res[3] = [ 'type' => "Clients", 'Count' => 0 ];
			}

			$res['success'] = true;

            return json_encode($res);
        } catch (\Exception $e) {
			echo $e;
            Logger::reportException($e, [], ['company-controller', 'getSummaryCardsCompanyDashboard'], false);
            $res['success'] = false;
        }
	}

	public function getInfoDashboard(Request $request){
		$info['success'] = false;
		try {
			$all_depart = request('all_depart');
			$department_id = request('department_id');
			$changeWeeklyMonthly = request('changeWeeklyMonthly');

			// TODOS OS DEPARTAMENTOS
			if(json_decode($all_depart[0])->id == 'all'){
				array_shift($all_depart);
			}

			// DEPARTAMENTO SELECIONADO
			$array_id_department = [];
			if(is_array($department_id)){
				foreach ($department_id as $key) {
					array_push($array_id_department, Crypt::decrypt(json_decode($key)->id));
				}
			}else{
				if(json_decode($department_id)->id == 'all'){
					foreach ($all_depart as $key) {
						array_push($array_id_department, Crypt::decrypt(json_decode($key)->id));
					}
				}
			}
			
			$array_id_department = implode(",", $array_id_department);
			$company_id = Crypt::decrypt(session('companyselected')['id']);
			$date_initial = request('date_initial');
			$date_final = request('date_final');
			
			$arrayName = array(
				'company_id1' => $company_id,
				'company_id2' => $company_id,
				'date_initial1' => $date_initial,
				'date_initial2' => $date_initial,
				'date_final1' => $date_final,
				'date_final2' => $date_final,
			);

			$and = 'AND c.company_department_id IN ('.$array_id_department.')';
			$and2 = 'AND t.company_department_id IN ('.$array_id_department.')';
			$and3 = 'AND c.company_department_id IN ('.$array_id_department.')';
			$and4 = 'AND t.company_department_id IN ('.$array_id_department.')';
			

			if($changeWeeklyMonthly == "true"){
				//-- 1 - Tickets/Chats abertos por dia
				$query = "SELECT type, _date, COUNT(*) as count
				FROM (
					SELECT 'Chats_Opened' as type,
					DATE(c.created_at) AS _date
					FROM chat c
					WHERE c.company_id = :company_id1 AND c.deleted_at IS NULL AND c.ticket_id IS NULL AND c.status != 'CANCELED'
					AND c.created_at BETWEEN :date_initial1 AND :date_final1 + INTERVAL 1 DAY
					".$and."
					UNION ALL
					SELECT 'Tickets_Opened' as type,
					DATE(t.created_at) AS _date
					FROM ticket t
					WHERE t.company_id = :company_id2 AND t.deleted_at IS NULL  AND t.status != 'CANCELED' AND t.status != 'MERGED'
					AND t.created_at BETWEEN :date_initial2 AND :date_final2 + INTERVAL 1 DAY
					".$and2."
				) sub
				GROUP BY type, _date
				ORDER BY _date, type;";
			}else{
				//-- 1) Tickets/Chats opened
				$query = "SELECT type, week, COUNT(*) as count
				FROM (
					SELECT 'Chats_Opened' as type,
						CASE
							WHEN EXTRACT(DAY FROM c.created_at) BETWEEN 1 AND 7 THEN 'Week 1'
							WHEN EXTRACT(DAY FROM c.created_at) BETWEEN 8 AND 14 THEN 'Week 2'
							WHEN EXTRACT(DAY FROM c.created_at) BETWEEN 15 AND 21 THEN 'Week 3'
							WHEN EXTRACT(DAY FROM c.created_at) > 21 THEN 'Week 4'
						END AS week
					FROM chat c
					WHERE c.company_id = :company_id1 AND c.deleted_at IS NULL AND c.ticket_id IS NULL AND c.status != 'CANCELED'
					AND c.created_at BETWEEN :date_initial1 AND :date_final1 + INTERVAL 1 DAY
					".$and."
					UNION ALL
					SELECT 'Tickets_Opened' as type,
						CASE
							WHEN EXTRACT(DAY FROM t.created_at) BETWEEN 1 AND 7 THEN 'Week 1'
							WHEN EXTRACT(DAY FROM t.created_at) BETWEEN 8 AND 14 THEN 'Week 2'
							WHEN EXTRACT(DAY FROM t.created_at) BETWEEN 15 AND 21 THEN 'Week 3'
							WHEN EXTRACT(DAY FROM t.created_at) > 21 THEN 'Week 4'
						END AS week
					FROM ticket t
					WHERE t.company_id = :company_id2 AND t.deleted_at IS NULL  AND t.status != 'CANCELED' AND t.status != 'MERGED'
					AND t.created_at BETWEEN :date_initial2 AND :date_final2 + INTERVAL 1 DAY
					".$and2."
				) sub
				GROUP BY type, week
				ORDER BY week, type;";
			}

			$info['result'] = DB::select($query, $arrayName);

			if($changeWeeklyMonthly == "true"){
				// -- 2 - Tickets/Chats closed/resolved por dia
				$query2 = "SELECT type, _date, COUNT(*) as count
				FROM (
					SELECT 'Chats_Closed' as type,
					DATE(c.created_at) AS _date
					FROM chat c
					WHERE c.company_id = :company_id1 AND c.deleted_at IS NULL AND c.ticket_id IS NULL AND c.status IN ('CLOSED', 'RESOLVED')
					AND c.created_at BETWEEN :date_initial1 AND :date_final1 + INTERVAL 1 DAY
					".$and3."
					UNION ALL
					SELECT 'Tickets_Closed' as type,
					DATE(t.created_at) AS _date
					FROM ticket t
					WHERE t.company_id = :company_id2 AND t.deleted_at IS NULL AND t.status IN ('CLOSED', 'RESOLVED')
					AND t.created_at BETWEEN :date_initial2 AND :date_final2 + INTERVAL 1 DAY
					".$and4."
				) sub
				GROUP BY type, _date
				ORDER BY _date, type;";
			}else{
				//-- 2 - Tickets/Chats closed/resolved
				$query2 = "SELECT type, week, COUNT(*) as count
				FROM (
					SELECT 'Chats_Closed' as type,
						CASE
							WHEN EXTRACT(DAY FROM c.update_status_closed_resolved) BETWEEN 1 AND 7 THEN 'Week 1'
							WHEN EXTRACT(DAY FROM c.update_status_closed_resolved) BETWEEN 8 AND 14 THEN 'Week 2'
							WHEN EXTRACT(DAY FROM c.update_status_closed_resolved) BETWEEN 15 AND 21 THEN 'Week 3'
							WHEN EXTRACT(DAY FROM c.update_status_closed_resolved) > 21 THEN 'Week 4'
						END AS week
					FROM chat c
					WHERE c.company_id = :company_id1 AND c.deleted_at IS NULL AND c.ticket_id IS NULL AND c.status IN ('CLOSED', 'RESOLVED')
					AND c.created_at BETWEEN :date_initial1 AND :date_final1 + INTERVAL 1 DAY
					".$and3."
					UNION ALL
					SELECT 'Tickets_Closed' as type,
						CASE
							WHEN EXTRACT(DAY FROM t.update_status_closed_resolved) BETWEEN 1 AND 7 THEN 'Week 1'
							WHEN EXTRACT(DAY FROM t.update_status_closed_resolved) BETWEEN 8 AND 14 THEN 'Week 2'
							WHEN EXTRACT(DAY FROM t.update_status_closed_resolved) BETWEEN 15 AND 21 THEN 'Week 3'
							WHEN EXTRACT(DAY FROM t.update_status_closed_resolved) > 21 THEN 'Week 4'
						END AS week
					FROM ticket t
					WHERE t.company_id = :company_id2 AND t.deleted_at IS NULL AND t.status IN ('CLOSED', 'RESOLVED')
					AND t.created_at BETWEEN :date_initial2 AND :date_final2 + INTERVAL 1 DAY
					".$and4."
				) sub
				GROUP BY type, week
				ORDER BY week, type;";
			}
			

			$info['result2'] = DB::select($query2, $arrayName);
			if($changeWeeklyMonthly == "true"){
				$info['result3'] = array_merge($info['result'],$info['result2']);
			}

			$info['success'] = true;
        } catch (\Exception $e) {
			echo $e;
            Logger::reportException($e, [], ['company-controller', 'getInfoDashboard'], false);
            $info['success'] = false;
        }

		return json_encode($info);
	}

	public function getInfoBugDashboard(Request $request){
		$info['success'] = false;
		try {
			$all_depart = request('all_depart');
			$department_id = request('department_id');

			// TODOS OS DEPARTAMENTOS
			if(json_decode($all_depart[0])->id == 'all'){
				array_shift($all_depart);
			}

			// DEPARTAMENTO SELECIONADO
			$array_id_department = [];
			if(is_array($department_id)){
				foreach ($department_id as $key) {
					array_push($array_id_department, Crypt::decrypt(json_decode($key)->id));
				}
			}else{
				if(json_decode($department_id)->id == 'all'){
					foreach ($all_depart as $key) {
						array_push($array_id_department, Crypt::decrypt(json_decode($key)->id));
					}
				}
			}
			
			$array_id_department = implode(",", $array_id_department);
			$company_id = Crypt::decrypt(session('companyselected')['id']);
			$date_initial = request('date_initial');
			$date_final = request('date_final');
			$valueHours = request('valueHours');


			$arrayName3 = array(
				'company_id1' => $company_id,
				'date_initial1' => $date_initial,
				'date_final1' => $date_final,
			);

			$and = 'AND c.company_department_id IN ('.$array_id_department.')';
			
			//-- 4) Number of bugs reported (Perhaps we can locate these through categories)
			// $query3 = "SELECT COUNT(*) Count, SUM(IF(status IN ('CLOSED', 'RESOLVED'), 1, 0)) AS closed_resolved
			// FROM (
			// 	SELECT c.status, c.company_department_id, cat.id AS subcategoria, JSON_VALUE(cat.description, '$[0].description') AS categoria
			// 	FROM chat c
			// 	JOIN chat_category cc ON c.id = cc.chat_id
			// 	JOIN category cat on cc.category_id = cat.id
			// 	WHERE c.company_id = :company_id1
			// 	AND c.deleted_at IS NULL AND c.status != 'CANCELED'
			// 	AND c.created_at BETWEEN :date_initial1 AND :date_final1 + INTERVAL 1 DAY
			// 	".$and."
			// 	HAVING categoria = 'Bug'
			// ) sub;";

			$query3 = "SELECT COUNT(*) Count, SUM(IF(type IN ('TICKET', 'CHANGED_TO_TICKET') 
			AND ticket_status IN ('CLOSED', 'RESOLVED'), 1, 0)) AS ticket_closed_resolved,
			SUM(IF(TYPE NOT IN ('TICKET', 'CHANGED_TO_TICKET') AND chat_status IN ('CLOSED', 'RESOLVED'), 1, 0)) AS chat_closed_resolved,
			SUM(IF((type IN ('TICKET', 'CHANGED_TO_TICKET') AND ticket_status IN ('OPENED', 'IN_PROGRESS') AND time_in_hours > 120) 
			OR (TYPE != 'TICKET' AND chat_status IN ('OPENED', 'IN_PROGRESS') AND time_in_hours > 120), 1, 0)) AS chats_tickets_overdue,
			SUM(IF((type IN ('TICKET', 'CHANGED_TO_TICKET') AND ticket_status IN ('OPENED', 'IN_PROGRESS') AND time_in_hours <= 120) 
			OR (TYPE != 'TICKET' AND chat_status IN ('OPENED', 'IN_PROGRESS') AND time_in_hours <= 120), 1, 0)) AS chats_tickets_on_time
			FROM (    SELECT c.type, c.status AS chat_status, t.status AS ticket_status, c.company_department_id, 
			c.created_at, TIMESTAMPDIFF(HOUR, c.created_at, NOW()) AS time_in_hours, cat.id AS subcategoria, 
			JSON_VALUE(cat.description, '$[0].description') AS categoria
			FROM chat c
			JOIN chat_category cc ON c.id = cc.chat_id
			JOIN category cat on cc.category_id = cat.id
			LEFT JOIN ticket t on c.ticket_id = t.id
			WHERE c.company_id = :company_id1 AND ((c.type NOT IN ('TICKET', 'CHANGED_TO_TICKET') 
			AND c.deleted_at IS NULL AND c.status != 'CANCELED')        
			OR (c.type IN ('TICKET', 'CHANGED_TO_TICKET') 
			AND t.deleted_at IS NULL AND t.status != 'CANCELED'))    
			AND c.created_at BETWEEN :date_initial1 AND :date_final1 + INTERVAL 1 DAY
			".$and."
			HAVING categoria = 'Bug') sub;";

			$info['result3'] = DB::select($query3, $arrayName3);

			foreach ($info['result3'] as $key) {
				$key->closed_resolved = $key->ticket_closed_resolved + $key->chat_closed_resolved;
			}

			$arrayName4 = array(
				'company_id1' => $company_id,
				'company_id2' => $company_id,
				'date_initial1' => $date_initial,
				'date_initial2' => $date_initial,
				'date_final1' => $date_final,
				'date_final2' => $date_final,
			);

			$and = 'AND company_department_id IN ('.$array_id_department.')';
			$and2 = 'AND company_department_id IN ('.$array_id_department.')';
			
			//-- 6) Ticket/chat average queue and closing time.
			$query4 = "SELECT 'Chat' as _type,
				ROUND(AVG(queue_time)) AS avg_queue_time,
				ROUND(AVG(service_time)) AS avg_service_time    
			FROM chat
				WHERE company_id = :company_id1 AND deleted_at IS NULL  AND status != 'CANCELED'
				AND created_at BETWEEN :date_initial1 AND :date_final1 + INTERVAL 1 DAY
				".$and."
				UNION ALL
				SELECT
					'Ticket' as _type,
				ROUND(AVG(queue_time)) AS avg_queue_time,
					ROUND(AVG(service_time)) AS avg_service_time
				FROM ticket
				WHERE company_id = :company_id2 AND deleted_at IS NULL  AND status != 'CANCELED' AND status != 'MERGED'
				AND created_at BETWEEN :date_initial2 AND :date_final2 + INTERVAL 1 DAY
				".$and2."
			;";

			$info['result4'] = DB::select($query4, $arrayName4);


			$arrayName6 = array(
				'company_id1' => $company_id,
				'date_initial1' => $date_initial,
				'date_final1' => $date_final,
				'tempo_in_segundos' => intval($valueHours) == 0 ? 48*60*60 : intval($valueHours)*60*60,
				'tempo_in_segundos2' => intval($valueHours) == 0 ? 48*60*60 : intval($valueHours)*60*60,
			);

			$and = 'AND t.company_department_id IN ('.$array_id_department.')';
			
			$query6 = "SELECT SUM(IF(queue_time >= :tempo_in_segundos, 1, 0)) AS avg_queue_time, SUM(IF(service_time >= :tempo_in_segundos2, 1, 0)) AS avg_service_time
			FROM (
				SELECT id, status, created_at, update_status_in_progress, update_status_closed_resolved,
					IF(queue_time IS NULL,
							IF(update_status_in_progress IS NULL, TIMESTAMPDIFF(SECOND, created_at, NOW()),
								TIMESTAMPDIFF(SECOND, created_at, update_status_in_progress)),
						queue_time) AS queue_time,
					IF(status = 'OPENED', 0,
						IF(update_status_in_progress IS NULL, TIMESTAMPDIFF(SECOND, created_at, NOW()),
							TIMESTAMPDIFF(SECOND, update_status_in_progress, NOW()))) AS service_time
				FROM ticket t
				WHERE company_id = :company_id1
				AND deleted_at IS NULL
				AND status IN ('OPENED', 'IN_PROGRESS')
				AND t.created_at BETWEEN :date_initial1 AND :date_final1 + INTERVAL 1 DAY
				".$and."
			) sub;";
			
			$info['result6'] = DB::select($query6, $arrayName6);

			$info['success'] = true;
        } catch (\Exception $e) {
			echo $e;
            Logger::reportException($e, [], ['company-controller', 'getInfoBugDashboard'], false);
            $info['success'] = false;
        }

		return json_encode($info);
	}

	public function getInfoTopThree(Request $request){
		$info['success'] = false;
		try {

			$all_depart = request('all_depart');
			$department_id = request('department_id');

			// TODOS OS DEPARTAMENTOS
			if(json_decode($all_depart[0])->id == 'all'){
				array_shift($all_depart);
			}

			// DEPARTAMENTO SELECIONADO
			$array_id_department = [];
			if(is_array($department_id)){
				foreach ($department_id as $key) {
					array_push($array_id_department, Crypt::decrypt(json_decode($key)->id));
				}
			}else{
				if(json_decode($department_id)->id == 'all'){
					foreach ($all_depart as $key) {
						array_push($array_id_department, Crypt::decrypt(json_decode($key)->id));
					}
				}
			}

			$array_id_department = implode(",", $array_id_department);
			$company_id = Crypt::decrypt(session('companyselected')['id']);
			$date_initial = request('date_initial');
			$date_final = request('date_final');
			
			$arrayName4 = array(
				'company_id1' => $company_id,
				'date_initial1' => $date_initial,
				'date_final1' => $date_final,
			);

			$and = 'AND c.company_department_id IN ('.$array_id_department.')';
			
			$query4 = "SELECT * FROM (
				SELECT week, id_categoria_pai, categoria_pai, count, ROW_NUMBER() OVER (PARTITION BY week ORDER BY week, Count DESC) AS _seq
				FROM (
					SELECT week, id_categoria_pai, categoria_pai, COUNT(*) AS count
					FROM
					(
						SELECT JSON_VALUE(cat.description, '$[0].description') AS categoria,
						cat.category_id as id_categoria_pai,
						(SELECT JSON_VALUE(cat2.description, '$[0].description') FROM category cat2 WHERE cat2.id = cat.category_id) AS categoria_pai,
						CASE
							WHEN EXTRACT(DAY FROM c.created_at) BETWEEN 1 AND 7 THEN 'Week 1'
							WHEN EXTRACT(DAY FROM c.created_at) BETWEEN 8 AND 14 THEN 'Week 2'
							WHEN EXTRACT(DAY FROM c.created_at) BETWEEN 15 AND 21 THEN 'Week 3'
							WHEN EXTRACT(DAY FROM c.created_at) > 21 THEN 'Week 4'
						END AS week
						FROM chat c
						JOIN chat_category cc ON c.id = cc.chat_id
						JOIN category cat on cc.category_id = cat.id
						WHERE c.company_id = :company_id1
						AND c.deleted_at IS NULL AND c.status != 'CANCELED'
						AND c.created_at BETWEEN :date_initial1 AND :date_final1 + INTERVAL 1 DAY
						".$and."
						HAVING categoria = 'Bug'
					) sub
					GROUP BY 1, 2
					ORDER BY Count DESC
				) sub2
			) sub3
			HAVING _seq IN (1, 2, 3);";

			$info['result5'] = DB::select($query4, $arrayName4);
			$aux = [];

			//GAMIARRA MASTER....
			$count = 0;
			foreach ($info['result5'] as $key) {
				if($key->week == 'Week 1' && $count <= 2){
					array_push($aux, $key);
					$count++;
				}
			}

			if($count != 3){
				for ($count; $count < 3; $count++) { 
					array_push($aux, (object)[
						"week" => "Week 1",
						"id_categoria_pai" => null,
						"categoria_pai" => "",
						"count" => "0",
						"_seq" => "0",
					]);
				}
			}

			if($count == 3){
				$count = 0;
				foreach ($info['result5'] as $key) {
					if($key->week == 'Week 2' && $count <= 2){
						array_push($aux, $key);
						$count++;
					}
				}
			}

			if($count != 3){
				for ($count; $count < 3; $count++) { 
					array_push($aux, (object)[
						"week" => "Week 2",
						"id_categoria_pai" => null,
						"categoria_pai" => "",
						"count" => "0",
						"_seq" => "0",
					]);
				}
			}

			if($count == 3){
				$count = 0;
				foreach ($info['result5'] as $key) {
					if($key->week == 'Week 3' && $count <= 2){
						array_push($aux, $key);
						$count++;
					}
				}
			}

			if($count != 3){
				for ($count; $count < 3; $count++) { 
					array_push($aux, (object)[
						"week" => "Week 3",
						"id_categoria_pai" => null,
						"categoria_pai" => "",
						"count" => "0",
						"_seq" => "0",
					]);
				}
			}

			if($count == 3){
				$count = 0;
				foreach ($info['result5'] as $key) {
					if($key->week == 'Week 4' && $count <= 2){
						array_push($aux, $key);
						$count++;
					}
				}
			}

			if($count != 3){
				for ($count; $count < 3; $count++) { 
					array_push($aux, (object)[
						"week" => "Week 4",
						"id_categoria_pai" => null,
						"categoria_pai" => "",
						"count" => "0",
						"_seq" => "0",
					]);
				}
			}
			//GAMIARRA MASTER....
			$info['result5'] = $aux;

			$info['success'] = true;
        } catch (\Exception $e) {
			echo $e;
            Logger::reportException($e, [], ['company-controller', 'getInfoBugDashboard'], false);
            $info['success'] = false;
        }

		return json_encode($info);
	}

	public function getInfoOverdueCT(Request $request) {
		$info['success'] = false;
		try {
			$department_id = Crypt::decrypt(request('department_id'));
			$company_id = Crypt::decrypt(session('companyselected')['id']);
			$date_initial = request('date_initial');
			$date_final = request('date_final');

			$arrayName6 = array(
				'company_id1' => $company_id,
				'department_id1' => $department_id,
				'date_initial1' => $date_initial,
				'date_final1' => $date_final,
				'tempo_in_segundos' => 1, //172800
				'tempo_in_segundos2' => 1,
			);

			$query6 = "SELECT SUM(IF(queue_time >= :tempo_in_segundos, 1, 0)) AS avg_queue_time, SUM(IF(service_time >= :tempo_in_segundos2, 1, 0)) AS avg_service_time
			FROM (
				SELECT id, status, created_at, update_status_in_progress, update_status_closed_resolved,
					IF(queue_time IS NULL,
							IF(update_status_in_progress IS NULL, TIMESTAMPDIFF(SECOND, created_at, NOW()),
								TIMESTAMPDIFF(SECOND, created_at, update_status_in_progress)),
						queue_time) AS queue_time,
					IF(status = 'OPENED', 0,
						IF(update_status_in_progress IS NULL, TIMESTAMPDIFF(SECOND, created_at, NOW()),
							TIMESTAMPDIFF(SECOND, update_status_in_progress, NOW()))) AS service_time
				FROM ticket t
				WHERE company_id = :company_id1
				AND deleted_at IS NULL
				AND status IN ('OPENED', 'IN_PROGRESS')
				AND t.created_at BETWEEN :date_initial1 AND :date_final1 + INTERVAL 1 DAY
				AND t.company_department_id = :department_id1
			) sub;";
			
			$info['result6'] = DB::select($query6, $arrayName6);

			$info['success'] = true;
        } catch (\Exception $e) {
			echo $e;
            Logger::reportException($e, [], ['company-controller', 'getInfoOverdueCT'], false);
            $info['success'] = false;
        }

		return json_encode($info);
	}

	public function getBarChartCompanyDashboard(Request $request) {
		try {

			$sql = "CALL pro_dashboard_company_occurrences_by_department(?, ?, ?, ?)";

			$res = DB::select($sql, [
				Crypt::decrypt(request('company_id')),
				Crypt::decrypt(request('department_id')),
				strtoupper(request('period')),
				0
			]);

            if(request('period') == 'week') {
                $aux = [
                    'labels' => [],
                    'chats' => [],
                    'tickets' => [],
                    'success' => false
                ];

                $first_day_of_week = date_sub(date_create(), date_interval_create_from_date_string( date('w', date_timestamp_get(date_create()))." days") );
                $interations = intval(date('w', date_timestamp_get(date_create())));
                for($i = 0; $i <= $interations; $i++) {
                    $aux['labels'][date_format($first_day_of_week, 'Ymd')] = [
                        'dia' => date_format($first_day_of_week, 'd'),
                        'mes' => date_format($first_day_of_week, 'm'),
                        'ano' => date_format($first_day_of_week, 'Y'),
                    ];
                    $aux['chats'][date_format($first_day_of_week, 'Ymd')] = 0;
                    $aux['tickets'][date_format($first_day_of_week, 'Ymd')] = 0;

                    $first_day_of_week = date_add($first_day_of_week, date_interval_create_from_date_string( "1 days") );
                }

				if(!empty($res)) {
					for($i = 0; $i < count($res); $i++) {
						if($res[$i]->type == 'Chat') {
							$dt = $res[$i]->year.($res[$i]->month < 10 ? '0'.$res[$i]->month :  $res[$i]->month).($res[$i]->day < 10 ? '0'.$res[$i]->day : $res[$i]->day );
							$aux['chats'][$dt] = intval($res[$i]->count);
						} else {
							$dt = $res[$i]->year.($res[$i]->month < 10 ? '0'.$res[$i]->month :  $res[$i]->month).($res[$i]->day < 10 ? '0'.$res[$i]->day : $res[$i]->day );
							$aux['tickets'][$dt] = intval($res[$i]->count);
						}
					}
				}

                $aux['labels'] = array_values($aux['labels']);
                $aux['chats'] = array_values($aux['chats']);
                $aux['tickets'] = array_values($aux['tickets']);

                $res = $aux;

			} else if(request('period') == 'month') {
				$aux = [
                    'labels' => [1,2,3,4],
                    'chats' => [0,0,0,0],
                    'tickets' => [0,0,0,0],
                    'success'=> false
                ];

				if(!empty($res)) {
					for($i = 0; $i < count($res); $i++){
						$index = intval($res[$i]->semana)-1;
						if($res[$i]->type == 'Chat') {
							$aux['chats'][$index] = intval($res[$i]->Count);
						} else {
							$aux['tickets'][$index] = intval($res[$i]->Count);
						}
					}
				}
                $res = $aux;
			} else {

                $aux = [
                    'labels' => [],
                    'chats' => [],
                    'tickets' => [],
                    'success' => false
                ];

                $current_month = intval(date_format(date_create(), 'n'));
                $current_year = date_format(date_create(), 'Y');
                for($i = 1; $i <= $current_month; $i++) {
                    $mes = $i < 10 ? '0'.$i : $i;
                    $aux['labels'][$current_year.$mes] = [
                        'mes' => $mes,
                        'ano' => $current_year,
                    ];
                    $aux['chats'][$current_year.$mes] = 0;
                    $aux['tickets'][$current_year.$mes] = 0;
                }

				if(!empty($res)) {
					for($i = 0; $i < count($res); $i++) {
						if($res[$i]->type == 'Chat') {
							$dt = $res[$i]->year.($res[$i]->month < 10 ? '0'.$res[$i]->month : $res[$i]->month);
							$aux['chats'][$dt] = intval($res[$i]->count);
						} else {
							$dt = $res[$i]->year.($res[$i]->month < 10 ? '0'.$res[$i]->month : $res[$i]->month);
							$aux['tickets'][$dt] = intval($res[$i]->count);
						}
					}
				}

                $aux['labels'] = array_values($aux['labels']);
                $aux['chats'] = array_values($aux['chats']);
                $aux['tickets'] = array_values($aux['tickets']);

                $res = $aux;
			}

            $res['success'] = true;

            return json_encode($res);
        } catch (\Exception $e) {
			echo $e;
            Logger::reportException($e, [], ['home-controller', 'getProgressCardsHomeDashboard'], false);
            $res['success'] = false;
        }
	}

	public function getDepartmentsCompanyDashboard(Request $request) {
		try {
			$sql = "
				SELECT id, name, (deleted_at IS NOT NULL) AS deleted, type
				FROM company_department
				WHERE company_id = ?
				AND deleted_at is null
				AND is_active = 1
				ORDER BY 3, 2;";

			$res['result'] = DB::select($sql, [
				Crypt::decrypt(request('company_id'))
			]);

			if(!empty($res)) {
				foreach($res['result'] as $key) {
					$key->id = Crypt::encrypt($key->id);
				}
			}

			$res['success'] = true;

            return json_encode($res);
		} catch (\Exception $e) {
			echo $e;
            Logger::reportException($e, [], ['company-controller', 'getDepartmentsCompanyDashboard'], false);
            $res['success'] = false;
        }
	}

	public function getTicketTimeCardsCompanyDashboard(Request $request) {
		try {
			$sql = "CALL pro_dashboard_company_tickets_average_time_queue_and_service(?, NULL, ?, ?);";

			$res = DB::select($sql, [
				Crypt::decrypt(request('company_id')),
				strtoupper(request('period')),
				0
			]);

			if(!empty($res) && isset($res[0]) ) {
				foreach($res[0] as $key => &$val){
					$val = $val != null ? intval($val) : $val;
				}
			} else {
				$res[0] = [
					'avg_queue_time' => null,
					'avg_service_time' => null
				];
			}

			$res['success'] = true;

            return json_encode($res);
        } catch (\Exception $e) {

            Logger::reportException($e, [], ['company-controller', 'getTicketTimeCardsCompanyDashboard'], false);
            $res['success'] = false;
        }
	}

	public function getChatTimeCardsCompanyDashboard(Request $request) {
		try {
			$sql = "CALL pro_dashboard_company_chats_average_time_queue_and_service(?, NULL, ?, ?);";

			$res = DB::select($sql, [
				Crypt::decrypt(request('company_id')),
				strtoupper(request('period')),
				0
			]);

			if(!empty($res) && isset($res[0]) ) {
				foreach($res[0] as $key => &$val){
					$val = $val != null ? intval($val) : $val;
				}
			} else {
				$res[0] = [
					'avg_queue_time' => null,
					'avg_service_time' => null
				];
			}

			$res['success'] = true;

            return json_encode($res);
        } catch (\Exception $e) {

            Logger::reportException($e, [], ['company-controller', 'getChatTimeCardsCompanyDashboard'], false);
            $res['success'] = false;
        }
	}

	public function getCountMembers(Request $request) {
		try {
	
			$sql1 = "SELECT COUNT(*) AS department FROM company_department 
			WHERE company_id = ?
			AND deleted_at IS NULL
			AND is_active = 1;";

			$sql2 = "SELECT COUNT(*) AS groupss FROM user_group 
			WHERE company_id = ?
			AND deleted_at IS NULL
			AND is_active = 1;";

			$sql3 = "SELECT COUNT(*) AS agents FROM company_user 
			WHERE company_id = ?
			AND deleted_at IS NULL;";

			$res['result1'] = DB::select($sql1, [
				Crypt::decrypt(request('company_id'))
			]);
			
			$res['result2'] = DB::select($sql2, [
				Crypt::decrypt(request('company_id'))
			]);
			
			$res['result3'] = DB::select($sql3, [
				Crypt::decrypt(request('company_id'))
			]);

			$res['success'] = true;

            return json_encode($res);
        } catch (\Exception $e) {
			echo $e;
            Logger::reportException($e, [], ['company-controller', 'getCountMembers'], false);
            $res['success'] = false;
        }
	}



	public function showintegration(Request $request) {
		return view('functions.admin.company.integration');
	}

	public function getintegration(Request $request) {

		$contact['company'] = DB::table('company')
		->select('hash_code')
		->where('id', Crypt::decrypt(session('companyselected')['id']))
		->first();

        return $contact;
	}

    public function generalSettings(Request $request) {

		if(session('loginUnknown') == '1'){
			$settings = (object) $array = [
				"general" => null,
			];

			return $settings->general;
		}

		$settings = (object) $array = [
			"general" => null,
		];

		if($request['company_id'] != null){
			$company_id = Crypt::decrypt($request['company_id']);
        	$settings = CompanySettings::where('company_id', $company_id)->first();

			if($settings == null){
				$settings = (object) $array = [
					"general" => null,
				];
			}

		}
        return $settings->general;
    }

	public function getConfigCompany(Request $request) {

		$company['success'] = false;

		try {

			$settings = CompanySettings::select('general')->where('company_id', Crypt::decrypt(session('companyselected')['id']))->first();
			
			if(!empty($settings->general['selectedStatus'])){
				$company['value'] = $settings->general['selectedStatus'];
			}else{
				$company['value'] = 'IN_PROGRESS';
			}

			$company['success'] = true;

		} catch (\Exception $e) {
			echo $e;
			Logger::reportException($e, [], ['company-controller', 'getConfigCompany'], false);
			$company['success'] = false;
		}

		return $company;
    }

	public function showUsersClient(Request $request) {
		return view('functions.admin.users.user-client');
	}

	public function getUsersClient(Request $request) {
		$users['success'] = false;
		$users['teste'] = request('status');
		// BLOCK CLIENT
		try {
			if(request('search') == null && request('status') == null || request('status') == 'ALL'){
				$users['result'] = DB::table('user_client')
				->join('user_auth', 'user_client.user_auth_id', 'user_auth.id')
				->join('company_user_client', 'user_client.id', 'company_user_client.user_client_id')
				->leftjoin('blacklist_user', 'user_auth.id', 'blacklist_user.blocked_id')
				->select('user_auth.id', 'user_auth.name', 'user_auth.email', DB::raw('COALESCE(!COALESCE(blacklist_user.id, 0) , 1) AS status'), 'blacklist_user.reason')
				->where('user_auth.deleted_at', null)
				->where('company_user_client.company_id', Crypt::decrypt(session('companyselected')['id']))
				->where('user_auth.is_anonymous', 0)
				->paginate(20);

			}else if(request('search') != null){
				$users['result'] = DB::table('user_client')
				->join('user_auth', 'user_client.user_auth_id', 'user_auth.id')
				->join('company_user_client', 'user_client.id', 'company_user_client.user_client_id')
				->leftjoin('blacklist_user', 'user_auth.id', 'blacklist_user.blocked_id')
				->select('user_auth.id', 'user_auth.name', 'user_auth.email', DB::raw('COALESCE(!COALESCE(blacklist_user.id, 0) , 1) AS status'), 'blacklist_user.reason')
				->where('user_auth.deleted_at', null)
				->where('company_user_client.company_id', Crypt::decrypt(session('companyselected')['id']))
				->where('user_auth.name', 'like', '%' . request('search') . '%')
				->where('user_auth.is_anonymous', 0)
				->paginate(20);

				if(count($users['result']) == 0){

					$users['result'] = DB::table('user_client')
					->join('user_auth', 'user_client.user_auth_id', 'user_auth.id')
					->join('company_user_client', 'user_client.id', 'company_user_client.user_client_id')
					->leftjoin('blacklist_user', 'user_auth.id', 'blacklist_user.blocked_id')
					->select('user_auth.id', 'user_auth.name', 'user_auth.email', DB::raw('COALESCE(!COALESCE(blacklist_user.id, 0) , 1) AS status'), 'blacklist_user.reason')
					->where('user_auth.deleted_at', null)
					->where('company_user_client.company_id', Crypt::decrypt(session('companyselected')['id']))
					->where('user_auth.email', 'like', '%' . request('search') . '%')
					->where('user_auth.is_anonymous', 0)
					->paginate(20);
				}
			}else if(request('status') == 'BLOCK'){
				$users['result'] = DB::table('user_client')
				->join('user_auth', 'user_client.user_auth_id', 'user_auth.id')
				->join('company_user_client', 'user_client.id', 'company_user_client.user_client_id')
				->join('blacklist_user', 'user_auth.id', 'blacklist_user.blocked_id')
				->select('user_auth.id', 'user_auth.name', 'user_auth.email', DB::raw('COALESCE(!COALESCE(blacklist_user.id, 0) , 1) AS status'), 'blacklist_user.reason')
				->where('user_auth.deleted_at', null)
				->where('company_user_client.company_id', Crypt::decrypt(session('companyselected')['id']))
				->where('user_auth.is_anonymous', 0)
				->paginate(20);

			}else if(request('status') == 'RELEASED'){
				$users['result'] = DB::table('user_client')
				->join('user_auth', 'user_client.user_auth_id', 'user_auth.id')
				->join('company_user_client', 'user_client.id', 'company_user_client.user_client_id')
				->leftjoin('blacklist_user', 'user_auth.id', 'blacklist_user.blocked_id')
				->select('user_auth.id', 'user_auth.name', 'user_auth.email', DB::raw('COALESCE(!COALESCE(blacklist_user.id, 0) , 1) AS status'), 'blacklist_user.reason')
				->where('user_auth.deleted_at', null)
				->where('company_user_client.company_id', Crypt::decrypt(session('companyselected')['id']))
				->where('blacklist_user.id', null)
				->where('user_auth.is_anonymous', 0)
				->groupBy('user_client.id')
				->paginate(20);
			}

			foreach ($users['result'] as $value=>$key){
				$key->id = Crypt::encrypt($key->id);
				$key->email = ClearEmail::clear($key->email);
				if(json_decode($key->reason) != null){
					$key->selectedTime = json_decode($key->reason)[1];
					$key->selectedBan = json_decode($key->reason)[2];
					$key->reason = json_decode($key->reason)[0];
				}
			}

			// dd($users['result']);

			$users['success'] = true;

		} catch (\Exception $e) {
			echo $e;
			Logger::reportException($e, [], ['company-controller', 'getUsersClient'], false);
			$users['success'] = false;
		}

		echo json_encode($users);
	}

	public function setClientStatus(Request $request) {

		$client['success'] = false;
		// BLOCK CLIENT
		try {

			$var = json_encode([
				request('textReason'),
				request('selectedTime'),
				request('selectedBan'),
			]);

			if(!request('status')){
				$client['result'] = BlockUsers::blockClient(Crypt::decrypt(request('client_id')), $var);
			}else{
				$client['result'] = BlockUsers::unlockClient(request('client_id'));
			}
			$client['success'] = true;

		} catch (\Exception $e) {
			echo $e;
			Logger::reportException($e, [], ['company-controller', 'delete-contact'], false);
			$client['success'] = false;
		}

		echo json_encode($client);
	}

	public function getDepartments(Request $request) {
		$department['success'] = false;
		try {

			$department['result'] = Company_department::select('company_department.id', 'company_department.name',  'company_department.is_active')
			->where('company_department.company_id', Crypt::decrypt(session('companyselected')['id']))
			->where('is_active', 1)
			->orderBy('company_department.name')
			->get();

			foreach ($department['result'] as $key) {
				$key->id = Crypt::encrypt($key->id);
			}

			$department['success'] = true;
		} catch (\Exception $e) {
			echo $e;
			Logger::reportException($e, [], ['company-controller', 'getDepartments'], false);
			$department['success'] = false;
		}

		return json_encode($department);
	}

	public function getAgents(Request $request) {
		$agents['success'] = false;
		try {
			
			$agents['result'] = DB::table('user_auth')
			->select('user_auth.id', 'user_auth.name', 'user_auth.email')
			->join('company_user', 'company_user.user_auth_id', 'user_auth.id')
			->where('company_user.company_id', Crypt::decrypt(session('companyselected')['id']))
			->where('company_user.is_active', 1)
			->whereNull('user_auth.deleted_at')
			->whereNull('company_user.deleted_at')
			->orderBy('user_auth.name')
			->get();

			foreach ($agents['result'] as $key) {
				$key->id = Crypt::encrypt($key->id);
			}

			$agents['success'] = true;
		} catch (\Exception $e) {
			echo $e;
			Logger::reportException($e, [], ['company-controller', 'getAgents'], false);
			$agents['success'] = false;
		}

		return json_encode($agents);
	}

	
	public function setPython($hostname, $title, $language) {

		if($hostname == 'ba-support.builderall.io' || $hostname == 'localhost'){
			putenv('ROBOT=/usr/bin/python3.9 /srv/ba-support/chatbot/training.py rebuild -c '.Crypt::decrypt(session('companyselected')['id']).' -t '.$title.' -l '.$language.' 2>&1');
		}

		if($hostname == 'ba-support.builderall.com' || $hostname == 'hs.builderall.com'){
			putenv('ROBOT=/usr/local/bin/python3.10 /srv/ba-support/chatbot/training.py rebuild -c '.Crypt::decrypt(session('companyselected')['id']).' -t '.$title.' -l '.$language.' 2>&1');
		}
		echo $this->getValuePython('ROBOT');
		return shell_exec($this->getValuePython('ROBOT'));
	}

	public function getValuePython($nome_variavel) {
		return getenv($nome_variavel);
	}


	public function ViewBlock(Request $request) {
		return view('functions.admin.users.user-client-blocked');
	}
}
