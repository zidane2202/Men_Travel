<?php 
// $clientData (profile), $orders (history) sont passés par le contrôleur
$admin_nom = $_SESSION['admin_nom'] ?? 'Admin';
$pageTitle = "Détails de " . ($clientData['prenom'] ?? 'Client');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($pageTitle) ?> - Men Travel Admin</title>
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
        
        /* Conteneurs de détails */
        .profile-card, .order-table { background: white; padding: 2rem; border-radius: 8px; box-shadow: 0 8px 20px rgba(0,0,0,0.08); }
        .data-item { padding: 0.5rem 0; border-bottom: 1px dashed #eee; }
        .data-item strong { display: inline-block; width: 140px; color: #495057; font-weight: 600; }

        /* Style de la table d'historique */
        .history-section { margin-top: 3rem; }
        .history-table { width: 100%; border-collapse: collapse; margin-top: 1rem; }
        .history-table th, .history-table td { padding: 12px 15px; text-align: left; border-bottom: 1px solid #eee; font-size: 0.9rem; }
        .history-table th { background-color: #e9ecef; font-weight: 600; color: #495057; }
        .history-table tr:hover { background-color: #f8f9fa; }

        /* Statuts */
        .status-pill { padding: 0.3rem 0.7rem; border-radius: 20px; font-size: 0.75rem; font-weight: 700; display: inline-block; }
        .status-PAYEE { background-color: #d4edda; color: #155724; }
        .status-EN_ATTENTE { background-color: #fff3cd; color: #856404; }
        .status-ANNULEE { background-color: #f8d7da; color: #721c24; }
        .btn-back { background-color: #6c757d; color: white; padding: 0.7rem 1.5rem; border-radius: 8px; text-decoration: none; font-weight: 600; }
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
                <a href="/admin/reservations">🎟️ Voir les Réservations</a>
                <a href="/admin/clients" class="active">👤 Gérer les Clients</a>
                <a href="/admin/employes">🛠️ Gérer le Personnel</a>
            </nav>
            <div class="sidebar-footer"><a href="/admin/logout" class="logout-link">Se déconnecter</a></div>
        </aside>

        <main class="main-content">
            <header class="main-header">
                <h1>Dossier Client : <?= htmlspecialchars($clientData['prenom'] . ' ' . $clientData['nom'] ?? 'N/A') ?></h1>
                <a href="/admin/clients" class="btn-back">← Retour à la liste</a>
            </header>

            <div class="profile-card">
                <h2>Informations Personnelles</h2>
                <div class="profile-info">
                    <p class="data-item"><strong>ID Client:</strong> <span><?= $clientData['id_client'] ?></span></p>
                    <p class="data-item"><strong>Sexe:</strong> <span><?= htmlspecialchars(ucfirst(strtolower($clientData['sexe'] ?? 'N/A'))) ?></span></p>
                    <p class="data-item"><strong>Date de Naissance:</strong> <span><?= date('d M Y', strtotime($clientData['date_naissance'] ?? '')) ?></span></p>
                    <p class="data-item"><strong>Téléphone:</strong> <span><?= htmlspecialchars($clientData['telephone'] ?? 'N/A') ?></span></p>
                    <p class="data-item"><strong>Email:</strong> <span><?= htmlspecialchars($clientData['email'] ?? 'N/A') ?></span></p>
                    <p class="data-item"><strong>Adresse:</strong> <span><?= htmlspecialchars($clientData['adresse'] ?? 'N/A') ?></span></p>
                    <p class="data-item"><strong>Inscrit depuis:</strong> <span><?= date('d M Y', strtotime($clientData['date_inscription'] ?? '')) ?></span></p>
                </div>
            </div>

            <div class="order-table" style="margin-top: 2rem;">
                <h2>Historique des Commandes (<?= count($orders) ?>)</h2>
                
                <table class="history-table">
                    <thead>
                        <tr>
                            <th>Commande ID</th>
                            <th>Trajet</th>
                            <th>Date Voyage</th>
                            <th>Sièges</th>
                            <th>Montant Total</th>
                            <th>Statut</th>
                            <th>Date Commande</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($orders)): ?>
                            <tr><td colspan="7" style="text-align: center; color: #777;">Aucune commande trouvée pour ce client.</td></tr>
                        <?php else: ?>
                            <?php foreach ($orders as $order): 
                                $statut = $order['statut'];
                                $classe = 'status-' . str_replace('_', '', strtolower($statut));
                            ?>
                                <tr>
                                    <td>#<?= $order['id_commande'] ?></td>
                                    <td><?= htmlspecialchars($order['ville_depart']) ?> → <?= htmlspecialchars($order['ville_arrivee']) ?></td>
                                    <td><?= date('d M Y à H:i', strtotime($order['date_depart'])) ?></td>
                                    <td>N° <?= htmlspecialchars($order['sieges_list'] ?? 'N/A') ?></td>
                                    <td><strong><?= number_format($order['montant_total'], 0, ',', ' ') ?> XAF</strong></td>
                                    <td>
                                        <span class="status-pill <?= $classe ?>">
                                            <?= htmlspecialchars(ucfirst(strtolower($statut))) ?>
                                        </span>
                                    </td>
                                    <td><?= date('d M Y', strtotime($order['date_commande'])) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</body>
</html>