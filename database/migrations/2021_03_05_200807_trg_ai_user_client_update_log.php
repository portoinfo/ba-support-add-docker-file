<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class TrgAiUserClientUpdateLog extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared("DROP TRIGGER IF EXISTS `ba_support`.`trg_ai_user_client_update_log`;

        DELIMITER $$
        USE `ba_support`$$
        CREATE DEFINER = CURRENT_USER TRIGGER `ba_support`.`trg_ai_user_client_update_log` AFTER INSERT ON `user_client` FOR EACH ROW
        BEGIN
            INSERT INTO `ba_support`.`log_user_client`
            (
                `action`,
                `user_client_id`,
                `user_auth_id`
            )
            VALUES
            (
                'INSERT',
                NEW.id,
                NEW.user_auth_id
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
        DB::unprepared("DROP TRIGGER IF EXISTS `ba_support`.`trg_ai_user_client_update_log`;");
    }
}
