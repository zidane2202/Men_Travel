<?php
$pageTitle = 'Tableau de Bord';
// Récupérer le nom depuis la session
$client_nom = $_SESSION['client_nom'] ?? 'Client';

// Variables passées par le contrôleur: $reservationStats, $nextTrip
$total_trips = $reservationStats['total_trips'] ?? 0;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?> - Men Travel</title>
    
    <style>
        /* Animation de fondu */
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }

        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; background-color: #f4f7f6; color: #333; margin: 0; padding: 0; }
        .dashboard-wrapper { display: flex; min-height: 100vh; }
        
        /* --- Sidebar --- */
        .sidebar { width: 260px; background-color: #003366; color: #ffffff; display: flex; flex-direction: column; position: fixed; height: 100%; animation: fadeIn 0.3s ease-out; }
        .sidebar-header { padding: 1.5rem; text-align: center; border-bottom: 1px solid #004a99; }
        .sidebar-header h2 { margin: 0; font-size: 1.5rem; }
        .sidebar-nav { flex-grow: 1; padding: 1rem 0; }
        .sidebar-nav a { display: block; color: #dfe9f3; text-decoration: none; padding: 0.9rem 1.5rem; font-size: 1.05rem; transition: background-color 0.3s, padding-left 0.3s; }
        .sidebar-nav a.active { background-color: #004a99; color: #ffffff; font-weight: 600; border-left: 5px solid #007bff; padding-left: calc(1.5rem - 5px); }
        .sidebar-nav a:hover:not(.active) { background-color: #004080; padding-left: 1.8rem; }
        .sidebar-footer { padding: 1.5rem; border-top: 1px solid #004a99; }
        .logout-link { display: block; background-color: #d9534f; color: #fff; text-align: center; padding: 0.75rem; border-radius: 8px; text-decoration: none; font-weight: 600; transition: background-color 0.3s; }
        
        /* --- Contenu Principal --- */
        .main-content { margin-left: 500px; padding: 2.5rem; animation: fadeIn 0.5s ease-out; }
        .main-header { margin-bottom: 2rem; }
        .main-header h1 { margin: 0; font-size: 2.2rem; font-weight: 700; color: #003366; }
        .main-header p { margin: 0.5rem 0 0; font-size: 1.1rem; color: #555; }
        
        /* Cartes de STATS */
        .content-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.5rem;
        }
        .stats-card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.05);
            padding: 1.5rem;
            display: flex;
            flex-direction: column;
        }
        .stats-card h3 {
            margin-top: 0;
            color: #0056b3;
        }
        .stats-value {
            font-size: 2.5rem;
            font-weight: 700;
            color: #007bff;
        }
        .stats-label {
            font-size: 0.9rem;
            color: #6c757d;
        }
        
        .cta-button { 
            width: auto; 
            padding: 0.9rem 1.2rem; 
            border: none; 
            border-radius: 8px; 
            background-color: #007bff; 
            color: white; 
            font-size: 0.9rem; 
            font-weight: 700; 
            text-decoration: none;
            display: inline-block;
            transition: background-color 0.3s; 
        }
        .cta-button:hover { background-color: #0056b3; }
        .next-trip {
            background-color: #f8f9fa;
            border: 1px solid #eee;
            padding: 1rem;
            border-radius: 8px;
        }

    </style>
</head>
<body>

    <div class="dashboard-wrapper">
        
        <aside class="sidebar">
            <div class="sidebar-header">
                <h2>Men Travel</h2>
            </div>
            
            <nav class="sidebar-nav">
                <a href="/dashboard" class="active">🏠 Tableau de bord</a>
                <a href="/search">🚌 Chercher un voyage</a>
                <a href="/my-reservations">🎟️ Mes réservations</a>
                <a href="/profile">👤 Mon profil</a>
            </nav>
            
            <div class="sidebar-footer">
                <a href="/logout" class="logout-link">Se déconnecter</a>
            </div>
        </aside>

        <main class="main-content">
            <header class="main-header">
                <h1>Bienvenue, <?= htmlspecialchars($client_nom) ?> !</h1>
                <p>Gérez vos voyages et réservez votre prochain trajet facilement.</p>
            </header>
            
            <div class="content-grid">
                
                <div class="stats-card">
                    <span class="stats-label">Voyages Effectués</span>
                    <div class="stats-value"><?= $total_trips ?></div>
                    <small>Réservations passées et payées</small>
                </div>

                <div class="stats-card">
                    <span class="stats-label">Dernière Réservation</span>
                    <div class="stats-value" style="font-size: 1.5rem;">
                        <?= ($reservationStats['last_booking']) ? date('d M Y', strtotime($reservationStats['last_booking'])) : 'Jamais' ?>
                    </div>
                    <small>Date de votre dernière commande confirmée</small>
                </div>
                
                <div class="stats-card" style="grid-column: 1 / 3;">
                    <h3>Prochain Voyage</h3>
                    <?php if ($nextTrip): ?>
                        <div class="next-trip">
                            <p style="margin: 0; font-size: 1.2rem; color: #003366;">
                                <strong><?= htmlspecialchars($nextTrip['ville_depart']) ?> → <?= htmlspecialchars($nextTrip['ville_arrivee']) ?></strong>
                            </p>
                            <p style="margin: 5px 0 10px; color: #333;">
                                Départ : <?= date('d M Y \à H:i', strtotime($nextTrip['date_depart'])) ?>
                            </p>
                            <a href="/my-reservations" class="cta-button" style="background-color:#28a745;">Voir mes tickets</a>
                        </div>
                    <?php else: ?>
                        <p>Vous n'avez aucun voyage à venir. Prêt(e) à partir ?</p>
                        <a href="/search" class="cta-button">Commencer une recherche</a>
                    <?php endif; ?>
                </div>

                <div class="content-card">
                    <h3>📦 Envoyer un colis</h3>
                    <p>Service rapide pour envoyer vos colis entre nos agences.</p>
                    <a href="/colis/register" style="display:inline-block; text-decoration:none; margin-top:1rem; color: #007bff; font-weight: 600;">Créer un envoi →</a>
                </div>

                <div class="content-card">
                    <h3>👤 Mon Profil</h3>
                    <p>Mettez à jour vos informations de contact et vos préférences.</p>
                    <a href="/profile" style="display:inline-block; text-decoration:none; margin-top:1rem; color: #007bff; font-weight: 600;">Gérer le profil →</a>
                </div>
            </div>
        </main>

    </div><style>
        .cta-button { width: auto; padding: 0.9rem 1.2rem; border: none; border-radius: 8px; background-color: #007bff; color: white; font-size: 0.9rem; font-weight: 700; cursor: pointer; transition: background-color 0.3s, transform 0.1s; }
        .cta-button:hover { background-color: #0056b3; }
        .cta-button:active { transform: scale(0.98); }
        .form-button { display: none; } /* Suppression du bouton obsolète */
    </style>

</body>
</html>