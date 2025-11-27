<?php 
$admin_nom = $_SESSION['admin_nom'] ?? 'Admin';
$pageTitle = 'Ajouter un véhicule';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <style>
        /* (Styles de base et formulaire copiés du personnel/voyage) */
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
        .form-actions { grid-column: 1 / 3; text-align: right; margin-top: 1rem; }
        .btn-submit { padding: 0.85rem 1.5rem; border: none; border-radius: 5px; background-color: #28a745; color: white; font-weight: 700; cursor: pointer; }
        .btn-cancel { background-color: #6c757d; color: white; padding: 0.7rem 1.5rem; border-radius: 8px; text-decoration: none; font-weight: 600; }
        .error-message { background-color: #f8d7da; color: #721c24; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; }
    </style>
</head>
<body>

    <div class="dashboard-wrapper">
        <aside class="sidebar">
            <div class="sidebar-header"><h2>Men Travel</h2><span style="font-size: 0.9rem;">Panneau d'Administration</span></div>
            <nav class="sidebar-nav">
                <a href="/admin/dashboard">🏠 Tableau de bord</a>
                <a href="/admin/voyages">🚌 Gérer les Voyages</a>
                <a href="/admin/vehicules" class="active">🚗 Gérer les Véhicules</a>
                <a href="/admin/reservations">🎟️ Voir les Réservations</a>
                <a href="/admin/employes">🛠️ Gérer le Personnel</a>
            </nav>
            <div class="sidebar-footer"><a href="/admin/logout" class="logout-link">Se déconnecter</a></div>
        </aside>

        <main class="main-content">
            <header class="main-header">
                <h1>Ajouter un nouveau véhicule</h1>
                <a href="/admin/vehicules" class="btn-action btn-cancel" style="background-color: #6c757d;">← Retour</a>
            </header>
            
            <?php if (isset($_GET['error'])): ?>
                <div class="error-message"><?= htmlspecialchars($_GET['error']) ?></div>
            <?php endif; ?>

            <div class="form-card">
                <form action="/admin/vehicules/store" method="POST">
                    <div class="form-grid">

                        <div class="form-group">
                            <label for="type">Type :</label>
                            <select id="type" name="type" required>
                                <option value="Bus">Bus</option>
                                <option value="Van">Van</option>
                                <option value="Minibus">Minibus</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="marque">Marque :</label>
                            <input type="text" id="marque" name="marque" required placeholder="Ex: Mercedes-Benz">
                        </div>

                        <div class="form-group">
                            <label for="immatriculation">Immatriculation :</label>
                            <input type="text" id="immatriculation" name="immatriculation" required placeholder="Ex: CE-100-AA">
                        </div>
                        
                        <div class="form-group">
                            <label for="nombre_places">Nombre de Places :</label>
                            <input type="number" id="nombre_places" name="nombre_places" required min="1">
                        </div>
                        
                        <div class="form-actions">
                            <button type="submit" class="btn-submit">Enregistrer le véhicule</button>
                        </div>
                    </div>
                </form>
            </div>
        </main>
    </div>
</body>
</html>