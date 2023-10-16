-- MySQL dump 10.17  Distrib 10.3.22-MariaDB, for Win64 (AMD64)
--
-- Host: 65.111.162.15    Database: ba_support
-- ------------------------------------------------------
-- Server version	10.3.27-MariaDB-log

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `avaliation`
--

DROP TABLE IF EXISTS `avaliation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `avaliation` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ticket_id` int(10) unsigned DEFAULT NULL,
  `chat_id` int(10) unsigned DEFAULT NULL,
  `stars_atendent` tinyint(4) DEFAULT NULL,
  `stars_service` tinyint(4) DEFAULT NULL,
  `comment` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_avaliation_ticket_id` (`ticket_id`),
  KEY `idx_avaliation_chat_id` (`chat_id`),
  CONSTRAINT `fk_avaliation_chat_id` FOREIGN KEY (`chat_id`) REFERENCES `chat` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_avaliation_ticket_id` FOREIGN KEY (`ticket_id`) REFERENCES `ticket` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `blacklist_email_domain`
--

DROP TABLE IF EXISTS `blacklist_email_domain`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `blacklist_email_domain` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `domain` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_domain` (`domain`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `blacklist_ip`
--

DROP TABLE IF EXISTS `blacklist_ip`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `blacklist_ip` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ip` varchar(45) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ip_UNIQUE` (`ip`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `blacklist_user`
--

DROP TABLE IF EXISTS `blacklist_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `blacklist_user` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `blocked_id` bigint(20) NOT NULL,
  `type` enum('CLIENT','AGENT') NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_blacklist_user_blocked_id_type` (`blocked_id`,`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `chat`
--

DROP TABLE IF EXISTS `chat`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `chat` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `company_id` int(10) unsigned NOT NULL,
  `company_department_id` int(10) unsigned NOT NULL,
  `comp_user_comp_depart_id_creator` int(10) unsigned DEFAULT NULL,
  `comp_user_comp_depart_id_current` int(10) unsigned DEFAULT NULL,
  `ticket_id` int(10) unsigned DEFAULT NULL,
  `type` enum('DEFAULT','TRANSFERED','TICKET') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'DEFAULT',
  `status` enum('OPENED','IN_PROGRESS','CLOSED','RESOLVED','CANCELED') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'OPENED',
  `priority` enum('NORMAL','MEDIUM','HIGH') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'NORMAL',
  `user_agent` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `update_status_in_progress` datetime DEFAULT NULL,
  `update_status_closed_resolved` datetime DEFAULT NULL,
  `queue_time` int(11) DEFAULT NULL,
  `service_time` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_chat_company_id` (`company_id`),
  KEY `idx_chat_company_department_id` (`company_department_id`),
  KEY `idx_chat_comp_user_comp_depart_id_creator` (`comp_user_comp_depart_id_creator`),
  KEY `idx_chat_ticket_id` (`ticket_id`),
  KEY `idx_chat_comp_user_comp_depart_id_current_id` (`comp_user_comp_depart_id_current`),
  KEY `idx_chat_status` (`status`),
  KEY `idx_chat_queue_time` (`queue_time`),
  KEY `idx_chat_service_time` (`service_time`),
  KEY `idx_chat_update_status_in_progress` (`update_status_in_progress`),
  KEY `idx_chat_update_status_closed_resolved` (`update_status_closed_resolved`),
  KEY `idx_chat_created_at` (`created_at`),
  KEY `idx_chat_deleted_at` (`deleted_at`),
  CONSTRAINT `fk_chat_comp_user_comp_depart_id_creator` FOREIGN KEY (`comp_user_comp_depart_id_creator`) REFERENCES `company_user_company_department` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_chat_comp_user_comp_depart_id_current` FOREIGN KEY (`comp_user_comp_depart_id_current`) REFERENCES `company_user_company_department` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_chat_company_department_id` FOREIGN KEY (`company_department_id`) REFERENCES `company_department` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_chat_company_id` FOREIGN KEY (`company_id`) REFERENCES `company` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_chat_ticket_id` FOREIGN KEY (`ticket_id`) REFERENCES `ticket` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `chat_history`
--

DROP TABLE IF EXISTS `chat_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `chat_history` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `chat_id` int(10) unsigned NOT NULL,
  `company_user_company_department_id` int(10) unsigned DEFAULT NULL,
  `type` enum('TEXT','URL','IMAGE','EMOTICON','AUDIO','GIF','EVENT','CALL','FILE','OPEN','CLOSE') DEFAULT NULL,
  `content` varchar(500) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_chat_history_chat_id` (`chat_id`),
  KEY `idx_chat_hist_comp_user_comp_dep_id` (`company_user_company_department_id`),
  CONSTRAINT `fk_chat_hist_comp_user_comp_dep_id` FOREIGN KEY (`company_user_company_department_id`) REFERENCES `company_user_company_department` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_chat_history_chat_id` FOREIGN KEY (`chat_id`) REFERENCES `chat` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=241 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `company`
