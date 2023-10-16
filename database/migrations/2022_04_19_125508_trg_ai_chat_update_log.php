<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class TrgAiChatUpdateLog extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared("
            DROP TRIGGER IF EXISTS `trg_ai_chat_update_log`;
            
            CREATE DEFINER=CURRENT_USER TRIGGER `trg_ai_chat_update_log` AFTER INSERT ON `chat` FOR EACH ROW
            BEGIN
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
                    'INSERT',
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
        Schema::dropIfExists('trg_ai_chat_update_log');
    }
}
