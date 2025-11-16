<?php
// Fichier: app/models/Commande.php

namespace App\Models;

use PDO;

class Commande {
    private $conn;
    private $table_name = "commandes";

    public function __construct(PDO $db) {
        $this->conn = $db;
    }

    /**
     * Crée une nouvelle commande (panier)
     */
    public function create($id_client, $id_voyage, $montant_total) {
        // ... (votre code create() existant) ...
        $query = "INSERT INTO " . $this->table_name . "
                  SET 
                      id_client = :id_client,
                      id_voyage = :id_voyage,
                      montant_total = :montant_total,
                      statut = 'EN_ATTENTE'";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_client', $id_client);
        $stmt->bindParam(':id_voyage', $id_voyage);
        $stmt->bindParam(':montant_total', $montant_total);
        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }

    /**
     * AJOUT 1: Trouve une commande par son ID et vérifie le client
     */
    public function findByIdAndClient($id_ref, $id_user, $forAdmin = false) {
        $field = $forAdmin ? 'v.id_voyage' : 'c.id_commande';
        $userCheck = $forAdmin ? '' : 'AND c.id_client = :id_client'; // Admin n'a pas besoin de vérifier le client

        $query = "SELECT
                    c.id_commande, c.montant_total, c.statut,
                    v.ville_depart, v.ville_arrivee, v.date_depart, v.prix
                FROM
                    commandes c
                JOIN
                    voyages v ON c.id_voyage = v.id_voyage
                WHERE
                    {$field} = :id_ref
                    {$userCheck}
                LIMIT 0,1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_ref', $id_ref);
        if (!$forAdmin) {
            $stmt->bindParam(':id_client', $id_user);
        }
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    /**
     * AJOUT 2: Met à jour le statut d'une commande
     */
    public function updateStatus($id_commande, $statut) {
        $query = "UPDATE " . $this->table_name . "
                  SET statut = :statut
                  WHERE id_commande = :id_commande";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':statut', $statut);
        $stmt->bindParam(':id_commande', $id_commande);

        return $stmt->execute();
    }
    
    /**
     * AJOUT 3: Récupère les infos pour l'email (commande + tous les sièges)
     */
    public function getCommandeDetailsForEmail($id_commande, $id_client) {
        // 1. Récupérer les infos client et voyage
        $details = $this->findByIdAndClient($id_commande, $id_client);
        if (!$details) return null;

        // 2. Récupérer les infos client (pour l'email)
        $clientQuery = "SELECT prenom, nom, email, sexe FROM clients WHERE id_client = :id_client";
        $stmt = $this->conn->prepare($clientQuery);
        $stmt->bindParam(':id_client', $id_client);
        $stmt->execute();
        $clientInfo = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $details['client_prenom'] = $clientInfo['prenom'];
        $details['client_nom'] = $clientInfo['nom'];
        $details['client_email'] = $clientInfo['email'];
        $details['client_sexe'] = $clientInfo['sexe'];

        // 3. Récupérer TOUS les sièges pour cette commande
        $seatsQuery = "SELECT numero_siege FROM reservations 
                       WHERE id_commande = :id_commande 
                       ORDER BY numero_siege ASC";
        $stmt = $this->conn->prepare($seatsQuery);
        $stmt->bindParam(':id_commande', $id_commande);
        $stmt->execute();
        
        $seats = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
        $details['sieges'] = $seats; // un tableau [5, 6, 7]
        
        // 4. Récupérer les infos du voyage (pour le template)
        $voyageQuery = "SELECT v.date_depart, veh.marque, veh.immatriculation
                        FROM voyages v 
                        JOIN vehicules veh ON v.id_vehicule = veh.id_vehicule
                        WHERE v.id_voyage = (SELECT id_voyage FROM commandes WHERE id_commande = :id_commande)";
        $stmt = $this->conn->prepare($voyageQuery);
        $stmt->bindParam(':id_commande', $id_commande);
        $stmt->execute();
        $voyageInfo = $stmt->fetch(PDO::FETCH_ASSOC);

        $details['date_depart'] = $voyageInfo['date_depart'];
        $details['vehicule_marque'] = $voyageInfo['marque'];
        $details['immatriculation'] = $voyageInfo['immatriculation'];

        return $details;
    }
    // ... (dans app/models/Commande.php, après les autres méthodes)

    /**
     * AJOUT 4: Récupère toutes les commandes d'un client pour la page "Mes Réservations"
     */
    public function findCommandsByClientId($id_client) {
        // 1. Récupérer toutes les commandes et les infos du voyage
        $query = "SELECT
                    c.id_commande,
                    c.montant_total,
                    c.statut,
                    c.date_commande,
                    v.ville_depart,
                    v.ville_arrivee,
                    v.date_depart
                FROM
                    " . $this->table_name . " c
                JOIN
                    voyages v ON c.id_voyage = v.id_voyage
                WHERE
                    c.id_client = :id_client
                ORDER BY
                    c.date_commande DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_client', $id_client);
        $stmt->execute();
        $commandes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // 2. Pour chaque commande, récupérer les sièges associés
        // (On utilise une 2ème requête pour éviter les duplicata)
        $seatsQuery = "SELECT numero_siege FROM reservations 
                       WHERE id_commande = :id_commande 
                       ORDER BY numero_siege ASC";
        
        // On prépare la requête une seule fois
        $seatStmt = $this->conn->prepare($seatsQuery);

        // On boucle sur les commandes (par référence '&') pour ajouter les sièges
        foreach ($commandes as &$commande) {
            $seatStmt->bindParam(':id_commande', $commande['id_commande']);
            $seatStmt->execute();
            $seats = $seatStmt->fetchAll(PDO::FETCH_COLUMN, 0);
            
            // On ajoute le tableau de sièges à notre commande
            $commande['sieges'] = $seats; 
        }

        return $commandes;
    }
}