--

DROP TABLE IF EXISTS `company`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `company` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` varchar(250) NOT NULL,
  `address` varchar(250) DEFAULT NULL,
  `logo` varchar(250) DEFAULT NULL,
  `hash_code` varchar(255) NOT NULL,
  `api_token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `hash_code_UNIQUE` (`hash_code`),
  UNIQUE KEY `api_token_UNIQUE` (`api_token`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `company_contact`
--

DROP TABLE IF EXISTS `company_contact`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `company_contact` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `company_id` int(10) unsigned NOT NULL,
  `type` enum('PHONE','EMAIL','WHATSAPP','WEBSITE') NOT NULL,
  `description` varchar(100) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_company_contact_company_id` (`company_id`),
  CONSTRAINT `fk_company_contact_company_id` FOREIGN KEY (`company_id`) REFERENCES `company` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `company_depart_settings_question`
--

DROP TABLE IF EXISTS `company_depart_settings_question`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `company_depart_settings_question` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `company_department_id` int(10) unsigned NOT NULL,
  `question` text NOT NULL,
  `type` enum('TEXT','SELECT','CHECK') DEFAULT 'TEXT',
  `mandatory` tinyint(4) NOT NULL DEFAULT 0,
  `active` tinyint(4) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_comp_depart_settings_question_comp_depart_id` (`company_department_id`),
  CONSTRAINT `fk_comp_depart_settings_question_comp_depart_id` FOREIGN KEY (`company_department_id`) REFERENCES `company_department` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `company_department`
--

DROP TABLE IF EXISTS `company_department`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `company_department` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `company_id` int(10) unsigned NOT NULL,
  `company_user_id` bigint(20) unsigned NOT NULL,
  `name` varchar(50) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `module` enum('CHAT','TICKET','ALL') DEFAULT NULL,
  `has_robot` tinyint(4) DEFAULT 0,
  `is_active` tinyint(4) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_company_department_company_id` (`company_id`),
  KEY `idx_company_department_company_user_id` (`company_user_id`),
  CONSTRAINT `fk_company_department_company_id` FOREIGN KEY (`company_id`) REFERENCES `company` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_company_department_company_user_id` FOREIGN KEY (`company_user_id`) REFERENCES `company_user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `company_department_settings`
--

DROP TABLE IF EXISTS `company_department_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `company_department_settings` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `company_department_id` int(10) unsigned NOT NULL,
  `settings` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_company_department_settings_company_department_id` (`company_department_id`),
  CONSTRAINT `fk_company_department_settings_company_department_id` FOREIGN KEY (`company_department_id`) REFERENCES `company_department` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `company_settings`
--

DROP TABLE IF EXISTS `company_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `company_settings` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `company_id` int(10) unsigned NOT NULL,
  `settings_chat` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `settings_ticket` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `released_domain` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `blocked_domain` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `general` text DEFAULT '{}',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_company_settings_company_id` (`company_id`),
  CONSTRAINT `fk_company_settings_company_id` FOREIGN KEY (`company_id`) REFERENCES `company` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `company_user`
--

DROP TABLE IF EXISTS `company_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `company_user` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `company_id` int(10) unsigned NOT NULL,
  `user_auth_id` bigint(20) unsigned NOT NULL,
  `is_admin` tinyint(4) NOT NULL DEFAULT 0,
  `is_active` tinyint(4) NOT NULL DEFAULT 1,
  `status` enum('AWAY','ONLINE','OFFLINE','BUSY') DEFAULT NULL,
  `last_login` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_company_user_company_id` (`company_id`),
  KEY `idx_company_user_user_auth_id` (`user_auth_id`),
  CONSTRAINT `fk_company_user_company_id` FOREIGN KEY (`company_id`) REFERENCES `company` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_company_user_user_auth_id` FOREIGN KEY (`user_auth_id`) REFERENCES `user_auth` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=42 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `company_user_client`
--

DROP TABLE IF EXISTS `company_user_client`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `company_user_client` (
  `user_client_id` bigint(20) unsigned NOT NULL,
  `company_id` int(10) unsigned NOT NULL,
  `last_login` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`user_client_id`,`company_id`),
  KEY `idx_company_user_client_company_id` (`company_id`),
  KEY `idx_company_user_client_user_client_id` (`user_client_id`),
  CONSTRAINT `fk_company_user_client_company_id` FOREIGN KEY (`company_id`) REFERENCES `company` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_company_user_client_user_client_id` FOREIGN KEY (`user_client_id`) REFERENCES `user_client` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `company_user_company_department`
--

DROP TABLE IF EXISTS `company_user_company_department`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `company_user_company_department` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `company_user_id` bigint(20) unsigned NOT NULL,
  `company_department_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_comp_user_comp_dpto_user_id_dep_id` (`company_user_id`,`company_department_id`),
  KEY `idx_company_user_company_department_company_department_id` (`company_department_id`),
  KEY `idx_company_user_company_department_company_user_id` (`company_user_id`),
  CONSTRAINT `fk_company_user_company_department_company_department_id` FOREIGN KEY (`company_department_id`) REFERENCES `company_department` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_company_user_company_department_company_user_id` FOREIGN KEY (`company_user_id`) REFERENCES `company_user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `company_user_share_link`
--

DROP TABLE IF EXISTS `company_user_share_link`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `company_user_share_link` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `share_link_id` int(10) unsigned NOT NULL,
  `company_user_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_company_user_share_link_share_link_id` (`share_link_id`),
  KEY `idx_company_user_share_link_company_user_id` (`company_user_id`),
  CONSTRAINT `fk_company_user_share_link_company_user_id` FOREIGN KEY (`company_user_id`) REFERENCES `company_user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_company_user_share_link_share_link_id` FOREIGN KEY (`share_link_id`) REFERENCES `share_link` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `company_user_user_group`
--

DROP TABLE IF EXISTS `company_user_user_group`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `company_user_user_group` (
  `company_user_id` bigint(20) unsigned NOT NULL,
  `user_group_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`company_user_id`,`user_group_id`),
  KEY `idx_company_user_user_group_user_group_id` (`user_group_id`),
  KEY `idx_company_user_user_group_company_user_id` (`company_user_id`),
  CONSTRAINT `fk_company_user_user_group_company_user_id` FOREIGN KEY (`company_user_id`) REFERENCES `company_user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_company_user_user_group_user_group_id` FOREIGN KEY (`user_group_id`) REFERENCES `user_group` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `department_robot`
