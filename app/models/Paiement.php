<?php
// Fichier: app/models/Paiement.php

namespace App\Models;

use PDO;

class Paiement {
    private $conn;
    private $table_name = "paiements";

    public function __construct(PDO $db) {
        $this->conn = $db;
    }

    /**
     * Crée un nouvel enregistrement de paiement.
     */
    public function create($id_reservation, $montant, $mode, $reference, $statut) {
        $query = "INSERT INTO " . $this->table_name . "
                  SET
                      id_reservation = :id_reservation,
                      montant = :montant,
                      mode = :mode,
                      reference_paiement = :reference,
                      statut = :statut";
        
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':id_reservation', $id_reservation);
        $stmt->bindParam(':montant', $montant);
        $stmt->bindParam(':mode', $mode);
        $stmt->bindParam(':reference', $reference);
        $stmt->bindParam(':statut', $statut);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }
}