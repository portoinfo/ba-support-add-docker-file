<?php

namespace App\Jobs;

use App\Chat;
use App\ChatHistory;
use App\Events\ClientNotification;
use App\Events\ClientQueueStatus;
use App\Events\GlobalNotification;
use App\Events\MessageSent;
use App\Tools\Crypt;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class alertEndOfChat implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $chat;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($chat)
    {
        $this->chat = $chat;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $chat_id = $this->chat['chat_id'];
        $chat_history_id = $this->chat['chat_history_id'];
        $chat = $this->chat['chat'];
        $company_id = $this->chat['company_id'];
        $company_user_id = $this->chat['company_user_id'];
        $company_department_id = $this->chat['company_department_id'];
        $user_client_id = $this->chat['user_client_id'];

        if (Chat::where('chat.id', $chat_id)->where('status', 'IN_PROGRESS')->exists()) {
            $last_id = ChatHistory::where('chat_id', $chat_id)->where('hidden_for_client', 0)->max('id');
            if ($chat_history_id == $last_id) {
                $create = ChatHistory::create([
                    'chat_id' => $chat_id,
                    'type' => 'CLOSE',
                    'content' => 'bs-the-chat-will-end-due-to-inactivity',
                    'created_by' => $this->chat['user']['id'],
                ]);

                broadcast(new ClientQueueStatus([
                    'chat_id' => Crypt::encrypt($chat_id),
                    'company_id' => $company_id,
                    'user_client_id' => $user_client_id,
                    'agent_answered' => 1,
                    'content' => $create->content,
                    'sent_by' => null
                ]));

                $create->chat_id = Crypt::encrypt($chat_id);
                $msg = json_decode(json_encode($create), true);
                $result =  array_merge($msg, [
                    'chat_history_id' => $create->id,
                    'chat' => $chat,
                    'company_id' => $company_id,
                    'created_by' => $create->created_by,
                    'user' => $this->chat['user'],
                    'company_department_id' => $company_department_id,
                    'company_user_id' => $company_user_id
                    ]);
                if (broadcast(new MessageSent($result))) {
                    broadcast(new GlobalNotification([
                        // título da notificação
                        'title' => 'bs-chat',
                        // O corpo(mensagem) da notificação.
                        'body' => 'bs-the-chat-will-end-due-to-inactivity',
                        // A URL da imagem usada como um ícone da notificação.
                        'icon' => 'https://builderall.com//franquias/2/73748/editor-html/3484811.png',
                        // (Opcional) URL para qual a notificação deve redirecionar
                        'url' => '',
                        //se for true, a notificação fica sem som (PS: está sendo disparado som no browser, então pode ficar true aqui que vai sair som igual)
                        'silent' => false,
                        // numero de identificação do chat / ticket
                        'number' => $chat_id,
                        // identifica se a notificação é de chat ou ticket
                        'type' => 'chat',
                        // identifica o status do chat/ticket para fazer a lógica do disparo
                        'status' => 'CLOSED',
                        // identifica o id do departamento para qual a notificação deve ser dispara
                        'company_department_id' => $company_department_id,
                        // envia o company user id do atendente que precisa receber a notificação (caso seja individual)
                        'company_user_id' => $company_user_id,
                        // Paramêtro de conexão no canal global de notificação, sempre deve ser passado
                        'company_id' => $company_id
                    ]));

                    closeUnansweredChat::dispatch($result)->delay(now()->addMinutes(1));
                }
            }
        }
    }
}
