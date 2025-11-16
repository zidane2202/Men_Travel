<?php
// $stats est maintenant disponible dans cette vue
$pageTitle = 'Tableau de Bord Admin';
$admin_nom = $_SESSION['admin_nom'] ?? 'Admin';

// Extraction et formatage des variables statistiques pour la clarté
$revenu_paye = number_format($stats['finance']['total_revenu_paye'] ?? 0, 0, ',', ' ');
$revenu_attente = number_format($stats['finance']['total_revenu_attente'] ?? 0, 0, ',', ' ');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?> - Men Travel</title>
    
    <style>
        /* (Styles généraux non répétés ici pour économiser de l'espace) */
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; background-color: #f4f7f6; color: #333; margin: 0; padding: 0; }
        .dashboard-wrapper { display: flex; min-height: 100vh; }
        
        /* Sidebar et Menu sont inchangés */
        .sidebar { width: 260px; background-color: #343a40; color: #ffffff; display: flex; flex-direction: column; position: fixed; height: 100%; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
        .sidebar-header { padding: 1.5rem; text-align: center; border-bottom: 1px solid #495057; }
        .sidebar-header h2 { margin: 0; font-size: 1.5rem; color: #d9534f; } 
        .sidebar-nav { flex-grow: 1; padding: 1rem 0; }
        .sidebar-nav a { display: flex; align-items: center; gap: 0.75rem; color: #dfe9f3; text-decoration: none; padding: 0.9rem 1.5rem; font-size: 1.05rem; transition: all 0.3s; }
        .sidebar-nav a.active { background-color: #495057; color: #ffffff; font-weight: 600; border-left: 5px solid #d9534f; padding-left: calc(1.5rem - 5px); }
        .sidebar-nav a:hover:not(.active) { background-color: #495057; padding-left: 1.8rem; }
        .sidebar-footer { padding: 1.5rem; border-top: 1px solid #495057; }
        .logout-link { display: block; background-color: #d9534f; color: #fff; text-align: center; padding: 0.75rem; border-radius: 8px; text-decoration: none; font-weight: 600; }
        
        /* Contenu Principal */
        .main-content { flex-grow: 1; margin-left: 260px; padding: 2.5rem; animation: fadeIn 0.5s ease-out; }
        .main-header { margin-bottom: 2rem; }
        .main-header h1 { margin: 0; font-size: 2.2rem; font-weight: 700; color: #343a40; }
        .main-header p { margin: 0.5rem 0 0; font-size: 1.1rem; color: #555; }
        
        /* Cartes de STATS */
        .content-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.5rem;
        }
        .stats-card {
            background: #ffffff;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .stats-card .value {
            font-size: 2.5rem;
            font-weight: 700;
            margin: 0.5rem 0 0;
            line-height: 1.1;
        }
        .stats-card .label {
            font-size: 0.9rem;
            color: #6c757d;
            text-transform: uppercase;
        }
        .text-revenue { color: #28a745; } /* Vert */
        .text-pending { color: #ffc107; } /* Jaune */
        .text-danger { color: #d9534f; } /* Rouge */
        .text-info { color: #007bff; } /* Bleu */
        
        /* Liste des dernières réservations */
        .recent-orders-list {
            list-style: none;
            padding: 0;
        }
        .recent-orders-list li {
            padding: 10px 0;
            border-bottom: 1px dashed #eee;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .recent-orders-list li:last-child {
            border-bottom: none;
        }
        .order-amount {
            font-weight: 700;
            color: #28a745;
        }
    </style>
</head>
<body>

    <div class="dashboard-wrapper">
        
        <aside class="sidebar">
            <div class="sidebar-header">
                <h2>Men Travel</h2>
                <span style="font-size: 0.9rem;">Panneau d'Administration</span>
            </div>
            
            <nav class="sidebar-nav">
                <a href="/admin/dashboard" class="active">🏠 Tableau de bord</a>
                <a href="/admin/voyages">🚌 Gérer les Voyages</a>
                <a href="/admin/vehicules">🚗 Gérer les Véhicules</a>
                <a href="/admin/reservations">🎟️ Voir les Réservations</a>
                <a href="/admin/clients">👤 Gérer les Clients</a>
                <a href="/admin/employes">🛠️ Gérer le Personnel</a>
            </nav>
            
            <div class="sidebar-footer">
                <a href="/admin/logout" class="logout-link">Se déconnecter</a>
            </div>
        </aside>

        <main class="main-content">
            <header class="main-header">
                <h1>Bienvenue, <?= htmlspecialchars($admin_nom) ?> !</h1>
                <p>Aperçu des performances et des opérations.</p>
            </header>
            
            <div class="content-grid">
                
                <div class="stats-card">
                    <span class="label">Revenu Net Confirmé</span>
                    <div class="value text-revenue"><?= $revenu_paye ?> XAF</div>
                    <small style="color: #28a745;">Total payé par les clients</small>
                </div>

                <div class="stats-card">
                    <span class="label">Revenu en Attente</span>
                    <div class="value text-pending"><?= $revenu_attente ?> XAF</div>
                    <small style="color: #ffc107;">Commandes non finalisées</small>
                </div>
                
                <div class="stats-card">
                    <span class="label">Bus/Vans Actifs</span>
                    <div class="value text-info"><?= $stats['fleet']['vehicules_actifs'] ?? 0 ?></div>
                    <small style="color: #007bff;">Prêts à être assignés</small>
                </div>
                
                <div class="stats-card">
                    <span class="label">Voyages à Venir</span>
                    <div class="value text-info"><?= $stats['fleet']['voyages_actifs'] ?? 0 ?></div>
                    <small style="color: #007bff;">Total programmé</small>
                </div>
                
                <div class="stats-card">
                    <span class="label">Chauffeurs Disponibles</span>
                    <div class="value text-info"><?= $stats['fleet']['chauffeurs_actifs'] ?? 0 ?></div>
                    <small style="color: #007bff;">Personnel assignable</small>
                </div>

                <div class="stats-card">
                    <span class="label">Total Clients</span>
                    <div class="value text-info"><?= $stats['fleet']['total_clients_inscrits'] ?? 0 ?></div>
                    <small style="color: #007bff;">Base d'utilisateurs inscrits</small>
                </div>
                
            </div>
            
            <div class="content-grid" style="margin-top: 2.5rem;">
                
                <div class="stats-card" style="grid-column: 1 / 3;">
                    <h3>Dernières Commandes</h3>
                    <ul class="recent-orders-list">
                        <?php if (empty($stats['recent_orders'])): ?>
                            <li style="text-align: center; color: #777;">Aucune commande récente.</li>
                        <?php else: ?>
                            <?php foreach ($stats['recent_orders'] as $order): ?>
                                <li>
                                    <span>
                                        <?= htmlspecialchars($order['ville_depart']) ?> → <?= htmlspecialchars($order['ville_arrivee']) ?>
                                        <small style="color:#777; margin-left: 10px;">(<?= date('d/m H:i', strtotime($order['date_commande'])) ?>)</small>
                                    </span>
                                    <span class="order-amount"><?= number_format($order['montant_total'], 0, ',', ' ') ?> XAF</span>
                                </li>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </ul>
                    <a href="/admin/reservations" style="display: block; margin-top: 1rem; text-align: center; color: #d9534f; font-weight: 600;">Voir toutes les commandes →</a>
                </div>
                
            </div>
            
        </main>

    </div></body>
</html>