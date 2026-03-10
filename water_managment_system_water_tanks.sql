-- MySQL dump 10.13  Distrib 8.0.44, for Win64 (x86_64)
--
-- Host: localhost    Database: water_managment_system
-- ------------------------------------------------------
-- Server version	8.0.44

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
-- Table structure for table `water_tanks`
--

DROP TABLE IF EXISTS `water_tanks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `water_tanks` (
  `TANK_ID` int NOT NULL,
  `TANK_NAME` varchar(30) DEFAULT NULL,
  `CAPACITY_LITERS` int DEFAULT NULL,
  `BUILDING_ID` int DEFAULT NULL,
  PRIMARY KEY (`TANK_ID`),
  KEY `BUILDING_ID` (`BUILDING_ID`),
  CONSTRAINT `water_tanks_ibfk_1` FOREIGN KEY (`BUILDING_ID`) REFERENCES `buildings` (`BUILDING_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `water_tanks`
--

LOCK TABLES `water_tanks` WRITE;
/*!40000 ALTER TABLE `water_tanks` DISABLE KEYS */;
INSERT INTO `water_tanks` VALUES (100,'Tank A',5000,1),(101,'Tank B',3000,1),(102,'Tank C',4500,2),(103,'Tank D',6000,3),(104,'Tank E',3500,3),(105,'Tank F',7000,4),(106,'Tank G',2500,5),(107,'Tank H',4000,6),(108,'Tank I',5500,7),(109,'Tank J',8000,2),(110,'Tank M',4500,9);
/*!40000 ALTER TABLE `water_tanks` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-03-10 18:06:15
