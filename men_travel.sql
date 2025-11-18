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
-- Table structure for table `agences`
--

DROP TABLE IF EXISTS `agences`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `agences` (
  `id_agence` int unsigned NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ville` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `adresse` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `telephone` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id_agence`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `agences`
--

LOCK TABLES `agences` WRITE;
/*!40000 ALTER TABLE `agences` DISABLE KEYS */;
INSERT INTO `agences` VALUES (1,'Blue Bird Express - Yaoundé','Yaoundé','Avenue des Banques, Quartier du Lac','+237 699 00 00 00');
/*!40000 ALTER TABLE `agences` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `chauffeurs`
--

DROP TABLE IF EXISTS `chauffeurs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `chauffeurs` (
  `id_chauffeur` int unsigned NOT NULL,
  `permis_numero` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id_chauffeur`),
  UNIQUE KEY `permis_numero` (`permis_numero`),
  CONSTRAINT `fk_chauffeurs_employes` FOREIGN KEY (`id_chauffeur`) REFERENCES `employes` (`id_employe`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `chauffeurs`
--

LOCK TABLES `chauffeurs` WRITE;
/*!40000 ALTER TABLE `chauffeurs` DISABLE KEYS */;
INSERT INTO `chauffeurs` VALUES (7,'1234fd1234rtg'),(2,'P-CM-0001'),(3,'P-CM-0002');
/*!40000 ALTER TABLE `chauffeurs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `clients`
--

DROP TABLE IF EXISTS `clients`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `clients` (
  `id_client` int unsigned NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `prenom` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `date_naissance` date NOT NULL,
  `sexe` enum('HOMME','FEMME') COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `telephone` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `adresse` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mot_de_passe` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `photo_profil` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Chemin vers l''image du profil',
  `date_inscription` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_client`),
  UNIQUE KEY `email_UNIQUE` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `clients`
--

LOCK TABLES `clients` WRITE;
/*!40000 ALTER TABLE `clients` DISABLE KEYS */;
INSERT INTO `clients` VALUES (1,'Kamga','Alain','1992-04-20','HOMME','alain.kamga@test.com','+237 690 12 34 56','Rue 100, Yaoundé','password123',NULL,'2025-11-16 13:53:37'),(2,'Sontia','Zidane','2025-11-06','HOMME','zsontia@gmail.com','+237 693273103','douala','$2y$10$Fkzy2SyI/ijFSfI1s.Gnb.AzeS.yxA7TRUx0cN0xOavxJhyzOD4na',NULL,'2025-11-16 14:39:32'),(3,'ondoa','tamar','2025-11-08','FEMME','zidanesontia22@icloud.com','+237694274104','yaounde','$2y$10$q5yYcukNEuO6oaJBO4W5d.udbDJGtYodwh6IS5T0.sSwxe5gUZPAO',NULL,'2025-11-16 20:35:41'),(4,'ETOUNDI','GABRIELLE','2005-12-11','FEMME','gabrielleetoundi1212@gmail.com','690522836','Yaoundé','$2y$10$YpKM2302cNtK7UelLJRX3ex9.YkKiEPbQ9OdRkiZ/W24k2VnrN9bC',NULL,'2025-11-18 11:41:12');
/*!40000 ALTER TABLE `clients` ENABLE KEYS */;
UNLOCK TABLES;

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

--
-- Table structure for table `commandes`
--

DROP TABLE IF EXISTS `commandes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `commandes` (
  `id_commande` int unsigned NOT NULL AUTO_INCREMENT,
  `id_client` int unsigned NOT NULL,
  `id_voyage` int unsigned NOT NULL,
  `montant_total` decimal(10,2) NOT NULL,
  `statut` enum('EN_ATTENTE','PAYEE','ANNULEE') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'EN_ATTENTE',
  `date_commande` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_commande`),
  KEY `fk_commandes_client_idx` (`id_client`),
  KEY `fk_commandes_voyage_idx` (`id_voyage`),
  CONSTRAINT `fk_commandes_client` FOREIGN KEY (`id_client`) REFERENCES `clients` (`id_client`),
  CONSTRAINT `fk_commandes_voyage` FOREIGN KEY (`id_voyage`) REFERENCES `voyages` (`id_voyage`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `commandes`
--

LOCK TABLES `commandes` WRITE;
/*!40000 ALTER TABLE `commandes` DISABLE KEYS */;
INSERT INTO `commandes` VALUES (1,2,4,28000.00,'PAYEE','2025-11-16 19:08:31'),(2,2,4,21000.00,'EN_ATTENTE','2025-11-16 19:09:04'),(3,2,4,7000.00,'PAYEE','2025-11-16 19:18:29'),(4,2,2,16000.00,'PAYEE','2025-11-16 20:34:23'),(5,2,2,8000.00,'PAYEE','2025-11-17 00:44:20'),(6,2,2,48000.00,'PAYEE','2025-11-17 10:23:04'),(7,2,2,16000.00,'PAYEE','2025-11-17 13:21:16'),(8,2,2,8000.00,'PAYEE','2025-11-17 17:17:20'),(9,2,5,40000.00,'PAYEE','2025-11-17 19:24:29'),(10,2,7,40000.00,'PAYEE','2025-11-18 09:34:39'),(11,2,3,35000.00,'PAYEE','2025-11-18 11:23:58');
/*!40000 ALTER TABLE `commandes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `comptes_employes`
--

DROP TABLE IF EXISTS `comptes_employes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `comptes_employes` (
  `id_compte` int unsigned NOT NULL AUTO_INCREMENT,
  `id_employe` int unsigned NOT NULL,
  `email` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mot_de_passe` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `est_super_admin` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_compte`),
  UNIQUE KEY `id_employe` (`id_employe`),
  UNIQUE KEY `email` (`email`),
  KEY `fk_comptes_employes_employes_idx` (`id_employe`),
  CONSTRAINT `fk_comptes_employes_employes` FOREIGN KEY (`id_employe`) REFERENCES `employes` (`id_employe`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `comptes_employes`
--

LOCK TABLES `comptes_employes` WRITE;
/*!40000 ALTER TABLE `comptes_employes` DISABLE KEYS */;
INSERT INTO `comptes_employes` VALUES (1,1,'super.admin@bluebird.com','admin123',1),(2,5,'marie.biloa@bluebird.com','defaultPass',0);
/*!40000 ALTER TABLE `comptes_employes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `discussions`
--

DROP TABLE IF EXISTS `discussions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `discussions` (
  `id_discussion` int unsigned NOT NULL AUTO_INCREMENT,
  `id_reservation` int unsigned NOT NULL,
  `statut` enum('OUVERTE','FERMEE') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'OUVERTE',
  `date_creation` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_discussion`),
  UNIQUE KEY `id_reservation` (`id_reservation`),
  KEY `fk_discussions_reservations_idx` (`id_reservation`),
  CONSTRAINT `fk_discussions_reservations` FOREIGN KEY (`id_reservation`) REFERENCES `reservations` (`id_reservation`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `discussions`
--

LOCK TABLES `discussions` WRITE;
/*!40000 ALTER TABLE `discussions` DISABLE KEYS */;
/*!40000 ALTER TABLE `discussions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `droits_modules`
--

DROP TABLE IF EXISTS `droits_modules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `droits_modules` (
  `id_compte` int unsigned NOT NULL,
  `id_module` int unsigned NOT NULL,
  PRIMARY KEY (`id_compte`,`id_module`),
  KEY `fk_droits_modules_modules_idx` (`id_module`),
  CONSTRAINT `fk_droits_modules_comptes` FOREIGN KEY (`id_compte`) REFERENCES `comptes_employes` (`id_compte`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_droits_modules_modules` FOREIGN KEY (`id_module`) REFERENCES `modules` (`id_module`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `droits_modules`
--

LOCK TABLES `droits_modules` WRITE;
/*!40000 ALTER TABLE `droits_modules` DISABLE KEYS */;
INSERT INTO `droits_modules` VALUES (2,1),(2,2),(2,3);
/*!40000 ALTER TABLE `droits_modules` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `employes`
--

DROP TABLE IF EXISTS `employes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `employes` (
  `id_employe` int unsigned NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `prenom` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `date_naissance` date NOT NULL,
  `email` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `telephone` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `photo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Chemin vers la photo de l''employé',
  `poste` enum('Chauffeur','Mecanicien','Comptable','AgentReservation','Gardien','Administrateur') COLLATE utf8mb4_unicode_ci NOT NULL,
  `date_embauche` date NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id_employe`),
  UNIQUE KEY `email_UNIQUE` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `employes`
--

LOCK TABLES `employes` WRITE;
/*!40000 ALTER TABLE `employes` DISABLE KEYS */;
INSERT INTO `employes` VALUES (1,'Admin','Super','1980-01-01','super.admin@bluebird.com','+237 677 00 00 01',NULL,'Administrateur','2020-01-01',1),(2,'Nkou','Jean','1990-05-15','jean.nkou@bluebird.com','+237 677 00 00 02',NULL,'Chauffeur','2021-03-10',1),(3,'Mvondo','Pierre','1985-11-20','pierre.mvondo@bluebird.com','+237 677 00 00 03',NULL,'Chauffeur','2022-07-25',1),(4,'sontia','junior','1995-02-28','juniorsontia@mentravel.com','+237 677 00 00 04',NULL,'Mecanicien','2023-01-15',1),(5,'Biloa','Marie','1998-08-10','marie.biloa@bluebird.com','+237 677 00 00 05',NULL,'AgentReservation','2023-10-01',1),(6,'tamo','evra','2025-10-01','evra@gmail.com','+237 694274104',NULL,'Comptable','2025-11-16',1),(7,'geremy','thieba','2025-10-28','alain@gmail.com','+237 694 27 41 06',NULL,'Chauffeur','2025-11-20',0);
/*!40000 ALTER TABLE `employes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `maintenances`
--

DROP TABLE IF EXISTS `maintenances`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `maintenances` (
  `id_maintenance` int unsigned NOT NULL AUTO_INCREMENT,
  `id_vehicule` int unsigned NOT NULL,
  `type_maintenance` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `zone` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_debut` date NOT NULL,
  `date_fin` date DEFAULT NULL,
  `statut` enum('en cours','terminee') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'en cours',
  PRIMARY KEY (`id_maintenance`),
  KEY `fk_maintenances_vehicules_idx` (`id_vehicule`),
  CONSTRAINT `fk_maintenances_vehicules` FOREIGN KEY (`id_vehicule`) REFERENCES `vehicules` (`id_vehicule`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `maintenances`
--

LOCK TABLES `maintenances` WRITE;
/*!40000 ALTER TABLE `maintenances` DISABLE KEYS */;
INSERT INTO `maintenances` VALUES (1,3,'Vidange moteur','Vidange complète et changement de filtres',NULL,'2025-11-16',NULL,'en cours');
/*!40000 ALTER TABLE `maintenances` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mecaniciens`
--

DROP TABLE IF EXISTS `mecaniciens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `mecaniciens` (
  `id_mecanicien` int unsigned NOT NULL,
  `specialite` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id_mecanicien`),
  CONSTRAINT `fk_mecaniciens_employes` FOREIGN KEY (`id_mecanicien`) REFERENCES `employes` (`id_employe`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mecaniciens`
--

LOCK TABLES `mecaniciens` WRITE;
/*!40000 ALTER TABLE `mecaniciens` DISABLE KEYS */;
INSERT INTO `mecaniciens` VALUES (4,'Moteurs Diesel biturbo');
/*!40000 ALTER TABLE `mecaniciens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `messages`
--

DROP TABLE IF EXISTS `messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `messages` (
  `id_message` int unsigned NOT NULL AUTO_INCREMENT,
  `id_discussion` int unsigned NOT NULL,
  `emetteur` enum('CLIENT','ADMIN') COLLATE utf8mb4_unicode_ci NOT NULL,
  `contenu` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `date_envoi` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_message`),
  KEY `fk_messages_discussions_idx` (`id_discussion`),
  CONSTRAINT `fk_messages_discussions` FOREIGN KEY (`id_discussion`) REFERENCES `discussions` (`id_discussion`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `messages`
--

LOCK TABLES `messages` WRITE;
/*!40000 ALTER TABLE `messages` DISABLE KEYS */;
/*!40000 ALTER TABLE `messages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `modules`
--

DROP TABLE IF EXISTS `modules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `modules` (
  `id_module` int unsigned NOT NULL AUTO_INCREMENT,
  `nom_module` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id_module`),
  UNIQUE KEY `nom_module` (`nom_module`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `modules`
--

LOCK TABLES `modules` WRITE;
/*!40000 ALTER TABLE `modules` DISABLE KEYS */;
INSERT INTO `modules` VALUES (2,'BAGAGES'),(8,'CHAT'),(6,'MAINTENANCE'),(7,'PARTENAIRES'),(5,'PERSONNEL'),(1,'RESERVATIONS'),(4,'VEHICULES'),(3,'VOYAGES');
/*!40000 ALTER TABLE `modules` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `paiements`
--

DROP TABLE IF EXISTS `paiements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `paiements` (
  `id_paiement` int unsigned NOT NULL AUTO_INCREMENT,
  `id_reservation` int unsigned NOT NULL,
  `montant` decimal(10,2) NOT NULL,
  `mode` enum('ORANGE_MONEY','MTN_MOMO','VIREMENT','ESPECES') COLLATE utf8mb4_unicode_ci NOT NULL,
  `reference_paiement` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `statut` enum('SUCCES','ECHEC','EN_COURS') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'SUCCES',
  `date_paiement` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_paiement`),
  UNIQUE KEY `reference_paiement` (`reference_paiement`),
  KEY `idx_paiements_reservation` (`id_reservation`),
  CONSTRAINT `fk_paiements_reservations` FOREIGN KEY (`id_reservation`) REFERENCES `reservations` (`id_reservation`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `paiements`
--

LOCK TABLES `paiements` WRITE;
/*!40000 ALTER TABLE `paiements` DISABLE KEYS */;
INSERT INTO `paiements` VALUES (1,1,28000.00,'ORANGE_MONEY','SIM_ORANGE_MONEY_20251116181719','SUCCES','2025-11-16 19:17:19'),(2,3,7000.00,'MTN_MOMO','SIM_MTN_MOMO_20251116181831','SUCCES','2025-11-16 19:18:31'),(3,4,16000.00,'MTN_MOMO','SIM_MTN_MOMO_20251116193426','SUCCES','2025-11-16 20:34:26'),(4,5,8000.00,'ORANGE_MONEY','SIM_ORANGE_MONEY_20251116234423','SUCCES','2025-11-17 00:44:23'),(5,6,48000.00,'ORANGE_MONEY','SIM_ORANGE_MONEY_20251117092306','SUCCES','2025-11-17 10:23:06'),(6,7,16000.00,'ORANGE_MONEY','SIM_ORANGE_MONEY_20251117122119','SUCCES','2025-11-17 13:21:19'),(7,9,40000.00,'ORANGE_MONEY','SIM_ORANGE_MONEY_20251117182432','SUCCES','2025-11-17 19:24:32'),(8,8,8000.00,'MTN_MOMO','SIM_MTN_MOMO_20251117182509','SUCCES','2025-11-17 19:25:09'),(9,10,40000.00,'ORANGE_MONEY','SIM_ORANGE_MONEY_20251118083443','SUCCES','2025-11-18 09:34:43'),(10,11,35000.00,'ORANGE_MONEY','SIM_ORANGE_MONEY_20251118102403','SUCCES','2025-11-18 11:24:03');
/*!40000 ALTER TABLE `paiements` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `partenaires`
--

DROP TABLE IF EXISTS `partenaires`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `partenaires` (
  `id_partenaire` int unsigned NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `service` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `contact` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id_partenaire`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `partenaires`
--

LOCK TABLES `partenaires` WRITE;
/*!40000 ALTER TABLE `partenaires` DISABLE KEYS */;
INSERT INTO `partenaires` VALUES (1,'Orange Cameroun','Paiement Mobile (Orange Money)','contact@orange.cm'),(2,'MTN Cameroun','Paiement Mobile (MTN MoMo)','contact@mtn.cm');
/*!40000 ALTER TABLE `partenaires` ENABLE KEYS */;
UNLOCK TABLES;

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
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reservations`
--

LOCK TABLES `reservations` WRITE;
/*!40000 ALTER TABLE `reservations` DISABLE KEYS */;
INSERT INTO `reservations` VALUES (1,2,4,1,'siege',3,'PAYEE','2025-11-16 19:08:31',NULL),(2,2,4,1,'siege',4,'PAYEE','2025-11-16 19:08:31',NULL),(3,2,4,1,'siege',8,'PAYEE','2025-11-16 19:08:31',NULL),(4,2,4,1,'siege',13,'PAYEE','2025-11-16 19:08:31',NULL),(5,2,4,2,'siege',27,'EN_ATTENTE','2025-11-16 19:09:04',NULL),(6,2,4,2,'siege',24,'EN_ATTENTE','2025-11-16 19:09:04',NULL),(7,2,4,2,'siege',20,'EN_ATTENTE','2025-11-16 19:09:04',NULL),(8,2,4,3,'siege',7,'PAYEE','2025-11-16 19:18:29',NULL),(9,2,2,4,'siege',23,'PAYEE','2025-11-16 20:34:23',NULL),(10,2,2,4,'siege',19,'PAYEE','2025-11-16 20:34:23',NULL),(11,2,2,5,'siege',39,'PAYEE','2025-11-17 00:44:20',NULL),(12,2,2,6,'siege',4,'PAYEE','2025-11-17 10:23:04',NULL),(13,2,2,6,'siege',8,'PAYEE','2025-11-17 10:23:04',NULL),(14,2,2,6,'siege',12,'PAYEE','2025-11-17 10:23:04',NULL),(15,2,2,6,'siege',3,'PAYEE','2025-11-17 10:23:04',NULL),(16,2,2,6,'siege',7,'PAYEE','2025-11-17 10:23:04',NULL),(17,2,2,6,'siege',11,'PAYEE','2025-11-17 10:23:04',NULL),(18,2,2,7,'siege',6,'PAYEE','2025-11-17 13:21:16',NULL),(19,2,2,7,'siege',10,'PAYEE','2025-11-17 13:21:16',NULL),(20,2,2,8,'siege',26,'PAYEE','2025-11-17 17:17:20',NULL),(21,2,5,9,'siege',21,'PAYEE','2025-11-17 19:24:29',NULL),(22,2,5,9,'siege',22,'PAYEE','2025-11-17 19:24:29',NULL),(23,2,5,9,'siege',25,'PAYEE','2025-11-17 19:24:29',NULL),(24,2,5,9,'siege',26,'PAYEE','2025-11-17 19:24:29',NULL),(25,2,7,10,'siege',21,'PAYEE','2025-11-18 09:34:39',NULL),(26,2,7,10,'siege',25,'PAYEE','2025-11-18 09:34:39',NULL),(27,2,7,10,'siege',22,'PAYEE','2025-11-18 09:34:39',NULL),(28,2,7,10,'siege',26,'PAYEE','2025-11-18 09:34:39',NULL),(29,2,3,11,'siege',14,'PAYEE','2025-11-18 11:23:58',NULL),(30,2,3,11,'siege',17,'PAYEE','2025-11-18 11:23:58',NULL),(31,2,3,11,'siege',30,'PAYEE','2025-11-18 11:23:58',NULL),(32,2,3,11,'siege',26,'PAYEE','2025-11-18 11:23:58',NULL),(33,2,3,11,'siege',25,'PAYEE','2025-11-18 11:23:58',NULL);
/*!40000 ALTER TABLE `reservations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vehicules`
--

DROP TABLE IF EXISTS `vehicules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `vehicules` (
  `id_vehicule` int unsigned NOT NULL AUTO_INCREMENT,
  `type` enum('Bus','Van','Minibus') COLLATE utf8mb4_unicode_ci NOT NULL,
  `marque` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `immatriculation` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nombre_places` int unsigned NOT NULL,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Chemin vers l''image du véhicule',
  `statut` enum('actif','en maintenance','hors service') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'actif',
  PRIMARY KEY (`id_vehicule`),
  UNIQUE KEY `immatriculation_UNIQUE` (`immatriculation`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vehicules`
--

LOCK TABLES `vehicules` WRITE;
/*!40000 ALTER TABLE `vehicules` DISABLE KEYS */;
INSERT INTO `vehicules` VALUES (1,'Bus','Mercedes-Benz','CE-100-AA',45,NULL,'actif'),(2,'Bus','Scania','CE-200-BB',40,NULL,'actif'),(3,'Van','Toyota','CE-300-CC',15,NULL,'actif');
/*!40000 ALTER TABLE `vehicules` ENABLE KEYS */;
UNLOCK TABLES;

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
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `voyages`
--

LOCK TABLES `voyages` WRITE;
/*!40000 ALTER TABLE `voyages` DISABLE KEYS */;
INSERT INTO `voyages` VALUES (1,'Yaoundé','Douala','2025-11-17 13:53:37',1,2,7500.00,'programmé',1),(2,'Douala','Yaoundé','2025-11-17 13:53:37',2,3,8000.00,'programmé',0),(3,'Yaoundé','Bafoussam','2025-11-18 13:53:37',1,3,7000.00,'programmé',0),(4,'Yaoundé','Douala','2025-11-30 08:00:00',1,2,7000.00,'programmé',0),(5,'Bafoussam','Douala','2025-11-22 12:30:00',2,3,10000.00,'programmé',0),(6,'Bafoussam','Douala','2025-11-22 12:30:00',2,3,10000.00,'programmé',0),(7,'Yaoundé','Douala','2025-11-20 12:30:00',1,3,10000.00,'programmé',0),(8,'Bafoussam','Douala','2025-11-08 10:20:00',3,3,5000.00,'programmé',0),(9,'Bafoussam','Douala','2025-11-21 10:21:00',3,3,5000.00,'programmé',0);
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

-- Dump completed on 2025-11-18 14:56:46
