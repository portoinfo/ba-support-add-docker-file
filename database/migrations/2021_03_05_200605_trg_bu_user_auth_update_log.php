<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class TrgBuUserAuthUpdateLog extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared("DROP TRIGGER IF EXISTS `ba_support`.`trg_bu_user_auth_update_log`;

        DELIMITER $$
        USE `ba_support`$$
        CREATE DEFINER = CURRENT_USER TRIGGER `ba_support`.`trg_bu_user_auth_update_log` BEFORE UPDATE ON `user_auth` FOR EACH ROW
        BEGIN
            DECLARE v_action VARCHAR(10);
            
            IF (OLD.deleted_at IS NULL AND NEW.deleted_at IS NOT NULL) THEN
                SET v_action = 'DELETE';
            ELSEIF (OLD.deleted_at IS NOT NULL AND NEW.deleted_at IS NULL) THEN
                SET v_action = 'RECOVER';
            ELSE
                SET v_action = 'UPDATE';
            END IF;
        
            IF (COALESCE(NEW.subsidiary_id, 0) != COALESCE(OLD.subsidiary_id, 0) OR COALESCE(NEW.name, '') != COALESCE(OLD.name, '')
                    OR COALESCE(NEW.email, '') != COALESCE(OLD.email, '') OR COALESCE(NEW.email_verified_at, 0) != COALESCE(OLD.email_verified_at, 0)
                    OR COALESCE(NEW.phone, '') != COALESCE(OLD.phone, '') OR COALESCE(NEW.language, '') != COALESCE(OLD.language, '')
                    OR COALESCE(NEW.hash_code, '') != COALESCE(OLD.hash_code, '') OR COALESCE(NEW.can_create_company, 0) != COALESCE(OLD.can_create_company, 0)
                    OR COALESCE(NEW.deleted_at, 0) != COALESCE(OLD.deleted_at, 0)) THEN
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
                    v_action,
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
            END IF;
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
        DB::unprepared("DROP TRIGGER IF EXISTS `ba_support`.`trg_bu_user_auth_update_log`;");
    }
}
