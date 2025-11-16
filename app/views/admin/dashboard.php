<?php
$pageTitle = 'Tableau de Bord Admin';
$admin_nom = $_SESSION['admin_nom'] ?? 'Admin';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?> - Men Travel</title>
    
    <style>
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
        body { 
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; 
            background-color: #f4f7f6; 
            color: #333; 
            margin: 0; 
            padding: 0; 
        }
        .dashboard-wrapper { display: flex; min-height: 100vh; }
        
        /* --- Sidebar Admin (Rouge) --- */
        .sidebar { 
            width: 260px; 
            background-color: #343a40; /* Gris foncé/Noir */
            color: #ffffff; 
            display: flex; 
            flex-direction: column; 
            position: fixed; 
            height: 100%; 
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        .sidebar-header { 
            padding: 1.5rem; 
            text-align: center; 
            border-bottom: 1px solid #495057; 
        }
        .sidebar-header h2 { margin: 0; font-size: 1.5rem; color: #d9534f; } /* Rouge Men Travel */
        .sidebar-nav { flex-grow: 1; padding: 1rem 0; }
        .sidebar-nav a { 
            display: flex; 
            align-items: center; 
            gap: 0.75rem;
            color: #dfe9f3; 
            text-decoration: none; 
            padding: 0.9rem 1.5rem; 
            font-size: 1.05rem; 
            transition: all 0.3s; 
        }
        .sidebar-nav a.active { 
            background-color: #495057; 
            color: #ffffff; 
            font-weight: 600; 
            border-left: 5px solid #d9534f; /* Rouge */
            padding-left: calc(1.5rem - 5px); 
        }
        .sidebar-nav a:hover:not(.active) { background-color: #495057; padding-left: 1.8rem; }
        
        .sidebar-footer { padding: 1.5rem; border-top: 1px solid #495057; }
        .logout-link { 
            display: block; 
            background-color: #d9534f; 
            color: #fff; 
            text-align: center; 
            padding: 0.75rem; 
            border-radius: 8px; 
            text-decoration: none; 
            font-weight: 600; 
        }
        
        /* --- Contenu Principal --- */
        .main-content { 
            flex-grow: 1; 
            margin-left: 260px; 
            padding: 2.5rem; 
            animation: fadeIn 0.5s ease-out; 
        }
        .main-header { margin-bottom: 2rem; }
        .main-header h1 { margin: 0; font-size: 2.2rem; font-weight: 700; color: #343a40; }
        .main-header p { margin: 0.5rem 0 0; font-size: 1.1rem; color: #555; }
        
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
            color: #d9534f;
        }
    </style>
</head>
<body>

    <div class="dashboard-wrapper">
        
        <aside class="sidebar">
            <div class="sidebar-header">
                <h2>Men Travel</h2>
                <span style="font-size: 0.9rem;">Panneau d'Administration</span>
            </div>
            
            <nav class="sidebar-nav">
                <a href="/admin/dashboard" class="active">🏠 Tableau de bord</a>
                <a href="/admin/voyages">🚌 Gérer les Voyages</a>
                <a href="/admin/reservations">🎟️ Voir les Réservations</a>
                <a href="/admin/clients">👤 Gérer les Clients</a>
                <a href="/admin/employes">🛠️ Gérer le Personnel</a>
            </nav>
            
            <div class="sidebar-footer">
                <a href="/admin/logout" class="logout-link">Se déconnecter</a>
            </div>
        </aside>

        <main class="main-content">
            <header class="main-header">
                <h1>Bienvenue, <?= htmlspecialchars($admin_nom) ?> !</h1>
                <p>Gérez les opérations de Men Travel depuis cet espace.</p>
            </header>
            
            <div class="content-grid">
                <div class="content-card">
                    <h3>🚌 Voyages Programmés</h3>
                    <p>Accédez à la liste des voyages pour en créer de nouveaux, modifier les prix ou les horaires.</p>
                    <a href="/admin/voyages" style="color: #d9534f; font-weight: 600;">Gérer les voyages →</a>
                </div>

                <div class="content-card">
                    <h3>🎟️ Réservations Récentes</h3>
                    <p>Consultez la liste des passagers pour les prochains départs et vérifiez les statuts de paiement.</p>
                    <a href="/admin/reservations" style="color: #d9534f; font-weight: 600;">Voir les réservations →</a>
                </div>
            </div>
        </main>

    </div></body>
</html>