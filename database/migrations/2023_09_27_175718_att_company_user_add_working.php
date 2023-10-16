<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AttCompanyUserAddWorking extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared("
            ALTER TABLE `company_user` 
            ADD COLUMN `opening_hours` TEXT NULL DEFAULT NULL AFTER `is_active`,
            ADD COLUMN `time_zone` VARCHAR(50) NULL DEFAULT NULL AFTER `opening_hours`,
            ADD COLUMN `is_working` TINYINT UNSIGNED NULL DEFAULT 0 AFTER `config`;


            ALTER TABLE `log_company_user` 
            ADD COLUMN `opening_hours` TEXT NULL DEFAULT NULL AFTER `is_active`,
            ADD COLUMN `time_zone` VARCHAR(50) NULL DEFAULT NULL AFTER `opening_hours`,
            ADD COLUMN `is_working` TINYINT UNSIGNED NULL DEFAULT NULL AFTER `last_login`;

            CREATE TABLE `log_company_user_working` (
                `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                `company_user_id` bigint(20) unsigned DEFAULT NULL,
                `is_working` tinyint(3) unsigned DEFAULT NULL,
                `created_at` timestamp NULL DEFAULT current_timestamp(),
                PRIMARY KEY (`id`),
                KEY `idx_log_comp_user_work_comp_user_id` (`company_user_id`)
              ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
              
              DROP TRIGGER IF EXISTS `trg_ai_company_user_update_log`;


            CREATE DEFINER=CURRENT_USER TRIGGER `trg_ai_company_user_update_log` AFTER INSERT ON `company_user` FOR EACH ROW
            BEGIN
                INSERT INTO `log_company_user` 
                (
                    `action`,
                    `company_user_id`,
                    `company_id`,
                    `user_auth_id`,
                    `is_admin`,
                    `is_active`,
                    `opening_hours`,
                    `time_zone`,
                    `status`,
                    `last_login`
                )
                VALUES
                (
                    'INSERT',
                    NEW.id,
                    NEW.company_id,
                    NEW.user_auth_id,
                    NEW.is_admin,
                    NEW.is_active,
                    NEW.opening_hours,
                    NEW.time_zone,
                    NEW.status,
                    NEW.last_login
                );
            END;

            DROP TRIGGER IF EXISTS `trg_bu_company_user_update_log`;
            CREATE DEFINER=CURRENT_USER TRIGGER `trg_bu_company_user_update_log` BEFORE UPDATE ON `company_user` FOR EACH ROW
            BEGIN
                DECLARE v_action VARCHAR(10);
                
                IF (OLD.deleted_at IS NULL AND NEW.deleted_at IS NOT NULL) THEN
                    SET v_action = 'DELETE';
                ELSEIF (OLD.deleted_at IS NOT NULL AND NEW.deleted_at IS NULL) THEN
                    SET v_action = 'RECOVER';
                ELSE
                    SET v_action = 'UPDATE';
                END IF;

                IF (COALESCE(NEW.company_id, 0) != COALESCE(OLD.company_id, 0) OR COALESCE(NEW.user_auth_id, 0) != COALESCE(OLD.user_auth_id, 0)
                                OR COALESCE(NEW.is_admin, 0) != COALESCE(OLD.is_admin, 0) OR COALESCE(NEW.is_active, '') != COALESCE(OLD.is_active, '')
                                OR COALESCE(NEW.opening_hours, 0) != COALESCE(OLD.opening_hours, 0) OR COALESCE(NEW.time_zone, '') != COALESCE(OLD.time_zone, '')
                                OR COALESCE(NEW.`status`, '') != COALESCE(OLD.`status`, '') OR COALESCE(NEW.last_login, '') != COALESCE(OLD.last_login, '')) THEN
                    INSERT INTO `log_company_user`
                    (
                        `action`,
                        `company_user_id`,
                        `company_id`,
                        `user_auth_id`,
                        `is_admin`,
                        `is_active`,
                        `opening_hours`,
                        `time_zone`,
                        `status`,
                        `last_login`
                    )
                    VALUES
                    (
                        v_action,
                        NEW.id,
                        NEW.company_id,
                        NEW.user_auth_id,
                        NEW.is_admin,
                        NEW.is_active,
                        NEW.opening_hours,
                        NEW.time_zone,
                        NEW.status,
                        NEW.last_login
                    );
                END IF;
                
                IF (COALESCE(NEW.is_working, 0) != COALESCE(OLD.is_working, 0)) THEN
                    INSERT INTO `log_company_user_working`
                    (
                        `company_user_id`,
                        `is_working`
                    )
                    VALUES
                    (
                        NEW.id,
                        NEW.is_working
                    );
                END IF;
            END;
                    
            DROP TRIGGER IF EXISTS `trg_ad_company_user_update_log`;
            CREATE DEFINER=CURRENT_USER TRIGGER `trg_ad_company_user_update_log` AFTER DELETE ON `company_user` FOR EACH ROW
            BEGIN
                INSERT INTO `log_company_user`
                (
                    `action`,
                    `company_user_id`,
                    `company_id`,
                    `user_auth_id`,
                    `is_admin`,
                    `is_active`,
                    `opening_hours`,
                    `time_zone`,
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
                    OLD.opening_hours,
                    OLD.time_zone,
                    OLD.status,
                    OLD.last_login
                );
            END;
        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