--

DROP TABLE IF EXISTS `department_robot`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `department_robot` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `department_robot_id` int(10) unsigned DEFAULT NULL,
  `company_department_id` int(10) unsigned NOT NULL,
  `question` varchar(100) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_depart_rob_depart_rob_id` (`department_robot_id`),
  KEY `idx_depart_rob_company_depart_id` (`company_department_id`),
  CONSTRAINT `fk_departmet_robot_company_department_id` FOREIGN KEY (`company_department_id`) REFERENCES `company_department` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_departmet_robot_departmet_robot_id` FOREIGN KEY (`department_robot_id`) REFERENCES `department_robot` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `departmet`
--

DROP TABLE IF EXISTS `departmet`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `departmet` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `initial_form`
--

DROP TABLE IF EXISTS `initial_form`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `initial_form` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `company_department_id` int(10) unsigned NOT NULL,
  `question_type` enum('TEXT','URL','EMAIL') DEFAULT NULL,
  `question` varchar(255) NOT NULL,
  `type` enum('TICKET','CHAT') DEFAULT NULL,
  `settings` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_initial_form_company_department_id` (`company_department_id`),
  CONSTRAINT `fk_initial_form_company_department_id` FOREIGN KEY (`company_department_id`) REFERENCES `company_department` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `initial_form_answer`
--

DROP TABLE IF EXISTS `initial_form_answer`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `initial_form_answer` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `initial_form_id` int(10) unsigned NOT NULL,
  `answer` text NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_initial_form_answer_initial_form_id` (`initial_form_id`),
  CONSTRAINT `fk_initial_form_answer_initial_form_id` FOREIGN KEY (`initial_form_id`) REFERENCES `initial_form` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `knowledge_base`
--

DROP TABLE IF EXISTS `knowledge_base`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `knowledge_base` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `company_id` int(10) unsigned NOT NULL,
  `company_department_id` int(10) unsigned DEFAULT NULL,
  `name` varchar(45) DEFAULT NULL,
  `description` varchar(250) DEFAULT NULL,
  `is_active` tinyint(4) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_knowledge_base_company_id` (`company_id`),
  KEY `idx_knowledge_base_company_department_id` (`company_department_id`),
  CONSTRAINT `fk_knowledge_base_company1` FOREIGN KEY (`company_id`) REFERENCES `company` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_knowledge_base_company_department1` FOREIGN KEY (`company_department_id`) REFERENCES `company_department` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `knowledge_base_content`
