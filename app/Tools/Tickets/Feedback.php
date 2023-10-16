<?php

namespace App\Tools\Tickets;

use App\Mail\sendEmailCustom;
use App\Models\UserClientTicket;
use App\Ticket;
use App\Tools\Builderall\Logger;
use App\Tools\ClearEmail;
use App\Tools\Crypt;
use App\User;
use Exception;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

/**
 * Feedback dos tickets para o Cliente
 */
class Feedback{

	/**
	 * Send ticket notification 
	 * @param string $status
	 * @param int $ticket_id
	 * @param int $company_id
	 * @param string $company_name
	 */
	// ENVIA PARA O ADMIN DEPOIS MB
    public static function send($status, $ticket_id, $company_id, $company_name)
    {
		$client_id = null;

        try 
		{
			$ticket    = Ticket::find($ticket_id);

			if ($ticket)
			{
				$user_client_ticket = $ticket->userClientTicket;
				if ($user_client_ticket)
				{
					$user_client = $user_client_ticket->userClient;
		
					if ($user_client)
					{
						$user = $user_client->userAuth;
						if ($user)
						{
							$client_id = $user->id;
						}
					}
				}
			}

			$department_id = $ticket ? $ticket->company_department_id : null;
	
			// Email
			$mail = new MailAdmin($company_id, $client_id, $department_id);
			
			// Notification
			$notification = new Notification($company_id, $client_id, $department_id);
			
			/**
			 * TRADUZIR
			 * Deixei sem tradução para a validação das frases com o pessoal do suporte BR.
			 */
			switch ($status) {
				case 'OPENED':
					$mail->ticketReceived();
					// $notification->send($company_name, Feedback::tClient("bs-we-received-your-ticket", $ticket_id)."#". $ticket_id." ".Feedback::tClient("bs-please-wait-and-we-will-answer-you-soon", $ticket_id).".");
					$notification->send($company_name, Feedback::tClient("bs-your-ticket", $ticket_id)." ". $ticket_id." ".Feedback::tClient("bs-has-been-successfully-submitted", $ticket_id).".");
					break;
				case 'IN_PROGRESS':
					$mail->ticketReplied();
					// $notification->send($company_name, Feedback::tClient("bs-your-ticket", $ticket_id)."#". $ticket_id." ".Feedback::tClient("bs-has-been-answered-click-here-to-view", $ticket_id));
					$notification->send($company_name, Feedback::tClient("bs-you-have-received-a-reply-on-your-ticket", $ticket_id)." ". $ticket_id." ".Feedback::tClient("bs-click-here-to-be-redirected-to-the-helpdes", $ticket_id).".");
					break;
				case 'CLOSED':
					$mail->ticketClosed();
					// $notification->send($company_name, Feedback::tClient("bs-answering-your-ticket", $ticket_id)."#". $ticket_id." ".Feedback::tClient("bs-has-been-terminated-click-here-to-view", $ticket_id));
					$notification->send($company_name, Feedback::tClient("bs-your-ticket", $ticket_id)." ". $ticket_id." ".Feedback::tClient("bs-has-been-answered-and-closed", $ticket_id)." ".Feedback::tClient("bs-click-here-to-be-redirected-to-the-helpdes", $ticket_id).".");
					break;
				case 'RESOLVED':
					$mail->ticketClosed();
					// $notification->send($company_name, Feedback::tClient("bs-answering-your-ticket", $ticket_id)."#". $ticket_id." ".Feedback::tClient("bs-has-been-terminated-click-here-to-view", $ticket_id));
					$notification->send($company_name, Feedback::tClient("bs-your-ticket", $ticket_id)." ". $ticket_id." ".Feedback::tClient("bs-has-been-answered-and-closed", $ticket_id)." ".Feedback::tClient("bs-click-here-to-be-redirected-to-the-helpdes", $ticket_id).".");
					break;
			}

		} 

		catch (Exception $e) 
		{
			Logger::reportException($e, ['status' => $status, 'ticket_id' => $ticket_id, 'company_id' => $company_id, 'company_name' => $company_name, 'client_id' => $client_id], ['send-ticket-feedback'], false);
		}
    }

