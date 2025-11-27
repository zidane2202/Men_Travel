<?php 
// $voyageDetails, $manifeste, $totalSeats, $reservedSeatsCount, $remainingSeats sont passés par le contrôleur
$admin_nom = $_SESSION['admin_nom'] ?? 'Admin';
$pageTitle = 'Manifeste: ' . ($voyageDetails['ville_depart'] ?? 'N/A') . ' → ' . ($voyageDetails['ville_arrivee'] ?? 'N/A');
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
        .details-box { background: white; padding: 1.5rem; border-radius: 8px; margin-bottom: 1.5rem; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }

        /* Style du tableau */
        .manifest-table { width: 100%; border-collapse: collapse; background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
        .manifest-table th, .manifest-table td { padding: 12px 15px; text-align: left; border-bottom: 1px solid #f0f0f0; font-size: 0.9rem; }
        .manifest-table th { background-color: #e9ecef; font-weight: 600; color: #495057; text-transform: uppercase; }
        .manifest-table tr:nth-child(even) { background-color: #fafafa; }
        
        /* Style des pilules de statut */
        .status-pill { padding: 0.3rem 0.7rem; border-radius: 20px; font-size: 0.75rem; font-weight: 700; display: inline-block; }
        .status-PAYEE { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .status-EN_ATTENTE { background-color: #fff3cd; color: #856404; border: 1px solid #ffeeba; }
        .btn-back { background-color: #6c757d; color: white; padding: 0.7rem 1.5rem; border-radius: 8px; text-decoration: none; font-weight: 600; }
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
                <h1>Manifeste Passagers</h1>
                <a href="/admin/voyages" class="btn-back">← Retour à la liste</a>
            </header>

            <?php if (!empty($voyageDetails)): ?>
                <div class="details-box">
                    <h2><?= htmlspecialchars($voyageDetails['ville_depart']) ?> → <?= htmlspecialchars($voyageDetails['ville_arrivee']) ?></h2>
                    <p>Départ prévu : <strong><?= date('d M Y à H:i', strtotime($voyageDetails['date_depart'])) ?></strong></p>
                    <p>Prix du ticket unitaire : <strong><?= number_format($voyageDetails['prix'], 0, ',', ' ') ?> XAF</strong></p>
                    
                    <p>
                        Places totales : <strong><?= $totalSeats ?></strong> | 
                        Places réservées : <strong><?= $reservedSeatsCount ?></strong> |
                        Places restantes : <strong style="color: <?= ($remainingSeats > 5) ? '#28a745' : '#dc3545' ?>;">
                            <?= $remainingSeats ?>
                        </strong>
                    </p>
                </div>
            <?php endif; ?>

            <table class="manifest-table">
                <thead>
                    <tr>
                        <th>Siège</th>
                        <th>Passager</th>
                        <th>Téléphone</th>
                        <th>Statut Paiement</th>
                        <th>Montant Payé (Total Commande)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($manifeste)): ?>
                        <tr><td colspan="5" style="text-align: center;">Aucun siège réservé pour ce voyage.</td></tr>
                    <?php else: ?>
                        <?php foreach ($manifeste as $passager): ?>
                            <tr>
                                <td><span style="font-size: 1.1rem; font-weight: 700; color: #007bff;"><?= $passager['numero_siege'] ?></span></td>
                                <td><?= htmlspecialchars($passager['client_prenom'] . ' ' . $passager['client_nom']) ?></td>
                                <td><?= htmlspecialchars($passager['client_telephone']) ?></td>
                                <td>
                                    <?php 
                                        $statut = $passager['statut_commande'];
                                        $classe = 'status-' . str_replace('_', '', strtolower($statut));
                                    ?>
                                    <span class="status-pill <?= $classe ?>">
                                        <?= htmlspecialchars(ucfirst($statut)) ?>
                                    </span>
                                </td>
                                <td>
                                    <?= number_format($passager['montant_paye'] ?? 0, 0, ',', ' ') ?> XAF
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