--

DROP TABLE IF EXISTS `knowledge_base_content`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `knowledge_base_content` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `knowledge_base_topic_id` int(10) unsigned NOT NULL,
  `content` text DEFAULT NULL,
  `is_active` tinyint(4) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_knowledge_base_content_knowledge_base_topic_id` (`knowledge_base_topic_id`),
  CONSTRAINT `fk_knowledge_base_content_knowledge_base_topic_id` FOREIGN KEY (`knowledge_base_topic_id`) REFERENCES `knowledge_base_topic` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `knowledge_base_content_artefact`
--

DROP TABLE IF EXISTS `knowledge_base_content_artefact`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `knowledge_base_content_artefact` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `knowledge_base_content_id` int(10) unsigned NOT NULL,
  `content` text DEFAULT NULL,
  `type` enum('IMAGE','VIDEO','GIF','AUDIO') DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_knowledge_base_content_artefact_knowledge_base_content_id` (`knowledge_base_content_id`),
  CONSTRAINT `fk_knowledge_base_content_artefact_knowledge_base_content_id` FOREIGN KEY (`knowledge_base_content_id`) REFERENCES `knowledge_base_content` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `knowledge_base_topic`
--

DROP TABLE IF EXISTS `knowledge_base_topic`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `knowledge_base_topic` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `knowledge_base_id` int(10) unsigned NOT NULL,
  `name` varchar(45) DEFAULT NULL,
  `description` varchar(250) DEFAULT NULL,
  `is_active` tinyint(4) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_knowledge_base_topic_knowledge_base_id` (`knowledge_base_id`),
  CONSTRAINT `fk_knowledge_base_topic_knowledge_base_id` FOREIGN KEY (`knowledge_base_id`) REFERENCES `knowledge_base` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `log_master_keys`
--

DROP TABLE IF EXISTS `log_master_keys`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `log_master_keys` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `master_keys_id` int(10) unsigned NOT NULL,
  `user_auth_id` bigint(20) unsigned NOT NULL,
  `action` varchar(50) NOT NULL,
  `action_data` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_log_master_keys_login_master_keys_id` (`master_keys_id`),
  KEY `idx_log_master_keys_login_user_auth_id` (`user_auth_id`),
  KEY `idx_log_master_keys_login_action` (`action`),
  CONSTRAINT `fk_log_master_keys_login_master_keys_id` FOREIGN KEY (`master_keys_id`) REFERENCES `master_keys` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_log_master_keys_login_user_auth_id` FOREIGN KEY (`user_auth_id`) REFERENCES `user_auth` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `master_keys`
--

DROP TABLE IF EXISTS `master_keys`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `master_keys` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `key` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_master_keys_name` (`name`),
  KEY `idx_master_keys_key` (`key`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `modelo`
--

DROP TABLE IF EXISTS `modelo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `modelo` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `perm_mod_func_admin_user_group_admin`
--

DROP TABLE IF EXISTS `perm_mod_func_admin_user_group_admin`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `perm_mod_func_admin_user_group_admin` (
  `perm_mod_func_admin_id` int(10) unsigned NOT NULL,
  `user_group_admin_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`perm_mod_func_admin_id`,`user_group_admin_id`),
  KEY `idx_perm_mod_func_admin_user_group_adm_id1` (`user_group_admin_id`),
  KEY `idx_perm_mod_func_admin_user_group_adm_id2` (`perm_mod_func_admin_id`),
  CONSTRAINT `fk_perm_mod_func_admin_user_group_admin_id1` FOREIGN KEY (`perm_mod_func_admin_id`) REFERENCES `permission_module_func_admin` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_perm_mod_func_admin_user_group_admin_id2` FOREIGN KEY (`user_group_admin_id`) REFERENCES `user_group_admin` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `perm_module_func`
--

DROP TABLE IF EXISTS `perm_module_func`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `perm_module_func` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `permission_module_id` int(10) unsigned NOT NULL,
  `name` varchar(45) DEFAULT NULL,
  `description` varchar(250) DEFAULT NULL,
  `menus` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `routes` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_perm_module_func_permission_module_id` (`permission_module_id`),
  CONSTRAINT `fk_perm_module_func_permission_module_id` FOREIGN KEY (`permission_module_id`) REFERENCES `permission_module` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `permission_module`
--

DROP TABLE IF EXISTS `permission_module`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `permission_module` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(45) DEFAULT NULL,
  `description` varchar(250) DEFAULT NULL,
  `is_active` tinyint(4) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `permission_module_admin`
