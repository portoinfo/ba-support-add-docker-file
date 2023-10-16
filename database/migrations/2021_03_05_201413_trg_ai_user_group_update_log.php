<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class TrgAiUserGroupUpdateLog extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared("DROP TRIGGER IF EXISTS `ba_support`.`trg_ai_user_group_update_log`;

        DELIMITER $$
        USE `ba_support`$$
        CREATE DEFINER = CURRENT_USER TRIGGER `ba_support`.`trg_ai_user_group_update_log` AFTER INSERT ON `user_group` FOR EACH ROW
        BEGIN
            INSERT INTO `ba_support`.`log_user_group`
            (
                `action`,
                `user_group_id`,
                `company_id`,
                `name`,
                `description`,
                `is_active`,
                `settings`
            )
            VALUES
            (
                'INSERT',
                NEW.id,
                NEW.company_id,
                NEW.name,
                NEW.description,
                NEW.is_active,
                NEW.settings
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
        DB::unprepared("DROP TRIGGER IF EXISTS `ba_support`.`trg_ai_user_group_update_log`;");
    }
}
