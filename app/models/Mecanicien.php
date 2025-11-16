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
}