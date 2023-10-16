<?php

namespace App\Tools;

use Illuminate\Database\Eloquent\Model;

class Timezone extends Model
{
    const all = [
        'US/Canada' => [
            "Pacific/Honolulu" => "Hawaii Time",
            "America/Anchorage" => "Alaska Time",
            "America/Los_Angeles" => "Pacific Time - US & Canada",
            "America/Phoenix" => "Arizona Time",
            "America/Denver" => "Mountain Time - US & Canada",
            "America/Chicago" => "Central Time - US & Canada",
            "America/New_York" => "Eastern Time - US & Canada",
        ],
        "America" => [
            "America/Adak" => "America/Adak",
            "America/Costa_Rica" => "America/Costa_Rica",
            "America/Santa_Isabel" => "America/Santa Isabel",
            "America/Guatemala" => "America/Guatemala",
            "America/Mazatlan" => "America/Mazatlan",
            "America/Bogota" => "America/Bogota",
            "America/Mexico_City" => "America/Mexico_City",
            "America/Asuncion" => "America/Asuncion",
            "America/Campo_Grande" => "America/Campo Grande",
            "America/Caracas" => "America/Caracas",
            "America/Havana" => "America/Havana",
            "America/Santiago" => "America/Santiago",
            "America/Argentina/Buenos_Aires" => "Buenos Aires",
            "America/Goose_Bay" => "Atlantic Time",
            "America/Montevideo" => "Montevideo",
            "America/Sao_Paulo" => "Horário de Brasília",
            "America/Godthab" => "America/Godthab",
            "America/Miquelon" => "America/Miquelon",
            "America/Noronha" => "America/Noronha",
        ],
        "Africa" => [
            "Africa/Lagos" => "West Africa Time",
            "Africa/Cairo" => "Africa/Cairo",
            "Africa/Johannesburg" => "Central Africa Time",
            "Africa/Windhoek" => "Africa/Windhoek",
        ],
        "Asia" => [
            "Asia/Amman" => "Jordan",
            "Asia/Baghdad" => "Baghdad, East Africa Time",
            "Asia/Beirut" => "Lebanon",
            "Asia/Damascus" => "Syria",
            "Asia/Gaza" => "Asia/Gaza",
            "Asia/Jerusalem" => "Asia/Jerusalem",
            "Asia/Baku" => "Asia/Bacu",
            "Asia/Dubai" => "Dubai",
            "Asia/Yerevan" => "Asia/Ierevan",
            "Asia/Kabul" => "Kabul",
            "Asia/Tehran" => "Tehran",
            "Asia/Karachi" => "Asia/Karachi",
            "Asia/Yekaterinburg" => "Asia/Yekaterinburg",
            "Asia/Kolkata" => "Asia/Kolkata",
            "Asia/Kathmandu" => "Asia/Kathmandu",
            "Asia/Dhaka" => "Asia/Daca",
            "Asia/Omsk" => "Asia/Omsk",
            "Asia/Rangoon" => "Asia/Rangum",
            "Asia/Jakarta" => "Asia/Jakarta",
            "Asia/Krasnoyarsk" => "Asia/Krasnoyarsk",
            "Asia/Irkutsk" => "Asia/Irkutsk",
            "Asia/Shanghai" => "Asia/Shanghai",
            "Asia/Tokyo" => "Asia/Tokyo",
            "Asia/Yakutsk" => "Asia/Yakutsk",
            "Asia/Vladivostok" => "Asia/Vladivostok",
            "Asia/Kamchatka" => "Asia/Kamchatka",
        ],
        "Atlantic" => [
            "Atlantic/Cape_Verde" => "Cape Verde",
            "Atlantic/Azores" => "Azores",
        ],
        "Australia" => [
            "Australia/Perth" => "Australia/Perth",
            "Australia/Eucla" => "Australia/Eucla",
            "Australia/Adelaide" => "Adelaide",
            "Australia/Darwin" => "Australia/Darwin",
            "Australia/Brisbane" => "Brisbane",
            "Australia/Sydney" => "Sydney, Melbourne",
            "Australia/Lord_Howe" => "Australia/Lord Howe",
        ],
        "UTC" => [
            "UTC" => "UTC Time",
        ],
        "Europe" => [
            "Europe/London" => "UK, Ireland, Lisbon",
            "Europe/Berlin" => "Central European",
            "Europe/Helsinki" => "Easter European",
            "Europe/Minsk" => "Minsk",
            "Europe/Moscow" => "Moscow",
        ],
        "Pacific" => [
            "Pacific/Pago_Pago" => "Pacific/Pago Pago",
            "Pacific/Marquesas" => "Pacific/Marquesas",
            "Pacific/Gambier" => "Pacific/Gambier",
            "Pacific/Pitcairn" => "Pacific/Pitcairn",
            "Pacific/Easter" => "Pacific/Easter",
            "Pacific/Norfolk" => "Pacific/Norfolk",
            "Pacific/Noumea" => "Pacific/Noumea",
            "Pacific/Auckland" => "Auckland Time",
            "Pacific/Fiji" => "Pacific/Fiji",
            "Pacific/Majuro" => "Pacific/Majuro",
            "Pacific/Tarawa" => "Pacific/Tarawa",
            "Pacific/Chatham" => "Pacific/Chatham",
            "Pacific/Apia" => "Pacific/Apia",
            "Pacific/Tongatapu" => "Pacific/Tongatapu",
            "Pacific/Kiritimati" => "Pacific/Kiritimati",
        ],
    ];

    public static function onlyKeys() {
        $return = [];
        foreach (self::all as $timezone_group => $timezones) {
            foreach ($timezones as $value => $text) {
                $return[] = $value;
            }
        }
        return $return;
    }

    public static function toSelect() {
        $return = [];
        foreach(self::all as $timezone_group => $timezones) {
            $options = [];
            foreach($timezones as $value => $text) {
                $options[] = ['value' => $value, 'text' => $text];
            }

            $return[] = ['label' => $timezone_group, 'options' => $options];
        }
        return $return;
    }

    public static function getLookup($key) {
        foreach(self::all as $timezone_group => $timezones) {
            foreach($timezones as $value => $text) {
                if ($key == $value)
                return $text;
            }
        }
    }

}
