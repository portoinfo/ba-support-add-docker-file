<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateLogUserGroup extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared("CREATE TABLE `log_user_group` (
            `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
            `action` enum('INSERT','UPDATE','DELETE','RECOVER') DEFAULT NULL,
            `user_group_id` int(10) unsigned DEFAULT NULL,
            `company_id` int(10) unsigned DEFAULT NULL,
            `name` varchar(45) DEFAULT NULL,
            `description` varchar(80) DEFAULT NULL,
            `is_active` tinyint(4) DEFAULT NULL,
            `settings` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
            `created_at` timestamp NULL DEFAULT current_timestamp(),
            PRIMARY KEY (`id`),
            KEY `idx_log_user_group_user_group_id` (`user_group_id`),
            KEY `idx_log_user_group_company_id` (`company_id`),
            KEY `idx_log_user_group_created_at` (`created_at`)
          ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('log_user_group');
    }
}
