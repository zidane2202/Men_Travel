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
        // Validation basique
        if (empty($_POST['id_employe']) || empty($_POST['email']) || empty($_POST['poste'])) {
             header("Location: /admin/employes?error=ID ou champs principaux manquants pour la mise à jour.");
             exit();
        }
        
        $id_employe = $_POST['id_employe'];
        $redirect_url = "/admin/employes/edit/" . $id_employe;

        try {
            $employeModel = new Employe($this->db);
            
            // 1. Mise à jour des données principales (table 'employes')
            $employeModel->update(
                $id_employe, $_POST['nom'], $_POST['prenom'], $_POST['date_naissance'], 
                $_POST['email'], $_POST['telephone'], $_POST['poste'], $_POST['date_embauche']
            );

            // 2. Mise à jour des spécialisations (Logique simple: UPDATE ou INSERT/DELETE)
            if ($_POST['poste'] === 'Chauffeur') {
                $chauffeurModel = new Chauffeur($this->db);
                // Si l'enregistrement n'existe pas encore, on le crée (le modèle doit gérer ça)
                $chauffeurModel->updateOrCreate($id_employe, $_POST['permis_numero']); 
            } elseif ($_POST['poste'] === 'Mecanicien') {
                $mecanicienModel = new Mecanicien($this->db);
                $mecanicienModel->updateOrCreate($id_employe, $_POST['specialite']);
            }
            
            // Note: Si le poste change (ex: Chauffeur -> Comptable), on devrait supprimer l'ancienne entrée
            // Pour simplifier, nous laissons les vieilles entrées de spécialisation intactes.

            header("Location: /admin/employes?success=Employé N°$id_employe mis à jour avec succès.");
            exit();

        } catch (PDOException $e) {
            $errorMessage = "Erreur BDD (Email déjà utilisé ou données incorrectes).";
            header("Location: " . $redirect_url . "?error=" . urlencode($errorMessage));
            exit();
        } catch (\Exception $e) {
            header("Location: " . $redirect_url . "?error=" . urlencode($e->getMessage()));
            exit();
        }
    }
    // ... (dans AdminPersonnelController.php, après updateEmploye())

    /**
     * Traite l'archivage logique d'un employé.
     */
    public function archiveEmploye() {
        try {
            $employeModel = new Employe($this->db);
            $id_employe = $_POST['id_employe'] ?? null;
            
            if (!$id_employe) {
                header("Location: /admin/employes?error=ID d'employé manquant.");
                exit();
            }

            $result = $employeModel->archive($id_employe);

            if ($result) {
                header("Location: /admin/employes?success=Employé N°$id_employe a été archivé et désactivé.");
                exit();
            } else {
                header("Location: /admin/employes?error=Échec de l'archivage de l'employé.");
                exit();
            }

        } catch (\Exception $e) {
            header("Location: /admin/employes?error=" . urlencode("Erreur d'archivage: " . $e->getMessage()));
            exit();
        }
    }
}