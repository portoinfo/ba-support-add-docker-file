<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class TrgAdUserGroupUpdateLog extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared("DROP TRIGGER IF EXISTS `ba_support`.`trg_ad_user_group_update_log`;

        DELIMITER $$
        USE `ba_support`$$
        CREATE DEFINER = CURRENT_USER TRIGGER `ba_support`.`trg_ad_user_group_update_log` AFTER DELETE ON `user_group` FOR EACH ROW
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
                'DELETE',
                OLD.id,
                OLD.company_id,
                OLD.name,
                OLD.description,
                OLD.is_active,
                OLD.settings
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
        DB::unprepared("DROP TRIGGER IF EXISTS `ba_support`.`trg_ad_user_group_update_log`;");
    }
}
