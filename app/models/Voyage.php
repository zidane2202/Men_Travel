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
     * On recherche par ville et date (le jour exact).
     */
    public function search($ville_depart, $ville_arrivee, $date) {
        
        $query = "SELECT
                    v.id_voyage,
                    v.ville_depart,
                    v.ville_arrivee,
                    v.date_depart,
                    v.prix,
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
                    v.ville_depart = :ville_depart
                    AND v.ville_arrivee = :ville_arrivee
                    AND DATE(v.date_depart) = :date_depart
                    AND v.statut = 'programmé'
                ORDER BY
                    v.date_depart ASC";

        $stmt = $this->conn->prepare($query);

        // Nettoyage des données
        $ville_depart = htmlspecialchars(strip_tags($ville_depart));
        $ville_arrivee = htmlspecialchars(strip_tags($ville_arrivee));
        $date = htmlspecialchars(strip_tags($date));

        // Liaison des paramètres
        $stmt->bindParam(':ville_depart', $ville_depart);
        $stmt->bindParam(':ville_arrivee', $ville_arrivee);
        $stmt->bindParam(':date_depart', $date);

        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Trouve un voyage spécifique par son ID
     * avec les détails du véhicule (nombre de places).
     */
    public function findById($id_voyage) {
        $query = "SELECT
                    v.id_voyage, v.ville_depart, v.ville_arrivee, v.date_depart, v.prix,
                    veh.nombre_places
                FROM
                    " . $this->table_name . " v
                JOIN
                    vehicules veh ON v.id_vehicule = veh.id_vehicule
                WHERE
                    v.id_voyage = :id_voyage
                    AND v.statut = 'programmé'
                LIMIT 0,1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_voyage', $id_voyage);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC); // Retourne un seul voyage
    }
}