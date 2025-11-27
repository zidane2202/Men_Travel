<?php
// Fichier: app/models/Reservation.php

namespace App\Models;

use PDO;

class Reservation {
    private $conn;
    private $table_name = "reservations";

    public function __construct(PDO $db) {
        $this->conn = $db;
    }

    /**
     * Récupère toutes les réservations d'un client
     * avec les détails du voyage associé.
     */
    public function getByClientId($id_client) {
        
        // Cette requête est puissante : elle joint les réservations et les voyages
        $query = "SELECT
                    r.id_reservation,
                    r.numero_siege,
                    r.statut AS statut_reservation,
                    r.date_reservation,
                    v.ville_depart,
                    v.ville_arrivee,
                    v.date_depart,
                    v.prix
                FROM
                    " . $this->table_name . " r
                JOIN
                    voyages v ON r.id_voyage = v.id_voyage
                WHERE
                    r.id_client = :id_client
                ORDER BY
                    v.date_depart DESC"; // Les plus récents en premier

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_client', $id_client);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getReservedSeats($id_voyage) {
        $query = "SELECT numero_siege FROM " . $this->table_name . "
                  WHERE id_voyage = :id_voyage 
                  AND statut != 'ANNULEE'"; // On ne compte pas les sièges annulés

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_voyage', $id_voyage);
        $stmt->execute();
        
        // Retourne un simple tableau de numéros [5, 12, 18]
        return $stmt->fetchAll(PDO::FETCH_COLUMN, 0); 
    }

    /**
     * Crée une nouvelle réservation dans la BDD.
     * Statut par défaut : EN_ATTENTE
     */
    public function create($id_client, $id_voyage, $id_commande, $numero_siege) {
        $query = "INSERT INTO " . $this->table_name . "
                  SET 
                      id_client = :id_client,
                      id_voyage = :id_voyage,
                      id_commande = :id_commande, -- NOUVEAU
                      numero_siege = :numero_siege,
                      statut = 'EN_ATTENTE', -- Statut hérité de la commande
                      type_reservation = 'siege'";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':id_client', $id_client);
        $stmt->bindParam(':id_voyage', $id_voyage);
        $stmt->bindParam(':id_commande', $id_commande); // NOUVEAU
        $stmt->bindParam(':numero_siege', $numero_siege);

        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }
  public function updateStatus($id_reservation, $statut) {
        $query = "UPDATE " . $this->table_name . "
                  SET statut = :statut
                  WHERE id_reservation = :id_reservation";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':statut', $statut);
        $stmt->bindParam(':id_reservation', $id_reservation);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    /**
     * Récupère les détails d'une réservation pour la page de paiement.
     * Vérifie que la réservation appartient bien au client.
     */
    public function getDetailsForPayment($id_client, $id_reservation) {
        $query = "SELECT
                    r.id_reservation, r.statut,
                    v.ville_depart, v.ville_arrivee, v.prix
                FROM
                    " . $this->table_name . " r
                JOIN
                    voyages v ON r.id_voyage = v.id_voyage
                WHERE
                    r.id_reservation = :id_reservation
                    AND r.id_client = :id_client
                LIMIT 0,1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_reservation', $id_reservation);
        $stmt->bindParam(':id_client', $id_client);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
public function getTicketDetails($id_client, $id_reservation) {
        $query = "SELECT
                    -- Réservation
                    r.id_reservation,
                    r.numero_siege,
                    r.statut AS statut_reservation,
                    r.date_reservation,
                    
                    -- Client
                    c.nom AS client_nom,
                    c.prenom AS client_prenom,
                    c.telephone AS client_telephone,
                    c.email AS client_email,
                    c.sexe AS client_sexe, -- <-- AJOUTEZ CETTE LIGNE
                    -- Voyage
                    v.ville_depart,
                    v.ville_arrivee,
                    v.date_depart,
                    v.prix,
                    
                    -- Véhicule
                    veh.marque AS vehicule_marque,
                    veh.immatriculation,
                    
                    -- Chauffeur
                    CONCAT(e.prenom, ' ', e.nom) as chauffeur_nom
                FROM
                    " . $this->table_name . " r
                JOIN
                    clients c ON r.id_client = c.id_client
                JOIN
                    voyages v ON r.id_voyage = v.id_voyage
                JOIN
                    vehicules veh ON v.id_vehicule = veh.id_vehicule
                JOIN
                    chauffeurs ch ON v.id_chauffeur = ch.id_chauffeur
                JOIN
                    employes e ON ch.id_chauffeur = e.id_employe
                WHERE
                    r.id_reservation = :id_reservation
                    AND r.id_client = :id_client
                LIMIT 0,1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_reservation', $id_reservation);
        $stmt->bindParam(':id_client', $id_client);
        $stmt->execute();

        // On retourne la ligne de résultat unique
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateStatusByCommandeId($id_commande, $statut) {
        $query = "UPDATE " . $this->table_name . "
                  SET statut = :statut
                  WHERE id_commande = :id_commande";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':statut', $statut);
        $stmt->bindParam(':id_commande', $id_commande);

        return $stmt->execute();
    }
    /**
     * Crée une nouvelle réservation dans la BDD.
     * Le statut par défaut est 'EN_ATTENTE' (défini dans votre SQL).
     */
   public function getManifestByVoyageId($id_voyage) {
        $query = "SELECT
                    r.numero_siege,
                    cm.statut AS statut_commande,
                    cm.montant_total AS montant_paye, -- <-- CORRECTION: Ajout de l'alias 'montant_paye'
                    cl.nom AS client_nom,
                    cl.prenom AS client_prenom,
                    cl.telephone AS client_telephone
                FROM
                    " . $this->table_name . " r
                JOIN
                    commandes cm ON r.id_commande = cm.id_commande
                JOIN
                    clients cl ON r.id_client = cl.id_client
                WHERE
                    r.id_voyage = :id_voyage
                ORDER BY
                    r.numero_siege ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_voyage', $id_voyage);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    // (Nous ajouterons les méthodes create() et getReservedSeats() plus tard)
}