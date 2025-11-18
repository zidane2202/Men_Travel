<?php 
// $clients est passé par le contrôleur
$admin_nom = $_SESSION['admin_nom'] ?? 'Admin';
$pageTitle = "Gestion des Clients";
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <style>
        /* Styles généraux pour l'administration */
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
        .client-table { width: 100%; border-collapse: collapse; background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 8px 20px rgba(0,0,0,0.08); border: none; }
        .client-table th, .client-table td { padding: 14px 16px; text-align: left; border-bottom: 1px solid #f0f0f0; font-size: 0.9rem; }
        .client-table th { background-color: #e9ecef; font-weight: 600; color: #495057; text-transform: uppercase; }
        .client-table tr:hover { background-color: #f8f9fa; }
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
                <a href="/admin/reservations">🎟️ Voir les Réservations</a>
                <a href="/admin/clients" class="active">👤 Gérer les Clients</a>
                <a href="/admin/employes">🛠️ Gérer le Personnel</a>
            </nav>
            <div class="sidebar-footer"><a href="/admin/logout" class="logout-link">Se déconnecter</a></div>
        </aside>

        <main class="main-content">
            <header class="main-header">
                <h1>Liste des Clients</h1>
            </header>
            
            <table class="client-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nom Complet</th>
                        <th>Email</th>
                        <th>Téléphone</th>
                        <th>Inscrit depuis</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($clients)): ?>
                        <tr><td colspan="6" style="text-align: center;">Aucun client enregistré.</td></tr>
                    <?php else: ?>
                        <?php foreach ($clients as $client): ?>
                            <tr>
                                <td><?= $client['id_client'] ?></td>
                                <td><?= htmlspecialchars($client['prenom'] . ' ' . $client['nom']) ?></td>
                                <td><?= htmlspecialchars($client['email']) ?></td>
                                <td><?= htmlspecialchars($client['telephone']) ?></td>

                                <td><?= date('d M Y', strtotime($client['date_inscription'])) ?></td>
                                <td>
                                    <a href="/admin/clients/view/<?= $client['id_client'] ?>" class="btn-action btn-detail">Détails</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </main>
    </div>
</body>
</html>