<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddTableChatCategory extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
         DB::unprepared("
            CREATE TABLE `chat_category` (
                `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                `category_id` INT UNSIGNED NOT NULL,
                `chat_id` INT UNSIGNED NOT NULL,
                `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `created_by` INT NULL DEFAULT NULL,
                PRIMARY KEY (`id`),
                INDEX `idx_chat_category_category_id` (`category_id` ASC),
                INDEX `idx_chat_category_chat_id` (`chat_id` ASC),
                CONSTRAINT `fk_chat_category_category_id`
                FOREIGN KEY (`category_id`)
                REFERENCES `category` (`id`)
                ON DELETE NO ACTION
                ON UPDATE NO ACTION,
                CONSTRAINT `fk_chat_category_chat_id`
                FOREIGN KEY (`chat_id`)
                REFERENCES `chat` (`id`)
                ON DELETE NO ACTION
            ON UPDATE NO ACTION);
        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('chat_category');
    }
}
