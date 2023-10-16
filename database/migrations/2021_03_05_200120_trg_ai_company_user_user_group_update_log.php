<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class TrgAiCompanyUserUserGroupUpdateLog extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared("DROP TRIGGER IF EXISTS `ba_support`.`trg_ai_company_user_user_group_update_log`;

        DELIMITER $$
        USE `ba_support`$$
        CREATE DEFINER=CURRENT_USER TRIGGER `ba_support`.`trg_ai_company_user_user_group_update_log` AFTER INSERT ON `company_user_user_group` FOR EACH ROW
        BEGIN
            INSERT INTO `ba_support`.`log_company_user_user_group`
            (
                `action`,
                `company_user_id`,
                `user_group_id`
            )
            VALUES
            (
                'INSERT',
                NEW.company_user_id,
                NEW.user_group_id
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
        DB::unprepared("DROP TRIGGER IF EXISTS `ba_support`.`trg_ai_company_user_user_group_update_log`;");
    }
}
