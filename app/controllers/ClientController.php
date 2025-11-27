<?php
// Fichier: app/controllers/ClientController.php

// Namespace correspondant au dossier (App\Controllers)
namespace App\controllers;

// On IMPORTE les classes dont on a besoin
use App\Core\Database;
use App\Models\Client;
use App\Models\Reservation; // <-- AJOUTEZ CETTE LIGNE
class ClientController {

    // Affiche la page d'accueil (simple)
    public function showHomePage() {
        echo "<h1>Bienvenue sur Men Travel !</h1>";
        echo "<p><a href='/register'>S'inscrire</a> | <a href='/login'>Se connecter</a></p>";
    }

    // Affiche la page d'inscription
    public function showRegisterPage() {
        // Charge la vue
        require __DIR__ . '/../views/client/register.php';
    }

    // Traite l'inscription (POST)
    public function handleRegister() {
        $database = new Database();
        $db = $database->getConnection();
        $client = new Client($db);

        // Récupérer les données
        $client->nom = $_POST['nom'];
        $client->prenom = $_POST['prenom'];
        $client->email = $_POST['email'];
        $client->mot_de_passe = $_POST['mot_de_passe'];
        $client->telephone = $_POST['telephone'];
        $client->date_naissance = $_POST['date_naissance'];
        $client->sexe = $_POST['sexe'];
        $client->adresse = $_POST['adresse'];

        if ($client->register()) {
            // Succès : Rediriger vers la page de connexion
            header("Location: /login?success=Inscription réussie !");
            exit();
        } else {
            // Échec (email existe)
            header("Location: /register?error=Cet email est déjà utilisé.");
            exit();
        }
    }

    // Affiche la page de connexion
    public function showLoginPage() {
        // Charge la vue
        require __DIR__ . '/../views/client/login.php';
    }

    // Traite la connexion (POST)
    public function handleLogin() {
        $database = new Database();
        $db = $database->getConnection();
        $client = new Client($db);

        $client->email = $_POST['email'];
        $client->mot_de_passe = $_POST['mot_de_passe'];

        if ($client->login()) {
            // SUCCÈS !
            $_SESSION['client_id'] = $client->id_client;
            $_SESSION['client_nom'] = $client->nom . ' ' . $client->prenom;
            
            // Rediriger vers le tableau de bord
            header("Location: /dashboard");
            exit();
        } else {
            // Échec
            header("Location: /login?error=Email ou mot de passe incorrect.");
            exit();
        }
    }

    // Affiche le tableau de bord (protégé)
   // Affiche le tableau de bord (protégé)
    public function showDashboard() {
        // Sécurité : vérifier si le client est connecté
        if (!isset($_SESSION['client_id'])) {
            header("Location: /login?error=Veuillez vous connecter.");
            exit();
        }

        // Si on est connecté, on CHARGE LA VUE
        require __DIR__ . '/../views/client/dashboard.php';
    }

    // Gère la déconnexion
    public function handleLogout() {
        session_destroy();
        header("Location: /login");
        exit();
    }

   /**
     * Affiche la page "Mes réservations" (protégée)
     */
    public function showMyReservations() {
        // Sécurité : vérifier si le client est connecté
        if (!isset($_SESSION['client_id'])) {
            header("Location: /login?error=Veuillez vous connecter.");
            exit();
        }

        // --- MISE À JOUR ---
        // On instancie la BDD et le nouveau modèle
        $database = new Database();
        $db = $database->getConnection();
        $reservationModel = new Reservation($db);
        
        // On récupère les VRAIES réservations de l'utilisateur connecté
        $reservations = $reservationModel->getByClientId($_SESSION['client_id']); 
        // --- FIN MISE À JOUR ---
        
        $pageTitle = "Mes réservations";
        require __DIR__ . '/../views/client/my_reservations.php';
    }

    /**
     * Affiche la page "Mon profil" (protégée)
     */
    public function showProfile() {
        // Sécurité : vérifier si le client est connecté
        if (!isset($_SESSION['client_id'])) {
            header("Location: /login?error=Veuillez vous connecter.");
            exit();
        }

        // On charge les infos actuelles du client
        $database = new Database();
        $db = $database->getConnection();
        $clientModel = new Client($db);
        
        // On utilise la fonction findById qu'on avait créée
        $clientData = $clientModel->findById($_SESSION['client_id']);

        $pageTitle = "Mon profil";
        // On passe les données du client à la vue
        require __DIR__ . '/../views/client/profile.php';
    }
    /**
     * Traite la mise à jour du formulaire de profil (POST)
     */
    public function handleProfileUpdate() {
        // Sécurité : vérifier si le client est connecté
        if (!isset($_SESSION['client_id'])) {
            header("Location: /login");
            exit();
        }

        // Connexion BDD et Modèle
        $database = new Database();
        $db = $database->getConnection();
        $clientModel = new Client($db);

        // Appel de la méthode de mise à jour
        $result = $clientModel->update(
            $_SESSION['client_id'],
            $_POST['nom'],
            $_POST['prenom'],
            $_POST['telephone'],
            $_POST['adresse'],
            $_POST['date_naissance']
        );

        if ($result) {
            // Important : Mettre à jour le nom dans la session !
            $_SESSION['client_nom'] = $_POST['prenom'] . ' ' . $_POST['nom'];
            
            // Rediriger avec un message de succès
            header("Location: /profile?success=Profil mis à jour avec succès !");
        } else {
            // Rediriger avec un message d'erreur
            header("Location: /profile?error=Erreur lors de la mise à jour.");
        }
        exit();
    }
}
