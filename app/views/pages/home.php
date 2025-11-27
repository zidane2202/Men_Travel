<?php 
// $pageTitle et $voyages sont passés par le contrôleur
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <style>
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; background-color: #f4f7f6; color: #333; margin: 0; padding: 0; }
        
        /* Layout simple (pas de sidebar) */
        .navbar {
            background: #003366;
            color: white;
            padding: 1rem 2.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        .navbar h1 { margin: 0; font-size: 1.8rem; }
        .navbar a { color: white; text-decoration: none; background: #007bff; padding: 0.6rem 1rem; border-radius: 8px; font-weight: 600; }
        
        .main-content { 
            max-width: 900px; /* Centrer le contenu */
            margin: 0 auto;
            padding: 2.5rem; 
            animation: fadeIn 0.5s ease-out; 
        }
        .main-header { margin-bottom: 2rem; }
        .main-header h2 { margin: 0; font-size: 2.2rem; font-weight: 700; color: #003366; }

        /* Style pour la liste des résultats */
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

    <nav class="navbar">
        <h1>Men Travel</h1>
        <a href="/login">Se connecter / S'inscrire</a>
    </nav>

    <main class="main-content">
        <header class="main-header">
            <h2>Tous nos voyages programmés</h2>
        </header>
        
        <ul class="voyage-list">
            <?php if (empty($voyages)): ?>
                <li class="no-results">
                    <p>😔 Aucun voyage n'est programmé pour le moment.</p>
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
                                <h3><?= htmlspecialchars($voyage['ville_depart']) ?> → <?= htmlspecialchars($voyage['ville_arrivee']) ?></h3>
                                <p>Départ à <strong><?= date('H:i', strtotime($voyage['date_depart'])) ?></strong></p>
                                <p>Chauffeur: <?= htmlspecialchars($voyage['chauffeur_nom']) ?></p>
                            </div>
                            <div class="voyage-action">
                                <div class="voyage-price"><?= number_format($voyage['prix'], 0, ',', ' ') ?> XAF</div>
                                
                                <a href="/reservation/select-seat/<?= $voyage['id_voyage'] ?>" class="btn-reserver">
                                    Réserver
                                </a>
                            </div>
                        </div>
                    </li>
                <?php endforeach; ?>
            <?php endif; ?>
        </ul>

    </main>
</body>
</html>