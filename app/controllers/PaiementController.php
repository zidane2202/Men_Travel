<?php
// Fichier: app/controllers/PaiementController.php

namespace App\Controllers;

use App\Core\Database;
use App\Models\Reservation;
use App\Models\Paiement;
use App\Core\Mailer;
use GuzzleHttp\Client; // Importé par Composer
use GuzzleHttp\Exception\RequestException;

class PaiementController {

    private $db;
    
    // N'oubliez pas de mettre votre clé API ici
    private $geminiApiKey = 'AIzaSyApFFcGR15p_rgoskoafcDPXyLnQ7clnjE'; // REMPLACEZ CECI

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    /**
     * Affiche la page de résumé du paiement.
     */
    public function show($id_reservation) {
        if (!isset($_SESSION['client_id'])) { /* (sécurité) */ }

        $reservationModel = new Reservation($this->db);
        $reservationDetails = $reservationModel->getDetailsForPayment($_SESSION['client_id'], $id_reservation);

        if (!$reservationDetails || $reservationDetails['statut'] !== 'EN_ATTENTE') {
            header("Location: /my-reservations?error=Réservation invalide ou déjà traitée.");
            exit();
        }

        $pageTitle = "Finaliser le paiement";
        require __DIR__ . '/../views/payment/show.php';
    }

    /**
     * Traite la simulation de paiement.
     */
    public function process() {
        if (!isset($_SESSION['client_id'])) { /* (sécurité) */ }

        $id_reservation = $_POST['id_reservation'] ?? null;
        $mode_paiement = $_POST['mode'] ?? 'INCONNU';
        $montant = $_POST['montant'] ?? 0;

        if (!$id_reservation) { /* (gestion erreur) */ }

        $paiementModel = new Paiement($this->db);
        $reservationModel = new Reservation($this->db);

        // --- DÉBUT TRANSACTION BDD ---
        try {
            $this->db->beginTransaction();
            $ref = "SIM_" . $mode_paiement . "_" . date('YmdHis');
            $paiementModel->create($id_reservation, $montant, $mode_paiement, $ref, 'SUCCES');
            $reservationModel->updateStatus($id_reservation, 'PAYEE');
            $this->db->commit();
        } catch (\Exception $e) {
            $this->db->rollBack();
            header("Location: /payment/show/$id_reservation?error=Erreur BDD: " . $e->getMessage());
            exit();
        }
        // --- FIN TRANSACTION BDD ---

        // Le paiement est confirmé, maintenant on prépare l'email (l'étape lente)
        
        try {
            // 1. Récupérer toutes les données du ticket
            $ticketData = $reservationModel->getTicketDetails($_SESSION['client_id'], $id_reservation);
            
            if ($ticketData) {
                
                // 2. Générer le message d'intro AVEC L'IA
                $introMessage = $this->generateIntroWithGemini($ticketData);
                
                // 3. Rendre le template HTML (en lui injectant le message de l'IA)
                $htmlBody = $this->renderTicketToHtml($ticketData, $introMessage);
                $subject = "Confirmation de votre ticket Men Travel pour " . $ticketData['ville_depart'];

                // 4. Envoyer l'email
                $mailer = new Mailer();
                $mailer->sendEmail(
                    $ticketData['client_email'], 
                    $ticketData['client_prenom'], 
                    $subject, 
                    $htmlBody
                );
            }
        } catch (\Exception $mailOrApiError) {
            // L'email (ou l'IA) a échoué, MAIS le paiement est confirmé.
             header("Location: /my-reservations?success=Paiement réussi ! (Erreur lors de l'envoi de l'email.)");
            exit();
        }

        // Tout a fonctionné (paiement ET email)
        header("Location: /my-reservations?success=Paiement réussi ! Votre ticket a été confirmé et envoyé par email.");
        exit();
    }


    /**
     * NOUVELLE MÉTHODE : Appelle l'API Gemini pour générer l'intro.
     */
    private function generateIntroWithGemini($ticketData) {
        
        $client = new Client([
            'base_uri' => 'https://generativelanguage.googleapis.com',
            'timeout'  => 30.0,
        ]);

        // 1. Préparer le "Prompt" (bien meilleur)
        $prompt = "Tu es un agent de réservation amical pour 'Men Travel'.
        Rédige un paragraphe d'introduction (2-3 phrases) pour un email de confirmation de ticket.
        Ne crée pas de tableau, juste le texte d'introduction.
        Le format doit être en HTML (juste des balises <p>).
        
        Infos sur le client:
        - Prénom: " . $ticketData['client_prenom'] . "
        - Destination: " . $ticketData['ville_arrivee'] . "
        
        Instructions pour le ton:
        - Adresse-toi au client par son prénom.
        - Sois chaleureux et professionnel.
        - Confirme que sa réservation est payée.
        - Souhaite-lui un bon voyage (personnalise si la destination est Douala ou Kribi, 'voyage vers la côte').
        - Dis-lui que les détails de son ticket sont 'ci-dessous'.
        - Ne signe pas l'email (pas de 'Cordialement').";

        $body = [
            'contents' => [ 'parts' => [ ['text' => $prompt] ] ]
        ];

        try {
            // 2. Envoyer la requête
            $response = $client->post(
                '/v1beta/models/gemini-1.5-flash:generateContent?key=' . $this->geminiApiKey,
                ['json' => $body]
            );

            $result = json_decode($response->getBody()->getContents(), true);
            $generatedText = $result['candidates'][0]['content']['parts'][0]['text'];
            
            // Nettoyage du Markdown
            $generatedText = str_replace("```html", "", $generatedText);
            $generatedText = str_replace("```", "", $generatedText);

            return $generatedText; // Retourne le <p>Bonjour Zidane...</p>

        } catch (RequestException $e) {
            // Si l'API échoue, on génère un message de base
            return "<p>Bonjour " . htmlspecialchars($ticketData['client_prenom']) . ", votre réservation est confirmée. Vous trouverez les détails de votre ticket ci-dessous.</p>";
        }
    }


    /**
     * MÉTHODE DE SECOURS : Charge le template d'email
     * (maintenant mis à jour pour accepter le message de l'IA)
     */
    private function renderTicketToHtml($ticketData, $ia_message) {
        ob_start();
        
        // On injecte le message (généré par l'IA ou par la méthode de secours)
        $ticketData['message_personnalise'] = $ia_message;
        
        require __DIR__ . '/../views/email/ticket_template.php';
        return ob_get_clean();
    }
}