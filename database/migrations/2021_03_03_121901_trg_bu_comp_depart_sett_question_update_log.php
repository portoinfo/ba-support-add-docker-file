<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class TrgBuCompDepartSettQuestionUpdateLog extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared("DROP TRIGGER IF EXISTS `ba_support`.`trg_bu_comp_depart_sett_question_update_log`;

        DELIMITER $$
        USE `ba_support`$$
        CREATE DEFINER = CURRENT_USER TRIGGER `ba_support`.`trg_bu_comp_depart_sett_question_update_log` BEFORE UPDATE ON `company_depart_settings_question` FOR EACH ROW
        BEGIN
            DECLARE v_action VARCHAR(10);
            
            IF (OLD.deleted_at IS NULL AND NEW.deleted_at IS NOT NULL) THEN
                SET v_action = 'DELETE';
            ELSEIF (OLD.deleted_at IS NOT NULL AND NEW.deleted_at IS NULL) THEN
                SET v_action = 'RECOVER';
            ELSE
                SET v_action = 'UPDATE';
            END IF;
        
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
                v_action,
                NEW.id,
                NEW.company_department_id,
                NEW.question,
                NEW.type,
                NEW.mandatory,
                NEW.active
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
        DB::unprepared("DROP TRIGGER IF EXISTS `ba_support`.`trg_bu_comp_depart_sett_question_update_log`;");
    }
}
