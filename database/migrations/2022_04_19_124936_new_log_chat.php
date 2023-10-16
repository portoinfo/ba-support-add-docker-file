<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class NewLogChat extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared("
            CREATE TABLE `log_chat` (
                `id` bigint(20) unsigned AUTO_INCREMENT,
                `action` enum('INSERT','UPDATE','DELETE','RECOVER') DEFAULT NULL,
                `chat_id`int(10) unsigned DEFAULT NULL,
                `company_department_id` int(10) unsigned DEFAULT NULL,
                `comp_user_comp_depart_id_current` int(10) unsigned DEFAULT NULL,
                `ticket_id` int(10) unsigned DEFAULT NULL,
                `type` enum('DEFAULT','TRANSFERED','TICKET','CHANGED_TO_TICKET') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                `changed_to_ticket_at` datetime DEFAULT NULL,
                `status` enum('OPENED','IN_PROGRESS','CLOSED','RESOLVED','CANCELED','ROBOT') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                `priority` enum('NORMAL','MEDIUM','HIGH') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                `update_status_in_progress` datetime DEFAULT NULL,
                `update_status_closed_resolved` datetime DEFAULT NULL,
                `update_status_canceled` datetime DEFAULT NULL,
                `created_at` timestamp NULL DEFAULT current_timestamp(),
                PRIMARY KEY (`id`),
                KEY `idx_log_chat_chat_id` (`chat_id`),
                KEY `idx_log_chat_company_department_id` (`company_department_id`),
                KEY `idx_log_chat_ticket_id` (`ticket_id`),
                KEY `idx_log_chat_comp_user_comp_depart_id_current_id` (`comp_user_comp_depart_id_current`),
                KEY `idx_log_chat_status` (`status`),
                KEY `idx_log_chat_update_status_in_progress` (`update_status_in_progress`),
                KEY `idx_log_chat_update_status_closed_resolved` (`update_status_closed_resolved`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('log_chat');
    }
}
