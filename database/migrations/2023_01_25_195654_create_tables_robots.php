<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateTablesRobots extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared("
        
            CREATE TABLE `company_faq_robot` (  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,  `company_id` int(10) unsigned NOT NULL,  `is_active` tinyint(3) unsigned NOT NULL DEFAULT 0,  `top_tools_show_count` tinyint(3) unsigned NOT NULL DEFAULT 0,  `created_by` int(11) unsigned DEFAULT NULL,  `created_at` timestamp NULL DEFAULT current_timestamp(),  `updated_by` int(11) unsigned DEFAULT NULL,  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),  `deleted_by` int(11) unsigned DEFAULT NULL,  `deleted_at` timestamp NULL DEFAULT NULL,  PRIMARY KEY (`id`),  KEY `idx_company_faq_robot_id` (`company_id`),  CONSTRAINT `fk_company_faq_robot_company_id` FOREIGN KEY (`company_id`) REFERENCES `company` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
            
            CREATE TABLE `company_faq_robot_info` (  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,  `company_faq_robot_id` int(10) unsigned NOT NULL,  `initial_message` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL,  `offline_tool_message` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,  `direct_message_to_attendant` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,  `language` varchar(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,  `created_by` int(11) unsigned DEFAULT NULL,  `created_at` timestamp NULL DEFAULT current_timestamp(),  `updated_by` int(11) unsigned DEFAULT NULL,  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),  `deleted_by` int(11) unsigned DEFAULT NULL,  `deleted_at` timestamp NULL DEFAULT NULL,  PRIMARY KEY (`id`),  KEY `idx_company_faq_robot_info_company_faq_robot_id` (`company_faq_robot_id`),  CONSTRAINT `fk_company_faq_robot_info_company_faq_robot_id` FOREIGN KEY (`company_faq_robot_id`) REFERENCES `company_faq_robot` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
            
            CREATE TABLE `company_faq_robot_tools` (  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,  `company_faq_robot_id` int(10) unsigned NOT NULL,  `company_faq_robot_tool_id` int(10) unsigned DEFAULT NULL,  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,  `language` varchar(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,  `click_count` int(10) unsigned DEFAULT NULL,  `tool_status` tinyint(4) NOT NULL DEFAULT 1,  `is_active` tinyint(4) NOT NULL DEFAULT 1,  `created_by` int(10) unsigned DEFAULT NULL,  `created_at` timestamp NULL DEFAULT current_timestamp(),  `updated_by` int(10) unsigned DEFAULT NULL,  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),  `deleted_by` int(10) unsigned DEFAULT NULL,  `deleted_at` timestamp NULL DEFAULT NULL,  PRIMARY KEY (`id`),  KEY `idx_company_faq_robot_tools_company_faq_robot_id` (`company_faq_robot_id`),  KEY `fk_company_faq_robot_tools_company_faq_robot_tool_id_idx` (`company_faq_robot_tool_id`),  CONSTRAINT `fk_company_faq_robot_tools_company_faq_robot_id` FOREIGN KEY (`company_faq_robot_id`) REFERENCES `company_faq_robot` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,  CONSTRAINT `fk_company_faq_robot_tools_company_faq_robot_tool_id` FOREIGN KEY (`company_faq_robot_tool_id`) REFERENCES `company_faq_robot_tools` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
            
            CREATE TABLE `company_faq_robot_tool_user_client` (  `int` bigint(20) unsigned NOT NULL AUTO_INCREMENT,  `user_client_id` bigint(20) unsigned NOT NULL,  `company_faq_robot_tool_id` int(10) unsigned NOT NULL,  `was_helped` tinyint(3) unsigned DEFAULT NULL,  PRIMARY KEY (`int`),  KEY `idx_comp_faq_robot_tool_user_client_id` (`user_client_id`),  KEY `idx_comp_faq_robot_tool_user_tool_id` (`company_faq_robot_tool_id`),  CONSTRAINT `fk_comp_faq_robot_tool_user_client_id` FOREIGN KEY (`user_client_id`) REFERENCES `user_client` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,  CONSTRAINT `fk_comp_faq_robot_tool_user_tool_id` FOREIGN KEY (`company_faq_robot_tool_id`) REFERENCES `company_faq_robot_tools` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
        Schema::dropIfExists('company_faq_robot_info');
        Schema::dropIfExists('company_faq_robot_tools');
        Schema::dropIfExists('company_faq_robot_tool_user_client');
    }
}