--

DROP TABLE IF EXISTS `permission_module_admin`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `permission_module_admin` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(45) DEFAULT NULL,
  `description` varchar(250) DEFAULT NULL,
  `is_active` tinyint(4) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `permission_module_func_admin`
--

DROP TABLE IF EXISTS `permission_module_func_admin`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `permission_module_func_admin` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `permission_module_admin_id` int(10) unsigned NOT NULL,
  `name` varchar(45) DEFAULT NULL,
  `description` varchar(250) DEFAULT NULL,
  `menus` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `routes` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_perm_mod_func_admin_perm_mod_admin_id` (`permission_module_admin_id`),
  CONSTRAINT `fk_perm_module_func_admin_perm_mod_admin_id` FOREIGN KEY (`permission_module_admin_id`) REFERENCES `permission_module_admin` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `share_link`
--

DROP TABLE IF EXISTS `share_link`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `share_link` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `hash_code` varchar(255) DEFAULT NULL,
  `type` enum('ONE_REGISTRATION','TIME_REGISTRATION') DEFAULT NULL,
  `expire_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `subsidiary`
--

DROP TABLE IF EXISTS `subsidiary`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `subsidiary` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `country` varchar(100) NOT NULL,
  `iso_code` char(2) NOT NULL,
  `default_language` varchar(5) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=193 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ticket`
--

DROP TABLE IF EXISTS `ticket`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ticket` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `company_id` int(10) unsigned NOT NULL,
  `company_department_id` int(10) unsigned NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `comments` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('OPENED','IN_PROGRESS','CLOSED','RESOLVED','CANCELED') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` enum('DEFAULT','TRANSFERED') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'DEFAULT',
  `priority` enum('NORMAL','MEDIUM','HIGH') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'NORMAL',
  `user_agent` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `update_status_in_progress` datetime DEFAULT NULL,
  `update_status_closed_resolved` datetime DEFAULT NULL,
  `queue_time` int(11) DEFAULT NULL,
  `service_time` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_ticket_company1_idx` (`company_id`),
  KEY `fk_ticket_company_department1_idx` (`company_department_id`),
  KEY `idx_ticket_status` (`status`),
  KEY `idx_ticket_queue_time` (`queue_time`),
  KEY `idx_ticket_service_time` (`service_time`),
  KEY `idx_ticket_update_status_in_progress` (`update_status_in_progress`),
  KEY `idx_ticket_update_status_closed_resolved` (`update_status_closed_resolved`),
  CONSTRAINT `fk_ticket_company_department_id` FOREIGN KEY (`company_department_id`) REFERENCES `company_department` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_ticket_company_id` FOREIGN KEY (`company_id`) REFERENCES `company` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=175 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ticket_chat_answer`
--

DROP TABLE IF EXISTS `ticket_chat_answer`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ticket_chat_answer` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `company_depart_settings_question_id` int(10) unsigned NOT NULL,
  `ticket_id` int(10) unsigned DEFAULT NULL,
  `chat_id` int(10) unsigned DEFAULT NULL,
  `answer` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_ticket_answer_ticket_id` (`ticket_id`),
  KEY `idx_ticket_answer_comp_depart_settings_question_id` (`company_depart_settings_question_id`),
  KEY `idx_ticket_answer_chat_id` (`chat_id`),
  CONSTRAINT `fk_ticket_answer_chat_id` FOREIGN KEY (`chat_id`) REFERENCES `chat` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_ticket_answer_comp_depart_settings_question_id` FOREIGN KEY (`company_depart_settings_question_id`) REFERENCES `company_depart_settings_question` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_ticket_answer_ticket_id` FOREIGN KEY (`ticket_id`) REFERENCES `ticket` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_admin`
--

DROP TABLE IF EXISTS `user_admin`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_admin` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_group_admin_id` int(10) unsigned NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `language` varchar(5) NOT NULL,
  `is_master` tinyint(4) DEFAULT 0,
  `is_active` tinyint(4) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`,`user_group_admin_id`),
  UNIQUE KEY `email_UNIQUE` (`email`),
  KEY `idx_user_admin_user_group_admin_id` (`user_group_admin_id`),
  CONSTRAINT `fk_user_admin_user_group_admin_id` FOREIGN KEY (`user_group_admin_id`) REFERENCES `user_group_admin` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_auth`
--

DROP TABLE IF EXISTS `user_auth`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_auth` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `subsidiary_id` int(10) unsigned NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `language` varchar(5) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `hash_code` varchar(255) DEFAULT NULL,
  `can_create_company` tinyint(4) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQUE_user_auth_email` (`email`),
  KEY `idx_user_auth_subsidiary_id` (`subsidiary_id`),
  CONSTRAINT `fk_user_auth_subsidiary_id` FOREIGN KEY (`subsidiary_id`) REFERENCES `subsidiary` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=86 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_client`
