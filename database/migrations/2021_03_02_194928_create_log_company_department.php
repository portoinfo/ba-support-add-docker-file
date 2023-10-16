<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateLogCompanyDepartment extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared("CREATE TABLE `log_company_department` (
            `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
            `action` enum('INSERT','UPDATE','DELETE','RECOVER') DEFAULT NULL,
            `company_department_id` int(10) unsigned DEFAULT NULL,
            `company_id` int(10) unsigned DEFAULT NULL,
            `company_user_id` bigint(20) unsigned DEFAULT NULL,
            `name` varchar(50) DEFAULT NULL,
            `description` varchar(255) DEFAULT NULL,
            `module` enum('CHAT','TICKET','ALL') DEFAULT NULL,
            `has_robot` tinyint(4) DEFAULT NULL,
            `is_active` tinyint(4) DEFAULT NULL,
            `created_at` timestamp NULL DEFAULT current_timestamp(),
            PRIMARY KEY (`id`),
            KEY `idx_log_comp_depart_company_depart_id` (`company_department_id`),
            KEY `idx_log_comp_depart_company_id` (`company_id`),
            KEY `idx_log_comp_depart_company_user_id` (`company_user_id`),
            KEY `idx_log_comp_depart_created_at` (`created_at`)
          ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('log_company_department');
    }
}
