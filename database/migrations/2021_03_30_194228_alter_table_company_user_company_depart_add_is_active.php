<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableCompanyUserCompanyDepartAddIsActive extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = "ALTER TABLE `ba_support`.`company_user_company_department` 
                ADD COLUMN `is_active` TINYINT UNSIGNED NOT NULL DEFAULT 1 AFTER `company_department_id`;";
        DB::unprepared($sql);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $sql = "ALTER TABLE `ba_support`.`company_user_company_department` DROP COLUMN is_active";
        DB::unprepared($sql);
    }
}
