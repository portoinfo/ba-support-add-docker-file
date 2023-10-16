<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class TrgBuChatUpdateLog extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared("
        DROP TRIGGER IF EXISTS `trg_bu_chat_update_log`;

        CREATE DEFINER = CURRENT_USER TRIGGER `trg_bu_chat_update_log` BEFORE UPDATE ON `chat` FOR EACH ROW
        BEGIN
            DECLARE v_action VARCHAR(10);
            
            IF (OLD.deleted_at IS NULL AND NEW.deleted_at IS NOT NULL) THEN
                SET v_action = 'DELETE';
            ELSEIF (OLD.deleted_at IS NOT NULL AND NEW.deleted_at IS NULL) THEN
                SET v_action = 'RECOVER';
            ELSE
                SET v_action = 'UPDATE';
            END IF;

            IF (COALESCE(NEW.company_department_id, 0) != COALESCE(OLD.company_department_id, 0) OR COALESCE(NEW.comp_user_comp_depart_id_current, 0) != COALESCE(OLD.comp_user_comp_depart_id_current, 0)
                    OR COALESCE(NEW.ticket_id, 0) != COALESCE(OLD.ticket_id, 0) OR COALESCE(NEW.`type`, '') != COALESCE(OLD.`type`, '')
                    OR COALESCE(NEW.`status`, '') != COALESCE(OLD.`status`, '') OR COALESCE(NEW.priority, '') != COALESCE(OLD.priority, '')
                    OR COALESCE(NEW.update_status_in_progress, 0) != COALESCE(OLD.update_status_in_progress, 0) OR COALESCE(NEW.update_status_closed_resolved, 0) != COALESCE(OLD.update_status_closed_resolved, 0)
                    OR COALESCE(NEW.update_status_canceled, 0) != COALESCE(OLD.update_status_canceled, 0) OR COALESCE(NEW.changed_to_ticket_at, 0) != COALESCE(OLD.changed_to_ticket_at, 0)
                    OR COALESCE(NEW.deleted_at, 0) != COALESCE(OLD.deleted_at, 0)) THEN
                INSERT INTO `log_chat`
                (
                    `action`,
                    `chat_id`,
                    `company_department_id`,
                    `comp_user_comp_depart_id_current`,
                    `ticket_id`,
                    `type`,
                    `changed_to_ticket_at`,
                    `status`,
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
                    NEW.comp_user_comp_depart_id_current,
                    NEW.ticket_id,
                    NEW.`type`,
                    NEW.`changed_to_ticket_at`,
                    NEW.`status`,
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
        Schema::dropIfExists('trg_bu_chat_update_log');
    }
}
