-- Fichier: sql/init.sql
-- Création de la base de données et des tables pour Blue Bird Express

-- Création de la base de données
CREATE DATABASE IF NOT EXISTS `men_travel` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `men_travel`;

-- -----------------------------------------------------
-- Table `agences`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `agences` (
  `id_agence` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `nom` VARCHAR(100) NOT NULL,
  `ville` VARCHAR(100) NOT NULL,
  `adresse` VARCHAR(255) NOT NULL,
  `telephone` VARCHAR(20) NOT NULL,
  PRIMARY KEY (`id_agence`)
) ENGINE = InnoDB CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- -----------------------------------------------------
-- Table `partenaires`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `partenaires` (
  `id_partenaire` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `nom` VARCHAR(100) NOT NULL,
  `service` VARCHAR(100) NOT NULL,
  `contact` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`id_partenaire`)
) ENGINE = InnoDB CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- -----------------------------------------------------
-- Table `clients`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `clients` (
  `id_client` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `nom` VARCHAR(100) NOT NULL,
  `prenom` VARCHAR(100) NOT NULL,
  `date_naissance` DATE NOT NULL,
  `sexe` ENUM('HOMME', 'FEMME') NOT NULL,
  `email` VARCHAR(150) NOT NULL,
  `telephone` VARCHAR(20) NOT NULL,
  `adresse` VARCHAR(255) NOT NULL,
  `mot_de_passe` VARCHAR(255) NOT NULL, -- Stocké en clair pour le contexte académique
  `photo_profil` TEXT NULL, -- Base64 ou URL
  `date_inscription` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_client`),
  UNIQUE INDEX `email_UNIQUE` (`email` ASC)
) ENGINE = InnoDB CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- -----------------------------------------------------
-- Table `employes` (Base pour tous les types de personnel)
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `employes` (
  `id_employe` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `nom` VARCHAR(100) NOT NULL,
  `prenom` VARCHAR(100) NOT NULL,
  `date_naissance` DATE NOT NULL,
  `email` VARCHAR(150) NOT NULL,
  `telephone` VARCHAR(20) NOT NULL,
  `photo` TEXT NULL, -- Base64 ou URL
  `poste` ENUM('Chauffeur', 'Mecanicien', 'Comptable', 'AgentReservation', 'Gardien', 'Administrateur') NOT NULL,
  `date_embauche` DATE NOT NULL,
  PRIMARY KEY (`id_employe`),
  UNIQUE INDEX `email_UNIQUE` (`email` ASC)
) ENGINE = InnoDB CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- -----------------------------------------------------
-- Table `chauffeurs` (Héritage de `employes`)
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `chauffeurs` (
  `id_chauffeur` INT UNSIGNED NOT NULL,
  `permis_numero` VARCHAR(50) NOT NULL UNIQUE,
  PRIMARY KEY (`id_chauffeur`),
  CONSTRAINT `fk_chauffeurs_employes`
    FOREIGN KEY (`id_chauffeur`)
    REFERENCES `employes` (`id_employe`)
    ON DELETE RESTRICT
    ON UPDATE CASCADE
) ENGINE = InnoDB CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- -----------------------------------------------------
-- Table `mecaniciens` (Héritage de `employes`)
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `mecaniciens` (
  `id_mecanicien` INT UNSIGNED NOT NULL,
  `specialite` VARCHAR(100) NULL,
  PRIMARY KEY (`id_mecanicien`),
  CONSTRAINT `fk_mecaniciens_employes`
    FOREIGN KEY (`id_mecanicien`)
    REFERENCES `employes` (`id_employe`)
    ON DELETE RESTRICT
    ON UPDATE CASCADE
) ENGINE = InnoDB CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- -----------------------------------------------------
-- Table `vehicules`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `vehicules` (
  `id_vehicule` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `type` ENUM('Bus', 'Van', 'Minibus') NOT NULL,
  `marque` VARCHAR(100) NOT NULL,
  `immatriculation` VARCHAR(50) NOT NULL,
  `nombre_places` INT UNSIGNED NOT NULL,
  `image` TEXT NULL, -- Base64 ou URL
  `statut` ENUM('actif', 'en maintenance', 'hors service') NOT NULL DEFAULT 'actif',
  PRIMARY KEY (`id_vehicule`),
  UNIQUE INDEX `immatriculation_UNIQUE` (`immatriculation` ASC)
) ENGINE = InnoDB CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- -----------------------------------------------------
-- Table `maintenances`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `maintenances` (
  `id_maintenance` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_vehicule` INT UNSIGNED NOT NULL,
  `type_maintenance` VARCHAR(100) NOT NULL,
  `description` TEXT NOT NULL,
  `zone` VARCHAR(100) NULL,
  `date_debut` DATE NOT NULL,
  `date_fin` DATE NULL,
  `statut` ENUM('en cours', 'terminee') NOT NULL DEFAULT 'en cours',
  PRIMARY KEY (`id_maintenance`),
  INDEX `fk_maintenances_vehicules_idx` (`id_vehicule` ASC),
  CONSTRAINT `fk_maintenances_vehicules`
    FOREIGN KEY (`id_vehicule`)
    REFERENCES `vehicules` (`id_vehicule`)
    ON DELETE RESTRICT
    ON UPDATE CASCADE
) ENGINE = InnoDB CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- -----------------------------------------------------
-- Table `voyages`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `voyages` (
  `id_voyage` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `ville_depart` VARCHAR(100) NOT NULL,
  `ville_arrivee` VARCHAR(100) NOT NULL,
  `date_depart` DATETIME NOT NULL,
  `id_vehicule` INT UNSIGNED NOT NULL,
  `id_chauffeur` INT UNSIGNED NOT NULL,
  `statut` ENUM('programmé', 'en cours', 'terminé', 'annulé') NOT NULL DEFAULT 'programmé',
  PRIMARY KEY (`id_voyage`),
  INDEX `fk_voyages_vehicules_idx` (`id_vehicule` ASC),
  INDEX `fk_voyages_chauffeurs_idx` (`id_chauffeur` ASC),
  INDEX `idx_villes_date` (`ville_depart` ASC, `ville_arrivee` ASC, `date_depart` ASC),
  CONSTRAINT `fk_voyages_vehicules`
    FOREIGN KEY (`id_vehicule`)
    REFERENCES `vehicules` (`id_vehicule`)
    ON DELETE RESTRICT
    ON UPDATE CASCADE,
  CONSTRAINT `fk_voyages_chauffeurs`
    FOREIGN KEY (`id_chauffeur`)
    REFERENCES `chauffeurs` (`id_chauffeur`)
    ON DELETE RESTRICT
    ON UPDATE CASCADE
) ENGINE = InnoDB CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- -----------------------------------------------------
-- Table `reservations`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `reservations` (
  `id_reservation` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_client` INT UNSIGNED NOT NULL,
  `id_voyage` INT UNSIGNED NOT NULL,
  `type_reservation` ENUM('siege', 'colis') NOT NULL DEFAULT 'siege',
  `numero_siege` INT UNSIGNED NULL, -- NULL si type_reservation est 'colis'
  `statut` ENUM('EN_ATTENTE', 'PAYEE', 'ANNULEE', 'TERMINEE') NOT NULL DEFAULT 'EN_ATTENTE',
  `date_reservation` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `billet_pdf_path` VARCHAR(255) NULL,
  PRIMARY KEY (`id_reservation`),
  INDEX `fk_reservations_clients_idx` (`id_client` ASC),
  INDEX `fk_reservations_voyages_idx` (`id_voyage` ASC),
  INDEX `idx_statut_reservation` (`statut` ASC),
  UNIQUE INDEX `idx_siege_voyage` (`id_voyage` ASC, `numero_siege` ASC), -- Un siège par voyage
  CONSTRAINT `fk_reservations_clients`
    FOREIGN KEY (`id_client`)
    REFERENCES `clients` (`id_client`)
    ON DELETE RESTRICT
    ON UPDATE CASCADE,
  CONSTRAINT `fk_reservations_voyages`
    FOREIGN KEY (`id_voyage`)
    REFERENCES `voyages` (`id_voyage`)
    ON DELETE RESTRICT
    ON UPDATE CASCADE
) ENGINE = InnoDB CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- -----------------------------------------------------
-- Table `paiements`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `paiements` (
  `id_paiement` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_reservation` INT UNSIGNED NOT NULL,
  `montant` DECIMAL(10, 2) NOT NULL,
  `mode` ENUM('ORANGE_MONEY', 'MTN_MOMO', 'VIREMENT', 'ESPECES') NOT NULL,
  `reference_paiement` VARCHAR(100) NOT NULL UNIQUE,
  `statut` ENUM('SUCCES', 'ECHEC', 'EN_COURS') NOT NULL DEFAULT 'SUCCES',
  `date_paiement` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_paiement`),
  UNIQUE INDEX `id_reservation_UNIQUE` (`id_reservation` ASC),
  CONSTRAINT `fk_paiements_reservations`
    FOREIGN KEY (`id_reservation`)
    REFERENCES `reservations` (`id_reservation`)
    ON DELETE RESTRICT
    ON UPDATE CASCADE
) ENGINE = InnoDB CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- -----------------------------------------------------
-- Table `colis` (Bagages/Colis)
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `colis` (
  `id_colis` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_client` INT UNSIGNED NOT NULL,
  `id_voyage` INT UNSIGNED NULL, -- Optionnel, si le colis est assigné à un voyage
  `poids` DECIMAL(5, 2) NOT NULL,
  `valeur_declaree` DECIMAL(10, 2) NULL,
  `frais` DECIMAL(10, 2) NOT NULL,
  `description` TEXT NULL,
  `ville_depart` VARCHAR(100) NOT NULL,
  `ville_arrivee` VARCHAR(100) NOT NULL,
  `telephone_destinataire` VARCHAR(20) NOT NULL,
  `statut` ENUM('ENREGISTRE', 'EN_TRANSIT', 'LIVRE', 'ANNULE') NOT NULL DEFAULT 'ENREGISTRE',
  `date_enregistrement` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_colis`),
  INDEX `fk_colis_clients_idx` (`id_client` ASC),
  INDEX `fk_colis_voyages_idx` (`id_voyage` ASC),
  INDEX `idx_statut_colis` (`statut` ASC),
  CONSTRAINT `fk_colis_clients`
    FOREIGN KEY (`id_client`)
    REFERENCES `clients` (`id_client`)
    ON DELETE RESTRICT
    ON UPDATE CASCADE,
  CONSTRAINT `fk_colis_voyages`
    FOREIGN KEY (`id_voyage`)
    REFERENCES `voyages` (`id_voyage`)
    ON DELETE SET NULL
    ON UPDATE CASCADE
) ENGINE = InnoDB CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- -----------------------------------------------------
-- Table `discussions` (Chat par réservation)
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `discussions` (
  `id_discussion` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_reservation` INT UNSIGNED NOT NULL UNIQUE,
  `statut` ENUM('OUVERTE', 'FERMEE') NOT NULL DEFAULT 'OUVERTE',
  `date_creation` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_discussion`),
  INDEX `fk_discussions_reservations_idx` (`id_reservation` ASC),
  CONSTRAINT `fk_discussions_reservations`
    FOREIGN KEY (`id_reservation`)
    REFERENCES `reservations` (`id_reservation`)
    ON DELETE RESTRICT
    ON UPDATE CASCADE
) ENGINE = InnoDB CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- -----------------------------------------------------
-- Table `messages`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `messages` (
  `id_message` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_discussion` INT UNSIGNED NOT NULL,
  `emetteur` ENUM('CLIENT', 'ADMIN') NOT NULL,
  `contenu` TEXT NOT NULL,
  `date_envoi` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_message`),
  INDEX `fk_messages_discussions_idx` (`id_discussion` ASC),
  CONSTRAINT `fk_messages_discussions`
    FOREIGN KEY (`id_discussion`)
    REFERENCES `discussions` (`id_discussion`)
    ON DELETE RESTRICT
    ON UPDATE CASCADE
) ENGINE = InnoDB CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- -----------------------------------------------------
-- Table `comptes_employes` (Comptes pour les employés non-admin)
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `comptes_employes` (
  `id_compte` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_employe` INT UNSIGNED NOT NULL UNIQUE,
  `email` VARCHAR(150) NOT NULL UNIQUE,
  `mot_de_passe` VARCHAR(255) NOT NULL, -- Mot de passe par défaut généré
  `est_super_admin` BOOLEAN NOT NULL DEFAULT FALSE,
  PRIMARY KEY (`id_compte`),
  INDEX `fk_comptes_employes_employes_idx` (`id_employe` ASC),
  CONSTRAINT `fk_comptes_employes_employes`
    FOREIGN KEY (`id_employe`)
    REFERENCES `employes` (`id_employe`)
    ON DELETE RESTRICT
    ON UPDATE CASCADE
) ENGINE = InnoDB CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- -----------------------------------------------------
-- Table `modules` (Liste des modules pour les droits)
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `modules` (
  `id_module` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `nom_module` VARCHAR(50) NOT NULL UNIQUE, -- RESERVATIONS, BAGAGES, VOYAGES, VEHICULES, PERSONNEL
  PRIMARY KEY (`id_module`)
) ENGINE = InnoDB CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- -----------------------------------------------------
-- Table `droits_modules` (Table de jointure pour les droits modulaires)
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `droits_modules` (
  `id_compte` INT UNSIGNED NOT NULL,
  `id_module` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`id_compte`, `id_module`),
  INDEX `fk_droits_modules_modules_idx` (`id_module` ASC),
  CONSTRAINT `fk_droits_modules_comptes`
    FOREIGN KEY (`id_compte`)
    REFERENCES `comptes_employes` (`id_compte`)
    ON DELETE RESTRICT
    ON UPDATE CASCADE,
  CONSTRAINT `fk_droits_modules_modules`
    FOREIGN KEY (`id_module`)
    REFERENCES `modules` (`id_module`)
    ON DELETE RESTRICT
    ON UPDATE CASCADE
) ENGINE = InnoDB CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- -----------------------------------------------------
-- SEEDS (Données initiales)
-- -----------------------------------------------------

