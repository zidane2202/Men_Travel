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
        // NOTE: Les requêtes sont combinées pour minimiser les appels PDO.

        $data = [];

        // 1. STATISTIQUES FINANCIÈRES CLÉS
        $financeQuery = "SELECT
            SUM(CASE WHEN statut = 'PAYEE' THEN montant_total ELSE 0 END) AS total_revenu_paye,
            SUM(CASE WHEN statut = 'EN_ATTENTE' THEN montant_total ELSE 0 END) AS total_revenu_attente,
            COUNT(id_commande) AS total_commandes
            FROM commandes";
        $data['finance'] = $this->conn->query($financeQuery)->fetch(PDO::FETCH_ASSOC);

        // 2. STATISTIQUES VOYAGES ET FLOTTE
        $tripFleetQuery = "SELECT
            (SELECT COUNT(id_voyage) FROM voyages WHERE statut = 'programmé' AND is_deleted = 0) AS voyages_actifs,
            (SELECT COUNT(id_employe) FROM employes WHERE poste = 'Chauffeur' AND is_active = 1) AS chauffeurs_actifs,
            (SELECT COUNT(id_vehicule) FROM vehicules WHERE statut = 'actif') AS vehicules_actifs,
            (SELECT COUNT(id_client) FROM clients) AS total_clients_inscrits";
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