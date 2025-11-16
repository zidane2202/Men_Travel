<?php
// Fichier: app/controllers/PaiementController.php

namespace App\Controllers;

use App\Core\Database;
use App\Models\Reservation;
use App\Models\Paiement;
use App\Core\Mailer;
class PaiementController {

    private $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    /**
     * Affiche la page de résumé du paiement.
     * (Appelée par GET /payment/show/{id})
     */
    public function show($id_reservation) {
        // Sécurité : vérifier la connexion
        if (!isset($_SESSION['client_id'])) {
            header("Location: /login");
            exit();
        }

        $reservationModel = new Reservation($this->db);
        // On a besoin d'une nouvelle méthode pour récupérer les détails
        $reservationDetails = $reservationModel->getDetailsForPayment($_SESSION['client_id'], $id_reservation);

        if (!$reservationDetails || $reservationDetails['statut'] !== 'EN_ATTENTE') {
            // Si la résa n'existe pas, n'appartient pas au client, ou est déjà payée/annulée
            header("Location: /my-reservations?error=Réservation invalide ou déjà traitée.");
            exit();
        }

        $pageTitle = "Finaliser le paiement";
        require __DIR__ . '/../views/payment/show.php';
    }

    /**
     * Traite la simulation de paiement.
     * (Appelée par POST /payment/process)
     */
    public function process() {
        // ... (tout le début ne change pas)

        try {
            $this->db->beginTransaction();

            // 1. Enregistrer le paiement
            $ref = "SIM_" . $mode_paiement . "_" . date('YmdHis');
            $paiementModel->create($id_reservation, $montant, $mode_paiement, $ref, 'SUCCES');

            // 2. Mettre à jour le statut
            $reservationModel->updateStatus($id_reservation, 'PAYEE');

            // 3. Valider la transaction
            $this->db->commit();

            // --- NOUVELLE PARTIE : ENVOI DE L'EMAIL ---
            try {
                // 4. Récupérer toutes les données du ticket
                $ticketData = $reservationModel->getTicketDetails($_SESSION['client_id'], $id_reservation);
                
                if ($ticketData) {
                    // 5. Rendre le template HTML
                    $htmlBody = $this->renderTicketToHtml($ticketData);
                    $subject = "Votre ticket Men Travel pour " . $ticketData['ville_depart'];

                    // 6. Envoyer l'email
                    $mailer = new Mailer();
                    $mailer->sendEmail(
                        $ticketData['client_email'], 
                        $ticketData['client_prenom'], 
                        $subject, 
                        $htmlBody
                    );
                }
            } catch (\Exception $mailError) {
                // L'email n'a pas pu être envoyé, MAIS le paiement est confirmé.
                // On ne bloque pas l'utilisateur pour ça.
                // Dans un vrai projet, on mettrait ceci dans un log.
            }
            // --- FIN ENVOI EMAIL ---

            // Rediriger vers les réservations avec un succès
            header("Location: /my-reservations?success=Paiement réussi ! Votre ticket a été confirmé et envoyé par email.");
            exit();

        } catch (\Exception $e) {
            $this->db->rollBack();
            header("Location: /payment/show/$id_reservation?error=Erreur BDD: " . $e->getMessage());
            exit();
        }
    }
    private function renderTicketToHtml($ticketData) {
        // ob_start "ouvre un tampon" pour capturer tout ce qui est "echo"
        ob_start();
        
        // Inclut le template. PHP va l'exécuter.
        // La variable $ticketData est maintenant disponible dans le template.
        require __DIR__ . '/../views/email/ticket_template.php';
        
        // Récupère le contenu du tampon et le nettoie
        return ob_get_clean();
    }
}