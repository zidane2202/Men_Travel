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
-- Table structure for table `colis`
--

DROP TABLE IF EXISTS `colis`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `colis` (
  `id_colis` int unsigned NOT NULL AUTO_INCREMENT,
  `id_client` int unsigned NOT NULL,
  `id_voyage` int unsigned DEFAULT NULL,
  `poids` decimal(5,2) NOT NULL,
  `valeur_declaree` decimal(10,2) DEFAULT NULL,
  `frais` decimal(10,2) NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `ville_depart` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ville_arrivee` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `telephone_destinataire` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `statut` enum('ENREGISTRE','EN_TRANSIT','LIVRE','ANNULE') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'ENREGISTRE',
  `date_enregistrement` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_colis`),
  KEY `fk_colis_clients_idx` (`id_client`),
  KEY `fk_colis_voyages_idx` (`id_voyage`),
  KEY `idx_statut_colis` (`statut`),
  CONSTRAINT `fk_colis_clients` FOREIGN KEY (`id_client`) REFERENCES `clients` (`id_client`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_colis_voyages` FOREIGN KEY (`id_voyage`) REFERENCES `voyages` (`id_voyage`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `colis`
--

LOCK TABLES `colis` WRITE;
/*!40000 ALTER TABLE `colis` DISABLE KEYS */;
INSERT INTO `colis` VALUES (1,1,NULL,5.50,NULL,2500.00,'Petit colis de documents','Yaoundé','Douala','+237 699 98 76 54','ENREGISTRE','2025-11-16 13:53:37');
/*!40000 ALTER TABLE `colis` ENABLE KEYS */;
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
