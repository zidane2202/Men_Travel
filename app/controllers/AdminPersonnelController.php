<?php
// Fichier: app/controllers/AdminPersonnelController.php

namespace App\Controllers;

use App\Core\Database;
use App\Models\Employe;
use App\Models\Chauffeur; // Pour la spécialisation
use App\Models\Mecanicien; // Pour la spécialisation
use PDOException;

class AdminPersonnelController {

    private $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    /**
     * Affiche la liste de tous les employés.
     */
    public function listEmployes() {
        $employeModel = new Employe($this->db);
        $employes = $employeModel->findAll(); 
        
        $pageTitle = "Gestion du Personnel";
        require __DIR__ . '/../views/admin/personnel/index.php';
    }

    /**
     * Affiche le formulaire de création de nouvel employé.
     */
    public function showCreateForm() {
        $pageTitle = "Ajouter un employé";
        require __DIR__ . '/../views/admin/personnel/create.php';
    }

    /**
     * Traite l'ajout d'un nouvel employé.
     */
    public function storeEmploye() {
        // Validation basique
        if (empty($_POST['email']) || empty($_POST['poste']) || empty($_POST['nom'])) {
             header("Location: /admin/employes/create?error=Champs requis manquants.");
             exit();
        }

        try {
            $employeModel = new Employe($this->db);
            
            // 1. Création de l'employé principal (dans la table 'employes')
            $id_employe = $employeModel->create(
                $_POST['nom'], $_POST['prenom'], $_POST['date_naissance'], 
                $_POST['email'], $_POST['telephone'], $_POST['poste'], $_POST['date_embauche']
            );

            if ($id_employe) {
                // 2. Création de la spécialisation (si c'est un chauffeur ou mécanicien)
                if ($_POST['poste'] === 'Chauffeur') {
                    $chauffeurModel = new Chauffeur($this->db);
                    $chauffeurModel->create($id_employe, $_POST['permis_numero']);
                } elseif ($_POST['poste'] === 'Mecanicien') {
                    $mecanicienModel = new Mecanicien($this->db);
                    $mecanicienModel->create($id_employe, $_POST['specialite']);
                }

                header("Location: /admin/employes?success=Employé N°$id_employe créé avec succès.");
                exit();
            } else {
                throw new \Exception("Échec de l'insertion principale.");
            }

        } catch (PDOException $e) {
            // Gérer les erreurs de clé unique (email, permis, etc.)
            $errorMessage = $e->getMessage();
            if (strpos($errorMessage, 'email_UNIQUE') !== false) {
                $errorMessage = "Cet email est déjà utilisé par un autre employé.";
            }
            header("Location: /admin/employes/create?error=" . urlencode($errorMessage));
            exit();
        } catch (\Exception $e) {
            header("Location: /admin/employes/create?error=" . urlencode($e->getMessage()));
            exit();
        }
    }
    
    /**
     * Affiche le formulaire d'édition pré-rempli.
     */
    public function showEditForm($id_employe) {
        $employeModel = new Employe($this->db);
        $employeData = $employeModel->findById($id_employe); // Nécessite une méthode findById
        $specialisationData = [];

        if (!$employeData) {
            header("Location: /admin/employes?error=Employé introuvable.");
            exit();
        }

        // Récupérer les données de spécialisation
        if ($employeData['poste'] === 'Chauffeur') {
            $chauffeurModel = new Chauffeur($this->db);
            $specialisationData = $chauffeurModel->findById($id_employe);
        } elseif ($employeData['poste'] === 'Mecanicien') {
            $mecanicienModel = new Mecanicien($this->db);
            $specialisationData = $mecanicienModel->findById($id_employe);
        }

        $pageTitle = "Modifier Employé N°$id_employe";
        require __DIR__ . '/../views/admin/personnel/edit.php';
    }

    /**
     * Traite la mise à jour de l'employé.
     */
    public function updateEmploye() {
        // La logique d'update et de mise à jour des spécialisations est complexe.
        // Nous la mettrons en place si l'administrateur en a besoin.
        header("Location: /admin/employes?warning=Fonction d'édition non implémentée.");
        exit();
    }
}