	// ENVIA DIRETO PARA O MB
	public static function directSendMB($status, $ticket_id, $email_send)
    {
		$client_id = null;
		$company_id = Crypt::decrypt(session('companyselected')['id']);
		$company_name = session('companyselected')['name'];

        try 
		{
			$ticket    = Ticket::find($ticket_id);

			if ($ticket)
			{
				$user_client_ticket = $ticket->userClientTicket;
				if ($user_client_ticket)
				{
					$user_client = $user_client_ticket->userClient;
		
					if ($user_client)
					{
						$user = $user_client->userAuth;
						if ($user)
						{
							$client_id = $user->id;
						}
					}
				}
			}

			$department_id = $ticket ? $ticket->company_department_id : null;
	
			// Notification
			$notification = new Notification($company_id, $client_id, $department_id);
			
			// Email
			$user = User::where('id', $client_id)->first();
			$mail = new sendEmailCustom($ticket_id, $user, ClearEmail::clear($email_send),'','','','',session('companyselected')['hash_code']);

			/**
			 * TRADUZIR
			 * Deixei sem tradução para a validação das frases com o pessoal do suporte BR.
			 */
			switch ($status) {
				case 'OPENED':
					$mail->ticketReceived();
					if(config('app.env') != 'sandbox') { 
						// $notification->send($company_name, Feedback::tClient("bs-we-received-your-ticket", $ticket_id)."#". $ticket_id." ".Feedback::tClient("bs-please-wait-and-we-will-answer-you-soon", $ticket_id).".");
						$notification->send($company_name, Feedback::tClient("bs-your-ticket", $ticket_id)." ". $ticket_id." ".Feedback::tClient("bs-has-been-successfully-submitted", $ticket_id).".");
					}
					break;
				case 'IN_PROGRESS':
					$mail->ticketReplied();
					if(config('app.env') != 'sandbox') { 
						// $notification->send($company_name, Feedback::tClient("bs-your-ticket", $ticket_id)."#". $ticket_id." ".Feedback::tClient("bs-has-been-answered-click-here-to-view", $ticket_id));
						$notification->send($company_name, Feedback::tClient("bs-you-have-received-a-reply-on-your-ticket", $ticket_id)." ". $ticket_id." ".Feedback::tClient("bs-click-here-to-be-redirected-to-the-helpdes", $ticket_id).".");
					}
					break;
				case 'CLOSED':
					$mail->ticketClosed();
					if(config('app.env') != 'sandbox') { 
						// $notification->send($company_name, Feedback::tClient("bs-answering-your-ticket", $ticket_id)."#". $ticket_id." ".Feedback::tClient("bs-has-been-terminated-click-here-to-view", $ticket_id));
						$notification->send($company_name, Feedback::tClient("bs-your-ticket", $ticket_id)." ". $ticket_id." ".Feedback::tClient("bs-has-been-answered-and-closed", $ticket_id)." ".Feedback::tClient("bs-click-here-to-be-redirected-to-the-helpdes", $ticket_id).".");
					}
					break;
				case 'RESOLVED':
					$mail->ticketClosed();
					if(config('app.env') != 'sandbox') { 
						// $notification->send($company_name, Feedback::tClient("bs-answering-your-ticket", $ticket_id)."#". $ticket_id." ".Feedback::tClient("bs-has-been-terminated-click-here-to-view", $ticket_id));
						$notification->send($company_name, Feedback::tClient("bs-your-ticket", $ticket_id)." ". $ticket_id." ".Feedback::tClient("bs-has-been-answered-and-closed", $ticket_id)." ".Feedback::tClient("bs-click-here-to-be-redirected-to-the-helpdes", $ticket_id).".");
					}
					break;
			}
		} 
		
		catch (Exception $e) 
		{
			Logger::reportException($e, ['status' => $status, 'ticket_id' => $ticket_id, 'company_id' => $company_id, 'company_name' => $company_name, 'client_id' => $client_id], ['send-ticket-feedback'], false);
		}
    }

	// TRADUZ A LINGUAGEM DO ATENDENTE.
	public static function t($text){
		return Lang::get("app.$text", [], auth()->user()->language);
	}

	// TRADUZ A LINGUAGEM DO ATENDENTE.
	public static function tl($text, $language){
		return Lang::get("app.$text", [], $language);
	}

	// TRADUZ COM A LINGUAGEM DO CLIENTE 
	public static function tClient($text, $ticket_id){

		$result = DB::table('ticket')
			->join('user_client_ticket', 'ticket.id', 'user_client_ticket.ticket_id')
			->join('user_client', 'user_client_ticket.user_client_id', 'user_client.id')
			->join('user_auth', 'user_client.user_auth_id', 'user_auth.id')
			->select('user_auth.language')
			->where('ticket.id', $ticket_id)
			->first();
		
		if($result != null){
			return Lang::get("app.$text", [], $result->language);
		}else{
			return Lang::get("app.$text", [], auth()->user()->language);
		}
	}
}