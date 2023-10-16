<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTicketComments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ticket_comments', function (Blueprint $table) {
            $table->bigInteger('id', 20);
            $table->integer('ticket_id')->unsigned();
            $table->bigInteger('user_auth_id')->unsigned()->nullable();
            $table->text('comment')->nullable();
            $table->timestamp('created_at');
            $table->foreign('ticket_id')->references('id')->on('ticket');
            $table->foreign('user_auth_id')->references('id')->on('user_auth');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ticket_comments');
    }
}



// CREATE TABLE `ticket_comments` (
//     `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
//     `ticket_id` int(10) unsigned NOT NULL,
//     `user_auth_id` bigint(20) unsigned DEFAULT NULL,
//     `comment` text DEFAULT NULL,
//     `created_at` timestamp NULL DEFAULT current_timestamp(),
//     PRIMARY KEY (`id`),
//     KEY `idx_ticket_comments_ticket_id` (`ticket_id`),
//     KEY `idx_ticket_comments_user_auth_id` (`user_auth_id`),
//     CONSTRAINT `fk_ticket_comments_ticket_id` FOREIGN KEY (`ticket_id`) REFERENCES `ticket` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
//     CONSTRAINT `fk_ticket_comments_user_auth_id` FOREIGN KEY (`user_auth_id`) REFERENCES `user_auth` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
//   ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;