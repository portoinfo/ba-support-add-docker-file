<?php

use App\CompanyUserCompanyDepartment;
use App\Ticket;
use App\Tools\Crypt;
use App\Tools\SystemState;
use App\UserClientChat;
use Illuminate\Support\Facades\Broadcast;

/*
begin of status channels
*/

// Broadcast::channel('status-online.{companyId}', function ($user, $companyId) {
//     if((int) Crypt::decrypt($companyId) === (int) Crypt::decrypt(session('companyselected')['id'])) {
//         $user->hash_id = Crypt::encrypt($user->id);
//         return $user->toArray();
//     };
// });

Broadcast::channel('status-online.{companyId}', function ($user, $companyId) {
    $company_selected = session('companyselected') ?? SystemState::getCacheForApi($user->id, 'companyselected', null);

    if((int) Crypt::decrypt($companyId) === (int) Crypt::decrypt($company_selected['id'])) {
        $user->hash_id = Crypt::encrypt($user->id);
        if(session('is_client') == 1 || SystemState::getCacheForApi($user->id, 'companyselected', null)) {
            $user->is_client = 1;
        } else if (session('is_client') == 0) {
            $user->is_client = 0;
        }
        if(session('status') === null) {
            $user->status = "online";
        } else {
            $user->status = session('status');
        }
        return $user->toArray();
    };

}, ['guards' => ['web', 'api']]);

/*
end of status channels
*/

/*
begin of tab channel
*/

Broadcast::channel('tabs.{companyId}.{userId}', function ($user, $companyId, $userId) {
    return (int) Crypt::decrypt($companyId) === (int) Crypt::decrypt(session('companyselected')['id']) && ((int) $user->id === (int) $userId);;
});

/*
end of tab channel
*/


/*
begin of notification channels
*/

Broadcast::channel('notification.{companyId}', function ($user, $companyId) {
    return (int) Crypt::decrypt($companyId) === (int) Crypt::decrypt(session('companyselected')['id']);
});
Broadcast::channel('client-notification.{companyId}.{userClientId}', function ($user, $companyId, $userClientId) {

    $company_selected = session('companyselected') ?? SystemState::getCacheForApi($user->id, 'companyselected', null);

    return (int) Crypt::decrypt($companyId) === (int) Crypt::decrypt($company_selected['id'])
    && (int) Crypt::decrypt($userClientId) === (int) Crypt::decrypt($company_selected['user_client_id']);

}, ['guards' => ['web', 'api']]);
/*
end of notification channels
*/
/*
begin of chat channels
*/
Broadcast::channel('chat.{chatId}', function ($user, $chatId) {

    $company_selected = session('companyselected') ?? SystemState::getCacheForApi($user->id, 'companyselected', null);

    if (isset(session('restriction')[0]) && (session('restriction')[0]->chat_admin || session('restriction')[0]->chat_alllist)) {
        return true;
    } else if (isset( $company_selected['user_client_id'])) {
        $result = UserClientChat::select('user_client_id')
            ->where('chat_id', Crypt::decrypt($chatId))
            ->where('user_client_id', Crypt::decrypt($company_selected['user_client_id']))
            ->first();

        if (isset($result->user_client_id)) {
            return (int) $result->user_client_id === (int) Crypt::decrypt($company_selected['user_client_id']);
        } else {
            return false;
        }
    } else {
        $result = CompanyUserCompanyDepartment::join('chat', 'chat.comp_user_comp_depart_id_current', 'company_user_company_department.id')
            ->whereNull('chat.deleted_at')
            ->select('company_user_company_department.company_user_id')
            ->where('chat.id', Crypt::decrypt($chatId))
            ->where('company_user_company_department.company_user_id', Crypt::decrypt($company_selected['company_user_id']))
            ->first();

        if (isset($result->company_user_id)) {
            return (int) $result->company_user_id === (int) Crypt::decrypt($company_selected['company_user_id']);
        } else {
            return false;
        }
    }
    return true;
}, ['guards' => ['web', 'api']]);

