<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use JetBrains\PhpStorm\Language;
use App\Tools\Tickets\Feedback;

class CompanyDepartmentSettings extends Model
{

    /**
     * Return the default array of settings for departments
     * @return array
     */
    public static function getDefaultSettings($tz, $language)
    {
        $arrayLanguage = [
            ["command" => "bd","status" => "START","description" => Feedback::t("bs-good-morning")],
            ["command" => "bt","status" => "START","description" => Feedback::t("bs-good-afternoon")],
            ["command" => "bn","status" => "START","description" => Feedback::t("bs-good-night")],
            ["command" => "eta","status" => "END","description" => Feedback::t("bs-hope-this-helps")]
        ];

        $msgOpenLanguage = Feedback::t('bs-welcome-wait-we-will-soon-assist-you');
        $msgCloseLanguage = Feedback::t('bs-thank-you-very-much-we-hope-we-have-help');
        $descriptionTicket = Feedback::t('bs-please-enter-the-ticket-description');

        $default =  [
            "commands" => $arrayLanguage,
            "quant_limity" => [
                "timewait" => "0",
                "quantidadechat" => "0",
                "quantidadeticket" => "0",
                "diasfecharchat" => "0",
                "inactivityMessage" => 30,
                "lateTime" => 60 * 24 * 7,
                "maxopentickclient" => "0",
                "maxdaysreopenticket" => "7",
            ],
            "evaluation" =>  [
                "text_atten" => "",
                "text_atten_active" => true,
                "text_serv" => "",
                "text_serv_active" => true,
                "text_comment" => "",
                "text_comment_active" => true,
                "typeevaluation" => 'stars',
            ],
            "general" =>  [
                "language" => $tz,
                "userLang" => [
                    [
                        "key"   =>  0,
                        "code"  => "ALL",
                        "label" => "bs-all",
                    ]
                ],
                "module" =>  [
                    "code"  => "ALL",
                    "label" => "ALL",
                ],
            ],
            "robot" => [
                "is_active" => false,
            ],
            "ticket" => [
                "showQueue" => false,
                "msgOpen" => $msgOpenLanguage,
                "desriptionTicket" => $descriptionTicket,
                "sendEmail" => false,
                "events" => true,
                "selectedStatus" => 'IN_PROGRESS',
                "selectedEvents" => [
                    "bs-attendant-created-a-ticket", 
                    "bs-closed-the-ticket",
                    "bs-reopened-the-ticket",
                    "bs-ticket-status-changed",
                    "bs-transferred-the-ticket-to-another-agent",
                    "bs-transferred-the-ticket-to-another-departme",
                    "bs-ticket-transferred-department-and-attendan",
                    "bs-marked-as-resolved",
                    "bs-joined-the-ticket",
                    "bs-canceled-the-ticket"
                ],
                "notificationsOficce" => false,
                "arrayTranslate" => [
                    "msgOpen" => [
                        ["text"=>"Bem vindo! Aguarde que em breve lhe atenderemos!","code"=>"BR"],
                        ["text"=>"Welcome! Wait, we will soon assist you!","code"=>"US"],
                    ],
                    "desriptionTicket" => [
                        ["text"=>"Por favor, informe a descrição do Ticket:","code"=>"BR"],
                        ["text"=>"Please enter the Ticket description:","code"=>"US"],
                    ],
                ],
                'showCategory' => false,
            ],
            "chat" =>  [
                "showQueue" => true,
                "openChat" => true,
                "openChatOnline" => false,
                "msgOpen" => $msgOpenLanguage,
                "msgClose" => $msgCloseLanguage,
                "arrayTranslate" => [
                    "msgOpen" => [
                        ["text"=>"Bem vindo! Aguarde que em breve lhe atenderemos!","code"=>"BR"],
                        ["text"=>"Welcome! Wait, we will soon assist you!","code"=>"US"],
                    ],
                    "msgClose" => [
                        ["text"=>"Muito obrigado(a)! Esperamos ter ajudado!","code"=>"BR"],
                        ["text"=>"Thank you very much! We hope we have helped!","code"=>"US"],
                    ],
                ],
                "openDepartment" => [
                    [
                        "am" => "00:00",
                        "ap" => "23:59",
                    ],
                    [
                        "am" => "00:00",
                        "ap" => "23:59",
                    ],
                    [
                        "am" => "00:00",
                        "ap" => "23:59",
                    ],
                    [
                        "am" => "00:00",
                        "ap" => "23:59",
                    ],
                    [
                        "am" => "00:00",
                        "ap" => "23:59",
                    ],
                    [
                        "am" => "00:00",
                        "ap" => "23:59",
                    ],
                    [
                        "am" => "00:00",
                        "ap" => "23:59",
                    ],
                ],
                "events" => true,
                "selectedEvents" => [
                    "bs-the-chat-ended-due-to-inactivity",
                    "bs-rated-the-chat",
                    "bs-turned-chat-into-ticket",
                    "bs-transferred-the-chat-to-another-agent",
                    "bs-transferred-the-chat-to-another-department",
                    "bs-reopened-the-chat",
                    "bs-took-over-the-chat",
                    "bs-closed-the-chat",
                    "bs-joined-the-chat",
                    "bs-marked-as-resolved",
                    "bs-canceled-the-chat"
                ],
                'showCategory' => false,
            ]
        ];

        return $default;
    }
}