-- Agence principale
INSERT INTO `agences` (`nom`, `ville`, `adresse`, `telephone`) VALUES
('Blue Bird Express - Yaoundé', 'Yaoundé', 'Avenue des Banques, Quartier du Lac', '+237 699 00 00 00');

-- Modules
INSERT INTO `modules` (`nom_module`) VALUES
('RESERVATIONS'), ('BAGAGES'), ('VOYAGES'), ('VEHICULES'), ('PERSONNEL'), ('MAINTENANCE'), ('PARTENAIRES'), ('CHAT');

-- Employés (incluant le Super Admin)
INSERT INTO `employes` (`nom`, `prenom`, `date_naissance`, `email`, `telephone`, `poste`, `date_embauche`) VALUES
('Admin', 'Super', '1980-01-01', 'super.admin@bluebird.com', '+237 677 00 00 01', 'Administrateur', '2020-01-01'),
('Nkou', 'Jean', '1990-05-15', 'jean.nkou@bluebird.com', '+237 677 00 00 02', 'Chauffeur', '2021-03-10'),
('Mvondo', 'Pierre', '1985-11-20', 'pierre.mvondo@bluebird.com', '+237 677 00 00 03', 'Chauffeur', '2022-07-25'),
('Eto', 'Marc', '1995-02-28', 'marc.eto@bluebird.com', '+237 677 00 00 04', 'Mecanicien', '2023-01-15'),
('Biloa', 'Marie', '1998-08-10', 'marie.biloa@bluebird.com', '+237 677 00 00 05', 'AgentReservation', '2023-10-01');

