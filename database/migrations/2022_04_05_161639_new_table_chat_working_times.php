<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class NewTableChatWorkingTimes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Schema::create('chat_working_times', function (Blueprint $table) {
        //     $table->bigInteger('id', 20);
        //     $table->integer('chat_id')->unsigned();
        //     $table->bigInteger('company_user_company_department_id')->unsigned()->nullable();
        //     $table->timestamp('initial_date');
        //     $table->timestamp('final_date');
        //     $table->integer('chat_history_id')->unsigned();
        //     $table->foreign('chat_id')->references('id')->on('chat');
        //     $table->foreign('company_user_company_department_id')->references('id')->on('company_user_company_department');
        // });

        DB::unprepared("
            CREATE TABLE `chat_working_times` (
                `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
                `chat_id` INT(10) UNSIGNED NOT NULL,
                `ticket_id` INT(10) UNSIGNED NULL,
                `company_user_company_department_id` INT(10) UNSIGNED NOT NULL,
                `initial_date` TIMESTAMP NULL,
                `final_date` TIMESTAMP NULL,
                `chat_history_id` BIGINT(20) UNSIGNED NULL,
                PRIMARY KEY (`id`),
                INDEX `idx_chat_working_times_chat_id` (`chat_id` ASC),
                INDEX `idx_chat_working_times_ticket_id` (`ticket_id` ASC),
                INDEX `idx_chat_work_times_comp_user_comp_dep_id` (`company_user_company_department_id` ASC),
                CONSTRAINT `fk_chat_working_times_chat_id`
                    FOREIGN KEY (`chat_id`)
                    REFERENCES `chat` (`id`)
                    ON DELETE NO ACTION
                    ON UPDATE NO ACTION,
                CONSTRAINT `fk_chat_working_times_ticket_id`
                    FOREIGN KEY (`ticket_id`)
                    REFERENCES `ticket` (`id`)
                    ON DELETE NO ACTION
                    ON UPDATE NO ACTION,
                CONSTRAINT `fk_chat_work_times_comp_user_comp_dep_id`
                    FOREIGN KEY (`company_user_company_department_id`)
                    REFERENCES `company_user_company_department` (`id`)
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
        Schema::dropIfExists('chat_working_times');
    }
}
