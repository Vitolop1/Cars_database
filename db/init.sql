-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: cars
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `cars`
--

DROP TABLE IF EXISTS `cars`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cars` (
  `car_id` int(11) NOT NULL AUTO_INCREMENT,
  `manufacturer_id` int(11) NOT NULL,
  `model` varchar(80) NOT NULL,
  `year` smallint(6) NOT NULL,
  `type_id` tinyint(4) NOT NULL,
  `country_of_origin` varchar(60) DEFAULT NULL,
  `price_usd` decimal(12,2) DEFAULT NULL,
  PRIMARY KEY (`car_id`),
  UNIQUE KEY `unq_make_model_year` (`manufacturer_id`,`model`,`year`),
  KEY `idx_cars_type` (`type_id`),
  CONSTRAINT `fk_cars_manufacturer` FOREIGN KEY (`manufacturer_id`) REFERENCES `manufacturers` (`manufacturer_id`) ON UPDATE CASCADE,
  CONSTRAINT `fk_cars_type` FOREIGN KEY (`type_id`) REFERENCES `vehicle_types` (`type_id`) ON UPDATE CASCADE,
  CONSTRAINT `chk_year` CHECK (`year` between 1886 and 2100)
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cars`
--

LOCK TABLES `cars` WRITE;
/*!40000 ALTER TABLE `cars` DISABLE KEYS */;
INSERT INTO `cars` VALUES (1,1,'320i',2022,1,'Germany',42000.00),(2,1,'X5',2023,3,'Germany',62000.00),(3,2,'A3',2021,1,'Germany',36000.00),(4,2,'Q5',2023,3,'Germany',55000.00),(5,3,'C-Class',2022,1,'Germany',45000.00),(6,3,'GLC',2023,3,'Germany',58000.00),(7,4,'Mustang',2023,5,'United States',42000.00),(8,4,'F-150',2023,4,'United States',47000.00),(9,5,'Camaro',2022,5,'United States',37000.00),(10,5,'Silverado',2023,4,'United States',46000.00),(11,6,'Corolla',2022,1,'Japan',21000.00),(12,6,'RAV4',2023,3,'Japan',32000.00),(13,7,'911 Carrera',2022,5,'Germany',105000.00),(14,7,'Cayenne',2023,3,'Germany',82000.00),(17,10,'Wrangler',2023,3,'United States',39000.00),(18,11,'Elantra',2023,1,'South Korea',20500.00),(19,12,'Civic',2023,1,'Japan',23000.00),(28,3,'SLK Mclaren',2015,5,'Germany',100000.00),(32,9,'Amarok TDI',2024,4,'Germany',75000.00),(34,7,'Cayenne',2025,3,'United States',100000.00);
/*!40000 ALTER TABLE `cars` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `manufacturers`
--

DROP TABLE IF EXISTS `manufacturers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `manufacturers` (
  `manufacturer_id` int(11) NOT NULL AUTO_INCREMENT,
  `manufacturer_name` varchar(80) NOT NULL,
  `country_of_origin` varchar(60) DEFAULT NULL,
  `founded_year` smallint(6) DEFAULT NULL,
  PRIMARY KEY (`manufacturer_id`),
  UNIQUE KEY `unq_manufacturer_name` (`manufacturer_name`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `manufacturers`
--

LOCK TABLES `manufacturers` WRITE;
/*!40000 ALTER TABLE `manufacturers` DISABLE KEYS */;
INSERT INTO `manufacturers` VALUES (1,'BMW','Germany',1916),(2,'Audi','Germany',1909),(3,'Mercedes-Benz','Germany',1926),(4,'Ford','United States',1903),(5,'Chevrolet','United States',1911),(6,'Toyota','Japan',1937),(7,'Porsche','Germany',1931),(8,'Tesla','United States',2003),(9,'Volkswagen','Germany',1937),(10,'Jeep','United States',1941),(11,'Hyundai','South Korea',1967),(12,'Honda','Japan',1948);
/*!40000 ALTER TABLE `manufacturers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vehicle_types`
--

DROP TABLE IF EXISTS `vehicle_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vehicle_types` (
  `type_id` tinyint(4) NOT NULL AUTO_INCREMENT,
  `type_name` varchar(40) NOT NULL,
  PRIMARY KEY (`type_id`),
  UNIQUE KEY `unq_type_name` (`type_name`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vehicle_types`
--

LOCK TABLES `vehicle_types` WRITE;
/*!40000 ALTER TABLE `vehicle_types` DISABLE KEYS */;
INSERT INTO `vehicle_types` VALUES (6,'Convertible'),(5,'Coupe'),(9,'Electric'),(2,'Hatchback'),(10,'Hybrid'),(7,'Minivan'),(4,'Pickup'),(1,'Sedan'),(3,'SUV'),(8,'Wagon');
/*!40000 ALTER TABLE `vehicle_types` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-11-17 19:22:58
