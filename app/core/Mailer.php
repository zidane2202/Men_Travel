<?php
// Fichier: app/core/Mailer.php

namespace App\Core;

// Importez les classes PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

class Mailer {
    
    private $mail;

    public function __construct() {
        // Initialise PHPMailer
        $this->mail = new PHPMailer(true);

        // --- VOTRE CONFIGURATION SMTP GMAIL ---
        // $this->mail->SMTPDebug = SMTP::DEBUG_SERVER;  // Décommentez pour un debug détaillé
        $this->mail->isSMTP();
        $this->mail->Host       = 'smtp.gmail.com';
        $this->mail->SMTPAuth   = true;
        $this->mail->Username   = 'votre.email@gmail.com'; // VOTRE ADRESSE GMAIL
        $this->mail->Password   = 'abcd efgh ijkl mnop';   // VOTRE MOT DE PASSE D'APPLICATION (16 chiffres)
        $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $this->mail->Port       = 587; // Port pour TLS
        
        // Qui envoie l'email ?
        $this->mail->setFrom('votre.email@gmail.com', 'Men Travel (Service de Réservation)');
        
        // S'assurer qu'on utilise l'UTF-8
        $this->mail->CharSet = 'UTF-8';
    }

    /**
     * Envoie un email
     * @param string $toEmail Email du destinataire
     * @param string $toName Nom du destinataire
     * @param string $subject Sujet de l'email
     * @param string $htmlBody Contenu HTML de l'email
     */
    public function sendEmail($toEmail, $toName, $subject, $htmlBody) {
        try {
            // Destinataire
            $this->mail->addAddress($toEmail, $toName);

            // Contenu
            $this->mail->isHTML(true);
            $this->mail->Subject = $subject;
            $this->mail->Body    = $htmlBody;
            // Optionnel : un corps de texte simple pour les clients mail non-HTML
            $this->mail->AltBody = 'Veuillez activer l\'HTML pour voir votre ticket.';

            $this->mail->send();
            return true;
        } catch (Exception $e) {
            // Gérer l'erreur (ex: l'écrire dans un log)
            // echo "Message could not be sent. Mailer Error: {$this->mail->ErrorInfo}";
            return false;
        }
    }
}