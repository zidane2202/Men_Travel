<?php 
// $voyages est passé par le contrôleur
$admin_nom = $_SESSION['admin_nom'] ?? 'Admin';
$pageTitle = 'Gestion des Voyages';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <style>
        /* (Le CSS pour la Sidebar est inchangé, mais nous allons ajouter des styles pour la table) */
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
        
        /* --- NOUVEAUX STYLES DE TABLEAU (Plus Modernes) --- */
        .voyage-table { width: 100%; border-collapse: collapse; background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 8px 20px rgba(0,0,0,0.08); border: none; }
        .voyage-table th, .voyage-table td { padding: 14px 16px; text-align: left; border-bottom: 1px solid #f0f0f0; font-size: 0.9rem; }
        .voyage-table th { background-color: #e9ecef; font-weight: 600; color: #495057; text-transform: uppercase; }
        .voyage-table tr:hover { background-color: #f8f9fa; }

        /* Style pour les PILULES DE STATUT */
        .status-pill {
            padding: 0.3rem 0.7rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 700;
            white-space: nowrap;
            display: inline-block;
        }
        .status-programmé { background-color: #eafbea; color: #28a745; border: 1px solid #28a745; }
        .status-en_cours { background-color: #fff3cd; color: #ffc107; border: 1px solid #ffc107; }
        .status-terminé { background-color: #e0e0e0; color: #6c757d; border: 1px solid #6c757d; }
        .status-annulé { background-color: #f7e0e2; color: #dc3545; border: 1px solid #dc3545; }

        /* Style pour les boutons d'action */
        .btn-action { 
            padding: 0.5rem 0.8rem; 
            border-radius: 5px; 
            text-decoration: none; 
            font-size: 0.8rem; 
            font-weight: 600; 
            transition: all 0.2s; 
            display: inline-block;
            margin-bottom: 3px; /* Espacement entre les boutons */
        }
        .btn-edit { background-color: #007bff; color: white; } 
        .btn-edit:hover { background-color: #0056b3; }
        .btn-passagers { background-color: #28a745; color: white; }
        .btn-passagers:hover { background-color: #218838; }
        .btn-add { background-color: #28a745; color: white; }
        .btn-add:hover { background-color: #218838; }

        .trajet-info { font-weight: 600; color: #003366; }
    </style>
</head>
<body>

    <div class="dashboard-wrapper">
        <aside class="sidebar">
            <div class="sidebar-header"><h2>Men Travel</h2><span style="font-size: 0.9rem;">Panneau d'Administration</span></div>
            <nav class="sidebar-nav">
                <a href="/admin/dashboard">🏠 Tableau de bord</a>
                <a href="/admin/voyages" class="active">🚌 Gérer les Voyages</a>
                <a href="/admin/reservations">🎟️ Voir les Réservations</a>
                <a href="/admin/clients">👤 Gérer les Clients</a>
                <a href="/admin/employes">🛠️ Gérer le Personnel</a>
            </nav>
            <div class="sidebar-footer"><a href="/admin/logout" class="logout-link">Se déconnecter</a></div>
        </aside>

        <main class="main-content">
            <header class="main-header">
                <h1>Liste des Voyages</h1>
                <a href="/admin/voyages/create" class="btn-action btn-add">➕ Ajouter un voyage</a>
            </header>
            
            <?php if (isset($_GET['success'])): ?>
                <div style="background-color: #d4edda; color: #155724; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem;">
                    <?= htmlspecialchars($_GET['success']) ?>
                </div>
            <?php endif; ?>

            <table class="voyage-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Trajet</th>
                        <th>Départ</th>
                        <th>Bus</th>
                        <th>Chauffeur</th>
                        <th>Prix</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($voyages)): ?>
                        <tr><td colspan="8" style="text-align: center;">Aucun voyage programmé trouvé.</td></tr>
                    <?php else: ?>
                        <?php foreach ($voyages as $voyage): 
                            $statut_classe = 'status-' . strtolower(str_replace(' ', '_', $voyage['statut']));
                        ?>
                            <tr>
                                <td><?= $voyage['id_voyage'] ?></td>
                                <td>
                                    <span class="trajet-info">
                                        <?= htmlspecialchars($voyage['ville_depart']) ?> → <?= htmlspecialchars($voyage['ville_arrivee']) ?>
                                    </span>
                                </td>
                                <td>
                                    <?= date('d M Y', strtotime($voyage['date_depart'])) ?><br>
                                    <small><?= date('H:i', strtotime($voyage['date_depart'])) ?></small>
                                </td>
                                <td><?= htmlspecialchars($voyage['marque']) ?> (<?= $voyage['nombre_places'] ?> places)</td>
                                <td><?= htmlspecialchars($voyage['chauffeur_nom']) ?></td>
                                <td><?= number_format($voyage['prix'], 0, ',', ' ') ?> XAF</td>
                                <td>
                                    <span class="status-pill <?= $statut_classe ?>">
                                        <?= htmlspecialchars(ucfirst($voyage['statut'] ?? '')) ?>
                                    </span>
                                </td> 
                                <td>
                                    <a href="/admin/voyages/edit/<?= $voyage['id_voyage'] ?>" class="btn-action btn-edit">📝 Modifier</a>
                                    <a href="/admin/voyages/manifest/<?= $voyage['id_voyage'] ?>" class="btn-action btn-passagers">👥 Passagers</a>
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