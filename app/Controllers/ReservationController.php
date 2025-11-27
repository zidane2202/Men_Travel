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
            
            // --- NOUVELLE LOGIQUE ---
            // On sauvegarde la page où l'utilisateur Voulait aller.
            $_SESSION['redirect_to'] = $_SERVER['REQUEST_URI']; // Ex: /reservation/select-seat/5
            
            header("Location: /login?error=Veuillez vous connecter pour réserver.");
            exit();
        }

        // Le code existant s'exécute si l'utilisateur EST connecté
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
     * Traite la création de la réservation.
     * (Appelée par POST /reservation/create)
     */
    public function create() {
        if (!isset($_SESSION['client_id'])) {
            header("Location: /login");
            exit();
        }

        // 1. Récupérer les données (un tableau de sièges)
        $id_voyage = $_POST['id_voyage'] ?? null;
        $id_client = $_SESSION['client_id'];
        
        // 'numero_siege' EST UN TABLEAU (ou vide si rien n'est sélectionné)
        $sieges_selectionnes = $_POST['numero_siege'] ?? []; 

        if (!$id_voyage || empty($sieges_selectionnes)) {
            header("Location: /search?error=Aucun siège sélectionné.");
            exit();
        }

        // 2. Initialiser les modèles
        $voyageModel = new \App\Models\Voyage($this->db);
        $reservationModel = new \App\Models\Reservation($this->db);
        $commandeModel = new \App\Models\Commande($this->db); 

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

            // 6. Vérifier si les sièges sont toujours libres (Sécurité contre le "race condition")
            $siegesReserves = $reservationModel->getReservedSeats($id_voyage);
            foreach ($sieges_selectionnes as $siege) {
                if (in_array((int)$siege, $siegesReserves)) {
                    throw new \Exception("Le siège N°$siege vient d'être réservé par quelqu'un d'autre.");
                }
            }

            // 7. Créer UNE (1) Commande
            $id_commande = $commandeModel->create($id_client, $id_voyage, $montant_total);

            // 8. Créer PLUSIEURS (N) Réservations (avec les 4 arguments)
            foreach ($sieges_selectionnes as $siege) {
                // Cette ligne est le point de l'erreur 
                $reservationModel->create($id_client, $id_voyage, $id_commande, (int)$siege);
            }

            // 9. Valider la transaction
            $this->db->commit();

            // 10. Rediriger vers le paiement AVEC L'ID DE LA COMMANDE
            header("Location: /payment/show/" . $id_commande);
            exit();

        } catch (\Exception $e) {
            // Une erreur est survenue, tout annuler
            $this->db->rollBack();
            // L'erreur sera affichée sur la page de sélection des sièges
            header("Location: /reservation/select-seat/$id_voyage?error=" . urlencode($e->getMessage()));
            exit();
        }
    }
}