<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateLogCompanyUserCompanyDepartment extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared("CREATE TABLE `log_company_user_company_department` (
            `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
            `action` enum('INSERT','UPDATE','DELETE','RECOVER') DEFAULT NULL,
            `company_user_company_department_id` int(10) unsigned DEFAULT NULL,
            `company_user_id` bigint(20) unsigned DEFAULT NULL,
            `company_department_id` int(10) unsigned DEFAULT NULL,
            `created_at` timestamp NULL DEFAULT current_timestamp(),
            PRIMARY KEY (`id`),
            KEY `idx_log_comp_user_comp_depart_user_depart_id` (`company_user_company_department_id`),
            KEY `idx_log_comp_user_comp_depart_company_user_id` (`company_user_id`),
            KEY `idx_log_comp_user_comp_depart_created_at` (`created_at`)
          ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('log_company_user_company_department');
    }
}
