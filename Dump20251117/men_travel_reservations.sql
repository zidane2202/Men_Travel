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
-- Table structure for table `reservations`
--

DROP TABLE IF EXISTS `reservations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `reservations` (
  `id_reservation` int unsigned NOT NULL AUTO_INCREMENT,
  `id_client` int unsigned NOT NULL,
  `id_voyage` int unsigned NOT NULL,
  `id_commande` int unsigned NOT NULL,
  `type_reservation` enum('siege','colis') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'siege',
  `numero_siege` int unsigned DEFAULT NULL,
  `statut` enum('EN_ATTENTE','PAYEE','ANNULEE','TERMINEE') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'EN_ATTENTE',
  `date_reservation` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `billet_pdf_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id_reservation`),
  UNIQUE KEY `idx_siege_voyage` (`id_voyage`,`numero_siege`),
  KEY `fk_reservations_clients_idx` (`id_client`),
  KEY `fk_reservations_voyages_idx` (`id_voyage`),
  KEY `idx_statut_reservation` (`statut`),
  KEY `fk_reservations_commandes_idx` (`id_commande`),
  CONSTRAINT `fk_reservations_clients` FOREIGN KEY (`id_client`) REFERENCES `clients` (`id_client`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_reservations_voyages` FOREIGN KEY (`id_voyage`) REFERENCES `voyages` (`id_voyage`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reservations`
--

LOCK TABLES `reservations` WRITE;
/*!40000 ALTER TABLE `reservations` DISABLE KEYS */;
INSERT INTO `reservations` VALUES (1,2,4,1,'siege',3,'PAYEE','2025-11-16 19:08:31',NULL),(2,2,4,1,'siege',4,'PAYEE','2025-11-16 19:08:31',NULL),(3,2,4,1,'siege',8,'PAYEE','2025-11-16 19:08:31',NULL),(4,2,4,1,'siege',13,'PAYEE','2025-11-16 19:08:31',NULL),(5,2,4,2,'siege',27,'EN_ATTENTE','2025-11-16 19:09:04',NULL),(6,2,4,2,'siege',24,'EN_ATTENTE','2025-11-16 19:09:04',NULL),(7,2,4,2,'siege',20,'EN_ATTENTE','2025-11-16 19:09:04',NULL),(8,2,4,3,'siege',7,'PAYEE','2025-11-16 19:18:29',NULL),(9,2,2,4,'siege',23,'PAYEE','2025-11-16 20:34:23',NULL),(10,2,2,4,'siege',19,'PAYEE','2025-11-16 20:34:23',NULL);
/*!40000 ALTER TABLE `reservations` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-11-17  0:14:10
