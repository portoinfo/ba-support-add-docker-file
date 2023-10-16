<?php

namespace App\Http\Controllers;

use App\Chat;
use App\TicketChatAnswer;
use App\Tools\Builderall\Logger;
use App\Tools\Crypt;
use Dotenv\Result\Success;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;

class TicketChatAnswerController extends Controller
{
    public function getTicketChatAnswers(Request $request)
    {   
        $reference = $request->reference; // chat_id ou ticket_id
        $id = $request->id; // id da referencia

        $result = DB::table('ticket_chat_answer')
            ->join('company_depart_settings_question', 'company_depart_settings_question.id', 'ticket_chat_answer.company_depart_settings_question_id')
            ->select(
                'ticket_chat_answer.'.$reference, 
                'company_depart_settings_question.question',
                'ticket_chat_answer.answer',
                //'ticket_chat_answer.created_at',

            )
            ->where($reference, Crypt::decrypt($id))
            ->get();

        foreach($result as $row) {
            $row->$reference = Crypt::encrypt($reference);
        }

        if($result) {
            return response()->json([
                'result' => $result,
                'status' => true
            ]);
        } else {
            return response()->json([
                'status' => false
            ]);
        }
    }

    public function getFaqRobotTools(Request $request){
        $result['result3'] = DB::table('company_faq_robot')
        ->select('is_active', 'top_tools_show_count as topNumberTools')
        ->where('company_id',Crypt::decrypt(session('companyselected')['id']))
        ->where('language', auth()->user()->language)
        ->first();

        if($result['result3'] == null){
            $result['result3'] = DB::table('company_faq_robot')
			->select('is_active', 'top_tools_show_count as topNumberTools')
			->where('company_id',Crypt::decrypt(session('companyselected')['id']))
			->where('language', 'en_US')
			->first();
        }

        if($result['result3'] == null || $result['result3']->is_active == 0 || $result['result3']->topNumberTools == 0){
            $result['result'] = [];
            $result['infos'] = null;
        }else{
            
            // $language = auth()->user()->language;
            // if(auth()->user()->language == 'pt_BR' || auth()->user()->language == 'en_US'){
            //     $language = auth()->user()->language;
            // }else{
            //     $language = 'en_US';
            // }
            $language = auth()->user()->language;
            if($result['result3']->topNumberTools > 0){ 
                if(request('numberTools') == null){
                    $result['result'] = DB::table('company_faq_robot_tools as cfrt')
                    ->join('company_faq_robot', 'company_faq_robot.id', 'cfrt.company_faq_robot_id')
                    ->join('company_faq_robot_to_train', 'cfrt.id', 'company_faq_robot_to_train.company_faq_robot_tool_id')
                    ->where('company_faq_robot.company_id', Crypt::decrypt(session('companyselected')['id']))
                    ->whereNull('cfrt.company_faq_robot_tool_id')
                    ->whereNull('cfrt.deleted_at')
                    ->where('cfrt.language', $language)
                    ->where('cfrt.is_active', 1)
                    ->select('cfrt.id', 'cfrt.title', 'cfrt.description', 'cfrt.tool_status')
                    ->limit($result['result3']->topNumberTools)
                    ->orderBy('cfrt.click_count', 'DESC')
                    ->get();
                }else{
                    $page = request('skip'); // Defina a página atual aqui

                    if($language == 'en_US' && $page == 1){
                        $limit = 3;
                    }else{
                        $limit = 5;
                    }
                    $offset = ($page - 1) * $limit;
                    
                    $result['result'] = DB::table('company_faq_robot_tools as cfrt')
                    ->join('company_faq_robot', 'company_faq_robot.id', 'cfrt.company_faq_robot_id')
                    ->join('company_faq_robot_to_train', 'cfrt.id', 'company_faq_robot_to_train.company_faq_robot_tool_id')
                    ->where('company_faq_robot.company_id', Crypt::decrypt(session('companyselected')['id']))
                    ->whereNull('cfrt.company_faq_robot_tool_id')
                    ->whereNull('cfrt.deleted_at')
                    ->where('cfrt.language', $language)
                    ->where('cfrt.is_active', 1)
                    ->select('cfrt.id', 'cfrt.title', 'cfrt.description', 'cfrt.tool_status')
                    ->limit(100) 
                    ->orderBy('cfrt.click_count', 'DESC')
                    ->offset($offset)
                    ->limit($limit)
                    ->get();
                    
                    if($result['result'] == '[]' && $page > 1){
                        $result['finishOptions'] = true;
                    }else{
                        $result['finishOptions'] = false;
                    }
                }
            }else{
                $result['result'] = DB::table('company_faq_robot_tools as cfrt')
			    ->join('company_faq_robot', 'company_faq_robot.id', 'cfrt.company_faq_robot_id')
                ->join('company_faq_robot_to_train', 'cfrt.id', 'company_faq_robot_to_train.company_faq_robot_tool_id')
			    ->where('company_faq_robot.company_id', Crypt::decrypt(session('companyselected')['id']))
                ->whereNull('cfrt.company_faq_robot_tool_id')
                ->whereNull('cfrt.deleted_at')
                ->where('cfrt.language', $language)
                ->select('cfrt.id', 'cfrt.title', 'cfrt.description', 'cfrt.tool_status')
                ->get();
            }

            $result['infos'] = DB::table('company_faq_robot_info')
                ->join('company_faq_robot', 'company_faq_robot.id', 'company_faq_robot_info.company_faq_robot_id')
                ->where('company_faq_robot.company_id', Crypt::decrypt(session('companyselected')['id']))
                ->where('company_faq_robot_info.language', $language)
                ->select('company_faq_robot_info.initial_message', 'company_faq_robot_info.offline_tool_message', 'company_faq_robot_info.confirm_keywords',
                'company_faq_robot_info.change_tools_keywords')
                ->first();


            $result['allTitles'] = DB::table('company_faq_robot_tools as cfrt')
            ->join('company_faq_robot', 'company_faq_robot.id', 'cfrt.company_faq_robot_id')
            ->join('company_faq_robot_to_train', 'cfrt.id', 'company_faq_robot_to_train.company_faq_robot_tool_id')
            ->where('company_faq_robot.company_id', Crypt::decrypt(session('companyselected')['id']))
            ->whereNull('cfrt.company_faq_robot_tool_id')
            ->whereNull('cfrt.deleted_at')
            ->where('cfrt.language', $language)
            ->where('cfrt.is_active', 1)
            ->select('cfrt.title', 'cfrt.keywords')
            ->orderBy('cfrt.click_count', 'DESC')
            ->get();
    
            $result['is_beta_tester'] = session('is_beta_tester');
            $result['is_master_user'] = session('is_master_user');

        }

        // $result['is_master_user'] = true; // TESTE LOCAL
        
        return json_encode($result);
    }

