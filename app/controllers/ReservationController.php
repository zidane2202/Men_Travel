<?php
// Fichier: app/controllers/ReservationController.php

namespace App\Controllers;

// Importez tous les modèles dont nous aurons besoin
use App\Core\Database;
use App\Models\Voyage;
use App\Models\Reservation;

class ReservationController {

    private $db;

    public function __construct() {
        // Initialiser la connexion BDD pour toutes les méthodes
        $database = new Database();
        $this->db = $database->getConnection();
    }

    /**
     * Affiche la page de sélection des sièges.
     * (Appelée par GET /reservation/select-seat/{id})
     */
    public function showSeatSelection($id_voyage) {
        // Sécurité : vérifier si le client est connecté
        if (!isset($_SESSION['client_id'])) {
            header("Location: /login?error=Veuillez vous connecter pour réserver.");
            exit();
        }

        // 1. Récupérer les détails du voyage
        $voyageModel = new Voyage($this->db);
        $voyage = $voyageModel->findById($id_voyage);

        if (!$voyage) {
            echo "Erreur : Voyage non trouvé ou n'est plus programmé.";
            exit();
        }

        // 2. Récupérer les sièges déjà réservés
        $reservationModel = new Reservation($this->db);
        $siegesReserves = $reservationModel->getReservedSeats($id_voyage);

        // 3. Afficher la vue en lui passant les données
        $pageTitle = "Choisir un siège";
        require __DIR__ . '/../views/reservation/select_seat.php';
    }

    /**
     * Traite la création de la réservation.
     * (Appelée par POST /reservation/create)
     */
    public function create() {
        // Sécurité : vérifier si le client est connecté
        if (!isset($_SESSION['client_id'])) {
            header("Location: /login");
            exit();
        }

        // Récupérer les données du formulaire
        $id_voyage = $_POST['id_voyage'] ?? null;
        $numero_siege = $_POST['numero_siege'] ?? null;
        $id_client = $_SESSION['client_id'];

        if (!$id_voyage || !$numero_siege) {
            header("Location: /search?error=Données de réservation manquantes.");
            exit();
        }

        $reservationModel = new Reservation($this->db);
        
        // Sécurité : Vérifier si le siège n'est pas déjà pris
        $siegesReserves = $reservationModel->getReservedSeats($id_voyage);
        if (in_array($numero_siege, $siegesReserves)) {
             header("Location: /reservation/select-seat/$id_voyage?error=Désolé, ce siège vient d'être pris !");
             exit();
        }

        // Créer la réservation
        $id_reservation = $reservationModel->create($id_client, $id_voyage, $numero_siege);

       if ($id_reservation) {
    // SUCCÈS ! Rediriger vers la nouvelle page de paiement
    header("Location: /payment/show/" . $id_reservation);
    exit();
}else {
            // Échec
            header("Location: /reservation/select-seat/$id_voyage?error=Une erreur est survenue lors de la création.");
            exit();
        }
    }
}