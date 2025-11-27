<?php
// Fichier: app/controllers/ClientController.php

// Namespace correspondant au dossier (App\Controllers)
namespace App\Controllers;

// On IMPORTE les classes dont on a besoin
use App\Core\Database;
use App\Models\Client;
use App\Models\Reservation;
use App\Models\Commande; // <-- IMPORTATION AJOUTÉE

use PDO;
class ClientController {

    private $db; // Propriété pour la connexion BDD

    // Constructeur pour initialiser la BDD
    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    // Affiche la page d'accueil (simple) - (Obsolète car vous redirigez)
    public function showHomePage() {
        echo "<h1>Bienvenue sur Men Travel !</h1>";
        echo "<p><a href='/register'>S'inscrire</a> | <a href='/login'>Se connecter</a></p>";
    }

    // Affiche la page d'inscription
    public function showRegisterPage() {
        require __DIR__ . '/../views/client/register.php';
    }

    // Traite l'inscription (POST)
    public function handleRegister() {
        // $this->db est maintenant disponible grâce au constructeur
        $client = new Client($this->db); 

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
            header("Location: /login?success=Inscription réussie !");
            exit();
        } else {
            header("Location: /register?error=Cet email est déjà utilisé.");
            exit();
        }
    }

    // Affiche la page de connexion
    public function showLoginPage() {
        require __DIR__ . '/../views/client/login.php';
    }

    // Traite la connexion (POST)
   public function handleLogin() {
        $client = new Client($this->db);

        $client->email = $_POST['email'];
        $client->mot_de_passe = $_POST['mot_de_passe'];

        if ($client->login()) {
            // SUCCÈS !
            $_SESSION['client_id'] = $client->id_client;
            $_SESSION['client_nom'] = $client->nom . ' ' . $client->prenom;
            
            // --- NOUVELLE LOGIQUE DE REDIRECTION ---
            // On vérifie s'il y a une redirection en attente
            if (isset($_SESSION['redirect_to'])) {
                $redirectUrl = $_SESSION['redirect_to'];
                unset($_SESSION['redirect_to']); // On nettoie la session
                header("Location: " . $redirectUrl);
            } else {
                // Sinon (connexion normale), on va au tableau de bord
                header("Location: /dashboard");
            }
            exit();
            // --- FIN DE LA NOUVELLE LOGIQUE ---

        } else {
            // Échec
            header("Location: /login?error=Email ou mot de passe incorrect.");
            exit();
        }
    }
    // Affiche le tableau de bord (protégé)
    public function showDashboard() {
        if (!isset($_SESSION['client_id'])) {
            header("Location: /login?error=Veuillez vous connecter.");
            exit();
        }

        $id_client = $_SESSION['client_id'];
        $clientModel = new Client($this->db);
        $commandeModel = new Commande($this->db);

        // 1. Récupérer le nom complet (si non déjà en session)
        $clientData = $clientModel->findById($id_client); // Réutilise findById pour les détails

        // 2. Récupérer les statistiques de réservation (par exemple, le nombre total)
        // NOTE: findCommandsByClientId dans Commande.php est un peu lourd pour juste un count.
        
        $statsQuery = "SELECT COUNT(id_commande) as total_trips, 
                              MAX(date_commande) as last_booking 
                       FROM commandes 
                       WHERE id_client = :id_client AND statut = 'PAYEE'";
        $statsStmt = $this->db->prepare($statsQuery);
        $statsStmt->bindParam(':id_client', $id_client);
        $statsStmt->execute();
        $reservationStats = $statsStmt->fetch(PDO::FETCH_ASSOC);

        // 3. Récupérer le prochain voyage (limit 1)
        $nextTripQuery = "SELECT v.ville_depart, v.ville_arrivee, v.date_depart 
                          FROM commandes c
                          JOIN voyages v ON c.id_voyage = v.id_voyage
                          WHERE c.id_client = :id_client 
                          AND c.statut = 'PAYEE'
                          AND v.date_depart >= NOW() 
                          ORDER BY v.date_depart ASC 
                          LIMIT 1";
        $tripStmt = $this->db->prepare($nextTripQuery);
        $tripStmt->bindParam(':id_client', $id_client);
        $tripStmt->execute();
        $nextTrip = $tripStmt->fetch(PDO::FETCH_ASSOC);

        // On passe les données à la vue
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
    * (VERSION CORRIGÉE)
    */
   public function showMyReservations() {
        // Sécurité
        if (!isset($_SESSION['client_id'])) {
            header("Location: /login?error=Veuillez vous connecter.");
            exit();
        }

        // --- MISE À JOUR ---
        // On importe le modèle Commande (on utilise $this->db)
        $commandeModel = new \App\Models\Commande($this->db);
        
        // On appelle la nouvelle méthode
        $commandes = $commandeModel->findCommandsByClientId($_SESSION['client_id']); 
        // --- FIN MISE À JOUR ---
        
        $pageTitle = "Mes réservations";
        
        // On envoie le tableau $commandes à la vue
        require __DIR__ . '/../views/client/my_reservations.php';
    }

    //
    // LE BLOC DE CODE ERRONÉ (lignes 128-138) A ÉTÉ COMPLÈTEMENT SUPPRIMÉ
    //

    /**
     * Affiche la page "Mon profil" (protégée)
     */
     public function showProfile() {
        if (!isset($_SESSION['client_id'])) {
            header("Location: /login?error=Veuillez vous connecter.");
            exit();
        }

        $clientModel = new Client($this->db);
        $clientData = $clientModel->findById($_SESSION['client_id']);

        $pageTitle = "Mon profil";
        require __DIR__ . '/../views/client/profile.php';
    }
    
    /**
     * Traite la mise à jour du formulaire de profil (POST)
     */
     public function handleProfileUpdate() {
        if (!isset($_SESSION['client_id'])) {
            header("Location: /login");
            exit();
        }

        $clientModel = new Client($this->db);

        $result = $clientModel->update(
            $_SESSION['client_id'],
            $_POST['nom'],
            $_POST['prenom'],
            $_POST['telephone'],
            $_POST['adresse'],
            $_POST['date_naissance']
        );

        if ($result) {
            $_SESSION['client_nom'] = $_POST['prenom'] . ' ' . $_POST['nom'];
            header("Location: /profile?success=Profil mis à jour avec succès !");
        } else {
            header("Location: /profile?error=Erreur lors de la mise à jour.");
        }
        exit();
    }
}
