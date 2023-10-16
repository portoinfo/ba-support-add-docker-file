<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class TrgAiCompDepartSettUpdateLog extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared("DROP TRIGGER IF EXISTS `ba_support`.`trg_ai_comp_depart_sett_update_log`;

        DELIMITER $$
        USE `ba_support`$$
        CREATE DEFINER = CURRENT_USER TRIGGER `ba_support`.`trg_ai_comp_depart_sett_update_log` AFTER INSERT ON `company_department_settings` FOR EACH ROW
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
                'INSERT',
                NEW.id,
                NEW.company_department_id,
                NEW.settings
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
        DB::unprepared("DROP TRIGGER IF EXISTS `ba_support`.`trg_ai_comp_depart_sett_update_log`;");
    }
}
