<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class TrgAdChatUpdateLog extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared("
        DROP TRIGGER IF EXISTS `trg_ad_chat_update_log`;

        CREATE DEFINER = CURRENT_USER TRIGGER `trg_ad_chat_update_log` AFTER DELETE ON `chat` FOR EACH ROW
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
                'DELETE',
                OLD.id,
                OLD.company_department_id,
                OLD.comp_user_comp_depart_id_current,
                OLD.ticket_id,
                OLD.`type`,
                OLD.`changed_to_ticket_at`,
                OLD.`status`,
                OLD.priority,
                OLD.update_status_in_progress,
                OLD.update_status_closed_resolved,
                OLD.update_status_canceled
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
        Schema::dropIfExists('trg_ad_chat_update_log');
    }
}