Broadcast::channel('chat-status-changer.{chatId}', function ($user, $chatId) {

    $company_selected = session('companyselected') ?? SystemState::getCacheForApi($user->id, 'companyselected', null);

    if (isset(session('restriction')[0]) && (session('restriction')[0]->chat_admin || session('restriction')[0]->chat_alllist)) {
        return true;
    } else if (isset($company_selected['user_client_id'])) {
        $result = UserClientChat::select('user_client_id')
            ->where('chat_id', Crypt::decrypt($chatId))
            ->where('user_client_id', Crypt::decrypt($company_selected['user_client_id']))
            ->first();

        if (isset($result->user_client_id)) {
            return (int) $result->user_client_id === (int) Crypt::decrypt($company_selected['user_client_id']);
        } else {
            return false;
        }
    } else {
        $result = CompanyUserCompanyDepartment::join('chat', 'chat.comp_user_comp_depart_id_current', 'company_user_company_department.id')
            ->whereNull('chat.deleted_at')
            ->select('company_user_company_department.company_user_id')
            ->where('chat.id', Crypt::decrypt($chatId))
            ->where('company_user_company_department.company_user_id', Crypt::decrypt($company_selected['company_user_id']))
            ->first();

        if (isset($result->company_user_id)) {
            return (int) $result->company_user_id === (int) Crypt::decrypt($company_selected['company_user_id']);
        } else {
            return false;
        }
    }
}, ['guards' => ['web', 'api']]);
/*
end of chat channels
*/
//---------------------------------------------------------------------------------------------------------------------------------------------------------
/*
begin of chat tables channels
*/
//client

Broadcast::channel('client-queue.{companyId}.{userClientId}', function ($user, $companyId, $userClientId) {
    return (int) Crypt::decrypt($userClientId) === (int) Crypt::decrypt(session('companyselected')['user_client_id'])
        && (int) Crypt::decrypt($companyId) === (int) Crypt::decrypt(session('companyselected')['company_id']);
});

//agent

// queue table chat
Broadcast::channel('queue.{companyId}', function ($user, $companyId) {
    if (isset(session('companyselected')['user_client_id'])) {
        return (int) Crypt::decrypt($companyId) === (int) Crypt::decrypt(session('companyselected')['company_id']);
    } else {
        return (int) Crypt::decrypt($companyId) === (int) Crypt::decrypt(session('companyselected')['id']);
    }
});

// queue table ticket
Broadcast::channel('ticket-queue.{companyId}', function ($user, $companyId) {
    if (isset(session('companyselected')['user_client_id'])) {
        return (int) Crypt::decrypt($companyId) === (int) Crypt::decrypt(session('companyselected')['company_id']);
    } else {
        return (int) Crypt::decrypt($companyId) === (int) Crypt::decrypt(session('companyselected')['id']);
    }
});

// in progress table
Broadcast::channel('in-progress.{companyId}.{userId}', function ($user, $companyId, $userId) {
    return ((int) Crypt::decrypt($companyId) === (int) Crypt::decrypt(session('companyselected')['id'])) && ((int) $user->id === (int) $userId);
});

// transferred table
Broadcast::channel('transferred.{companyId}', function ($user, $companyId) {
    return (int) Crypt::decrypt($companyId) === (int) Crypt::decrypt(session('companyselected')['id']);
});

// closed table
Broadcast::channel('closed.{companyId}.{userId}', function ($user, $companyId, $userId) {
    return ((int) Crypt::decrypt($companyId) === (int) Crypt::decrypt(session('companyselected')['id'])) && ((int) $user->id === (int) $userId);
});

// resolved table
Broadcast::channel('resolved.{companyId}.{userId}', function ($user, $companyId, $userId) {
    return ((int) Crypt::decrypt($companyId) === (int) Crypt::decrypt(session('companyselected')['id'])) && ((int) $user->id === (int) $userId);
});

// canceled table
Broadcast::channel('canceled.{companyId}.{userId}', function ($user, $companyId, $userId) {
    return ((int) Crypt::decrypt($companyId) === (int) Crypt::decrypt(session('companyselected')['id'])) && ((int) $user->id === (int) $userId);
});
/*
end of chat tables channels
*/
//------------------------------------------------------------------------------------------------------------------------------------------------
/*
begin of full chat tables channels
*/
Broadcast::channel('full-chat.progress.{companyId}', function ($user, $companyId) {
    return ((int) Crypt::decrypt($companyId) === (int) Crypt::decrypt(session('companyselected')['id']));
});
Broadcast::channel('full-chat.closed.{companyId}', function ($user, $companyId) {
    return ((int) Crypt::decrypt($companyId) === (int) Crypt::decrypt(session('companyselected')['id']));
});
Broadcast::channel('full-chat.resolved.{companyId}', function ($user, $companyId) {
    return ((int) Crypt::decrypt($companyId) === (int) Crypt::decrypt(session('companyselected')['id']));
});
Broadcast::channel('full-chat.canceled.{companyId}', function ($user, $companyId) {
    return ((int) Crypt::decrypt($companyId) === (int) Crypt::decrypt(session('companyselected')['id']));
});
/*
begin of full ticket tables channels
*/
Broadcast::channel('full-ticket.progress.{companyId}', function ($user, $companyId) {
    return ((int) Crypt::decrypt($companyId) === (int) Crypt::decrypt(session('companyselected')['id']));
});
Broadcast::channel('full-ticket.closed.{companyId}', function ($user, $companyId) {
    return ((int) Crypt::decrypt($companyId) === (int) Crypt::decrypt(session('companyselected')['id']));
});
Broadcast::channel('full-ticket.resolved.{companyId}', function ($user, $companyId) {
    return ((int) Crypt::decrypt($companyId) === (int) Crypt::decrypt(session('companyselected')['id']));
});
Broadcast::channel('full-ticket.canceled.{companyId}', function ($user, $companyId) {
    return ((int) Crypt::decrypt($companyId) === (int) Crypt::decrypt(session('companyselected')['id']));
});
/*
end of full chat tables channels
*/
//------------------------------------------------------------------------------------------------------------------------------------------------
/*
end of ticket channels
*/
// agent table list
Broadcast::channel('tickets-list.{companyId}', function ($user, $companyId) {
    return (int) Crypt::decrypt($companyId) === (int) Crypt::decrypt(session('companyselected')['id']);
});

