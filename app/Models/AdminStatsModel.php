<?php
// Fichier: app/models/AdminStatsModel.php

namespace App\Models;

use PDO;

class AdminStatsModel {
    private $conn;

    public function __construct(PDO $db) {
        $this->conn = $db;
    }

    /**
     * Récupère toutes les statistiques essentielles pour le tableau de bord.
     */
    public function getDashboardStats() {
        $data = [];

        // 1. STATISTIQUES FINANCIÈRES CLÉS
        $financeQuery = "SELECT
            SUM(CASE WHEN statut = 'PAYEE' THEN montant_total ELSE 0 END) AS total_revenu_paye,
            SUM(CASE WHEN statut = 'EN_ATTENTE' THEN montant_total ELSE 0 END) AS total_revenu_attente,
            COUNT(id_commande) AS total_commandes
            FROM commandes";
        $data['finance'] = $this->conn->query($financeQuery)->fetch(PDO::FETCH_ASSOC);

        // 2. STATISTIQUES VOYAGES ET FLOTTE (AVEC OCCUPATION ET NOUVEAUX CLIENTS)
        $tripFleetQuery = "SELECT
            -- Flotte et Personnel
            (SELECT COUNT(id_voyage) FROM voyages WHERE statut = 'programmé' AND is_deleted = 0) AS voyages_actifs,
            (SELECT COUNT(id_employe) FROM employes WHERE poste = 'Chauffeur' AND is_active = 1) AS chauffeurs_actifs,
            (SELECT COUNT(id_vehicule) FROM vehicules WHERE statut = 'actif') AS vehicules_actifs,
            (SELECT COUNT(id_vehicule) FROM vehicules WHERE statut = 'en maintenance') AS vehicules_maintenance,
            (SELECT COUNT(id_client) FROM clients) AS total_clients_inscrits,
            
            -- Croissance des clients (30 derniers jours)
            (SELECT COUNT(id_client) FROM clients WHERE date_inscription >= DATE_SUB(NOW(), INTERVAL 30 DAY)) AS clients_nouveaux,

            -- Taux d'occupation (Calcul de la capacité totale vs sièges réservés)
            (SELECT SUM(veh.nombre_places) 
                FROM voyages v 
                JOIN vehicules veh ON v.id_vehicule = veh.id_vehicule 
                WHERE v.statut = 'programmé' AND v.is_deleted = 0) AS capacite_totale,
                
            (SELECT COUNT(id_reservation) FROM reservations r 
                JOIN commandes cmd ON r.id_commande = cmd.id_commande 
                JOIN voyages v ON cmd.id_voyage = v.id_voyage
                WHERE v.statut = 'programmé' AND v.is_deleted = 0 AND cmd.statut IN ('PAYEE', 'EN_ATTENTE')) AS total_reserves
            ";
        $data['fleet'] = $this->conn->query($tripFleetQuery)->fetch(PDO::FETCH_ASSOC);

        // 3. RÉSERVATIONS RÉCENTES (Pour un aperçu rapide)
        $recentOrdersQuery = "SELECT
            v.ville_depart, v.ville_arrivee, c.montant_total, c.date_commande
            FROM commandes c
            JOIN voyages v ON c.id_voyage = v.id_voyage
            ORDER BY c.date_commande DESC
            LIMIT 5";
        $data['recent_orders'] = $this->conn->query($recentOrdersQuery)->fetchAll(PDO::FETCH_ASSOC);

        return $data;
    }
}