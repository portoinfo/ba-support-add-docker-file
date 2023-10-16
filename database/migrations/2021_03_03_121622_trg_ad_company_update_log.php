<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class TrgAdCompanyUpdateLog extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared("DROP TRIGGER IF EXISTS `ba_support`.`trg_ad_company_update_log`;

        DELIMITER $$
        USE `ba_support`$$
        CREATE DEFINER = CURRENT_USER TRIGGER `ba_support`.`trg_ad_company_update_log` AFTER DELETE ON `company` FOR EACH ROW
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
                'DELETE',
                OLD.id,
                OLD.name,
                OLD.description,
                OLD.address,
                OLD.logo,
                OLD.hash_code,
                OLD.api_token
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
        DB::unprepared("DROP TRIGGER IF EXISTS `ba_support`.`trg_ad_company_update_log`;");
    }
}
