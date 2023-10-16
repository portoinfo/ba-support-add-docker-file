<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class TrgAdCompanySettingsUpdateLog extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared("DROP TRIGGER IF EXISTS `ba_support`.`trg_ad_company_settings_update_log`;

        DELIMITER $$
        USE `ba_support`$$
        CREATE DEFINER = CURRENT_USER TRIGGER `ba_support`.`trg_ad_company_settings_update_log` AFTER DELETE ON `company_settings` FOR EACH ROW
        BEGIN
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
                'DELETE',
                OLD.id,
                OLD.company_id,
                OLD.settings_chat,
                OLD.settings_ticket,
                OLD.released_domain,
                OLD.blocked_domain,
                OLD.general
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
        DB::unprepared("DROP TRIGGER IF EXISTS `ba_support`.`trg_ad_company_settings_update_log`;");
    }
}