-- Comptes Employés (Super Admin et Agent de Réservation)
-- Le mot de passe pour le Super Admin sera géré par config/admin-config.php, mais nous créons un compte ici pour la structure
INSERT INTO `comptes_employes` (`id_employe`, `email`, `mot_de_passe`, `est_super_admin`) VALUES
(1, 'super.admin@bluebird.com', 'admin123', TRUE), -- ID 1: Super Admin
(5, 'marie.biloa@bluebird.com', 'defaultPass', FALSE); -- ID 5: Agent de Réservation

-- Droits pour l'Agent de Réservation (ID 5)
INSERT INTO `droits_modules` (`id_compte`, `id_module`) VALUES
(2, 1), -- AgentReservation (id_compte 2) a accès à RESERVATIONS (id_module 1)
(2, 2), -- AgentReservation (id_compte 2) a accès à BAGAGES (id_module 2)
(2, 3); -- AgentReservation (id_compte 2) a accès à VOYAGES (id_module 3)

-- Spécialisation Chauffeurs
INSERT INTO `chauffeurs` (`id_chauffeur`, `permis_numero`) VALUES
(2, 'P-CM-0001'),
(3, 'P-CM-0002');

-- Spécialisation Mécaniciens
INSERT INTO `mecaniciens` (`id_mecanicien`, `specialite`) VALUES
(4, 'Moteurs Diesel');

