<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateLogCompany extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared("CREATE TABLE `log_company` (
            `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
            `action` enum('INSERT','UPDATE','DELETE','RECOVER') DEFAULT NULL,
            `company_id` int(10) unsigned DEFAULT NULL,
            `name` varchar(100) DEFAULT NULL,
            `description` varchar(250) DEFAULT NULL,
            `address` varchar(250) DEFAULT NULL,
            `logo` varchar(250) DEFAULT NULL,
            `hash_code` varchar(255) DEFAULT NULL,
            `api_token` varchar(255) DEFAULT NULL,
            `created_at` timestamp NULL DEFAULT current_timestamp(),
            PRIMARY KEY (`id`),
            KEY `idx_log_company_company_id` (`company_id`),
            KEY `idx_log_company_created_at` (`created_at`)
          ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('log_company');
    }
}
