<?php
// Fichier: app/controllers/AdminVehiculeController.php

namespace App\Controllers;

use App\Core\Database;
use App\Models\Vehicule;
use PDOException;

class AdminVehiculeController {

    private $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    /**
     * Affiche la liste de tous les véhicules.
     */
    public function listVehicules() {
        $vehiculeModel = new Vehicule($this->db);
        $vehicules = $vehiculeModel->findAll(); 
        
        $pageTitle = "Gestion des Véhicules";
        require __DIR__ . '/../views/admin/vehicules/index.php';
    }

    /**
     * Affiche le formulaire de création de nouveau véhicule.
     */
    public function showCreateForm() {
        $pageTitle = "Ajouter un véhicule";
        require __DIR__ . '/../views/admin/vehicules/create.php';
    }

    /**
     * Traite l'ajout d'un nouveau véhicule.
     */
    public function storeVehicule() {
        // Validation basique
        if (empty($_POST['marque']) || empty($_POST['immatriculation']) || empty($_POST['nombre_places'])) {
             header("Location: /admin/vehicules/create?error=Tous les champs sont requis.");
             exit();
        }

        try {
            $vehiculeModel = new Vehicule($this->db);
            
            $id_vehicule = $vehiculeModel->create(
                $_POST['type'], $_POST['marque'], $_POST['immatriculation'], $_POST['nombre_places']
            );

            if ($id_vehicule) {
                header("Location: /admin/vehicules?success=Véhicule N°$id_vehicule ajouté avec succès.");
                exit();
            } else {
                throw new \Exception("Échec de l'insertion.");
            }

        } catch (PDOException $e) {
            $errorMessage = $e->getMessage();
            if (strpos($errorMessage, 'immatriculation_UNIQUE') !== false) {
                $errorMessage = "Cette immatriculation est déjà enregistrée.";
            }
            header("Location: /admin/vehicules/create?error=" . urlencode($errorMessage));
            exit();
        } catch (\Exception $e) {
            header("Location: /admin/vehicules/create?error=" . urlencode($e->getMessage()));
            exit();
        }
    }
    
    /**
     * Affiche le formulaire d'édition pré-rempli.
     */
    public function showEditForm($id_vehicule) {
        $vehiculeModel = new Vehicule($this->db);
        $vehiculeData = $vehiculeModel->findById($id_vehicule);

        if (!$vehiculeData) {
            header("Location: /admin/vehicules?error=Véhicule introuvable.");
            exit();
        }

        $pageTitle = "Modifier Véhicule N°$id_vehicule";
        require __DIR__ . '/../views/admin/vehicules/edit.php';
    }

    /**
     * Traite la mise à jour du véhicule.
     */
    public function updateVehicule() {
        // Logique similaire à store, mais avec UPDATE
        header("Location: /admin/vehicules?warning=Fonction d'édition non implémentée.");
        exit();
    }
    
    /**
     * Traite la suppression LOGIQUE (Soft Delete).
     */
    public function deleteVehicule() {
        // Logique similaire à l'archivage d'employé
        header("Location: /admin/vehicules?warning=Fonction d'archivage non implémentée.");
        exit();
    }
}