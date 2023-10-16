<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class NewLogTicket extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared("
            CREATE TABLE `log_ticket` (
                `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                `action` enum('INSERT','UPDATE','DELETE','RECOVER') DEFAULT NULL,
                `ticket_id` int(10) unsigned NULL,
                `company_department_id` int(10) unsigned DEFAULT NULL,
                `description` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                `comments` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                `status` enum('OPENED','IN_PROGRESS','CLOSED','RESOLVED','CANCELED') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                `type` enum('DEFAULT','TRANSFERED') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                `priority` enum('NORMAL','MEDIUM','HIGH') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                `update_status_in_progress` datetime DEFAULT NULL,
                `update_status_closed_resolved` datetime DEFAULT NULL,
                `update_status_canceled` datetime DEFAULT NULL,
                `created_at` timestamp NULL DEFAULT current_timestamp(),
                PRIMARY KEY (`id`),
                KEY `idx_log_ticket_ticket_id` (`ticket_id`),
                KEY `idx_log_ticket_company_department_id` (`company_department_id`),
                KEY `idx_log_ticket_update_status_in_progress` (`update_status_in_progress`),
                KEY `idx_log_ticket_update_status_closed_resolved` (`update_status_closed_resolved`)
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
        Schema::dropIfExists('log_ticket');
    }
}
