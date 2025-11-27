<?php
// Fichier: app/controllers/PaiementController.php

namespace App\Controllers;

use App\Core\Database;
use App\Models\Reservation;
use App\Models\Paiement;
use App\Models\Commande;
use App\Core\Mailer;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class PaiementController {

    private $db;
    private $geminiApiKey = 'VOTRE_CLE_API_GEMINI_ICI'; // N'oubliez pas votre clé

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    /**
     * Affiche la page de paiement pour une COMMANDE
     */
    // publicS-Function: show <-- ERREUR SUPPRIMÉE
    public function show($id_commande) {
        if (!isset($_SESSION['client_id'])) {
            header("Location: /login");
            exit();
        }

        $commandeModel = new Commande($this->db);
        $commandeDetails = $commandeModel->findByIdAndClient($id_commande, $_SESSION['client_id']);

        if (!$commandeDetails || $commandeDetails['statut'] !== 'EN_ATTENTE') {
            header("Location: /my-reservations?error=Commande invalide ou déjà traitée.");
            exit();
        }

        $pageTitle = "Finaliser le paiement";
        require __DIR__ . '/../views/payment/show.php';
    }

    /**
     * Traite le paiement pour une COMMANDE
     */
    public function process() {
        if (!isset($_SESSION['client_id'])) { 
            header("Location: /login");
            exit();
        }

        $id_commande = $_POST['id_commande'] ?? null;
        $mode_paiement = $_POST['mode'] ?? 'INCONNU';
        $montant = $_POST['montant'] ?? 0;

        if (!$id_commande) { 
            header("Location: /my-reservations?error=Une erreur est survenue.");
            exit();
        }

        $paiementModel = new Paiement($this->db);
        $reservationModel = new Reservation($this->db); 
        $commandeModel = new Commande($this->db);
        
        try {
            $this->db->beginTransaction();

            $ref = "SIM_" . $mode_paiement . "_" . date('YmdHis');
            $paiementModel->create($id_commande, $montant, $mode_paiement, $ref, 'SUCCES');
            $commandeModel->updateStatus($id_commande, 'PAYEE');
            $reservationModel->updateStatusByCommandeId($id_commande, 'PAYEE');

            $this->db->commit();
        } catch (\Exception $e) {
            $this->db->rollBack();
            header("Location: /payment/show/$id_commande?error=Erreur BDD: " . $e->getMessage());
            exit();
        }
        
        // --- GESTION DE L'EMAIL ---
        try {
            $ticketData = $commandeModel->getCommandeDetailsForEmail($id_commande, $_SESSION['client_id']);
            
            if ($ticketData) {
                $introHtmlBlock = $this->generateIntroWithGemini($ticketData);
                $htmlBody = $this->renderTicketToHtml($ticketData, $introHtmlBlock);
                $subject = "Confirmation de votre commande Men Travel pour " . $ticketData['ville_depart'];

                $mailer = new Mailer();
                $mailer->sendEmail(
                    $ticketData['client_email'], 
                    $ticketData['client_prenom'], 
                    $subject, 
                    $htmlBody
                );
            }
        } catch (\Exception $mailOrApiError) {
             header("Location: /my-reservations?success=Paiement réussi ! (Erreur lors de l'envoi de l'email.)");
            exit();
        }

        header("Location: /my-reservations?success=Paiement réussi ! Votre commande a été confirmée et envoyée par email.");
        exit();
    }


    /**
     * Génère l'intro (l'IA utilise maintenant un tableau de sièges)
     */
    private function generateIntroWithGemini($ticketData) {
        $client = new Client([
            'base_uri' => 'https://generativelanguage.googleapis.com',
            'timeout'  => 30.0,
        ]);

        $politesse = ($ticketData['client_sexe'] === 'HOMME') ? "Cher" : "Chère";
        $sieges_str = "N°" . implode(" et N°", $ticketData['sieges']);

        $prompt = "Tu es un agent de réservation amical pour 'Men Travel'.
        Rédige l'introduction HTML pour un email de confirmation de commande.
        Le format doit être en HTML.
        
        Instructions:
        1.  Commence par <h1 style=\"color: #003366;\">Commande Confirmée !</h1>.
        2.  Ensuite, écris un paragraphe <p> qui utilise EXACTEMENT: '$politesse " . $ticketData['client_prenom'] . ",'
        3.  Confirme que le paiement pour la commande (pas juste un ticket) est réussi.
        4.  Mentionne qu'ils ont réservé plusieurs sièges.
        5.  Termine en disant 'Vous trouverez les détails de vos tickets ci-dessous.'
        
        Informations:
        - Salutation: '$politesse " . $ticketData['client_prenom'] . "'
        - Destination: " . $ticketData['ville_arrivee'] . "
        - Sièges réservés: " . $sieges_str . "
        
        Exemple : 
        '<h1 style=\"color: #003366;\">Commande Confirmée !</h1><p>$politesse " . $ticketData['client_prenom'] . ", félicitations ! Votre paiement a été accepté. Votre réservation pour les sièges $sieges_str vers " . $ticketData['ville_arrivee'] . " est confirmée. Vous trouverez tous les détails ci-dessous.</p>'";

        $body = [ 'contents' => [ 'parts' => [ ['text' => $prompt] ] ] ];

        try {
            $response = $client->post(
                '/v1beta/models/gemini-1.5-flash:generateContent?key=' . $this->geminiApiKey,
                ['json' => $body]
            );
            $result = json_decode($response->getBody()->getContents(), true);
            $generatedText = $result['candidates'][0]['content']['parts'][0]['text'];
            $generatedText = str_replace(["```html", "```"], "", $generatedText);
            return $generatedText;
        } catch (RequestException $e) {
            return "<h1 style=\"color: #003366;\">Commande Confirmée !</h1><p>" . htmlspecialchars($politesse) . " " . htmlspecialchars($ticketData['client_prenom']) . ", votre réservation pour les sièges " . htmlspecialchars($sieges_str) . " est confirmée. Vous trouverez les détails ci-dessous.</p>";
        }
    }

    /**
     * Charge le template d'email
     */
    private function renderTicketToHtml($ticketData, $ia_message) {
        ob_start();
        $ticketData['message_personnalise'] = $ia_message;
        require __DIR__ . '/../views/email/ticket_template.php';
        return ob_get_clean();
    }
}