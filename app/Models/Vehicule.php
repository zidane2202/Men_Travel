<?php
// Fichier: app/models/Vehicule.php

namespace App\Models;

use PDO;

class Vehicule {
    private $conn;
    private $table_name = "vehicules";

    public function __construct(PDO $db) {
        $this->conn = $db;
    }

    /**
     * Récupère TOUS les véhicules, même inactifs (pour l'admin).
     */
    public function findAll() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY statut DESC, marque ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Récupère les véhicules ACTIFS (pour le formulaire de création de voyage).
     */
    public function findAllActive() {
        $query = "SELECT id_vehicule, marque, immatriculation, nombre_places
                FROM " . $this->table_name . "
                WHERE statut IN ('actif', 'en maintenance')
                ORDER BY marque ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Trouve un véhicule par son ID.
     */
    public function findById($id_vehicule) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id_vehicule = :id_vehicule";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_vehicule', $id_vehicule);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Crée un nouveau véhicule.
     */
    public function create($type, $marque, $immatriculation, $nombre_places) {
        $query = "INSERT INTO " . $this->table_name . "
                  SET type=:type, marque=:marque, immatriculation=:immatriculation, 
                      nombre_places=:nombre_places, statut='actif'"; // Statut par défaut
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':type', $type);
        $stmt->bindParam(':marque', $marque);
        $stmt->bindParam(':immatriculation', $immatriculation);
        $stmt->bindParam(':nombre_places', $nombre_places);

        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }

    /**
     * Met à jour un véhicule existant.
     */
    public function update($id_vehicule, $type, $marque, $immatriculation, $nombre_places, $statut) {
        $query = "UPDATE " . $this->table_name . "
                  SET type=:type, marque=:marque, immatriculation=:immatriculation, 
                      nombre_places=:nombre_places, statut=:statut
                  WHERE id_vehicule = :id_vehicule";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':type', $type);
        $stmt->bindParam(':marque', $marque);
        $stmt->bindParam(':immatriculation', $immatriculation);
        $stmt->bindParam(':nombre_places', $nombre_places);
        $stmt->bindParam(':statut', $statut);
        $stmt->bindParam(':id_vehicule', $id_vehicule);

        return $stmt->execute();
    }

    /**
     * Supprime Logiquement un véhicule (hors service).
     */
    public function delete($id_vehicule) {
        $query = "UPDATE " . $this->table_name . " 
                  SET statut = 'hors service'
                  WHERE id_vehicule = :id_vehicule";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_vehicule', $id_vehicule);

        return $stmt->execute();
    }
}