<?php
// Fichier: app/models/CompteEmploye.php

namespace App\Models;

use PDO;

class CompteEmploye {
    private $conn;
    private $table_name = "comptes_employes";

    public function __construct(PDO $db) {
        $this->conn = $db;
    }

    /**
     * Tente de connecter un employé.
     */
    public function login($email, $password) {
        
        $query = "SELECT
                    ce.id_compte, ce.id_employe, ce.est_super_admin,
                    ce.mot_de_passe, -- <-- CETTE LIGNE A ÉTÉ AJOUTÉE
                    e.nom, e.prenom, e.poste
                FROM
                    " . $this->table_name . " ce
                JOIN
                    employes e ON ce.id_employe = e.id_employe
                WHERE
                    ce.email = :email
                LIMIT 0,1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        
        if ($stmt->rowCount() == 1) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Le code de débogage a été supprimé
            
            // !! ATTENTION: Vérification en clair
            // Cette vérification va maintenant fonctionner
            if ($password === $row['mot_de_passe']) { 
                // Le mot de passe est correct
                return $row; // Retourne toutes les infos de l'employé
            }
        }
        
        return false; // Échec
    }
}