-- Véhicules
INSERT INTO `vehicules` (`type`, `marque`, `immatriculation`, `nombre_places`, `statut`) VALUES
('Bus', 'Mercedes-Benz', 'CE-100-AA', 45, 'actif'),
('Bus', 'Scania', 'CE-200-BB', 40, 'actif'),
('Van', 'Toyota', 'CE-300-CC', 15, 'actif');

-- Voyages (Exemples)
-- Voyage 1: Yaoundé - Douala, demain matin
INSERT INTO `voyages` (`ville_depart`, `ville_arrivee`, `date_depart`, `id_vehicule`, `id_chauffeur`, `statut`) VALUES
('Yaoundé', 'Douala', DATE_ADD(NOW(), INTERVAL 1 DAY), 1, 2, 'programmé'),
('Douala', 'Yaoundé', DATE_ADD(NOW(), INTERVAL 1 DAY), 2, 3, 'programmé'),
('Yaoundé', 'Bafoussam', DATE_ADD(NOW(), INTERVAL 2 DAY), 1, 3, 'programmé');

-- Client de démo
INSERT INTO `clients` (`nom`, `prenom`, `date_naissance`, `sexe`, `email`, `telephone`, `adresse`, `mot_de_passe`) VALUES
('Kamga', 'Alain', '1992-04-20', 'HOMME', 'alain.kamga@test.com', '+237 690 12 34 56', 'Rue 100, Yaoundé', 'password123');

