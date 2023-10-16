<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class TrgAiTicketUpdateLog extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared("
        DROP TRIGGER IF EXISTS `trg_ai_ticket_update_log`;

        CREATE DEFINER=CURRENT_USER TRIGGER `trg_ai_ticket_update_log` AFTER INSERT ON `ticket` FOR EACH ROW
        BEGIN
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
                'INSERT',
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
        Schema::dropIfExists('trg_ai_ticket_update_log');
    }
}
