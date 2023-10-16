<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateLogUserClient extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared("CREATE TABLE `log_user_client` (
            `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            `action` enum('INSERT','UPDATE','DELETE','RECOVER') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
            `user_client_id` bigint(20) unsigned DEFAULT NULL,
            `user_auth_id` bigint(20) unsigned DEFAULT NULL,
            `created_at` timestamp NULL DEFAULT current_timestamp(),
            PRIMARY KEY (`id`),
            KEY `idx_log_user_client_user_client_id` (`user_client_id`),
            KEY `idx_log_user_client_user_auth_id` (`user_auth_id`),
            KEY `idx_log_user_client_created_at` (`created_at`)
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
        Schema::dropIfExists('log_user_client');
    }
}
