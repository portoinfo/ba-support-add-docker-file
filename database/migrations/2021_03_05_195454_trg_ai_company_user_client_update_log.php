<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class TrgAiCompanyUserClientUpdateLog extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared("DROP TRIGGER IF EXISTS `ba_support`.`trg_ai_company_user_client_update_log`;

        DELIMITER $$
        USE `ba_support`$$
        CREATE DEFINER = CURRENT_USER TRIGGER `ba_support`.`trg_ai_company_user_client_update_log` AFTER INSERT ON `company_user_client` FOR EACH ROW
        BEGIN
            INSERT INTO `ba_support`.`log_company_user_client`
            (
                `action`,
                `user_client_id`,
                `company_id`,
                `last_login`
            )
            VALUES
            (
                'INSERT',
                NEW.user_client_id,
                NEW.company_id,
                NEW.last_login
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
        DB::unprepared("DROP TRIGGER IF EXISTS `ba_support`.`trg_ai_company_user_client_update_log`;");
    }
}
