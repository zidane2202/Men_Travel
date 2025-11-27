<?php
// Fichier: app/controllers/AdminReservationController.php

namespace App\Controllers;

use App\Core\Database;
use App\Models\Commande; // Le modèle clé
use App\Models\Reservation; // Pour les détails des sièges
use PDOException;

class AdminReservationController {

    private $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    /**
     * Affiche la liste de toutes les commandes/réservations.
     */
    public function listCommandes() {
        $commandeModel = new \App\Models\Commande($this->db);
        
        // Récupération des données (Ceci fonctionne)
        $commandes = $commandeModel->findAllForAdmin(); 

        $pageTitle = "Vue d'ensemble des Réservations";
        
        // --- BYPASS TEMPORAIRE DU CHEMIN ---
        try {
            // Tentative d'inclusion de la vue
            require __DIR__ . '/../views/admin/reservations/index.php';
        } catch (\Throwable $e) {
            // Si l'inclusion échoue (votre cas), affichez un message de fallback
            echo "<h1>Erreur d'affichage de la vue Admin</h1>";
            echo "<p>La page `/views/admin/reservation/index.php` n'a pas été trouvée (vérifiez la casse des dossiers).";
            echo "<p>Vérification des données (si vous voyez un tableau ci-dessous, la base de données est OK) :</p>";
            echo "<pre>"; var_dump($commandes); echo "</pre>";
        }
    }
}