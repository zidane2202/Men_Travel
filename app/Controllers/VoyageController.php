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
        // Sécurité
        if (!isset($_SESSION['client_id'])) {
            header("Location: /login?error=Veuillez vous connecter.");
            exit();
        }

        $database = new Database();
        $db = $database->getConnection();
        $voyageModel = new Voyage($db);

        // Variables pour la vue
        $searchParams = $_GET;
        $voyages = [];
        $pageTitle = "Chercher un voyage";
        $resultsTitle = ""; // Titre pour la section des résultats

        // Vérifie si un formulaire a été soumis avec des données valides
        if (isset($searchParams['ville_depart']) && !empty($searchParams['ville_depart']) && !empty($searchParams['ville_arrivee'])) {
            // -- CAS 1: L'utilisateur a fait une recherche --
            $voyages = $voyageModel->search(
                $searchParams['ville_depart'],
                $searchParams['ville_arrivee'],
                $searchParams['date_depart']
            );
            $pageTitle = "Résultats de la recherche";
            $resultsTitle = "Résultats de votre recherche";
        } else {
            // -- CAS 2: L'utilisateur vient d'arriver sur la page --
            $voyages = $voyageModel->getAllProgrammed();
            $resultsTitle = "Tous les voyages programmés";
        }
        
        // On envoie les variables à la vue
        require __DIR__ . '/../views/voyage/search.php';
    }
}