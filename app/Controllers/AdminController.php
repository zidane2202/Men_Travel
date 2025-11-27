<?php
// Fichier: app/controllers/AdminController.php

namespace App\Controllers;

use App\Core\Database;
use App\Models\CompteEmploye;
use App\Models\AdminStatsModel; // <-- AJOUTEZ CETTE LIGNE
use App\Models\Client; // <-- AJOUTEZ CETTE LIGNE
use App\Models\Commande; // Assurez-vous que cette ligne est en haut du fichier
class AdminController {

    private $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    /**
     * Affiche la page de login admin.
     */
    public function showLoginPage() {
        require __DIR__ . '/../views/admin/login.php';
    }

    /**
     * Traite le formulaire de login admin.
     */
    public function handleLogin() {
        $email = $_POST['email'] ?? null;
        $password = $_POST['mot_de_passe'] ?? null;

        $compteModel = new CompteEmploye($this->db);
        $employeData = $compteModel->login($email, $password);

        if ($employeData) {
            // SUCCÈS ! Créer une session admin
            $_SESSION['admin_id'] = $employeData['id_compte'];
            $_SESSION['admin_nom'] = $employeData['prenom'] . ' ' . $employeData['nom'];
            $_SESSION['admin_super'] = (bool)$employeData['est_super_admin'];
            
            header("Location: /admin/dashboard");
            exit();
        } else {
            // Échec
            header("Location: /admin/login?error=Email ou mot de passe incorrect.");
            exit();
        }
    }

    /**
     * Affiche le tableau de bord admin.
     */
   public function showDashboard() {
        if (!isset($_SESSION['admin_id'])) {
            header('Location: /admin/login?error=Accès refusé. Veuillez vous connecter.');
            exit();
        }
        
        $statsModel = new AdminStatsModel($this->db);
        $stats = $statsModel->getDashboardStats(); // <-- Récupération de toutes les données

        $pageTitle = "Tableau de Bord Admin";
        require __DIR__ . '/../views/admin/dashboard.php'; // On passe $stats à la vue
    }
    /**
     * Gère la déconnexion admin.
     */
    public function handleLogout() {
        // Détruit seulement les clés de session admin
        unset($_SESSION['admin_id']);
        unset($_SESSION['admin_nom']);
        unset($_SESSION['admin_super']);
        
        header("Location: /admin/login?success=Vous avez été déconnecté.");
        exit();
    }
    public function listClients() {
        $clientModel = new Client($this->db);
        $clients = $clientModel->findAll(); 

        $pageTitle = "Gestion des Clients";
        require __DIR__ . '/../views/admin/clients/index.php';
    }
    public function viewClientDetails($id_client) {
        $clientModel = new \App\Models\Client($this->db);
        $commandeModel = new Commande($this->db); // Utilisation du modèle Commande
        
        // 1. Récupérer les détails du profil
        $clientData = $clientModel->findById($id_client); 
        
        if (!$clientData) {
            header("Location: /admin/clients?error=Client introuvable.");
            exit();
        }

        // 2. Récupérer tout l'historique des commandes (AVEC LES SIÈGES)
        // La complexité est gérée dans le modèle Commande, c'est propre.
        $orders = $commandeModel->findByClientIdForAdmin($id_client);

        $pageTitle = "Client N°" . $id_client . ": " . $clientData['prenom'] . " " . $clientData['nom'];
        
        // On envoie $clientData et $orders à la vue
        require __DIR__ . '/../views/admin/clients/detail.php';
    }
}