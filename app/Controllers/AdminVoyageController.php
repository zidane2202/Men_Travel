<?php
// Fichier: app/controllers/AdminVoyageController.php

namespace App\Controllers;

use App\Core\Database;
use App\Models\Voyage;
use App\Models\Employe;
use App\Models\Vehicule;
use App\Models\Commande; // <-- AJOUTEZ CETTE LIGNE
use App\Models\Reservation; // <-- AJOUTEZ CETTE LIGNE
use PDOException; // Pour les erreurs BDD

class AdminVoyageController {

    private $db;

    public function __construct() {
        // La sécurité est gérée par le filtre 'before' dans index.php
        $database = new Database();
        $this->db = $database->getConnection();
    }

    /**
     * Affiche la liste de tous les voyages programmés.
     */
    public function listVoyages() {
        $voyageModel = new Voyage($this->db);
        // On crée une nouvelle méthode getAll pour l'admin (qui aura plus de détails)
        $voyages = $voyageModel->getAllProgrammed(); 

        $pageTitle = "Gestion des Voyages";
        require __DIR__ . '/../views/admin/voyages/index.php';
    }

    /**
     * Affiche le formulaire de création de voyage.
     */
    public function showCreateForm() {
        // Pour les formulaires, on doit récupérer la liste des chauffeurs et véhicules
        $employeModel = new Employe($this->db);
        $vehiculeModel = new Vehicule($this->db);
        
        // Nous aurons besoin de créer ces méthodes dans les modèles
        $chauffeurs = $employeModel->findChauffeurs();
        $vehicules = $vehiculeModel->findAllActive();

        $pageTitle = "Créer un nouveau voyage";
        require __DIR__ . '/../views/admin/voyages/create.php';
    }

    /**
     * Traite l'ajout d'un nouveau voyage.
     */
    public function storeVoyage() {
        try {
            $voyageModel = new Voyage($this->db);
            
            // Validation simple
            if (empty($_POST['ville_depart']) || empty($_POST['id_vehicule']) || empty($_POST['prix'])) {
                 header("Location: /admin/voyages/create?error=Tous les champs principaux sont requis.");
                 exit();
            }
            
            // Création du voyage (Nous devons ajouter la méthode create à Voyage.php)
            $id_voyage = $voyageModel->create(
                $_POST['ville_depart'],
                $_POST['ville_arrivee'],
                $_POST['date_depart'],
                $_POST['id_vehicule'],
                $_POST['id_chauffeur'],
                $_POST['prix']
            );

            if ($id_voyage) {
                header("Location: /admin/voyages?success=Voyage N°$id_voyage créé avec succès.");
                exit();
            } else {
                throw new \Exception("Échec de l'insertion dans la base de données.");
            }

        } catch (PDOException $e) {
            header("Location: /admin/voyages/create?error=Erreur BDD: " . urlencode($e->getMessage()));
            exit();
        } catch (\Exception $e) {
            header("Location: /admin/voyages/create?error=" . urlencode($e->getMessage()));
            exit();
        }
    }

    public function showManifest($id_voyage) {
        $reservationModel = new Reservation($this->db);
        $commandeModel = new Commande($this->db);
        $voyageModel = new Voyage($this->db); // <-- NOUVEAU MODÈLE

        // 1. Récupérer les détails du voyage (pour l'en-tête de la page)
        $voyageDetails = $commandeModel->findByIdAndClient($id_voyage, $_SESSION['admin_id'], true);
        
        // 2. Récupérer la liste des passagers
        $manifeste = $reservationModel->getManifestByVoyageId($id_voyage);
        
        // 3. Calculer les places restantes
        $totalSeats = $voyageModel->getTotalSeatsByVoyageId($id_voyage);
        $reservedSeatsCount = count($manifeste);
        $remainingSeats = $totalSeats - $reservedSeatsCount;
        
        $pageTitle = "Manifeste du Voyage N°$id_voyage";
        
        // On passe les variables nécessaires
        require __DIR__ . '/../views/admin/voyages/manifest.php';
    }

    public function showEditForm($id_voyage) {
        $voyageModel = new Voyage($this->db);
        
        // --- Correction de l'initialisation des modèles ---
        $employeModel = new \App\Models\Employe($this->db);
        $vehiculeModel = new \App\Models\Vehicule($this->db);
        
        // 1. Récupérer les données du voyage
        $voyageData = $voyageModel->findById($id_voyage); 

        if (!$voyageData) {
            header("Location: /admin/voyages?error=Voyage introuvable.");
            exit();
        }
        
        // 2. Récupérer les listes pour les dropdowns
        $chauffeurs = $employeModel->findChauffeurs();
        $vehicules = $vehiculeModel->findAllActive();

        $pageTitle = "Modifier Voyage N°$id_voyage";
        require __DIR__ . '/../views/admin/voyages/edit.php'; 
    }

    /**
     * Traite la soumission du formulaire d'édition.
     * (Route: POST /admin/voyages/update)
     */
    public function updateVoyage() {
        try {
            $voyageModel = new Voyage($this->db);
            
            // 1. Validation simple
            if (empty($_POST['id_voyage']) || empty($_POST['ville_depart']) || empty($_POST['prix'])) {
                 header("Location: /admin/voyages?error=Champs requis manquants pour la mise à jour.");
                 exit();
            }
            
            // 2. Mise à jour du voyage
            $result = $voyageModel->update(
                $_POST['id_voyage'],
                $_POST['ville_depart'],
                $_POST['ville_arrivee'],
                $_POST['date_depart'],
                $_POST['id_vehicule'],
                $_POST['id_chauffeur'],
                $_POST['prix'],
                $_POST['statut'] // Le statut peut être modifié ici
            );

            if ($result) {
                header("Location: /admin/voyages?success=Voyage N°" . $_POST['id_voyage'] . " mis à jour avec succès.");
                exit();
            } else {
                // Si execute() retourne true mais aucune ligne n'est affectée (aucune modification)
                header("Location: /admin/voyages?warning=Aucune modification détectée ou échec de la mise à jour.");
                exit();
            }

        } catch (\Exception $e) {
            header("Location: /admin/voyages?error=" . urlencode("Erreur lors de la mise à jour: " . $e->getMessage()));
            exit();
        }
    }

    /**
     * Traite la suppression d'un voyage.
     * (Route: POST /admin/voyages/delete)
     */
   public function deleteVoyage() {
        try {
            $voyageModel = new Voyage($this->db);
            $id_voyage = $_POST['id_voyage'] ?? null;
            
            if (!$id_voyage) {
                header("Location: /admin/voyages?error=ID de voyage manquant.");
                exit();
            }

            // NOTE: On n'utilise plus DELETE FROM, mais UPDATE SET is_deleted = 1
            $result = $voyageModel->archive($id_voyage);

            if ($result) {
                header("Location: /admin/voyages?success=Voyage N°$id_voyage archivé avec succès (Soft Delete).");
                exit();
            } else {
                header("Location: /admin/voyages?error=Échec de l'archivage.");
                exit();
            }

        } catch (\Exception $e) {
            header("Location: /admin/voyages?error=" . urlencode("Erreur lors de l'archivage: " . $e->getMessage()));
            exit();
        }
    }
}