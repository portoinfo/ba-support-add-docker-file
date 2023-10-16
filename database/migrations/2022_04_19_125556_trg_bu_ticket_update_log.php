<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class TrgBuTicketUpdateLog extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared("
        DROP TRIGGER IF EXISTS `trg_bu_ticket_update_log`;

        CREATE DEFINER = CURRENT_USER TRIGGER `trg_bu_ticket_update_log` BEFORE UPDATE ON `ticket` FOR EACH ROW
        BEGIN
            DECLARE v_action VARCHAR(10);
            
            IF (OLD.deleted_at IS NULL AND NEW.deleted_at IS NOT NULL) THEN
                SET v_action = 'DELETE';
            ELSEIF (OLD.deleted_at IS NOT NULL AND NEW.deleted_at IS NULL) THEN
                SET v_action = 'RECOVER';
            ELSE
                SET v_action = 'UPDATE';
            END IF;

            IF (COALESCE(NEW.company_department_id, 0) != COALESCE(OLD.company_department_id, 0) OR COALESCE(NEW.description, '') != COALESCE(OLD.description, '')
                    OR COALESCE(NEW.comments, '') != COALESCE(OLD.comments, '') OR COALESCE(NEW.`status`, '') != COALESCE(OLD.`status`, '')
                    OR COALESCE(NEW.`type`, '') != COALESCE(OLD.`type`, '') OR COALESCE(NEW.priority, '') != COALESCE(OLD.priority, '')
                    OR COALESCE(NEW.update_status_in_progress, 0) != COALESCE(OLD.update_status_in_progress, 0) OR COALESCE(NEW.update_status_closed_resolved, 0) != COALESCE(OLD.update_status_closed_resolved, 0)
                    OR COALESCE(NEW.update_status_canceled, 0) != COALESCE(OLD.update_status_canceled, 0) OR COALESCE(NEW.deleted_at, 0) != COALESCE(OLD.deleted_at, 0)) THEN
                INSERT INTO `log_ticket`
                (
                    `action`,
                    `ticket_id`,
                    `company_department_id`,
                    `description`,
                    `comments`,
                    `status`,
                    `type`,
                    `priority`,
                    `update_status_in_progress`,
                    `update_status_closed_resolved`,
                    `update_status_canceled`
                )
                VALUES
                (
                    v_action,
                    NEW.id,
                    NEW.company_department_id,
                    NEW.description,
                    NEW.comments,
                    NEW.`status`,
                    NEW.`type`,
                    NEW.priority,
                    NEW.update_status_in_progress,
                    NEW.update_status_closed_resolved,
                    NEW.update_status_canceled
                );				
            END IF;
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
        Schema::dropIfExists('trg_bu_ticket_update_log');
    }
}
