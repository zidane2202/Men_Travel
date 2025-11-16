<?php
// Fichier: app/models/Chauffeur.php

namespace App\Models;

use PDO;

class Chauffeur {
    private $conn;
    private $table_name = "chauffeurs";

    public function __construct(PDO $db) {
        $this->conn = $db;
    }

    /**
     * Crée l'enregistrement de spécialisation pour un employé.
     */
    public function create($id_chauffeur, $permis_numero) {
        $query = "INSERT INTO " . $this->table_name . "
                  SET id_chauffeur=:id_chauffeur, permis_numero=:permis_numero";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':id_chauffeur', $id_chauffeur);
        $stmt->bindParam(':permis_numero', $permis_numero);

        return $stmt->execute();
    }
}