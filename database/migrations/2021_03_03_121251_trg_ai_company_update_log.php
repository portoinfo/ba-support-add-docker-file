<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class TrgAiCompanyUpdateLog extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared("DROP TRIGGER IF EXISTS `ba_support`.`trg_ai_company_update_log`;

        DELIMITER $$
        USE `ba_support`$$
        CREATE DEFINER = CURRENT_USER TRIGGER `ba_support`.`trg_ai_company_update_log` AFTER INSERT ON `company` FOR EACH ROW
        BEGIN
            INSERT INTO `ba_support`.`log_company`
            (
                `action`,
                `company_id`,
                `name`,
                `description`,
                `address`,
                `logo`,
                `hash_code`,
                `api_token`
            )
            VALUES
            (
                'INSERT',
                NEW.id,
                NEW.name,
                NEW.description,
                NEW.address,
                NEW.logo,
                NEW.hash_code,
                NEW.api_token
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
        DB::unprepared("DROP TRIGGER IF EXISTS `ba_support`.`trg_ai_company_update_log`;");
    }
}
