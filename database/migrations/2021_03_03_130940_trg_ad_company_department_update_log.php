<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class TrgAdCompanyDepartmentUpdateLog extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared("DROP TRIGGER IF EXISTS `ba_support`.`trg_ad_company_department_update_log`;

        DELIMITER $$
        USE `ba_support`$$
        CREATE DEFINER = CURRENT_USER TRIGGER `ba_support`.`trg_ad_company_department_update_log` AFTER DELETE ON `company_department` FOR EACH ROW
        BEGIN
            INSERT INTO `ba_support`.`log_company_department`
            (
                `action`,
                `company_department_id`,
                `company_id`,
                `company_user_id`,
                `name`,
                `description`,
                `module`,
                `has_robot`,
                `is_active`
            )
            VALUES
            (
                'DELETE',
                OLD.id,
                OLD.company_id,
                OLD.company_user_id,
                OLD.name,
                OLD.description,
                OLD.module,
                OLD.has_robot,
                OLD.is_active
            );
        END$$
        DELIMITER ;
        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared("DROP TRIGGER IF EXISTS `ba_support`.`trg_ad_company_department_update_log`;");
    }
}
