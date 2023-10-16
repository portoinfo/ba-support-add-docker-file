<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddTableTicketMerge extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared("
            CREATE TABLE `ticket_merge` (
                `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                `ticket_id_origin` int(10) unsigned DEFAULT NULL,
                `ticket_id_merge` int(10) unsigned DEFAULT NULL,
                `created_at` timestamp NULL DEFAULT current_timestamp(),
                `created_by` int(11) DEFAULT NULL,
                PRIMARY KEY (`id`),
                UNIQUE KEY `unique_ticket_merge_ticket_id_merge` (`ticket_id_merge`),
                KEY `idx_ticket_merge_ticket_id_origin` (`ticket_id_origin`),
                CONSTRAINT `fk_ticket_merge_ticket_id_merge` FOREIGN KEY (`ticket_id_merge`) REFERENCES `ticket` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
                CONSTRAINT `fk_ticket_merge_ticket_id_origin` FOREIGN KEY (`ticket_id_origin`) REFERENCES `ticket` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ticket_merge');
    }
}
