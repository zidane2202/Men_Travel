-- MySQL dump 10.13  Distrib 8.0.40, for Win64 (x86_64)
--
-- Host: localhost    Database: men_travel
-- ------------------------------------------------------
-- Server version	8.2.0

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
-- Table structure for table `voyages`
--

DROP TABLE IF EXISTS `voyages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `voyages` (
  `id_voyage` int unsigned NOT NULL AUTO_INCREMENT,
  `ville_depart` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ville_arrivee` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `date_depart` datetime NOT NULL,
  `id_vehicule` int unsigned NOT NULL,
  `id_chauffeur` int unsigned NOT NULL,
  `prix` decimal(10,2) NOT NULL DEFAULT '0.00',
  `statut` enum('programmé','en cours','terminé','annulé') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'programmé',
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_voyage`),
  KEY `fk_voyages_vehicules_idx` (`id_vehicule`),
  KEY `fk_voyages_chauffeurs_idx` (`id_chauffeur`),
  KEY `idx_villes_date` (`ville_depart`,`ville_arrivee`,`date_depart`),
  CONSTRAINT `fk_voyages_chauffeurs` FOREIGN KEY (`id_chauffeur`) REFERENCES `chauffeurs` (`id_chauffeur`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_voyages_vehicules` FOREIGN KEY (`id_vehicule`) REFERENCES `vehicules` (`id_vehicule`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `voyages`
--

LOCK TABLES `voyages` WRITE;
/*!40000 ALTER TABLE `voyages` DISABLE KEYS */;
INSERT INTO `voyages` VALUES (1,'Yaoundé','Douala','2025-11-17 13:53:37',1,2,7500.00,'programmé',1),(2,'Douala','Yaoundé','2025-11-17 13:53:37',2,3,8000.00,'programmé',0),(3,'Yaoundé','Bafoussam','2025-11-18 13:53:37',1,3,7000.00,'programmé',0),(4,'Yaoundé','Douala','2025-11-30 08:00:00',1,2,7000.00,'programmé',0),(5,'Bafoussam','Douala','2025-11-22 12:30:00',2,3,10000.00,'programmé',0),(6,'Bafoussam','Douala','2025-11-22 12:30:00',2,3,10000.00,'programmé',0),(7,'Yaoundé','Douala','2025-11-20 12:30:00',1,3,10000.00,'programmé',0);
/*!40000 ALTER TABLE `voyages` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-11-17  0:14:09
