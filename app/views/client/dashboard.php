<?php
$pageTitle = 'Tableau de Bord';
// Récupérer le nom depuis la session
$client_nom = $_SESSION['client_nom'] ?? 'Client';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?> - Men Travel</title>
    
    <style>
        /* Animation de fondu */
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            background-color: #f4f7f6;
            color: #333;
            margin: 0;
            padding: 0;
        }

        .dashboard-wrapper {
            display: flex;
            min-height: 100vh;
        }

        /* --- La Sidebar --- */
        .sidebar {
            width: 260px;
            background-color: #003366; /* Bleu nuit, plus pro */
            color: #ffffff;
            display: flex;
            flex-direction: column;
            position: fixed;
            height: 100%;
            animation: fadeIn 0.3s ease-out;
        }

        .sidebar-header {
            padding: 1.5rem;
            text-align: center;
            border-bottom: 1px solid #004a99;
        }
        .sidebar-header h2 {
            margin: 0;
            font-size: 1.5rem;
        }

        .sidebar-nav {
            flex-grow: 1;
            padding: 1rem 0;
        }
        .sidebar-nav a {
            display: block;
            color: #dfe9f3;
            text-decoration: none;
            padding: 0.9rem 1.5rem;
            font-size: 1.05rem;
            transition: background-color 0.3s, padding-left 0.3s;
        }
        /* Style pour le lien "actif" */
        .sidebar-nav a.active {
            background-color: #004a99;
            color: #ffffff;
            font-weight: 600;
            border-left: 5px solid #007bff;
            padding-left: calc(1.5rem - 5px); /* Compenser la bordure */
        }
        .sidebar-nav a:hover:not(.active) {
            background-color: #004080;
            padding-left: 1.8rem;
        }

        .sidebar-footer {
            padding: 1.5rem;
            border-top: 1px solid #004a99;
        }
        .logout-link {
            display: block;
            background-color: #d9534f;
            color: #fff;
            text-align: center;
            padding: 0.75rem;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: background-color 0.3s;
        }
        .logout-link:hover {
            background-color: #c9302c;
        }

        /* --- Le Contenu Principal --- */
        .main-content {
            flex-grow: 1;
            /* Laisser la place pour la sidebar fixe */
            margin-left: 260px; 
            padding: 2.5rem;
            animation: fadeIn 0.5s ease-out;
        }

        .main-header {
            margin-bottom: 2rem;
        }
        .main-header h1 {
            margin: 0;
            font-size: 2.2rem;
            font-weight: 700;
        }
        .main-header p {
            margin: 0.5rem 0 0;
            font-size: 1.1rem;
            color: #555;
        }
        
        /* Cartes de contenu */
        .content-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.5rem;
        }
        .content-card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.05);
            padding: 1.5rem;
        }
        .content-card h3 {
            margin-top: 0;
            color: #0056b3;
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
                <p>C'est un plaisir de vous revoir.</p>
            </header>
            
            <div class="content-grid">
                <div class="content-card">
                    <h3>🚌 Prêt à voyager ?</h3>
                    <p>Trouvez votre prochain trajet vers Douala, Bafoussam, Kribi et d'autres destinations.</p>
                    <a href="/search" class="form-button" style="display:inline-block; text-decoration:none; margin-top:1rem; padding: 0.7rem 1rem;">Commencer une recherche</a>
                </div>

                <div class="content-card">
                    <h3>📦 Envoyer un colis</h3>
                    <p>Un service rapide et fiable pour vos envois de colis entre nos agences.</p>
                </div>
                
                 <div class="content-card">
                    <h3>🎟️ Vos tickets</h3>
                    <p>Accédez rapidement à l'historique de vos voyages et à vos réservations en cours.</p>
                </div>
            </div>
        </main>

    </div><style>
        .form-button { width: auto; padding: 0.9rem 1.2rem; border: none; border-radius: 8px; background-color: #007bff; color: white; font-size: 0.9rem; font-weight: 700; cursor: pointer; transition: background-color 0.3s, transform 0.1s; }
        .form-button:hover { background-color: #0056b3; }
        .form-button:active { transform: scale(0.98); }
    </style>

</body>
</html>