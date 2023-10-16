<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class TrgAdUserAuthUpdateLog extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared("DROP TRIGGER IF EXISTS `ba_support`.`trg_ad_user_auth_update_log`;

        DELIMITER $$
        USE `ba_support`$$
        CREATE DEFINER = CURRENT_USER TRIGGER `ba_support`.`trg_ad_user_auth_update_log` AFTER DELETE ON `user_auth` FOR EACH ROW
        BEGIN
            INSERT INTO `ba_support`.`log_user_auth`
            (
                `action`,
                `user_auth_id`,
                `subsidiary_id`,
                `name`,
                `email`,
                `email_verified_at`,
                `phone`,
                `language`,
                `hash_code`,
                `can_create_company`
            )
            VALUES
            (
                'DELETE',
                OLD.id,
                OLD.subsidiary_id,
                OLD.name,
                OLD.email,
                OLD.email_verified_at,
                OLD.phone,
                OLD.language,
                OLD.hash_code,
                OLD.can_create_company
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
        DB::unprepared("DROP TRIGGER IF EXISTS `ba_support`.`trg_ad_user_auth_update_log`;");
    }
}
