<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class TrgAdCompDepartSettUpdateLog extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared("DROP TRIGGER IF EXISTS `ba_support`.`trg_ad_comp_depart_sett_update_log`;

        DELIMITER $$
        USE `ba_support`$$
        CREATE DEFINER=`ba_support`@`%` TRIGGER `ba_support`.`trg_ad_comp_depart_sett_update_log` AFTER DELETE ON `company_department_settings` FOR EACH ROW
        BEGIN
            INSERT INTO `ba_support`.`log_comp_depart_settings`
            (
                `action`,
                `company_department_settings_id`,
                `company_department_id`,
                `settings`
            )
            VALUES
            (
                'DELETE',
                OLD.id,
                OLD.company_department_id,
                OLD.settings
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
        DB::unprepared("DROP TRIGGER IF EXISTS `ba_support`.`trg_ad_comp_depart_sett_update_log`;");
    }
}
