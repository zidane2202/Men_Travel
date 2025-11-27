<?php 
// $employes est passé par le contrôleur
$admin_nom = $_SESSION['admin_nom'] ?? 'Admin';
$pageTitle = 'Gestion du Personnel';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <style>
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
        .personnel-table { width: 100%; border-collapse: collapse; background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 8px 20px rgba(0,0,0,0.08); }
        .personnel-table th, .personnel-table td { padding: 14px 16px; text-align: left; border-bottom: 1px solid #f0f0f0; font-size: 0.9rem; }
        .personnel-table th { background-color: #e9ecef; font-weight: 600; color: #495057; text-transform: uppercase; }
        .personnel-table tr:hover { background-color: #f8f9fa; }
        .btn-action { padding: 0.5rem 0.8rem; border-radius: 5px; text-decoration: none; font-size: 0.8rem; font-weight: 600; transition: background-color 0.2s; }
        .btn-add { background-color: #28a745; color: white; }
        .btn-edit { background-color: #007bff; color: white; margin-right: 5px; }
        .status-pill { padding: 0.3rem 0.7rem; border-radius: 20px; font-size: 0.75rem; font-weight: 600; display: inline-block; background-color: #f0f0f0; }
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
                <a href="/admin/clients">👤 Gérer les Clients</a>
                <a href="/admin/employes" class="active">🛠️ Gérer le Personnel</a>
            </nav>
            <div class="sidebar-footer"><a href="/admin/logout" class="logout-link">Se déconnecter</a></div>
        </aside>

        <main class="main-content">
            <header class="main-header">
                <h1>Gestion du Personnel</h1>
                <a href="/admin/employes/create" class="btn-action btn-add">➕ Ajouter un employé</a>
            </header>
            
            <?php if (isset($_GET['success'])): ?>
                <div style="background-color: #d4edda; color: #155724; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem;">
                    <?= htmlspecialchars($_GET['success']) ?>
                </div>
            <?php endif; ?>

            <table class="personnel-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nom Complet</th>
                        <th>Poste</th>
                        <th>Contact</th>
                        <th>Email</th>
                        <th>Embauche</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($employes)): ?>
                        <tr><td colspan="7" style="text-align: center;">Aucun employé enregistré.</td></tr>
                    <?php else: ?>
                        <?php foreach ($employes as $e): ?>
                            <tr>
                                <td><?= $e['id_employe'] ?></td>
                                <td><?= htmlspecialchars($e['prenom'] . ' ' . $e['nom']) ?></td>
                                <td><span class="status-pill"><?= htmlspecialchars($e['poste']) ?></span></td>
                                <td><?= htmlspecialchars($e['telephone']) ?></td>
                                <td><?= htmlspecialchars($e['email']) ?></td>
                               <td><?= date('d M Y', strtotime($e['date_embauche'])) ?></td>
                                <td>
                                    <a href="/admin/employes/edit/<?= $e['id_employe'] ?>" class="btn-action btn-edit">Modifier</a>
                                    
                                    <form action="/admin/employes/archive" method="POST" style="display:inline-block;" onsubmit="return confirm('Êtes-vous sûr de vouloir ARCHIVER le contrat de cet employé?');">
                                        <input type="hidden" name="id_employe" value="<?= $e['id_employe'] ?>">
                                        <button type="submit" class="btn-action" style="background-color: #dc3545; color: white;">Archiver</button>
                                    </form>
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