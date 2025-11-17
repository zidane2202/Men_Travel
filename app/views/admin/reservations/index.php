<?php 
// $commandes est passé par le contrôleur
$admin_nom = $_SESSION['admin_nom'] ?? 'Admin';
$pageTitle = "Vue d'ensemble des Réservations";
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <style>
        /* Styles de base pour l'administration */
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; background-color: #f4f7f6; color: #333; margin: 0; padding: 0; }
        .dashboard-wrapper { display: flex; min-height: 100vh; }
        .sidebar { width: 260px; background-color: #343a40; color: #ffffff; display: flex; flex-direction: column; position: fixed; height: 100%; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
        .sidebar-header { padding: 1.5rem; text-align: center; border-bottom: 1px solid #495057; }
        .sidebar-header h2 { margin: 0; font-size: 1.5rem; color: #d9534f; } 
        .sidebar-nav { flex-grow: 1; padding: 1rem 0; }
        .sidebar-nav a { display: flex; align-items: center; gap: 0.75rem; color: #dfe9f3; text-decoration: none; padding: 0.9rem 1.5rem; font-size: 1.05rem; transition: all 0.3s; }
        .sidebar-nav a.active { background-color: #495057; color: #ffffff; font-weight: 600; border-left: 5px solid #d9534f; padding-left: calc(1.5rem - 5px); }
        .sidebar-nav a:hover:not(.active) { background-color: #495057; padding-left: 1.8rem; }
        .sidebar-footer { padding: 1.5rem; border-top: 1px solid #495057; }
        .logout-link { display: block; background-color: #d9534f; color: #fff; text-align: center; padding: 0.75rem; border-radius: 8px; text-decoration: none; font-weight: 600; }
        .main-content { flex-grow: 1; margin-left: 260px; padding: 2.5rem; animation: fadeIn 0.5s ease-out; }
        .main-header { margin-bottom: 2rem; display: flex; justify-content: space-between; align-items: center; }
        .main-header h1 { margin: 0; font-size: 2.2rem; font-weight: 700; color: #343a40; }
        
        /* Style du tableau */
        .commande-table { width: 100%; border-collapse: collapse; background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 8px 20px rgba(0,0,0,0.08); border: none; }
        .commande-table th, .commande-table td { padding: 14px 16px; text-align: left; border-bottom: 1px solid #f0f0f0; font-size: 0.9rem; }
        .commande-table th { background-color: #e9ecef; font-weight: 600; color: #495057; text-transform: uppercase; }
        .commande-table tr:hover { background-color: #f8f9fa; }

        /* Pilules de statut */
        .status-pill { padding: 0.3rem 0.7rem; border-radius: 20px; font-size: 0.75rem; font-weight: 700; display: inline-block; }
        .status-PAYEE { background-color: #d4edda; color: #155724; border: 1px solid #28a745; }
        .status-EN_ATTENTE { background-color: #fff3cd; color: #856404; border: 1px solid #ffc107; }
        .status-ANNULEE { background-color: #f8d7da; color: #721c24; border: 1px solid #dc3545; }
        .btn-action { padding: 0.5rem 0.8rem; border-radius: 5px; text-decoration: none; font-size: 0.8rem; font-weight: 600; transition: all 0.2s; }
        .btn-detail { background-color: #007bff; color: white; }
    </style>
</head>
<body>

    <div class="dashboard-wrapper">
        <aside class="sidebar">
            <div class="sidebar-header"><h2>Men Travel</h2><span style="font-size: 0.9rem;">Panneau d'Administration</span></div>
            <nav class="sidebar-nav">
                <a href="/admin/dashboard">🏠 Tableau de bord</a>
                <a href="/admin/voyages">🚌 Gérer les Voyages</a>
                <a href="/admin/vehicules">🚗 Gérer les Véhicules</a>
                <a href="/admin/reservations" class="active">🎟️ Voir les Réservations</a>
                <a href="/admin/clients">👤 Gérer les Clients</a>
                <a href="/admin/employes">🛠️ Gérer le Personnel</a>
            </nav>
            <div class="sidebar-footer"><a href="/admin/logout" class="logout-link">Se déconnecter</a></div>
        </aside>

        <main class="main-content">
            <header class="main-header">
                <h1>Vue d'ensemble des Commandes</h1>
            </header>
            
            <table class="commande-table">
                <thead>
                    <tr>
                        <th>ID Commande</th>
                        <th>Trajet</th>
                        <th>Client</th>
                        <th>Sièges</th>
                        <th>Montant</th>
                        <th>Statut</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($commandes)): ?>
                        <tr><td colspan="7" style="text-align: center;">Aucune commande enregistrée.</td></tr>
                    <?php else: ?>
                        <?php foreach ($commandes as $cmd): 
                            $status_class = 'status-' . str_replace('_', '', strtoupper($cmd['statut']));
                        ?>
                            <tr>
                                <td>#<?= $cmd['id_commande'] ?></td>
                                <td><?= htmlspecialchars($cmd['ville_depart']) ?> → <?= htmlspecialchars($cmd['ville_arrivee']) ?></td>
                                <td><?= htmlspecialchars($cmd['client_prenom'] . ' ' . $cmd['client_nom']) ?></td>
                                <td><strong><?= htmlspecialchars($cmd['sieges_list']) ?></strong></td>
                                <td><strong><?= number_format($cmd['montant_total'], 0, ',', ' ') ?> XAF</strong></td>
                                <td>
                                    <span class="status-pill <?= $status_class ?>">
                                        <?= htmlspecialchars(ucfirst(strtolower(str_replace('_', ' ', $cmd['statut'])))) ?>
                                    </span>
                                </td>
                                <td><?= date('d M Y à H:i', strtotime($cmd['date_commande'])) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </main>
    </div>
</body>
</html>