<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateLogUserAuth extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared("CREATE TABLE `log_user_auth` (
            `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            `action` enum('INSERT','UPDATE','DELETE','RECOVER') DEFAULT NULL,
            `user_auth_id` bigint(20) unsigned DEFAULT NULL,
            `subsidiary_id` int(10) unsigned DEFAULT NULL,
            `name` varchar(100) DEFAULT NULL,
            `email` varchar(255) DEFAULT NULL,
            `email_verified_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
            `phone` varchar(20) DEFAULT NULL,
            `language` varchar(5) DEFAULT NULL,
            `hash_code` varchar(255) DEFAULT NULL,
            `can_create_company` tinyint(4) DEFAULT NULL,
            `created_at` timestamp NULL DEFAULT current_timestamp(),
            PRIMARY KEY (`id`),
            KEY `idx_log_user_auth_user_auth_id` (`user_auth_id`),
            KEY `idx_log_user_auth_subsidiary_id` (`subsidiary_id`),
            KEY `idx_log_user_auth_created_at` (`created_at`)
          ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('log_user_auth');
    }
}
