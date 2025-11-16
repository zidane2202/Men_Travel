<?php
// Fichier: app/models/Employe.php

namespace App\Models;

use PDO;

class Employe {
    private $conn;
    private $table_name = "employes";

    public function __construct(PDO $db) {
        $this->conn = $db;
    }

    /**
     * Récupère TOUS les employés ACTIFS pour la liste admin. (FIXED)
     */
    public function findAll() {
        $query = "SELECT 
                    e.id_employe, e.nom, e.prenom, e.email, e.telephone, e.poste, e.date_embauche
                FROM " . $this->table_name . " e
                WHERE e.is_active = 1  -- <-- Condition ajoutée
                ORDER BY e.poste ASC, e.nom ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Trouve un employé par son ID
     */
    public function findById($id_employe) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id_employe = :id_employe";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_employe', $id_employe);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Crée un nouvel employé (base pour tous les types).
     */
    public function create($nom, $prenom, $date_naissance, $email, $telephone, $poste, $date_embauche) {
        // La colonne is_active est gérée par le DEFAULT 1 dans la BDD
        $query = "INSERT INTO " . $this->table_name . "
                  SET nom=:nom, prenom=:prenom, date_naissance=:date_naissance, email=:email, 
                      telephone=:telephone, poste=:poste, date_embauche=:date_embauche";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':nom', $nom);
        $stmt->bindParam(':prenom', $prenom);
        $stmt->bindParam(':date_naissance', $date_naissance);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':telephone', $telephone);
        $stmt->bindParam(':poste', $poste);
        $stmt->bindParam(':date_embauche', $date_embauche);

        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }
    
    /**
     * Met à jour les informations principales d'un employé.
     */
    public function update($id_employe, $nom, $prenom, $date_naissance, $email, $telephone, $poste, $date_embauche) {
        $query = "UPDATE " . $this->table_name . "
                  SET nom=:nom, prenom=:prenom, date_naissance=:date_naissance, email=:email, 
                      telephone=:telephone, poste=:poste, date_embauche=:date_embauche
                  WHERE id_employe = :id_employe";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':nom', $nom);
        $stmt->bindParam(':prenom', $prenom);
        $stmt->bindParam(':date_naissance', $date_naissance);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':telephone', $telephone);
        $stmt->bindParam(':poste', $poste);
        $stmt->bindParam(':date_embauche', $date_embauche);
        $stmt->bindParam(':id_employe', $id_employe);

        return $stmt->execute();
    }
    
    /**
     * Récupère uniquement la liste des chauffeurs actifs (pour les voyages). (FIXED)
     */
    public function findChauffeurs() {
        $query = "SELECT
                    e.id_employe, e.nom, e.prenom
                FROM
                    " . $this->table_name . " e
                JOIN
                    chauffeurs ch ON e.id_employe = ch.id_chauffeur
                WHERE
                    e.poste = 'Chauffeur'
                    AND e.is_active = 1  -- <-- Condition ajoutée
                ORDER BY e.nom ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Archive/Désactive un employé (Fin de contrat / Soft Delete).
     */
    public function archive($id_employe) {
        $query = "UPDATE " . $this->table_name . " 
                  SET is_active = 0
                  WHERE id_employe = :id_employe";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_employe', $id_employe);

        return $stmt->execute();
    }
}