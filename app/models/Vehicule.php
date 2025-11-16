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
     * Récupère les véhicules actifs (disponibles pour de nouveaux voyages).
     */
    public function findAllActive() {
        $query = "SELECT
                    id_vehicule,
                    marque,
                    immatriculation,
                    nombre_places
                FROM
                    " . $this->table_name . "
                WHERE
                    statut = 'actif'
                ORDER BY marque ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}