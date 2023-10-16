<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class TrgAiCompanyUserUpdateLog extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared("DROP TRIGGER IF EXISTS `ba_support`.`trg_ai_company_user_update_log`;

        DELIMITER $$
        USE `ba_support`$$
        CREATE DEFINER=CURRENT_USER TRIGGER `ba_support`.`trg_ai_company_user_update_log` AFTER INSERT ON `company_user` FOR EACH ROW
        BEGIN
            INSERT INTO `ba_support`.`log_company_user` 
            (
                `action`,
                `company_user_id`,
                `company_id`,
                `user_auth_id`,
                `is_admin`,
                `is_active`,
                `status`,
                `last_login`
            )
            VALUES
            (
                'INSERT',
                NEW.id,
                NEW.company_id,
                NEW.user_auth_id,
                NEW.is_admin,
                NEW.is_active,
                NEW.status,
                NEW.last_login
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
        DB::unprepared("DROP TRIGGER IF EXISTS `ba_support`.`trg_ai_company_user_update_log`;");
    }
}