--

DROP TABLE IF EXISTS `user_client`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_client` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_auth_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_fk_user_client_user_auth_id` (`user_auth_id`),
  CONSTRAINT `fk_user_client_user_auth_id` FOREIGN KEY (`user_auth_id`) REFERENCES `user_auth` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_client_chat`
--

DROP TABLE IF EXISTS `user_client_chat`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_client_chat` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_client_id` bigint(20) unsigned NOT NULL,
  `chat_id` int(10) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_user_client_chat_user_client1_idx` (`user_client_id`),
  KEY `fk_user_client_chat_chat1_idx` (`chat_id`),
  CONSTRAINT `fk_user_client_chat_chat1` FOREIGN KEY (`chat_id`) REFERENCES `chat` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_user_client_chat_user_client1` FOREIGN KEY (`user_client_id`) REFERENCES `user_client` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_client_ticket`
--

DROP TABLE IF EXISTS `user_client_ticket`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_client_ticket` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ticket_id` int(10) unsigned NOT NULL,
  `user_client_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_user_client_ticket_ticket_id` (`ticket_id`),
  KEY `idx_user_client_ticket_user_client_id` (`user_client_id`),
  CONSTRAINT `fk_user_client_ticket_ticket_id` FOREIGN KEY (`ticket_id`) REFERENCES `ticket` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_user_client_ticket_user_client1` FOREIGN KEY (`user_client_id`) REFERENCES `user_client` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_group`
--

