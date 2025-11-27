<?php 
// $voyageData, $chauffeurs, et $vehicules sont passés par le contrôleur
$admin_nom = $_SESSION['admin_nom'] ?? 'Admin';
$pageTitle = 'Modifier Voyage N°' . ($voyageData['id_voyage'] ?? 'N/A');
// Convertir le DATETIME de la BDD au format HTML requis (YYYY-MM-DDTHH:MM)
$date_html = date('Y-m-d\TH:i', strtotime($voyageData['date_depart'] ?? 'now'));
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
        .main-content { flex-grow: 1; margin-left: 560px; padding: 2.5rem; animation: fadeIn 0.5s ease-out; }
        .main-header { margin-bottom: 2rem; display: flex; justify-content: space-between; align-items: center; }
        .main-header h1 { margin: 0; font-size: 2.2rem; font-weight: 700; color: #343a40; }
        
        /* Formulaire */
        .form-card { max-width: 600px; background: white; padding: 2rem; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; }
        .form-group { display: flex; flex-direction: column; }
        .form-group label { margin-bottom: 0.5rem; font-weight: 600; font-size: 0.9rem; color: #495057; }
        .form-group input, .form-group select { padding: 0.85rem; border: 1px solid #ddd; border-radius: 5px; }
        .form-actions { grid-column: 1 / 3; text-align: right; margin-top: 1rem; display: flex; justify-content: space-between; align-items: center;}
        .btn-submit { padding: 0.85rem 1.5rem; border: none; border-radius: 5px; background-color: #007bff; color: white; font-weight: 700; cursor: pointer; }
        .btn-cancel { background-color: #6c757d; color: white; padding: 0.7rem 1.5rem; border-radius: 8px; text-decoration: none; font-weight: 600; }
        .error-message { background-color: #f8d7da; color: #721c24; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; }
        .btn-delete { padding: 0.85rem 1.5rem; border: none; border-radius: 5px; background-color: #dc3545; color: white; font-weight: 700; cursor: pointer; }
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
                <h1>Modifier Voyage N°<?= $voyageData['id_voyage'] ?? 'N/A' ?></h1>
                <a href="/admin/voyages" class="btn-action btn-cancel" style="background-color: #6c757d;">← Retour</a>
            </header>
            
            <?php if (isset($_GET['error'])): ?>
                <div class="error-message"><?= htmlspecialchars($_GET['error']) ?></div>
            <?php endif; ?>

            <div class="form-card">
                
                <form action="/admin/voyages/update" method="POST">
                    <input type="hidden" name="id_voyage" value="<?= $voyageData['id_voyage'] ?? '' ?>">

                    <div class="form-grid">

                        <div class="form-group" style="grid-column: 1 / 3;">
                            <label for="statut">Statut du Voyage :</label>
                            <select id="statut" name="statut" required>
                                <?php 
                                    $statuses = ['programmé', 'en cours', 'terminé', 'annulé'];
                                    foreach ($statuses as $s):
                                ?>
                                    <option value="<?= $s ?>" <?= (($voyageData['statut'] ?? '') == $s) ? 'selected' : '' ?>>
                                        <?= ucfirst($s) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="ville_depart">Ville de Départ :</label>
                            <select id="ville_depart" name="ville_depart" required>
                                <?php 
                                    $villes = ['Yaoundé', 'Douala', 'Bafoussam']; // Liste à élargir
                                    foreach ($villes as $v):
                                ?>
                                    <option value="<?= $v ?>" <?= (($voyageData['ville_depart'] ?? '') == $v) ? 'selected' : '' ?>>
                                        <?= $v ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="ville_arrivee">Ville d'Arrivée :</label>
                            <select id="ville_arrivee" name="ville_arrivee" required>
                                <?php 
                                    $villes = ['Yaoundé', 'Douala', 'Bafoussam']; // Liste à élargir
                                    foreach ($villes as $v):
                                ?>
                                    <option value="<?= $v ?>" <?= (($voyageData['ville_arrivee'] ?? '') == $v) ? 'selected' : '' ?>>
                                        <?= $v ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="date_depart">Date et Heure de Départ :</label>
                            <input type="datetime-local" id="date_depart" name="date_depart" value="<?= $date_html ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="prix">Prix du Ticket (XAF) :</label>
                            <input type="number" id="prix" name="prix" step="100" value="<?= $voyageData['prix'] ?? '' ?>" required min="1000">
                        </div>

                        <div class="form-group">
                            <label for="id_vehicule">Véhicule (Capacité) :</label>
                            <select id="id_vehicule" name="id_vehicule" required>
                                <?php foreach ($vehicules as $v): ?>
                                    <option value="<?= $v['id_vehicule'] ?>" <?= (($voyageData['id_vehicule'] ?? '') == $v['id_vehicule']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($v['marque']) ?> (<?= $v['immatriculation'] ?> - <?= $v['nombre_places'] ?> places)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="id_chauffeur">Chauffeur Assigné :</label>
                            <select id="id_chauffeur" name="id_chauffeur" required>
                                <?php foreach ($chauffeurs as $c): ?>
                                    <option value="<?= $c['id_employe'] ?>" <?= (($voyageData['id_chauffeur'] ?? '') == $c['id_employe']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($c['prenom'] . ' ' . $c['nom']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn-submit btn-action" style="background-color: #007bff;">Enregistrer les modifications</button>
                        </div>
                    </div>
                </form>

                <form action="/admin/voyages/delete" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce voyage? Ceci annulera toutes les réservations liées.');">
                    <input type="hidden" name="id_voyage" value="<?= $voyageData['id_voyage'] ?? '' ?>">
                    <div style="margin-top: 2rem; text-align: left;">
<button type="submit" class="btn-delete btn-action">Archiver le voyage (Soft Delete)</button>                    </div>
                </form>
            </div>
        </main>
    </div>
</body>
</html>