<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class TrgAdCompanyUserUpdateLog extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared("DROP TRIGGER IF EXISTS `ba_support`.`trg_ad_company_user_update_log`;

        DELIMITER $$
        USE `ba_support`$$
        CREATE DEFINER = CURRENT_USER TRIGGER `ba_support`.`trg_ad_company_user_update_log` AFTER DELETE ON `company_user` FOR EACH ROW
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
                'DELETE',
                OLD.id,
                OLD.company_id,
                OLD.user_auth_id,
                OLD.is_admin,
                OLD.is_active,
                OLD.status,
                OLD.last_login
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
        DB::unprepared("DROP TRIGGER IF EXISTS `ba_support`.`trg_ad_company_user_update_log`;");
    }
}
