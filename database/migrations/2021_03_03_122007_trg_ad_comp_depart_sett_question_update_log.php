<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class TrgAdCompDepartSettQuestionUpdateLog extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared("DROP TRIGGER IF EXISTS `ba_support`.`trg_ad_comp_depart_sett_question_update_log`;

        DELIMITER $$
        USE `ba_support`$$
        CREATE DEFINER = CURRENT_USER TRIGGER `ba_support`.`trg_ad_comp_depart_sett_question_update_log` AFTER DELETE ON `company_depart_settings_question` FOR EACH ROW
        BEGIN
            INSERT INTO `ba_support`.`log_comp_depart_sett_question`
            (
                `action`,
                `comp_depart_sett_question_id`,
                `company_department_id`,
                `question`,
                `type`,
                `mandatory`,
                `active`
            )
            VALUES
            (
                'DELETE',
                OLD.id,
                OLD.company_department_id,
                OLD.question,
                OLD.type,
                OLD.mandatory,
                OLD.active
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
        DB::unprepared("DROP TRIGGER IF EXISTS `ba_support`.`trg_ad_comp_depart_sett_question_update_log`;");
    }
}
