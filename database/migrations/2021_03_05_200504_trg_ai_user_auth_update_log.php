<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class TrgAiUserAuthUpdateLog extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared("DROP TRIGGER IF EXISTS `ba_support`.`trg_ai_user_auth_update_log`;

        DELIMITER $$
        USE `ba_support`$$
        CREATE DEFINER = CURRENT_USER TRIGGER `ba_support`.`trg_ai_user_auth_update_log` AFTER INSERT ON `user_auth` FOR EACH ROW
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
                'INSERT',
                NEW.id,
                NEW.subsidiary_id,
                NEW.name,
                NEW.email,
                NEW.email_verified_at,
                NEW.phone,
                NEW.language,
                NEW.hash_code,
                NEW.can_create_company
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
        DB::unprepared("DROP TRIGGER IF EXISTS `ba_support`.`trg_ai_user_auth_update_log`;");
    }
}
