<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddTableCategory extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared("
            CREATE TABLE `category` (
                `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                `category_id` int(10) unsigned DEFAULT NULL,
                `company_id` int(10) unsigned NOT NULL,
                `description` text DEFAULT NULL,
                `created_at` timestamp NULL DEFAULT current_timestamp(),
                `created_by` int(11) DEFAULT NULL,
                PRIMARY KEY (`id`),
                KEY `idx_category_category_id` (`category_id`),
                KEY `idx_category_company_id` (`company_id`),
                CONSTRAINT `fk_category_category_id` FOREIGN KEY (`category_id`) REFERENCES `category` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
                CONSTRAINT `fk_category_company_id` FOREIGN KEY (`company_id`) REFERENCES `company` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
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
        Schema::dropIfExists('category');
    }
}
