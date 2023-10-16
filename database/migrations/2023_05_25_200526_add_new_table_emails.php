<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddNewTableEmails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared("
            CREATE TABLE IF NOT EXISTS `emails` (
            `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
            `company_id` int(11) unsigned DEFAULT NULL,
            `type` enum('opened','replied','closed','password','attendant') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
            `title` varchar(250) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
            `email` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
            `name_sender` varchar(250) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
            `email_sender` varchar(250) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
            `language` varchar(5) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
            `created_at` timestamp NULL DEFAULT NULL,
            `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
            `created_by` int(11) DEFAULT NULL,
            PRIMARY KEY (`id`),
            KEY `idx_company_emails_id` (`company_id`),
            CONSTRAINT `fk_company_emails_company_id` FOREIGN KEY (`company_id`) REFERENCES `company` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
            ) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('company_faq_robot');
    }
}
