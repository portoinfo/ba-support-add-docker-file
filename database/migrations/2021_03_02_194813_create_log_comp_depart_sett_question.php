<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateLogCompDepartSettQuestion extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared("CREATE TABLE `log_comp_depart_sett_question` (
            `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
            `action` enum('INSERT','UPDATE','DELETE','RECOVER') DEFAULT NULL,
            `comp_depart_sett_question_id` int(10) unsigned DEFAULT NULL,
            `company_department_id` int(10) unsigned DEFAULT NULL,
            `question` text DEFAULT NULL,
            `type` enum('TEXT','SELECT','CHECK') DEFAULT NULL,
            `mandatory` tinyint(4) DEFAULT NULL,
            `active` tinyint(4) DEFAULT NULL,
            `created_at` timestamp NULL DEFAULT current_timestamp(),
            PRIMARY KEY (`id`),
            KEY `idx_log_comp_depart_sett_question_id` (`comp_depart_sett_question_id`),
            KEY `idx_log_comp_depart_sett_comp_depart_id` (`company_department_id`),
            KEY `idx_log_comp_depart_sett_created_at` (`created_at`)
          ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('log_comp_depart_sett_question');
    }
}
