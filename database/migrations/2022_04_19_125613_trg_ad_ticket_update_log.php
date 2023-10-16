<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class TrgAdTicketUpdateLog extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared("
        DROP TRIGGER IF EXISTS `trg_ad_ticket_update_log`;

        CREATE DEFINER = CURRENT_USER TRIGGER `trg_ad_ticket_update_log` AFTER DELETE ON `ticket` FOR EACH ROW
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
                'DELETE',
                OLD.id,
                OLD.company_department_id,
                OLD.description,
                OLD.comments,
                OLD.`status`,
                OLD.`type`,
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
        Schema::dropIfExists('trg_ad_ticket_update_log');
    }
}
