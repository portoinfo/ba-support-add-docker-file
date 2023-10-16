<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class TrgBuCompUserCompDepartUpdateLog extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared("DROP TRIGGER IF EXISTS `ba_support`.`trg_bu_comp_user_comp_depart_update_log`;

        DELIMITER $$
        USE `ba_support`$$
        CREATE DEFINER = CURRENT_USER TRIGGER `ba_support`.`trg_bu_comp_user_comp_depart_update_log` BEFORE UPDATE ON `company_user_company_department` FOR EACH ROW
        BEGIN
            INSERT INTO `ba_support`.`log_company_user_company_department`
            (
                `action`,
                `company_user_company_department_id`,
                `company_user_id`,
                `company_department_id`
            )
            VALUES
            (
                'UPDATE',
                NEW.id,
                NEW.company_user_id,
                NEW.company_department_id
            );
        END$$
        DELIMITER ;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared("DROP TRIGGER IF EXISTS `ba_support`.`trg_bu_comp_user_comp_depart_update_log`;");
    }
}
