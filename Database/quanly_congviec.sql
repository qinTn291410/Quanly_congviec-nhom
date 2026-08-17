CREATE DATABASE  IF NOT EXISTS `quanly_congviec` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci */;
USE `quanly_congviec`;
-- MySQL dump 10.13  Distrib 8.0.45, for Win64 (x86_64)
--
-- Host: localhost    Database: quanly_congviec
-- ------------------------------------------------------
-- Server version	5.5.5-10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `activity_logs`
--

DROP TABLE IF EXISTS `activity_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `activity_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `action` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `project_id` (`project_id`),
  CONSTRAINT `activity_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `activity_logs_ibfk_2` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `activity_logs`
--

LOCK TABLES `activity_logs` WRITE;
/*!40000 ALTER TABLE `activity_logs` DISABLE KEYS */;
INSERT INTO `activity_logs` VALUES (16,8,1,'đã thêm một công việc mới: <b>Chọn địa điểm</b>','2026-08-17 09:06:26'),(17,8,1,'đã chuyển trạng thái công việc <b>Chọn địa điểm</b> sang <b>IN PROGRESS</b>','2026-08-17 09:06:30'),(18,8,1,'đã chuyển trạng thái công việc <b>Chọn địa điểm</b> sang <b>REVIEW</b>','2026-08-17 09:06:32'),(19,8,1,'đã chuyển trạng thái công việc <b>Chọn địa điểm</b> sang <b>DONE</b>','2026-08-17 09:06:34');
/*!40000 ALTER TABLE `activity_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Temporary view structure for view `fewgeg`
--

DROP TABLE IF EXISTS `fewgeg`;
/*!50001 DROP VIEW IF EXISTS `fewgeg`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `fewgeg` AS SELECT 
 1 AS `id`,
 1 AS `user_id`,
 1 AS `title`,
 1 AS `description`,
 1 AS `start_date`,
 1 AS `due_date`,
 1 AS `priority`,
 1 AS `status`,
 1 AS `created_at`*/;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `goals`
--

DROP TABLE IF EXISTS `goals`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `goals` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `type` enum('Tuần','Tháng','Quý') DEFAULT 'Tuần',
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `status` enum('Đang tiến hành','Hoàn thành') DEFAULT 'Đang tiến hành',
  PRIMARY KEY (`id`),
  KEY `fk_goals_users` (`user_id`),
  CONSTRAINT `fk_goals_users` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `goals`
--