-- Réservation de démo (pour le client Alain Kamga, Voyage 1, Siège 5)
INSERT INTO `reservations` (`id_client`, `id_voyage`, `type_reservation`, `numero_siege`, `statut`) VALUES
(1, 1, 'siege', 5, 'PAYEE');

-- Paiement de démo
INSERT INTO `paiements` (`id_reservation`, `montant`, `mode`, `reference_paiement`, `statut`) VALUES
(1, 10000.00, 'ORANGE_MONEY', 'REF-OM-20251116-0001', 'SUCCES');

-- Colis de démo
INSERT INTO `colis` (`id_client`, `poids`, `frais`, `description`, `ville_depart`, `ville_arrivee`, `telephone_destinataire`, `statut`) VALUES
(1, 5.5, 2500.00, 'Petit colis de documents', 'Yaoundé', 'Douala', '+237 699 98 76 54', 'ENREGISTRE');

-- Maintenance de démo
INSERT INTO `maintenances` (`id_vehicule`, `type_maintenance`, `description`, `date_debut`, `statut`) VALUES
(3, 'Vidange moteur', 'Vidange complète et changement de filtres', CURDATE(), 'en cours');

-- Partenaires de démo
INSERT INTO `partenaires` (`nom`, `service`, `contact`) VALUES
('Orange Cameroun', 'Paiement Mobile (Orange Money)', 'contact@orange.cm'),
('MTN Cameroun', 'Paiement Mobile (MTN MoMo)', 'contact@mtn.cm');
