<?php
// Fichier: app/controllers/ReservationController.php

namespace App\Controllers;

use App\Core\Database;
use App\Models\Voyage;
use App\Models\Reservation;
use App\Models\Commande; // <-- IMPORTER LE NOUVEAU MODÈLE

class ReservationController {

    private $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    /**
     * Affiche la page de sélection des sièges.
     */
    public function showSeatSelection($id_voyage) {
        if (!isset($_SESSION['client_id'])) {
            header("Location: /login?error=Veuillez vous connecter pour réserver.");
            exit();
        }

        $voyageModel = new Voyage($this->db);
        $voyage = $voyageModel->findById($id_voyage);

        if (!$voyage) {
            echo "Erreur : Voyage non trouvé.";
            exit();
        }

        $reservationModel = new Reservation($this->db);
        $siegesReserves = $reservationModel->getReservedSeats($id_voyage);

        $pageTitle = "Choisir vos sièges";
        require __DIR__ . '/../views/reservation/select_seat.php';
    }

    /**
     * ======================================
     * NOUVELLE LOGIQUE DE CRÉATION
     * ======================================
     */
    public function create() {
        if (!isset($_SESSION['client_id'])) {
            header("Location: /login");
            exit();
        }

        // 1. Récupérer les données (un tableau de sièges)
        $id_voyage = $_POST['id_voyage'] ?? null;
        $id_client = $_SESSION['client_id'];
        
        // 'numero_siege' EST MAINTENANT UN TABLEAU
        $sieges_selectionnes = $_POST['numero_siege'] ?? []; 

        if (!$id_voyage || empty($sieges_selectionnes)) {
            header("Location: /search?error=Aucun siège sélectionné.");
            exit();
        }

        // 2. Initialiser les modèles
        $voyageModel = new Voyage($this->db);
        $reservationModel = new Reservation($this->db);
        $commandeModel = new Commande($this->db); // <-- NOUVEAU

        // 3. Récupérer le prix du voyage
        $voyage = $voyageModel->findById($id_voyage);
        if (!$voyage) {
            header("Location: /search?error=Voyage introuvable.");
            exit();
        }
        
        // 4. Calculer le total
        $prix_unitaire = (float)$voyage['prix'];
        $nombre_sieges = count($sieges_selectionnes);
        $montant_total = $prix_unitaire * $nombre_sieges;

        // 5. Démarrer une transaction (TRÈS IMPORTANT)
        try {
            $this->db->beginTransaction();

            // 6. Vérifier si les sièges sont toujours libres
            $siegesReserves = $reservationModel->getReservedSeats($id_voyage);
            foreach ($sieges_selectionnes as $siege) {
                if (in_array($siege, $siegesReserves)) {
                    // Un des sièges a été pris ! Annuler.
                    throw new \Exception("Le siège N°$siege vient d'être réservé par quelqu'un d'autre.");
                }
            }

            // 7. Créer UNE (1) Commande
            $id_commande = $commandeModel->create($id_client, $id_voyage, $montant_total);

            // 8. Créer PLUSIEURS (N) Réservations
            foreach ($sieges_selectionnes as $siege) {
                $reservationModel->create($id_client, $id_voyage, $id_commande, $siege);
            }

            // 9. Valider la transaction
            $this->db->commit();

            // 10. Rediriger vers le paiement AVEC L'ID DE LA COMMANDE
            header("Location: /payment/show/" . $id_commande);
            exit();

        } catch (\Exception $e) {
            // Une erreur est survenue, tout annuler
            $this->db->rollBack();
            // Renvoyer l'utilisateur à la page de sélection avec le message d'erreur
            header("Location: /reservation/select-seat/$id_voyage?error=" . urlencode($e->getMessage()));
            exit();
        }
    }
}