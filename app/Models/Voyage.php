<?php
// Fichier: app/models/Voyage.php

namespace App\Models;

use PDO;

class Voyage {
    private $conn;
    private $table_name = "voyages";

    public function __construct(PDO $db) {
        $this->conn = $db;
    }

    /**
     * Recherche dynamique des voyages disponibles. (CLIENT SIDE)
     * Applique les filtres (ville/date) seulement s'ils sont fournis.
     */
    public function search($ville_depart, $ville_arrivee, $date) {
        
        $query = "SELECT
                    v.id_voyage, v.ville_depart, v.ville_arrivee, v.date_depart, v.prix, v.statut,
                    veh.marque, veh.nombre_places,
                    CONCAT(e.prenom, ' ', e.nom) as chauffeur_nom
                FROM
                    " . $this->table_name . " v
                JOIN
                    vehicules veh ON v.id_vehicule = veh.id_vehicule
                JOIN
                    chauffeurs c ON v.id_chauffeur = c.id_chauffeur
                JOIN
                    employes e ON c.id_chauffeur = e.id_employe
                WHERE
                    v.statut = 'programmé' 
                    AND v.is_deleted = 0
                    AND v.date_depart >= NOW()"; // <-- Exclut les voyages passés

        $params = [];
        $where_clauses = ""; 

        if (!empty($ville_depart)) {
            $where_clauses .= " AND v.ville_depart = :ville_depart";
            $params[':ville_depart'] = htmlspecialchars(strip_tags($ville_depart));
        }

        if (!empty($ville_arrivee)) {
            $where_clauses .= " AND v.ville_arrivee = :ville_arrivee";
            $params[':ville_arrivee'] = htmlspecialchars(strip_tags($ville_arrivee));
        }

        if (!empty($date)) {
            // Si la date est spécifiée, on ne la recherche que pour le jour donné
            $where_clauses .= " AND DATE(v.date_depart) = :date_depart";
            $params[':date_depart'] = htmlspecialchars(strip_tags($date));
        }
        // Note: Si la date est vide, le filtre principal (v.date_depart >= NOW()) est utilisé.

        $query .= $where_clauses . " ORDER BY v.date_depart ASC";

        $stmt = $this->conn->prepare($query);

        foreach ($params as $key => &$value) {
            $stmt->bindParam($key, $value);
        }

        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Récupère TOUS les voyages programmés (actifs et futurs).
     */
    public function getAllProgrammed() {
        
        $query = "SELECT
                    v.id_voyage, v.ville_depart, v.ville_arrivee, v.date_depart, v.prix,
                    v.statut, veh.marque, veh.nombre_places, CONCAT(e.prenom, ' ', e.nom) as chauffeur_nom
                FROM
                    " . $this->table_name . " v
                JOIN
                    vehicules veh ON v.id_vehicule = veh.id_vehicule
                JOIN
                    chauffeurs c ON v.id_chauffeur = c.id_chauffeur
                JOIN
                    employes e ON c.id_chauffeur = e.id_employe
                WHERE
                    v.statut = 'programmé'
                    AND v.is_deleted = 0
                    AND v.date_depart >= NOW() -- <-- CONDITION CLÉ
                ORDER BY
                    v.date_depart ASC"; 

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Trouve un voyage spécifique par son ID (Pour l'édition et l'affichage des sièges).
     */
   public function findById($id_voyage) {
        $query = "SELECT
                    v.id_voyage, 
                    v.ville_depart, 
                    v.ville_arrivee, 
                    v.date_depart, 
                    v.prix,
                    v.statut, 
                    v.id_vehicule, 
                    v.id_chauffeur, 
                    veh.nombre_places
                FROM
                    " . $this->table_name . " v
                JOIN
                    vehicules veh ON v.id_vehicule = veh.id_vehicule
                WHERE
                    v.id_voyage = :id_voyage
                LIMIT 0,1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_voyage', $id_voyage);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC); 
    }

    /**
     * Crée un nouveau voyage (pour l'administration)
     */
    public function create($ville_depart, $ville_arrivee, $date_depart, $id_vehicule, $id_chauffeur, $prix) {
        $query = "INSERT INTO " . $this->table_name . "
                  SET 
                      ville_depart = :ville_depart,
                      ville_arrivee = :ville_arrivee,
                      date_depart = :date_depart,
                      id_vehicule = :id_vehicule,
                      id_chauffeur = :id_chauffeur,
                      prix = :prix,
                      statut = 'programmé'";
        
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':ville_depart', $ville_depart);
        $stmt->bindParam(':ville_arrivee', $ville_arrivee);
        $stmt->bindParam(':date_depart', $date_depart);
        $stmt->bindParam(':id_vehicule', $id_vehicule);
        $stmt->bindParam(':id_chauffeur', $id_chauffeur);
        $stmt->bindParam(':prix', $prix);

        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }
    
    /**
     * Récupère le nombre total de places pour un voyage donné.
     */
    public function getTotalSeatsByVoyageId($id_voyage) {
        $query = "SELECT
                    veh.nombre_places
                FROM
                    " . $this->table_name . " v
                JOIN
                    vehicules veh ON v.id_vehicule = veh.id_vehicule
                WHERE
                    v.id_voyage = :id_voyage";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_voyage', $id_voyage);
        $stmt->execute();
        
        return $stmt->fetchColumn(); 
    }
    
    /**
     * Met à jour les informations d'un voyage existant.
     */
    public function update($id_voyage, $ville_depart, $ville_arrivee, $date_depart, $id_vehicule, $id_chauffeur, $prix, $statut) {
        $query = "UPDATE " . $this->table_name . "
                  SET 
                      ville_depart = :ville_depart,
                      ville_arrivee = :ville_arrivee,
                      date_depart = :date_depart,
                      id_vehicule = :id_vehicule,
                      id_chauffeur = :id_chauffeur,
                      prix = :prix,
                      statut = :statut
                  WHERE 
                      id_voyage = :id_voyage";
        
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':ville_depart', $ville_depart);
        $stmt->bindParam(':ville_arrivee', $ville_arrivee);
        $stmt->bindParam(':date_depart', $date_depart);
        $stmt->bindParam(':id_vehicule', $id_vehicule);
        $stmt->bindParam(':id_chauffeur', $id_chauffeur);
        $stmt->bindParam(':prix', $prix);
        $stmt->bindParam(':statut', $statut);
        $stmt->bindParam(':id_voyage', $id_voyage);

        return $stmt->execute();
    }

    /**
     * Supprime logiquement un voyage par son ID (Soft Delete).
     */
   public function archive($id_voyage) {
        $query = "UPDATE " . $this->table_name . " 
                  SET is_deleted = 1
                  WHERE id_voyage = :id_voyage";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_voyage', $id_voyage);

        return $stmt->execute();
    }
}