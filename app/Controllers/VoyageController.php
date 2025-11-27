<?php
// Fichier: app/controllers/VoyageController.php

namespace App\Controllers;

// Importez les modèles
use App\Core\Database;
use App\Models\Voyage;

class VoyageController {

    /**
     * Affiche le formulaire de recherche
     * OU les résultats si le formulaire est soumis.
     */
    public function showSearchPage() {
        // Sécurité : L'utilisateur doit être connecté pour chercher
        if (!isset($_SESSION['client_id'])) {
            header("Location: /login?error=Veuillez vous connecter.");
            exit();
        }

        $database = new Database();
        $db = $database->getConnection();
        $voyageModel = new Voyage($db);

        // Variables pour la vue
        $pageTitle = "Chercher un voyage";
        $voyages = []; // Par défaut, pas de résultats
        $searchParams = $_GET; // Récupère les ?ville_depart=...

        // Si le formulaire est soumis (on vérifie si les clés existent)
        if (isset($searchParams['ville_depart']) && isset($searchParams['ville_arrivee']) && isset($searchParams['date_depart'])) {
            
            if (!empty($searchParams['ville_depart']) && !empty($searchParams['ville_arrivee']) && !empty($searchParams['date_depart'])) {
                // Lancer la recherche
                $voyages = $voyageModel->search(
                    $searchParams['ville_depart'],
                    $searchParams['ville_arrivee'],
                    $searchParams['date_depart']
                );
                $pageTitle = "Résultats de la recherche";
            }
        }
        
        // On charge la même vue, qu'on ait des résultats ou non
        require __DIR__ . '/../views/voyage/search.php';
    }
}