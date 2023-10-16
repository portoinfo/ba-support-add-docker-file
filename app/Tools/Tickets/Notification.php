<?php

namespace App\Tools\Tickets;

use App\Models\Company;
use App\Models\Company_department;
use App\Tools\Builderall\OfficeNotification;
use App\Tools\Client;
use App\User;

class Notification{

    private const CATEGORY = 'support-system';

    private $client;
    private $company;
    private $department;
    private $notification_to;
    private $allowed_to_send;

    public function __construct(int $company_id, int $client_id, int $department_id)
    {
        $this->client      = User::find($client_id);
        $this->company     = Company::find($company_id);
        $this->department  = Company_department::find($department_id);
        $this->notification_to = Client::getCleanEmail($this->client->email, $company_id);

        if ($this->department)
        {
            $this->allowed_to_send = config('app.env') != 'local' && $this->department->ticketNotificationsSettings('office_notification');
        }
    }

    /**
     * Send a ticket notification
     * @param string $title
     * @param string $text
     */
    public function send(string $title, string $text)
    {
        if (!$this->allowed_to_send)
        {
            return;
        }

        // $action = route('client-access-link', ['company' => $this->company->hash_code, 'user' => $this->client->hash_code, 'type' => 'ticket']);
        $action = 'https://office.builderall.com/us/office/login?redir=%2Fus%2Foffice%2Fsupport-system%2Fticket';
        
        if(env('APP_ENV') == 'sandbox'){
            $action = 'https://office.builderall.io/us/office/login?redir=%2Fus%2Foffice%2Fsupport-system%2Fticket';
        }

        OfficeNotification::send($this->notification_to, $title, $text, $action, null, self::CATEGORY);
    }
}