LOCK TABLES `goals` WRITE;
/*!40000 ALTER TABLE `goals` DISABLE KEYS */;
INSERT INTO `goals` VALUES (4,7,'Đồ án môn Kỹ năng nghề','Tuần','2026-03-13','2026-05-18','Đang tiến hành'),(5,1,'MixiCity','Quý','2026-08-15','2027-01-01','Đang tiến hành'),(6,1,'W/N','Tháng','2026-05-01','2026-07-31','Đang tiến hành');
/*!40000 ALTER TABLE `goals` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notifications`
--

DROP TABLE IF EXISTS `notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `notifications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_notifications_users` (`user_id`),
  CONSTRAINT `fk_notifications_users` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notifications`
--

LOCK TABLES `notifications` WRITE;
/*!40000 ALTER TABLE `notifications` DISABLE KEYS */;
INSERT INTO `notifications` VALUES (1,3,'CÓ VIỆC MỚI: Sếp vừa giao cho bạn việc \'456\'',1,'2026-05-16 12:13:56'),(2,3,'CÓ VIỆC MỚI: Sếp vừa giao cho bạn việc \'213\'',1,'2026-05-16 12:18:39'),(3,3,'CÓ VIỆC MỚI: Sếp vừa giao cho bạn việc \'dừ\'',1,'2026-05-16 12:24:35'),(4,3,'CÓ VIỆC MỚI: Sếp vừa giao cho bạn việc \'fff\'',0,'2026-05-16 12:25:26'),(5,4,'CÓ VIỆC MỚI: Sếp vừa giao cho bạn việc \'789\'',1,'2026-05-17 19:17:42'),(6,4,'CẬP NHẬT: Công việc \'789\' vừa bị chuyển trạng thái thành: DONE',1,'2026-05-17 19:18:09'),(7,4,'CÓ VIỆC MỚI: Bạn vừa được giao việc \'khảo sát\'',1,'2026-08-17 08:23:49'),(8,8,'CÓ VIỆC MỚI: Bạn vừa được giao việc \'mẻbfjb f\'',1,'2026-08-17 08:25:20'),(9,4,'TAG: Huỳnh Bá Việt Tín vừa nhắc đến bạn trong kênh thảo luận dự án. Vào xem ngay!',1,'2026-08-17 08:56:52');
/*!40000 ALTER TABLE `notifications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `project_comments`
--

DROP TABLE IF EXISTS `project_comments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `project_comments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `content` text DEFAULT NULL,
  `file_url` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `project_comments`
--

LOCK TABLES `project_comments` WRITE;
/*!40000 ALTER TABLE `project_comments` DISABLE KEYS */;
INSERT INTO `project_comments` VALUES (1,4,1,'dqdd',NULL,'2026-05-16 09:53:56'),(2,4,1,'alo alo',NULL,'2026-05-16 09:54:02'),(3,4,1,'em gửi file','1778925254_task_detail.php','2026-05-16 09:54:14'),(4,4,1,'case \'add-project-comment\':\r\n        require_once PROJECT_ROOT . \'/src/Controllers/TeamController.php\';\r\n        $chatController = new \\Tinhu\\TaskManager\\Controllers\\TeamController();\r\n        $chatController->addProjectComment();\r\n        break;',NULL,'2026-05-16 09:54:32'),(5,4,3,'nghe',NULL,'2026-05-16 09:58:28'),(6,4,1,'alo alo',NULL,'2026-05-16 10:07:24'),(7,4,8,'aloa lo',NULL,'2026-05-16 10:18:20'),(8,4,1,'oke oke',NULL,'2026-05-16 10:18:35'),(9,4,8,'123',NULL,'2026-05-16 10:18:50'),(10,4,1,'a độ mixi',NULL,'2026-05-16 10:39:56'),(11,4,8,'fwvfwfhefb',NULL,'2026-05-16 10:43:28'),(12,4,1,'alo alo',NULL,'2026-05-16 11:00:17'),(13,4,8,'sdsg',NULL,'2026-05-16 11:00:22'),(14,5,1,'alo',NULL,'2026-08-14 18:26:35'),(15,5,1,'hú',NULL,'2026-08-14 18:39:59'),(16,8,1,'@Mayday làm việc đê cu elm',NULL,'2026-08-17 08:56:52');
/*!40000 ALTER TABLE `project_comments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `projects`
--

DROP TABLE IF EXISTS `projects`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `projects` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `team_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `manager_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `projects`
--

LOCK TABLES `projects` WRITE;
/*!40000 ALTER TABLE `projects` DISABLE KEYS */;
INSERT INTO `projects` VALUES (5,5,'Mixi cup','a lề a lế a lê','2026-05-01','2026-05-31',1,'2026-05-13 19:13:27'),(7,6,'Susan 0175','lol','2026-05-02','2026-05-31',7,'2026-05-13 19:16:47'),(8,7,'khảo sát','test','2026-08-17','2027-03-17',1,'2026-08-17 08:22:38');
/*!40000 ALTER TABLE `projects` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `system_configs`
--

DROP TABLE IF EXISTS `system_configs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `system_configs` (
  `config_key` varchar(50) NOT NULL,
  `config_value` text DEFAULT NULL,
  PRIMARY KEY (`config_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `system_configs`
--

LOCK TABLES `system_configs` WRITE;
/*!40000 ALTER TABLE `system_configs` DISABLE KEYS */;
INSERT INTO `system_configs` VALUES ('smtp_host','smtp.gmail.com'),('smtp_pass','uldy pplc xjel ercz'),('smtp_port','587'),('smtp_user','tinhuynhba1289@gmail.com'),('system_email_enabled','1'),('task_labels','Công việc, Học tập, Sức khỏe, Tài chính, Khác'),('task_priorities','Low, Medium, High'),('task_statuses','Backlog, To-do, Doing, Review, Pending, Done');
/*!40000 ALTER TABLE `system_configs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tasks`
--

DROP TABLE IF EXISTS `tasks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tasks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `priority` enum('Low','Medium','High') DEFAULT 'Medium',
  `status` enum('To-do','Doing','Done','Pending') DEFAULT 'To-do',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `category` varchar(50) DEFAULT 'Khác',
  `goal` varchar(50) DEFAULT 'Không',
  `is_reminded` tinyint(1) DEFAULT 0,
  `goal_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_tasks_users` (`user_id`),
  CONSTRAINT `fk_tasks_users` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `tasks_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=37 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tasks`
--

LOCK TABLES `tasks` WRITE;
/*!40000 ALTER TABLE `tasks` DISABLE KEYS */;
INSERT INTO `tasks` VALUES (27,3,'qưe','qưe','2026-04-09','2026-04-27','High','To-do','2026-04-28 12:39:20','Học tập','Thói quen',1,NULL),(30,7,'miximoi','na na na na phung thanh do','2026-04-30','2026-05-12','Medium','Done','2026-05-13 16:17:30','Công việc','Không',1,4),(31,7,'123324','3213124','2026-05-01','2026-05-17','High','Doing','2026-05-13 20:17:33','Tài chính','Không',0,4),(32,1,'1234','hgbjvbsgvlrjgn','2026-08-14','2026-08-31','Medium','To-do','2026-08-14 16:59:40','Khác','Không',0,NULL),(35,1,'SSD','3107-SSD(2026)','2026-03-01','2026-07-31','High','Done','2026-08-17 08:47:31','Khác','Không',1,6),(36,1,'Text07','3107-Text(2025)','2025-01-17','2025-07-31','High','Doing','2026-08-17 08:48:48','Khác','Không',1,6);
/*!40000 ALTER TABLE `tasks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `team_comments`
--

DROP TABLE IF EXISTS `team_comments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `team_comments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `team_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `content` text DEFAULT NULL,
  `file_url` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `team_comments`
--

LOCK TABLES `team_comments` WRITE;
/*!40000 ALTER TABLE `team_comments` DISABLE KEYS */;
INSERT INTO `team_comments` VALUES (1,4,1,'alo\r\n\\',NULL,'2026-05-16 10:33:09'),(2,4,8,'nghe',NULL,'2026-05-16 10:33:18'),(3,4,8,'alo alo',NULL,'2026-05-16 10:40:21'),(4,4,1,'hú hú ạc ạc',NULL,'2026-05-16 10:59:36'),(5,4,8,'acj acj',NULL,'2026-05-16 10:59:52'),(6,4,1,'alo',NULL,'2026-05-17 19:00:41'),(7,4,4,'hú hú',NULL,'2026-05-17 19:00:50'),(8,4,4,'aloalo',NULL,'2026-05-18 12:46:08'),(9,5,1,'alo',NULL,'2026-08-14 16:30:47'),(10,5,1,'alo',NULL,'2026-08-14 16:31:43'),(11,5,1,'hvjbjhb',NULL,'2026-08-14 16:37:32'),(12,5,1,'hú hú',NULL,'2026-08-14 16:38:30'),(13,7,4,'hú',NULL,'2026-08-17 08:12:21'),(14,7,1,'alo',NULL,'2026-08-17 08:22:02'),(15,5,8,'aloalo',NULL,'2026-08-17 08:30:54'),(16,5,1,'hú hú ạc ạc',NULL,'2026-08-17 08:31:12');
/*!40000 ALTER TABLE `team_comments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `team_members`
--

DROP TABLE IF EXISTS `team_members`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `team_members` (
  `team_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `role` enum('Leader','Manager','Member','Viewer') DEFAULT 'Member',
  `joined_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`team_id`,`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `team_members`
--

LOCK TABLES `team_members` WRITE;
/*!40000 ALTER TABLE `team_members` DISABLE KEYS */;
INSERT INTO `team_members` VALUES (2,1,'Leader','2026-05-13 16:26:35'),(5,1,'Leader','2026-05-13 19:10:43'),(5,7,'Member','2026-05-13 19:15:18'),(5,8,'Member','2026-08-17 08:30:23'),(6,1,'Member','2026-05-13 19:14:23'),(6,7,'Leader','2026-05-13 19:14:10'),(7,1,'Leader','2026-08-17 08:11:55'),(7,3,'Member','2026-08-17 08:56:02'),(7,4,'Manager','2026-08-17 08:12:07'),(7,8,'Viewer','2026-08-17 08:20:43');
/*!40000 ALTER TABLE `team_members` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `team_task_comments`
--

DROP TABLE IF EXISTS `team_task_comments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `team_task_comments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `task_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `content` text DEFAULT NULL,
  `file_url` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `team_task_comments`
--

LOCK TABLES `team_task_comments` WRITE;
/*!40000 ALTER TABLE `team_task_comments` DISABLE KEYS */;
INSERT INTO `team_task_comments` VALUES (1,7,1,'lỗi rồi nè','1778692102_Screenshot 2026-05-13 233833.png','2026-05-13 17:08:22'),(2,4,1,'oke rồi nhá',NULL,'2026-05-13 17:57:08'),(3,4,7,'oke sếp',NULL,'2026-05-13 17:58:06'),(4,4,7,'oke oke oke oke',NULL,'2026-05-13 18:17:49'),(5,9,1,'alo a độ mixi','1778923575_OIP.jpg','2026-05-16 09:26:15'),(6,10,8,'alo alo',NULL,'2026-05-16 10:17:54'),(7,15,1,'alo alo',NULL,'2026-05-16 10:39:20'),(8,15,1,'dfbvwehjfbw',NULL,'2026-05-16 10:43:01'),(9,10,8,'123',NULL,'2026-05-16 11:00:41'),(10,10,1,'123',NULL,'2026-05-16 11:00:46'),(11,19,8,'ban kho ga',NULL,'2026-06-08 11:29:01'),(12,11,1,'alo',NULL,'2026-08-14 18:17:59'),(13,21,1,'alo',NULL,'2026-08-14 18:18:21');
/*!40000 ALTER TABLE `team_task_comments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `team_tasks`
--

DROP TABLE IF EXISTS `team_tasks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `team_tasks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `assigned_to` int(11) DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `status` enum('Backlog','In Progress','Review','Done') DEFAULT 'Backlog',
  `priority` enum('Low','Medium','High') DEFAULT 'Medium',
  `due_date` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `team_tasks`
--

LOCK TABLES `team_tasks` WRITE;
/*!40000 ALTER TABLE `team_tasks` DISABLE KEYS */;
INSERT INTO `team_tasks` VALUES (11,5,1,'Bàn chuyện','Sắp xếp lịch','Review','High','2026-05-31','2026-05-13 19:46:20'),(21,5,1,'fwrwgwt','ưgwtwgwwg','Backlog','Medium','2026-08-31','2026-08-14 17:35:26'),(22,8,4,'khảo sát','','Done','Medium','2026-08-31','2026-08-17 08:23:49'),(24,8,1,'Khảo sát','khảo sát sân','Done','High','2026-09-30','2026-08-17 08:50:23'),(25,8,4,'đặt sân','đặt sân','In Progress','High','2026-10-14','2026-08-17 08:51:29'),(26,8,8,'dự trù ','dự trù kinh phí','Review','Medium','2026-08-18','2026-08-17 08:52:24'),(27,8,8,'thông báo ','thông báo cho thí sinh','Backlog','Medium','2026-08-31','2026-08-17 08:55:07'),(28,8,3,'Chọn địa điểm','Chọn địa điểm tổ chức','Done','Medium','2026-08-17','2026-08-17 09:06:26');
/*!40000 ALTER TABLE `team_tasks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `teams`
--

DROP TABLE IF EXISTS `teams`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `teams` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_teams_users` (`created_by`),
  CONSTRAINT `fk_teams_users` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `teams`
--

LOCK TABLES `teams` WRITE;
/*!40000 ALTER TABLE `teams` DISABLE KEYS */;
INSERT INTO `teams` VALUES (5,'500Bros','Bomman',1,'2026-05-13 19:10:43'),(6,'SBTC','Anh Trung Sa Đéc',7,'2026-05-13 19:14:10'),(7,'Mixisong','Mixisong2026',1,'2026-08-17 08:11:55');
/*!40000 ALTER TABLE `teams` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fullname` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','user') DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `phone` varchar(20) DEFAULT NULL,
  `avatar` varchar(255) DEFAULT 'default.png',
  `notifications` tinyint(1) DEFAULT 1,
  `language` varchar(10) DEFAULT 'vi',
  `timezone` varchar(50) DEFAULT 'Asia/Ho_Chi_Minh',
  `dob` date DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `bio` text DEFAULT NULL,
  `notify_deadline` tinyint(1) DEFAULT 1,
  `notify_alerts` tinyint(1) DEFAULT 1,
  `is_locked` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Huỳnh Bá Việt Tín','tinhuynhba1289@gmail.com','$2y$10$cM28PmsO06ubReCxH/i4u.xmMeXj6Y6Knuy0VA8/hzAJ31lILKJmK','admin','2026-04-26 09:15:25','0868894087','1779856225_OIP.jpg',1,'vi','Asia/Ho_Chi_Minh','2004-01-29','Ngõ 120, Yên Lãng, Hà Lội','Anh tên là Nhím, Anh tên là Nhím, Anh tên là Nhím.',1,0,0),(3,'hbvtin-cntt17','hbvtin-cntt17@tdu.edu.vn','$2y$10$/4jmgya9lAQe88g7jtqxBujVK1V8X5vv7IbMQrqGGyS6ZCzBRSln6','user','2026-04-28 12:33:24','0956789JQK','1777379739_meomeo.jpg',1,'vi','Asia/Ho_Chi_Minh','2010-01-27','Ngõ 120, Yên Lãng, Hà Lội','meo meo meo meo',1,0,1),(4,'Mayday','maydayzzz7879@gmail.com','$2y$10$ZpSzfgHPLrFWvl3tutta9.HjkGCJdhLOXRn5cmbby0UiX9MJRmvk.','user','2026-04-28 18:26:44','','1786954264_Switch.jpg',1,'en','Asia/Ho_Chi_Minh','0000-00-00','','',0,1,0),(6,'Ngọc','nguyenhongngoc02102004@gmail.com','$2y$10$.lEgVv1ez/g8ROsqN.zzU.GKbyeCzUem1ApFjUN9YBXevMy7ZwLAC','user','2026-05-09 09:58:42',NULL,'default.png',1,'vi','Asia/Ho_Chi_Minh',NULL,NULL,NULL,1,1,1),(7,'Huynhbaviettinzzz','tinfclienquan@gmail.com','$2y$10$b3bRbhJHNDsJXZsxCNvHueLK7jqIlHxZJPCV60fJV54L/SNyqaN5q','user','2026-05-13 16:02:44','0868894789','1778688421_meomeo.jpg',1,'vi','Asia/Ho_Chi_Minh','2014-01-28','fggsgdgsg','gằhtesettevhbawhfhabf',1,1,0),(8,'baochou','dangngocbaochau9597@gmail.com','$2y$10$QxqddMNDsjEP.ASZQ/U2ZOjC9yTcxmtPFtxm3C0Ioa64D.7embfma','user','2026-05-16 10:16:30','09123456789jqk','1780918031_z7905542663616_6e3b3bd5a76dd45c476a805a1169a8ca.jpg',1,'vi','Asia/Ho_Chi_Minh','2026-06-26','Ngõ 120, Yên Lãng, Hà Lội','cam on a do mixi',1,1,0),(19,'tinhuynhp','tinhuynhq149411@gmail.com','$2y$10$hy/dsuoWqd2tULNc64rm4eDrQCP9hWieAG0oxls9/X5pfddpTgwQq','user','2026-05-17 19:47:29',NULL,'default.png',1,'vi','Asia/Ho_Chi_Minh',NULL,NULL,NULL,1,1,1);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Final view structure for view `fewgeg`
--

/*!50001 DROP VIEW IF EXISTS `fewgeg`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY INVOKER */
/*!50001 VIEW `fewgeg` AS select `tasks`.`id` AS `id`,`tasks`.`user_id` AS `user_id`,`tasks`.`title` AS `title`,`tasks`.`description` AS `description`,`tasks`.`start_date` AS `start_date`,`tasks`.`due_date` AS `due_date`,`tasks`.`priority` AS `priority`,`tasks`.`status` AS `status`,`tasks`.`created_at` AS `created_at` from `tasks` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-08-17 16:57:02
