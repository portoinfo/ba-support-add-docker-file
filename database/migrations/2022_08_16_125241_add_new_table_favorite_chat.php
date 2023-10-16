<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddNewTableFavoriteChat extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared("
            CREATE TABLE `chat_favorite` (
                `company_user_id` BIGINT UNSIGNED NOT NULL,
                `chat_id` INT UNSIGNED NOT NULL,
                PRIMARY KEY (`company_user_id`, `chat_id`),
                INDEX `idx_chat_favorite_chat_id` (`chat_id` ASC),
                CONSTRAINT `fk_chat_favorite_company_user_id`
                FOREIGN KEY (`company_user_id`)
                REFERENCES `company_user` (`id`)
                ON DELETE NO ACTION
                ON UPDATE NO ACTION,
                CONSTRAINT `fk_cchat_favorite_chat_id`
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
        Schema::dropIfExists('chat_favorite');
    }
}
