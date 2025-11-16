<?php
// $pageTitle, $searchParams, et $voyages sont passés par le contrôleur
$client_nom = $_SESSION['client_nom'] ?? 'Client';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?> - Men Travel</title>
    
    <style>
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; background-color: #f4f7f6; color: #333; margin: 0; padding: 0; }
        .dashboard-wrapper { display: flex; min-height: 100vh; }
        .sidebar { width: 260px; background-color: #003366; color: #ffffff; display: flex; flex-direction: column; position: fixed; height: 100%; }
        .sidebar-header { padding: 1.5rem; text-align: center; border-bottom: 1px solid #004a99; }
        .sidebar-header h2 { margin: 0; font-size: 1.5rem; }
        .sidebar-nav { flex-grow: 1; padding: 1rem 0; }
        .sidebar-nav a { display: flex; align-items: center; gap: 0.75rem; color: #dfe9f3; text-decoration: none; padding: 0.9rem 1.5rem; font-size: 1.05rem; transition: all 0.3s; }
        .sidebar-nav a.active { background-color: #004a99; color: #ffffff; font-weight: 600; border-left: 5px solid #007bff; padding-left: calc(1.5rem - 5px); }
        .sidebar-nav a:hover:not(.active) { background-color: #004080; padding-left: 1.8rem; }
        .sidebar-footer { padding: 1.5rem; border-top: 1px solid #004a99; }
        .logout-link { display: block; background-color: #d9534f; color: #fff; text-align: center; padding: 0.75rem; border-radius: 8px; text-decoration: none; font-weight: 600; }
        .main-content { flex-grow: 1; margin-left: 260px; padding: 2.5rem; animation: fadeIn 0.5s ease-out; }
        .main-header { margin-bottom: 2rem; }
        .main-header h1 { margin: 0; font-size: 2.2rem; font-weight: 700; color: #003366; }

        /* Style pour le formulaire de recherche */
        .search-form-container { background: #fff; padding: 2rem; border-radius: 12px; box-shadow: 0 8px 15px rgba(0,0,0,0.05); margin-bottom: 2rem; }
        .search-form-grid { display: grid; grid-template-columns: 1fr 1fr 1fr auto; /* 3 champs + 1 bouton */ gap: 1rem; align-items: flex-end; }
        .form-group { display: flex; flex-direction: column; }
        .form-group label { margin-bottom: 0.5rem; font-weight: 600; font-size: 0.9rem; color: #555; }
        .form-group input, .form-group select { width: 100%; padding: 0.85rem; border: 1px solid #ddd; border-radius: 8px; box-sizing: border-box; }
        .btn-primary { padding: 0.85rem 1.5rem; border: none; border-radius: 8px; background-color: #007bff; color: white; font-size: 1rem; font-weight: 700; cursor: pointer; transition: background-color 0.3s; }
        .btn-primary:hover { background-color: #0056b3; }

        /* Style pour la liste des résultats (copié de "Mes réservations") */
        .results-header { margin-top: 2rem; border-bottom: 2px solid #eee; padding-bottom: 1rem; }
        .voyage-list { list-style: none; padding: 0; margin-top: 2rem; }
        .voyage-item { background: #fff; border-radius: 10px; box-shadow: 0 8px 15px rgba(0,0,0,0.05); margin-bottom: 1.5rem; display: flex; overflow: hidden; transition: all 0.3s ease; }
        .voyage-item:hover { transform: translateY(-5px); box-shadow: 0 12px 20px rgba(0,0,0,0.08); }
        .voyage-date { background-color: #0056b3; color: white; padding: 1.5rem; text-align: center; width: 100px; display: flex; flex-direction: column; justify-content: center; }
        .voyage-date-day { font-size: 2.5rem; font-weight: 700; line-height: 1; }
        .voyage-date-month { font-size: 1rem; text-transform: uppercase; }
        .voyage-details { padding: 1.5rem; flex-grow: 1; display: grid; grid-template-columns: 2fr 1fr; align-items: center; }
        .voyage-info h3 { margin: 0 0 0.5rem; color: #003366; }
        .voyage-info p { margin: 0.25rem 0; color: #555; }
        .voyage-action { text-align: right; }
        .voyage-price { font-size: 1.6rem; font-weight: 700; color: #0056b3; margin-bottom: 0.5rem; }
        .btn-reserver { display: inline-block; padding: 0.7rem 1.2rem; background: #007bff; color: #fff; text-decoration: none; border-radius: 8px; font-weight: 600; }
        .no-results { text-align: center; padding: 3rem 2rem; background: #fff; border-radius: 12px; }
    </style>
</head>
<body>
    <div class="dashboard-wrapper">
        
        <aside class="sidebar">
            <div class="sidebar-header">
                <h2>Men Travel</h2>
            </div>
            <nav class="sidebar-nav">
                <a href="/dashboard">🏠 Tableau de bord</a>
                <a href="/search" class="active">🚌 Chercher un voyage</a>
                <a href="/my-reservations">🎟️ Mes réservations</a>
                <a href="/profile">👤 Mon profil</a>
            </nav>
            <div class="sidebar-footer">
                <a href="/logout" class="logout-link">Se déconnecter</a>
            </div>
        </aside>

        <main class="main-content">
            <header class="main-header">
                <h1>Chercher un voyage</h1>
            </header>
            
            <div class="search-form-container">
                <form action="/search" method="GET">
                    <div class="search-form-grid">
                        <div class="form-group">
                            <label for="ville_depart">Départ :</label>
                            <select id="ville_depart" name="ville_depart" required>
                                <option value="Yaoundé" <?= ($searchParams['ville_depart'] ?? '') == 'Yaoundé' ? 'selected' : '' ?>>Yaoundé</option>
                                <option value="Douala" <?= ($searchParams['ville_depart'] ?? '') == 'Douala' ? 'selected' : '' ?>>Douala</option>
                                <option value="Bafoussam" <?= ($searchParams['ville_depart'] ?? '') == 'Bafoussam' ? 'selected' : '' ?>>Bafoussam</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="ville_arrivee">Arrivée :</label>
                            <select id="ville_arrivee" name="ville_arrivee" required>
                                <option value="Yaoundé" <?= ($searchParams['ville_arrivee'] ?? '') == 'Yaoundé' ? 'selected' : '' ?>>Yaoundé</option>
                                <option value="Douala" <?= ($searchParams['ville_arrivee'] ?? '') == 'Douala' ? 'selected' : '' ?>>Douala</option>
                                <option value="Bafoussam" <?= ($searchParams['ville_arrivee'] ?? '') == 'Bafoussam' ? 'selected' : '' ?>>Bafoussam</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="date_depart">Date :</label>
                            <input type="date" id="date_depart" name="date_depart" required 
                                   value="<?= htmlspecialchars($searchParams['date_depart'] ?? date('Y-m-d')) ?>"
                                   min="<?= date('Y-m-d') ?>">
                        </div>
                        <button type="submit" class="btn-primary">Rechercher</button>
                    </div>
                </form>
            </div>

            <?php if (isset($searchParams['ville_depart'])): ?>
                <div class="results-header">
                    <h2>Résultats</h2>
                </div>
                
                <ul class="voyage-list">
                    <?php if (empty($voyages)): ?>
                        <li class="no-results">
                            <p>😔 Aucun voyage trouvé pour cette date et cette destination.</p>
                        </li>
                    <?php else: ?>
                        <?php foreach ($voyages as $voyage): ?>
                            <li class="voyage-item">
                                <div class="voyage-date">
                                    <span class="voyage-date-day"><?= date('d', strtotime($voyage['date_depart'])) ?></span>
                                    <span class="voyage-date-month"><?= date('M', strtotime($voyage['date_depart'])) ?></span>
                                </div>
                                <div class="voyage-details">
                                    <div class="voyage-info">
                                        <h3><?= date('H:i', strtotime($voyage['date_depart'])) /* Affiche 08:00 */ ?></h3>
                                        <p><?= htmlspecialchars($voyage['marque']) ?> (<?= $voyage['nombre_places'] ?> places)</p>
                                        <p>Chauffeur: <?= htmlspecialchars($voyage['chauffeur_nom']) ?></p>
                                    </div>
                                    <div class="voyage-action">
                                        <div class="voyage-price"><?= number_format($voyage['prix'], 0, ',', ' ') ?> XAF</div>
                                        <a href="/reservation/select-seat/<?= $voyage['id_voyage'] ?>" class="btn-reserver">
                                            Choisir Siège
                                        </a>
                                    </div>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </ul>
            <?php endif; ?>

        </main>
    </div>
</body>
</html>