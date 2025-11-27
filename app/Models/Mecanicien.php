<?php
// Fichier: app/models/Mecanicien.php

namespace App\Models;

use PDO;

class Mecanicien {
    private $conn;
    private $table_name = "mecaniciens";

    public function __construct(PDO $db) {
        $this->conn = $db;
    }

    /**
     * Crée l'enregistrement de spécialisation pour un employé.
     */
    public function create($id_mecanicien, $specialite) {
        $query = "INSERT INTO " . $this->table_name . "
                  SET id_mecanicien=:id_mecanicien, specialite=:specialite";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':id_mecanicien', $id_mecanicien);
        $stmt->bindParam(':specialite', $specialite);

        return $stmt->execute();
    }
    public function findById($id_mecanicien) {
        $query = "SELECT specialite FROM " . $this->table_name . " WHERE id_mecanicien = :id_mecanicien";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_mecanicien', $id_mecanicien);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function updateOrCreate($id_mecanicien, $specialite) {
        $query = "INSERT INTO " . $this->table_name . " (id_mecanicien, specialite)
                  VALUES (:id_mecanicien, :specialite)
                  ON DUPLICATE KEY UPDATE specialite = :specialite";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_mecanicien', $id_mecanicien);
        $stmt->bindParam(':specialite', $specialite);

        return $stmt->execute();
    }
}