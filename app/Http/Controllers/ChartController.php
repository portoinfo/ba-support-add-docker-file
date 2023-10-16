<?php

namespace App\Http\Controllers;

use App\Http\Controllers\HelpDesk\BuilderallController;
use App\Tools\Crypt;
use App\Tools\Crypt\RC4;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Tools\Tickets\Feedback;
use Builderall\Authenticator\BuilderallAuth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use App\Mail\OrderShipped;
use App\Mail\sendEmailCustom;
use App\Models\Order;
use App\Tools\AutomaticChat;
use App\User;
use Illuminate\Support\Facades\Mail;

class ChartController extends Controller
{
	public function index(){
		return view('functions.employee.chart.chart');
 	}

	public function teste(Request $request){
		// mb_public_key: 0c2c76b80a50feee173ca2dcc5ec5 3b11786b4be
		// mb_private_key: eee377d6a009d2a3f0cc14dc7ca93ee7280ca72b
		
		// $email = 'marlos_gpi@live.com';
		// // $email = 'marlosayfera@gmail.com';
		
		// $user = User::where('email', 'marlos_gpi@live.com')->where('user_uuid', null)->first();

		// $mail = new sendEmailCustom('122',$user,$email,'title','textHtml','name_sender','email_sender','company');

		// $data = RC4::decryptClientAccess(config('app.rc4_key'), 'WU1EkXsI53Acbck8GTP%2Fqr74spBna%2BFYsOAizlxYrZ1%2Fl6WEqU%2BWETjdF8192FmJFF1ghvIy41f2SEg7zHjx27VD3ixBkvb8jRV9xE%2Fmf%2FApPL2LMYk40sGI7tBplkfu%2B5PpGB50mX5QjSqUA6vgSgMjfKUlk1%2Fo25guqpmx%2BNHR4HMN34gj%2FHhGt0A4aQHQ%2B1frrVyK%2BTqQvp0XO0d0u3wcrEjvKDU9Ke9KfUpifzya2l01V0%2FoB8K7mcv1vt1Ch2hGD0qhieTm769dh9Vs5ynKlVmg%2BTRPlSEsxbAHgjNYkNSO0fk9NEG3gMMyuWWanFBmRZV9WF58YLR3Pu9sM0iWetzaoWvJ1F7Ek959x0V3Un36qLacPDUkG0o5euw%3D');
		// $data = RC4::decryptClientAccess(config('app.rc4_key'), 'WU1EkXsI53Acbck8GTP%252Fqr74spBna%252BFYsOAizlxYrZ1%252Fl6WEqU%252BWETjdF8192FmJFF1ghvIy41f2SEg7zHjx27VD3ixBkvb8jRV9xE%252Fmf%252FApPL2cMIsnlZmIpoZ0klu8pOqOb2E80n4b02bbAbKgBExpeq4rn0Hz8YgQupnno4jJ%252BCINw48puicK%252BEIhKU%252BMtUbp7gjGtjiJ%252FtNdfEQz5DBTrlH%252BaGpiY%252BZcUVFTZyym1Uw4BRC%252BT9G%252F2Zrj%252BpBXrW5EB1aMjPzzwKsc3tx45S6NgwDg5S58hy0uhuYNvD5IlpfZ1uk9NBig2YFe%252Fy7GnB91TZ0sB1Bob%252BhlNPtoYFSVfZzPrira2wLLyJ92xll%252BVi%252BtrKzLMDRwV0x5ZKRXKc1p3OodAb9l%252FWIbN6vqYm3fs8yER6uOa7z%252BhAZ7GcWf6%252BMU8Z%252F7IA53XPwZaGtpJLs19FkmGHH11QPJLRQGPwiKNXfqbAGdNVk%252BZ0Kvg69SyFdQhx0VRg%253D%253D');
		// $data = Crypt::decrypt('eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJVVUlEIjoiZDQ1ZjRhNmYtZDFkMS00N2UxLWEyNDQtODQyNjU2ZTZmNWZmIiwiaXNfYWRtaW5fc2Vzc2lvbiI6ZmFsc2UsImlzX3RlYW1fYWNjZXNzX3Nlc3Npb24iOmZhbHNlLCJleHAiOjE2OTM2ODE4NDR9.QR_pwCfdj8YspRoIgcVp4xwQJPtc8Zs3drnHsr6q85Y');
		// $data = RC4::decryptClientAccess(config('app.rc4_key'), 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJVVUlEIjoiZDQ1ZjRhNmYtZDFkMS00N2UxLWEyNDQtODQyNjU2ZTZmNWZmIiwiaXNfYWRtaW5fc2Vzc2lvbiI6ZmFsc2UsImlzX3RlYW1fYWNjZXNzX3Nlc3Npb24iOmZhbHNlLCJleHAiOjE2OTM2ODE4NDR9.QR_pwCfdj8YspRoIgcVp4xwQJPtc8Zs3drnHsr6q85Y');
		// http://localhost:8000/helpdesk/login-by-token?token=eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJVVUlEIjoiZDQ1ZjRhNmYtZDFkMS00N2UxLWEyNDQtODQyNjU2ZTZmNWZmIiwiaXNfYWRtaW5fc2Vzc2lvbiI6ZmFsc2UsImlzX3RlYW1fYWNjZXNzX3Nlc3Npb24iOmZhbHNlLCJleHAiOjE2OTM2NzQ4OTh9.4cnEWYqOvlIvaxq7MbCpgaNsAyGC-k2tLaLBSVjJ6Is
		// dd($data);
		
		// https://office.builderall.com/br/office/support-system/ticket?=fast-ticket=1
		// $response = Client::newAccess($data, $company, $request);
		
		// $ticket_id = 123; 
		// $var = Feedback::tClient("bs-your-ticket", $ticket_id)."-". $ticket_id." ".Feedback::tClient("bs-has-been-successfully-submitted", $ticket_id).".";
		// dd($var);
		// $mail->ticketReceived();
		// $mail->ticketReplied();
		// $mail->ticketClosed();

		// $next_attendant = AutomaticChat::distribution('MGE4Um9Mcjl0ZkpmWHR1NXNZNDVNZz09', "America/Sao_Paulo");



		
		dd(session('companyselected')['name']);


		// $result = DB::table('emails')
		// ->where('id', 1)
		// ->first()->email;


		// $nome = "João";
		// $empresa = "Builderall";

		// $result = str_replace('{name}', $nome, $result);
		// $result = str_replace('{company}', $empresa, $result);


		// // return new newLaraveltips($request->user());
		// $return = Mail::send(new sendEmailCustom($request->user(),$result));



		// Mail::to($request->user())
		// ->cc('marlos_gpi@live.com')
		// ->bcc('marlos_gpi@live.com')
		// ->send('aeae');




		echo 'Você é uma pessoa incrível!';
		// http://localhost:8000/helpdesk/login-by-token?token=eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJleHAiOjE2ODI0Mzg5ODYsIlVVSUQiOiI2M2I0YjBkZS1lMTcyLTQxMjctYmViYy1jYzU1ZTkzYjgzYzkifQ.1gSwNXgWWrfw8PhQPB8SWmexxovlZOS4ZmRaJpR8iZk
		
		// $token = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJleHAiOjE2ODI0Mzg2NTgsIlVVSUQiOiI2M2I0YjBkZS1lMTcyLTQxMjctYmViYy1jYzU1ZTkzYjgzYzkifQ.xdeAUenFdPUnJQYYKaSriA_AAo4KsYpgJbuozajcjvc';

		// dd(Crypt::encrypt('jacky@jackydeklerk.com'));
		// dd(Crypt::decrypt('eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJleHAiOjE2ODI0Mzg2NTgsIlVVSUQiOiI2M2I0YjBkZS1lMTcyLTQxMjctYmViYy1jYzU1ZTkzYjgzYzkifQ.xdeAUenFdPUnJQYYKaSriA_AAo4KsYpgJbuozajcjvc'));
		
		// $dd = BuilderallAuth::getUserDataByToken($token);

		// dd($dd);
		
		
		
		
		// $url = "https://translation.googleapis.com/language/translate/v2?key=" . env('MIX_KEY_GOOGLE_TRANSLATOR');
		// $postData = array(
		//   'target' => 'en_US',
		//   'q' => 'Olá marlos, meu nome é siti um robot automatico implementado para melhorar sua vida.'
		// );
		// $options = array(
		//   'http' => array(
		// 	'header' => "Content-type: application/json\r\n",
		// 	'method' => 'POST',
		// 	'content' => json_encode($postData)
		//   )
		// );
		// $context = stream_context_create($options);
		// $response = file_get_contents($url, false, $context);
		// $data = json_decode($response, true);
		// if(isset($data['data']['translations'][0]['translatedText'])){

		// }
		// $data = $request->access_key ? RC4::decryptClientAccess(config('app.rc4_key'), $request->access_key) : $request->all();
		// dd($data); //-- 3180294337041603216
		// $email = Str::upper('PREFIX_WL_CBP_carlos.rocha548@hotmail.com');
        // if (Str::startsWith($email, 'PREFIX_WL_'))
        // {
        //     $email = Str::replaceFirst('PREFIX_WL_', '', $email);
        //     $email = preg_replace('/^\w+_/', '', $email);
        // }

		// dd($email);
        // Feedback::tClient('bs-welcome', 618);
		// return view('welcome');
    }

 	public function show(){

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

    public function getDiff(Request $request) {
        $d1 = date('d/m/Y H:i:s');
        $d2 =date_create($request['created_at']);
        $d2 = date_format($d2,"d/m/Y H:i:s");

        $now = Carbon::createFromFormat('d/m/Y H:i:s', $d1, "UTC");
        $created_at = Carbon::createFromFormat('d/m/Y H:i:s', $d2, "UTC");

        return $now->diffInSeconds($created_at);
    }
}
