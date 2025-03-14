-- MySQL dump 10.13  Distrib 8.0.40, for Win64 (x86_64)
--
-- Host: 127.0.0.1    Database: nutrition_tracker
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
-- Table structure for table `daily_goals`
--

DROP TABLE IF EXISTS `daily_goals`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `daily_goals` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `calories` int(11) NOT NULL,
  `protein` int(11) NOT NULL,
  `carbs` int(11) NOT NULL,
  `date_set` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `daily_goals`
--

LOCK TABLES `daily_goals` WRITE;
/*!40000 ALTER TABLE `daily_goals` DISABLE KEYS */;
/*!40000 ALTER TABLE `daily_goals` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `meals`
--

DROP TABLE IF EXISTS `meals`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `meals` (
  `meal_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `meal_name` varchar(255) NOT NULL,
  `calories` decimal(10,2) NOT NULL,
  `protein` decimal(10,2) NOT NULL,
  `carbs` decimal(10,2) NOT NULL,
  `date_added` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`meal_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `meals_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=41 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `meals`
--

LOCK TABLES `meals` WRITE;
/*!40000 ALTER TABLE `meals` DISABLE KEYS */;
INSERT INTO `meals` VALUES (1,1,'egg',147.00,12.50,0.70,'2025-03-08 10:49:44'),(2,1,'apple',53.00,0.30,14.10,'2025-03-08 11:03:47'),(3,1,'apple',53.00,0.30,14.10,'2025-03-08 11:04:04'),(4,1,'apple',53.00,0.30,14.10,'2025-03-08 11:04:06'),(5,1,'apple',53.00,0.30,14.10,'2025-03-08 11:04:07'),(6,1,'apple',53.00,0.30,14.10,'2025-03-08 11:04:08'),(7,1,'apple',53.00,0.30,14.10,'2025-03-08 11:04:08'),(8,1,'apple',53.00,0.30,14.10,'2025-03-08 11:04:09'),(9,1,'apple',53.00,0.30,14.10,'2025-03-07 16:00:00'),(10,1,'apple',53.00,0.30,14.10,'2025-03-07 16:00:00'),(11,1,'apple',53.00,0.30,14.10,'2025-03-07 16:00:00'),(12,1,'egg',147.00,12.50,0.70,'2025-03-08 11:04:34'),(13,1,'egg',147.00,12.50,0.70,'2025-03-08 11:04:35'),(14,1,'pizza',262.90,11.40,32.90,'2025-03-08 11:06:57'),(15,1,'steak',273.40,26.00,0.00,'2025-03-08 11:07:05'),(16,1,'turkey',193.10,28.60,0.10,'2025-03-08 11:09:15'),(17,1,'tacos',205.50,9.20,19.90,'2025-03-07 16:00:00'),(18,1,'tacos',205.50,9.20,19.90,'2025-03-07 16:00:00'),(19,1,'tacos',205.50,9.20,19.90,'2025-03-07 16:00:00'),(20,1,'cake',393.60,3.00,57.20,'2025-03-07 16:00:00'),(21,3,'venison',149.40,30.00,0.00,'2025-03-08 13:54:30'),(22,1,'coconut',455.30,3.20,52.30,'2025-03-08 15:47:23'),(23,1,'coconut',455.30,3.20,52.30,'2025-03-08 15:49:06'),(24,1,'sourdough bread',278.60,10.80,52.10,'2025-03-08 15:55:36'),(25,1,'sourdough bread',278.60,10.80,52.10,'2025-03-08 15:58:41'),(26,1,'sourdough bread',278.60,10.80,52.10,'2025-03-08 15:59:33'),(27,1,'sourdough bread',278.60,10.80,52.10,'2025-03-08 16:02:18'),(28,1,'sourdough bread',278.60,10.80,52.10,'2025-03-08 16:02:39'),(29,3,'egg',147.00,12.50,0.70,'2025-03-08 16:04:31'),(30,1,'tuna',133.30,29.40,0.00,'2025-03-08 16:05:14'),(31,3,'egg',147.00,12.50,0.70,'2025-03-08 16:05:30'),(32,3,'egg',147.00,12.50,0.70,'2025-03-08 16:05:33'),(33,1,'cheese burger',263.80,15.10,19.70,'2025-03-08 17:20:45'),(34,4,'pizza',262.90,11.40,32.90,'2025-03-10 03:37:47'),(35,4,'pie',232.20,1.90,34.00,'2025-03-10 03:37:56'),(36,4,'apple',53.00,0.30,14.10,'2025-03-10 03:44:01'),(37,5,'steak',273.40,26.00,0.00,'2025-03-10 03:44:26'),(38,8,'burger',237.70,15.20,18.10,'2025-03-14 02:23:21'),(39,8,'pizza',262.90,11.40,32.90,'2025-03-14 02:23:27'),(40,1,'pizza',262.90,11.40,32.90,'2025-03-14 03:07:42');
/*!40000 ALTER TABLE `meals` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `posts`
--

DROP TABLE IF EXISTS `posts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `posts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_name` varchar(255) NOT NULL,
  `user_avatar` varchar(255) DEFAULT 'photos/user.png',
  `post_content` text NOT NULL,
  `post_time` timestamp NOT NULL DEFAULT current_timestamp(),
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `posts`
--

LOCK TABLES `posts` WRITE;
/*!40000 ALTER TABLE `posts` DISABLE KEYS */;
INSERT INTO `posts` VALUES (1,'Alice Johnson','photos/user.png','Just finished my morning workout! Feeling great. ? #FitnessGoals','2025-03-12 14:19:55',0),(2,'Michael Smith','photos/user.png','Excited to start a new project today! Let’s get coding. ?‍? #DeveloperLife','2025-03-12 14:19:55',0),(3,'Emily Davis','photos/user.png','Taking a break with some coffee and a good book. ☕? #Relaxation','2025-03-12 14:19:55',0),(4,'James Wilson','photos/user.png','Had a great time at the networking event today! #CareerGrowth','2025-03-12 14:19:55',0),(5,'Sophia Martinez','photos/user.png','The weekend is finally here! Time to unwind and have fun. ? #WeekendVibes','2025-03-12 14:19:55',0),(6,'William Brown','photos/user.png','Remember, every day is a chance to improve yourself. #Motivation','2025-03-12 14:19:55',0),(7,'Olivia Taylor','photos/user.png','Finally reached my fitness goal after months of hard work! #Success','2025-03-12 14:19:55',0),(8,'Ethan Miller','photos/user.png','Just watched an amazing documentary about space. ? #ScienceLover','2025-03-12 14:19:55',0),(9,'Charlotte Anderson','photos/user.png','Starting a new book series today. Any recommendations? ? #Bookworm','2025-03-12 14:19:55',0),(10,'Daniel Thomas','photos/user.png','Had an amazing home-cooked meal tonight. ?️ #FoodieLife','2025-03-12 14:19:55',0),(11,'1','photos/user.png','lets go running #patotoya','2025-03-12 15:51:33',0),(12,'1','photos/user.png','lezgoo','2025-03-12 15:52:37',0),(13,'1','photos/user.png','lets go running #patotoya asodjfkhn','2025-03-12 15:56:26',0),(14,'admin1','photos/85488205667d39c354740f.jpg','runrunrun','2025-03-12 16:00:37',1),(15,'admin1','photos/85488205667d39c354740f.jpg','runrunr','2025-03-12 16:00:42',1),(16,'admin3','photos/default.png','123','2025-03-12 17:07:08',3),(17,'admin3','photos/default.png','runrunrun123','2025-03-12 17:14:37',3),(18,'admin3','photos/default.png','123qwerr','2025-03-12 17:23:00',3),(19,'admin1','photos/85488205667d39c354740f.jpg','12341234','2025-03-13 14:04:32',1),(20,'admin1','photos/85488205667d39c354740f.jpg','123','2025-03-13 15:50:09',1),(21,'admin1','photos/85488205667d39c354740f.jpg','654','2025-03-13 16:00:21',1),(22,'admin1','photos/85488205667d39c354740f.jpg','123','2025-03-13 16:04:01',1),(23,'admin1','photos/85488205667d39c354740f.jpg','123','2025-03-13 16:07:23',0),(24,'admin1','photos/85488205667d39c354740f.jpg','123','2025-03-13 16:07:26',0),(25,'admin1','photos/85488205667d39c354740f.jpg','123qwerr','2025-03-13 16:11:14',0),(26,'admin1','photos/85488205667d39c354740f.jpg','123','2025-03-13 16:20:37',0);
/*!40000 ALTER TABLE `posts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_name` varchar(45) NOT NULL,
  `last_name` varchar(45) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `age` int(11) DEFAULT NULL,
  `height` decimal(10,2) DEFAULT NULL,
  `weight` decimal(10,2) DEFAULT NULL,
  `profile_avatar` text DEFAULT 'photos/user.png',
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'admin1','Cortez','cortezvince08@gmail.com','5f4dcc3b5aa765d61d8327deb882cf99','Vincent Robert',19,123.00,123.00,'photos/85488205667d39c354740f.jpg'),(3,'admin3','cortez','admin@gmail.com','5f4dcc3b5aa765d61d8327deb882cf99','vincent',0,0.00,NULL,'photos/default.png'),(4,'admin11','Robert Cortez','ADMIN01@EMAIL.COM','319f4d26e3c536b5dd871bb2c52e3178','Vincent',0,0.00,NULL,'photos/default.png'),(5,'admin111','Robert Cortez','ADMIN011@EMAIL.COM','319f4d26e3c536b5dd871bb2c52e3178','Vincent',0,0.00,NULL,'photos/default.png'),(6,'user','sample','usersample@gmail.com','5e8ff9bf55ba3508199d22e984129be6','user',0,0.00,NULL,'photos/default.png'),(7,'TheAmazing1','Robert Cortez','cortezvince108@gmail.com','5f4dcc3b5aa765d61d8327deb882cf99','Vincent',NULL,NULL,NULL,'photos/user.png'),(8,'TheAmazing2','Robert Cortez','user@123gmail.com','5f4dcc3b5aa765d61d8327deb882cf99','Vincent',NULL,NULL,NULL,'photos/user.png');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-03-14 11:10:31
