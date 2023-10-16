<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class TrgBuCompanySettingsUpdateLog extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared("DROP TRIGGER IF EXISTS `ba_support`.`trg_bu_company_settings_update_log`;

        DELIMITER $$
        USE `ba_support`$$
        CREATE DEFINER = CURRENT_USER TRIGGER `ba_support`.`trg_bu_company_settings_update_log` BEFORE UPDATE ON `company_settings` FOR EACH ROW
        BEGIN
            DECLARE v_action VARCHAR(10);
            
            IF (OLD.deleted_at IS NULL AND NEW.deleted_at IS NOT NULL) THEN
                SET v_action = 'DELETE';
            ELSEIF (OLD.deleted_at IS NOT NULL AND NEW.deleted_at IS NULL) THEN
                SET v_action = 'RECOVER';
            ELSE
                SET v_action = 'UPDATE';
            END IF;
        
            INSERT INTO `ba_support`.`log_company_settings`
            (
                `action`,
                `company_settings_id`,
                `company_id`,
                `settings_chat`,
                `settings_ticket`,
                `released_domain`,
                `blocked_domain`,
                `general`
            )
            VALUES
            (
                v_action,
                NEW.id,
                NEW.company_id,
                NEW.settings_chat,
                NEW.settings_ticket,
                NEW.released_domain,
                NEW.blocked_domain,
                NEW.general
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
        DB::unprepared("DROP TRIGGER IF EXISTS `ba_support`.`trg_bu_company_settings_update_log`;");
    }
}