    public function getFaqRobotToolsId(Request $request){
        
        if(auth()->user()->language == 'pt_BR' || auth()->user()->language == 'en_US'){
            $language = auth()->user()->language;
        }else{
            $language = 'en_US';
        }
        $page = request('skip'); // Defina a página atual aqui
        $limit = 5; // Defina o limite de registros por página aqui
        $offset = ($page - 1) * $limit;

        $result = DB::table('company_faq_robot_tools as cfrt')
            ->join('company_faq_robot', 'company_faq_robot.id', 'cfrt.company_faq_robot_id')
            ->where('company_faq_robot.company_id', Crypt::decrypt(session('companyselected')['id']))
            ->where('cfrt.company_faq_robot_tool_id', $request->id)
            ->select('cfrt.id', 'cfrt.title', 'cfrt.description', 'company_faq_robot.top_subtools_show_count as topSubNumberTools')
            ->where('cfrt.language', $language)
            ->where('cfrt.is_active', 1)
            ->whereNull('cfrt.deleted_at') 
            ->offset($offset)
            ->limit($limit)
            ->get();
        
        foreach ($result as $key) {
            if($key->description == ''){
                $key->description = ' ';
            }
        }

        $click_count = DB::table('company_faq_robot_tools as cfrt')
        ->where('id', $request->id)
        ->first()->click_count;

        DB::table('company_faq_robot_tools')
        ->where('id', $request->id)
        ->update([
            'click_count' => $click_count+1,
        ]);

        return json_encode($result);
    }

    public function setInfoRobotFinish(Request $request){
        try {
            // http://localhost:8000/set-info-robot-finish?company_id=6&language=pt_BR
            $company_id = request('company_id');
            $language = request('language');

            if($company_id != null && $language != null){
                DB::table('company_faq_robot')
                ->where('company_id', $company_id)
                ->where('language', $language)
                ->update([
                    'is_finish' => 0,
                ]);

                return 'true';
            }
        } catch (\Exception $e) {
            echo $e;
            Logger::reportException($e, [], ['TicketChatAnswerController', 'setInfoRobotFinish'], false);
        }

        return 'false';
    }
    
    public function setUserClickCount(){
        try {
            $id = request('id');
            $click_count = DB::table('company_faq_robot_tools as cfrt')
            ->where('id', $id)
            ->first()->click_count;

            $result['value'] = DB::table('company_faq_robot_tools')
            ->where('id', $id)
            ->update([
                'click_count' => $click_count+1,
            ]);
            $result['success'] = true;
        } catch (\Exception $e) {
            echo $e;
            Logger::reportException($e, [], ['TicketChatAnswerController', 'setUserClickCount'], false);
            $eval['success'] = false;
        }
        return json_encode($result);
    }

    public function setFaqUser(){
        $eval['success'] = false;

        $id = request('id');
        $bool = request('bool');
        try {

            DB::table('company_faq_robot_tool_user_client')->insertGetId([
                'user_client_id' => Crypt::decrypt(session('companyselected')['user_client_id']),
                'company_faq_robot_tool_id' => $id,
                'was_helped' => $bool
            ]);

            $eval['success'] = true;
       } catch (\Exception $e) {
            echo $e;
            Logger::reportException($e, [], ['TicketChatAnswerController', 'setFaqUser'], false);
            $eval['success'] = false;
        }

        return json_encode($eval);
    }

    public function files3($company_id, $id, $filename){
		// $company_id = Chat::where('id', Crypt::decrypt(request('id')))
		// ->select('company_id')->firstOrFail()->company_id;

		if (!isset($company_id)) {
			abort(404);
		} else {
			$path = '..' . 
            DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'public' . 
            DIRECTORY_SEPARATOR . 'company' . DIRECTORY_SEPARATOR . Crypt::decrypt($company_id) . DIRECTORY_SEPARATOR . 'faq' . 
            DIRECTORY_SEPARATOR . Crypt::decrypt($id) . DIRECTORY_SEPARATOR . $filename;
			if (!File::exists($path)) {
				abort(404);
			}

			$file = File::get($path);
			$type = File::mimeType($path);

			ob_end_clean();

			$response = Response::make($file, 200);
			$response->header("Content-Type", $type);
			// caso queira que o arquivo seja baixado automaticamente e com o nome original ao acessar a rota,
			// descomentar a linha abaixo e deixar o parametro filename com o nome original do arquivo...
			//$response->header('Content-disposition','attachment; filename="nome-do-arquivo.pdf"');

			return $response;
		}
    }
    




}
