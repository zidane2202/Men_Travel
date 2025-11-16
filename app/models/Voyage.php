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
     * Recherche des voyages disponibles.
     */
    /**
     * Recherche dynamique des voyages disponibles.
     * Les critères sont appliqués SEULEMENT s'ils sont fournis.
     */
    public function search($ville_depart, $ville_arrivee, $date) {
        
        // 1. Début de la requête SQL (sans la clause WHERE)
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
                    AND v.is_deleted = 0"; // Toujours filtrer les voyages actifs

        $params = [];
        $where_clauses = ""; // Initialiser la chaîne de clauses

        // 2. Construction dynamique de la clause WHERE
        
        if (!empty($ville_depart)) {
            $where_clauses .= " AND v.ville_depart = :ville_depart";
            $params[':ville_depart'] = htmlspecialchars(strip_tags($ville_depart));
        }

        if (!empty($ville_arrivee)) {
            $where_clauses .= " AND v.ville_arrivee = :ville_arrivee";
            $params[':ville_arrivee'] = htmlspecialchars(strip_tags($ville_arrivee));
        }

        if (!empty($date)) {
            // Important: DATE(v.date_depart) = :date_depart pour ignorer l'heure
            $where_clauses .= " AND DATE(v.date_depart) = :date_depart";
            $params[':date_depart'] = htmlspecialchars(strip_tags($date));
        } else {
            // Si aucune date n'est fournie, afficher seulement les voyages futurs
            $where_clauses .= " AND v.date_depart >= NOW()"; 
        }

        // 3. Finalisation de la requête
        $query .= $where_clauses . " ORDER BY v.date_depart ASC";

        // 4. Préparation et liaison
        $stmt = $this->conn->prepare($query);

        foreach ($params as $key => &$value) {
            $stmt->bindParam($key, $value);
        }

        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Trouve un voyage spécifique par son ID
     */
   public function findById($id_voyage) {
        $query = "SELECT
                    v.id_voyage, 
                    v.ville_depart, 
                    v.ville_arrivee, 
                    v.date_depart, 
                    v.prix,
                    v.statut,         -- <-- AJOUTÉ
                    v.id_vehicule,    -- <-- AJOUTÉ
                    v.id_chauffeur,   -- <-- AJOUTÉ
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
     * NOUVELLE MÉTHODE: Récupère TOUS les voyages programmés.
     */
    public function getAllProgrammed() {
        
        $query = "SELECT
                    v.id_voyage,
                    v.ville_depart,
                    v.ville_arrivee,
                    v.date_depart,
                    v.prix,
                    v.statut,    -- <-- CETTE LIGNE A ÉTÉ AJOUTÉE
                    veh.marque,
                    veh.nombre_places,
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
                    AND v.is_deleted = 0  -- <-- AJOUT DE LA CONDITION
                ORDER BY
                    v.date_depart ASC"; 

        // --- CORRECTION ICI ---
        // C'était $this.conn, maintenant c'est $this->conn
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
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
            // --- CORRECTION ICI ---
            return $this->conn->lastInsertId(); // Utilise ->conn
        }
        return false;
    }
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
        
        // Retourne le nombre de places (un entier)
        return $stmt->fetchColumn(); 
    }
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
     * Supprime un voyage par son ID.
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