DROP TABLE IF EXISTS `user_group`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_group` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `company_id` int(10) unsigned NOT NULL,
  `name` varchar(45) NOT NULL,
  `description` varchar(80) DEFAULT NULL,
  `is_active` tinyint(4) DEFAULT NULL,
  `settings` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_user_group_company_id` (`company_id`),
  CONSTRAINT `fk_user_group_company_id` FOREIGN KEY (`company_id`) REFERENCES `company` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_group_admin`
--

DROP TABLE IF EXISTS `user_group_admin`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_group_admin` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(45) NOT NULL,
  `description` varchar(80) DEFAULT NULL,
  `is_active` tinyint(4) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_group_perm_mod_func`
--

DROP TABLE IF EXISTS `user_group_perm_mod_func`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_group_perm_mod_func` (
  `user_group_id` int(10) unsigned NOT NULL,
  `perm_module_func_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`user_group_id`,`perm_module_func_id`),
  KEY `idx_user_group_perm_mod_func_perm_module_func_id` (`perm_module_func_id`),
  KEY `idx_user_group_perm_mod_func_user_group_id` (`user_group_id`),
  CONSTRAINT `fk_user_group_perm_mod_func_perm_module_func_id` FOREIGN KEY (`perm_module_func_id`) REFERENCES `perm_module_func` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_user_group_perm_mod_func_user_group_id` FOREIGN KEY (`user_group_id`) REFERENCES `user_group` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_ticket`
--

DROP TABLE IF EXISTS `user_ticket`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_ticket` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `company_user_id` bigint(20) unsigned NOT NULL,
  `ticket_id` int(10) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_user_ticket_ticket_id` (`ticket_id`),
  KEY `idx_user_ticket_company_user_id` (`company_user_id`),
  CONSTRAINT `fk_user_ticket_company_user_id` FOREIGN KEY (`company_user_id`) REFERENCES `company_user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_user_ticket_ticket_id` FOREIGN KEY (`ticket_id`) REFERENCES `ticket` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping events for database 'ba_support'
--

--
-- Dumping routines for database 'ba_support'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2021-02-02 10:38:32


/*
-- Query: select id, REPLACE(name, 'BuilderAll - ', '') as name, country, iso_code from subsidiary
LIMIT 0, 10000

-- Date: 2020-12-07 16:57
*/

INSERT INTO `subsidiary` (`id`,`name`,`country`,`iso_code`) VALUES (1,'United States','United States','US'), (2,'Brasil','Brasil','BR'), (3,'Afghanistan','افغانستان','AF'), (4,'Albania','Shqipëria','AL'), (5,'Algeria','الجزائر','DZ'), (6,'Andorra','Andorra','AD'), (7,'Angola','Angola','AO'), (8,'Antigua and Barbuda','Antigua and Barbuda','AG'), (9,'Argentina','Argentina','AR'), (10,'Armenia','Հայաստան','AM'), (11,'Australia','Australia','AU'), (12,'Austria','Österreich','AT'), (13,'Azerbaijan','Azərbaycan','AZ'), (14,'Bahamas','Bahamas','BS'), (15,'Bahrain','Bahrain','BH'), (16,'Bangladesh','Bangladesh','BD'), (17,'Barbados','Barbados','BB'), (18,'Belarus','Belarus','BY'), (19,'Belgium','België','BE'), (20,'Belize','Belize','BZ'), (21,'Benin','Benin','BJ'), (22,'Bhutan','Bhutan','BT'), (23,'Bolivia','Bolivia','BO'), (24,'Bosnia and Herzegovina','Bosna i Hercegovina','BA'), (25,'Botswana','Botswana','BW'), (26,'Brunei ','Brunei ','BN'), (27,'Bulgaria','Bulgaria','BG'), (28,'Burkina Faso','Burkina Faso','BF'), (29,'Burundi','Burundi','BI'), (30,'Cambodia','Cambodia','KH'), (31,'Cameroon','Cameroon','CM'), (32,'Canada','Canada','CA'), (33,'Cape Verde','Cabo Verde','CV'), (34,'Central African Republic','Central African Republic','CF'), (35,'Chad','Chad','TD'), (36,'Chile','Chile','CL'), (37,'China','中國','CN'), (38,'Colombia','Colombia','CO'), (39,'Comoros','Comoros','KM'), (40,'Congo','Congo','CD'), (41,'Costa Rica','Costa Rica','CR'), (42,'Croatia','Croatia','HR'), (43,'Cuba','Cuba','CU'), (44,'Cyprus','Cyprus','CY'), (45,'Czech Republic','Česká republika','CZ'), (46,'Denmark','Denmark','DK'), (47,'Djibouti','Djibouti','DJ'), (48,'Dominica','Dominica','DM'), (49,'Dominican Republic','República Dominicana','DO'), (50,'East Timor ','Timor-Leste','TL'), (51,'Ecuador','Ecuador','EC'), (52,'El Salvador','El Salvador','SV'), (53,'Equatorial Guinea','Guinea Ecuatorial','GQ'), (54,'Eritrea','Eritrea','ER'), (55,'Estonia','Estonia','EE'), (56,'Ethiopia','Ethiopia','ET'), (57,'Fiji','Fiji','FJ'), (58,'Finland','Finland','FI'), (59,'France','France','FR'), (60,'Gabon','Gabon','GA'), (61,'Gambia','Gambia','GM'), (62,'Georgia','Georgia','GE'), (63,'Germany','Deutschland','DE'), (64,'Ghana','Ghana','GH'), (65,'Greece','Ελλάδα','GR'), (66,'Grenada','Grenada','GD'), (67,'Guatemala','Guatemala','GT'), (68,'Guinea','Guinée','GN'), (69,'Guinea-Bissau','Guiné-Bissau','GW'), (70,'Guyana','Guyana','GY'), (71,'Haiti','Haiti','HT'), (72,'Honduras','Honduras','HN'), (73,'Hungary','Magyarország','HU'), (74,'Iceland','Iceland','IS'), (75,'India','भारत','IN'), (76,'Indonesia','Indonesia','ID'), (77,'Iran','ایران','IR'), (78,'Iraq','العراق','IQ'), (79,'Ireland','Ireland','IE'), (80,'Israel','ישראל','IL'), (81,'Italy','Italia','IT'), (82,'Ivory Coast','Cote d\'Ivoire','CI'), (83,'Jamaica','Jamaica','JM'), (84,'Japan','日本','JP'), (85,'Jordan','الاردن','JO'), (86,'Kazakhstan','Kazakhstan','KZ'), (87,'Kenya','Kenya','KE'), (88,'Kiribati','Kiribati','KI'), (89,'Kuwait','Kuwait','KW'), (90,'Kyrgyzstan','Kyrgyzstan','KG'), (91,'Laos','Laos','LA'), (92,'Latvia','Latvia','LV'), (93,'Lebanon','Lebanon','LB'), (94,'Lesotho','Lesotho','LS'), (95,'Liberia','Liberia','LR'), (96,'Libya','Libya','LY'), (97,'Liechtenstein','Liechtenstein','LI'), (98,'Lithuania','Lithuania','LT'), (99,'Luxembourg','Luxemburg','LU'), (100,'Macedonia','Macedonia','MK'), (101,'Madagascar','Madagascar','MG'), (102,'Malawi','Malawi','MW'), (103,'Malaysia','Malaysia','MY'), (104,'Maldives','Maldives','MV'), (105,'Mali','Mali','ML'), (106,'Malta','Malta','MT'), (107,'Marshall Islands','Marshall Islands','MH'), (108,'Mauritania','Mauritania','MR'), (109,'Mauritius','Mauritius','MU'), (110,'Mexico','México','MX'), (111,'Micronesia','Micronesia','FM'), (112,'Moldova','Moldova','MD'), (113,'Monaco','Monaco','MC'), (114,'Mongolia','Mongolia','MN'), (115,'Montenegro','Montenegro','ME'), (116,'Morocco','Morocco','MA'), (117,'Mozambique','Mozambique','MZ'), (118,'Namibia','Namibia','NA'), (119,'Nauru','Nauru','NR'), (120,'Nepal','Nepal','NP'), (121,'Netherlands','Nederland','NL'), (122,'New Zealand','New Zealand','NZ'), (123,'Nicaragua','Nicaragua','NI'), (124,'Niger','Niger','NE'), (125,'Nigeria','Nigeria','NG'), (126,'North Korea','North Korea','KP'), (127,'Norway','Norway','NO'), (128,'Oman','Oman','OM'), (129,'Pakistan','Pakistan','PK'), (130,'Palau','Palau','PW'), (131,'Panama','Panamá','PA'), (132,'Papua New Guinea','Papua New Guinea','PG'), (133,'Paraguay','Paraguay','PY'), (134,'Peru','Perú','PE'), (135,'Philippines','Philippines','PH'), (136,'Poland','Polska','PL'), (137,'Portugal','Portugal','PT'), (138,'Qatar','Qatar','QA'), (139,'Romania','Romania','RO'), (140,'Russia','Россия','RU'), (141,'Rwanda','Rwanda','RW'), (142,'Saint Kitts and Nevis','Saint Kitts and Nevis','KN'), (143,'Saint Lucia','Saint Lucia','LC'), (144,'Samoa ','Samoa ','WS'), (145,'San Marino','San Marino','SM'), (146,'Sao Tome and Principe','Sao Tome and Principe','ST'), (147,'Saudi Arabia','المملكة العربية السعودية','SA'), (148,'Senegal','Senegal','SN'), (149,'Serbia','Serbia','RS'), (150,'Seychelles','Seychelles','SC'), (151,'Sierra Leone','Sierra Leone','SL'), (152,'Singapore','Singapore','SG'), (153,'Slovakia','Slovakia','SK'), (154,'Slovenia','Slovenia','SI'), (155,'Solomon Islands','Solomon Islands','SB'), (156,'Somalia','Somalia','SO'), (157,'South Africa','South Africa','ZA'), (158,'South Korea','South Korea','KR'), (159,'Spain','España','ES'), (160,'Sri Lanka','Sri Lanka','LK'), (161,'Sudan','Sudan','SD'), (162,'Suriname','Suriname','SR'), (163,'Swaziland','Swaziland','SZ'), (164,'Sweden','Sverige','SE'), (165,'Switzerland','Schweiz','CH'), (166,'Syria','Syria','SY'), (167,'Taiwan','台灣','TW'), (168,'Tajikistan','Tajikistan','TJ'), (169,'Tanzania','Tanzania','TZ'), (170,'Thailand','ประเทศไทย ','TH'), (171,'Togo','Togo','TG'), (172,'Tonga','Tonga','TO'), (173,'Trinidad and Tobago','Trinidad and Tobago','TT'), (174,'Tunisia','Tunisia','TN'), (175,'Turkey','Türkiye','TR'), (176,'Turkmenistan','Turkmenistan','TM'), (177,'Tuvalu','Tuvalu','TV'), (178,'Uganda','Uganda','UG'), (179,'Ukraine','Україна','UA'), (180,'United Arab Emirates','الإمارات العربيّة المتّحدة','AE'), (181,'United Kingdom','United Kingdom','GB'), (182,'Uruguay','Uruguay','UY'), (183,'Uzbekistan','Ўзбекистон','UZ'), (184,'Vanuatu','Vanuatu','VU'), (185,'Vatican','Vaticano','VA'), (186,'Venezuela','Venezuela','VE'), (187,'Vietnam','Vietnam','VN'), (188,'Yemen','Yemen','YE'), (189,'Zambia','Zambia','ZM'), (190,'Zimbabwe ','Zimbabwe ','ZW'), (191,'Arab Republic of Egypt','جمهورية مصر العربية','EG'), (192,'Puerto Rico','Puerto Rico','PR');
