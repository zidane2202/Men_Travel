<?php
// Fichier: app/controllers/TicketController.php

namespace App\Controllers;

use App\Core\Database;
use App\Models\Reservation; // Nous aurons besoin du modèle Réservation

class TicketController {

    private $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    /**
     * Affiche la page du ticket (pour impression/visualisation)
     * (Appelée par GET /ticket/view/{id})
     */
    public function show($id_reservation) {
        // Sécurité : vérifier la connexion
        if (!isset($_SESSION['client_id'])) {
            header("Location: /login");
            exit();
        }
        $id_client = $_SESSION['client_id'];

        $reservationModel = new Reservation($this->db);
        
        // On a besoin d'une nouvelle méthode qui joint toutes les tables
        $ticketData = $reservationModel->getTicketDetails($id_client, $id_reservation);

        if (!$ticketData) {
            // Si le ticket n'existe pas ou n'appartient pas au client
            header("Location: /my-reservations?error=Ticket introuvable.");
            exit();
        }
        
        // On vérifie aussi que le ticket est bien PAYE
        if ($ticketData['statut_reservation'] !== 'PAYEE') {
             header("Location: /my-reservations?error=Ce ticket n'est pas encore payé.");
            exit();
        }

        $pageTitle = "Votre Ticket de Voyage";
        // On passe toutes les données du ticket à la vue
        require __DIR__ . '/../views/ticket/view.php';
    }
}