<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AttSpLogCucd extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared("
            ALTER TABLE `log_company_user_company_department` 
            ADD COLUMN `is_active` TINYINT NULL DEFAULT NULL AFTER `company_department_id`;

            DROP TRIGGER IF EXISTS `trg_ai_comp_user_comp_depart_update_log`;
            CREATE DEFINER=CURRENT_USER TRIGGER `trg_ai_comp_user_comp_depart_update_log` AFTER INSERT ON `company_user_company_department` FOR EACH ROW
            BEGIN
                INSERT INTO `log_company_user_company_department`
                (
                    `action`,
                    `company_user_company_department_id`,
                    `company_user_id`,
                    `company_department_id`,
                    `is_active`
                )
                VALUES
                (
                    'INSERT',
                    NEW.id,
                    NEW.company_user_id,
                    NEW.company_department_id,
                    NEW.is_active
                );
            END;


            DROP TRIGGER IF EXISTS `trg_bu_comp_user_comp_depart_update_log`;
            CREATE DEFINER=CURRENT_USER TRIGGER `trg_bu_comp_user_comp_depart_update_log` BEFORE UPDATE ON `company_user_company_department` FOR EACH ROW
            BEGIN
                INSERT INTO `log_company_user_company_department`
                (
                    `action`,
                    `company_user_company_department_id`,
                    `company_user_id`,
                    `company_department_id`,
                    `is_active`
                )
                VALUES
                (
                    'UPDATE',
                    NEW.id,
                    NEW.company_user_id,
                    NEW.company_department_id,
                    NEW.is_active
                );
            END;
        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Schema::dropIfExists('chat_category');
    }
}