Broadcast::channel('tickets-list-full-ticket.{companyId}', function ($user, $companyId) {
    return (int) Crypt::decrypt($companyId) === (int) Crypt::decrypt(session('companyselected')['id']);
});

Broadcast::channel('tickets-agent-list.{companyId}.{userId}', function ($user, $companyId, $userId) {
    return ((int) Crypt::decrypt($companyId) === (int) Crypt::decrypt(session('companyselected')['id']))
        && ((int) $user->id === (int) $userId);
});

Broadcast::channel('client-tickets-list.{companyId}.{userClientId}', function ($user, $companyId, $userClientId) {
    return (int) Crypt::decrypt($userClientId) === (int) Crypt::decrypt(session('companyselected')['user_client_id'])
        && (int) Crypt::decrypt($companyId) === (int) Crypt::decrypt(session('companyselected')['company_id']);
});

// MARLOS CRIOU ESSE - CULPA DELE
Broadcast::channel('client-tickets-answer.{companyId}', function ($user, $companyId) {
    return ((int) Crypt::decrypt($companyId) === (int) Crypt::decrypt(session('companyselected')['company_id']));
});

Broadcast::channel('ticket.{chatId}', function ($user, $chatId) {
    if (isset(session('restriction')[0]) && (session('restriction')[0]->ticket_admin || session('restriction')[0]->ticket_alllist)) {
        return true;
    } else if (isset(session('companyselected')['user_client_id'])) {
        $result = UserClientChat::select('user_client_id')
            ->where('chat_id', Crypt::decrypt($chatId))
            ->where('user_client_id', Crypt::decrypt(session('companyselected')['user_client_id']))
            ->first();

        if (isset($result->user_client_id)) {
            return (int) $result->user_client_id === (int) Crypt::decrypt(session('companyselected')['user_client_id']);
        } else {
            return false;
        }
    } else {
        $result = CompanyUserCompanyDepartment::join('chat', 'chat.comp_user_comp_depart_id_current', 'company_user_company_department.id')
            ->whereNull('chat.deleted_at')
            ->select('company_user_company_department.company_user_id')
            ->where('chat.id', Crypt::decrypt($chatId))
            ->where('company_user_company_department.company_user_id', Crypt::decrypt(session('companyselected')['company_user_id']))
            ->first();

        if (isset($result->company_user_id)) {
            return (int) $result->company_user_id === (int) Crypt::decrypt(session('companyselected')['company_user_id']);
        } else {
            return false;
        }
    }
});

// comments tickets
Broadcast::channel('ticket-comment.{ticket_id}', function ($user, $ticket_id) {
   $company_id = session('companyselected')['id'];

   $result = Ticket::select('id')
    ->where('id', $ticket_id)
    ->where('company_id', Crypt::decrypt($company_id))
    ->first();

    if (isset($result->id)) {
        return $result->id == $ticket_id;
    } else {
        return false;
    }

});

// evaluations
Broadcast::channel('evaluation.{companyId}', function ($user, $companyId) {
    return ((int) Crypt::decrypt($companyId) === (int) Crypt::decrypt(session('companyselected')['id']));
});

Broadcast::channel('chat-ticket-delete.{companyId}', function ($user, $companyId) {
    return ((int) Crypt::decrypt($companyId) === (int) Crypt::decrypt(session('companyselected')['id']));
});


//------------------------------------------------------------------------------------------------------------------------------------------------
