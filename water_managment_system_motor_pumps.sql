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
-- Table structure for table `motor_pumps`
--

DROP TABLE IF EXISTS `motor_pumps`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `motor_pumps` (
  `MOTOR_ID` int NOT NULL,
  `MOTOR_NAME` varchar(30) DEFAULT NULL,
  `POWER_RATING` varchar(20) DEFAULT NULL,
  `TANK_ID` int DEFAULT NULL,
  PRIMARY KEY (`MOTOR_ID`),
  KEY `TANK_ID` (`TANK_ID`),
  CONSTRAINT `motor_pumps_ibfk_1` FOREIGN KEY (`TANK_ID`) REFERENCES `water_tanks` (`TANK_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `motor_pumps`
--

LOCK TABLES `motor_pumps` WRITE;
/*!40000 ALTER TABLE `motor_pumps` DISABLE KEYS */;
INSERT INTO `motor_pumps` VALUES (110,'M-A','1',110),(201,'M1','2HP',100),(202,'M2','3HP',101),(203,'M3','2HP',102),(204,'M4','5HP',103),(205,'M5','3HP',104),(206,'M6','4HP',105),(207,'M7','2HP',106),(208,'M8','3HP',107),(209,'M9','4HP',108),(210,'M10','5HP',109);
/*!40000 ALTER TABLE `motor_pumps` ENABLE KEYS */;
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
