<?php
// Fichier: app/controllers/PagesController.php

namespace App\Controllers;

use App\Core\Database;
use App\Models\Voyage;

class PagesController {

    private $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    /**
     * Affiche la page d'accueil publique
     * avec la liste de tous les voyages.
     */
    public function showHomePage() {
        $voyageModel = new Voyage($this->db);
        
        // On utilise la méthode que nous avons déjà créée
        $voyages = $voyageModel->getAllProgrammed();

        $pageTitle = "Bienvenue chez Men Travel";
        
        // On charge une NOUVELLE vue
        require __DIR__ . '/../views/pages/home.php';
    }
}