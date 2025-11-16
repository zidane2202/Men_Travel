<?php
$pageTitle = 'Mes réservations';
$client_nom = $_SESSION['client_nom'] ?? 'Client';

// La variable est maintenant $commandes (un tableau de commandes)
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?> - Men Travel</title>
    
    <style>
        /* --- Base et Animations --- */
        @keyframes fadeIn { 
            from { opacity: 0; transform: translateY(10px); } 
            to { opacity: 1; transform: translateY(0); }
        }
        
        body { 
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; 
            background-color: #f4f7f6; 
            color: #333; 
            margin: 0; 
            padding: 0; 
        }
        
        /* --- Layout (Sidebar + Contenu) --- */
        .dashboard-wrapper { display: flex; min-height: 100vh; }
        
        .sidebar { 
            width: 260px; 
            background-color: #003366; 
            color: #ffffff; 
            display: flex; 
            flex-direction: column; 
            position: fixed; 
            height: 100%; 
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        .sidebar-header { padding: 1.5rem; text-align: center; border-bottom: 1px solid #004a99; }
        .sidebar-header h2 { margin: 0; font-size: 1.5rem; }
        .sidebar-nav { flex-grow: 1; padding: 1rem 0; }
        .sidebar-nav a { 
            display: flex; 
            align-items: center; 
            gap: 0.75rem; /* Espace entre l'icône et le texte */
            color: #dfe9f3; 
            text-decoration: none; 
            padding: 0.9rem 1.5rem; 
            font-size: 1.05rem; 
            transition: all 0.3s; 
        }
        .sidebar-nav a.active { 
            background-color: #004a99; 
            color: #ffffff; 
            font-weight: 600; 
            border-left: 5px solid #007bff; 
            padding-left: calc(1.5rem - 5px); 
        }
        .sidebar-nav a:hover:not(.active) { background-color: #004080; padding-left: 1.8rem; }
        
        .sidebar-footer { padding: 1.5rem; border-top: 1px solid #004a99; }
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
        .logout-link:hover { background-color: #c9302c; }
        
        /* --- Contenu Principal --- */
        .main-content { 
            flex-grow: 1; 
            margin-left: 260px; 
            padding: 2.5rem; 
            animation: fadeIn 0.5s ease-out; 
        }
        .main-header { margin-bottom: 2rem; }
        .main-header h1 { 
            margin: 0; 
            font-size: 2.2rem; 
            font-weight: 700; 
            color: #003366;
        }

        /* --- Style "TICKET" --- */
        .reservation-list { 
            list-style: none; 
            padding: 0; 
        }
        
        .ticket {
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0, 51, 102, 0.07);
            margin-bottom: 1.5rem;
            display: flex; /* Sépare le ticket en 2 parties */
            overflow: hidden;
            border: 1px solid #e0e0e0;
            transition: all 0.3s ease;
            animation: fadeIn 0.5s ease-out;
        }
        .ticket:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0, 51, 102, 0.1);
        }

        .ticket-main {
            padding: 1.5rem;
            flex-grow: 1; /* Prend le plus de place */
        }
        
        .ticket-header h3 {
            margin: 0 0 1.25rem;
            color: #003366;
            font-size: 1.5rem;
            font-weight: 700;
        }
        .ticket-header .arrow {
            color: #007bff;
            margin: 0 0.5rem;
        }
        
        .ticket-details {
            display: grid;
            grid-template-columns: 1fr 1fr; /* 2 colonnes */
            gap: 1rem;
        }
        
        .detail-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .detail-item span { /* L'icône emoji */
            font-size: 1.25rem;
        }
        .detail-item p {
            margin: 0;
            font-size: 0.95rem;
            color: #333;
        }
        .detail-item p strong { /* Le label (ex: Date:) */
            display: block;
            font-size: 0.8rem;
            color: #777;
            font-weight: 600;
        }
        
        /* Partie droite du ticket (Prix & Statut) */
        .ticket-aside {
            width: 200px;
            background: #f9f9f9;
            padding: 1.5rem;
            text-align: right;
            border-left: 2px dashed #e0e0e0;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: flex-end;
        }
        
        .ticket-price {
            font-size: 1.75rem;
            font-weight: 700;
            color: #0056b3;
            margin: 0 0 1rem;
        }
        
        /* Statut (combiné et amélioré) */
        .status {
            display: inline-flex;
            align-items: center;
            font-weight: 700;
            padding: 0.4rem 0.8rem;
            border-radius: 20px; /* Style "pillule" */
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .status::before { /* Ajoute un point de couleur */
            content: '●';
            margin-right: 0.5rem;
            font-size: 1rem;
        }
        
        .status-payee { background-color: #d4edda; color: #155724; }
        .status-payee::before { color: #28a745; }
        
        .status-en-attente { background-color: #fff3cd; color: #856404; }
        .status-en-attente::before { color: #ffc107; }
        
        .status-annulee { background-color: #f8d7da; color: #721c24; }
        .status-annulee::before { color: #dc3545; }
        
        .status-terminee { background-color: #e2e3e5; color: #383d41; }
        .status-terminee::before { color: #6c757d; }

        /* Carte "Aucun résultat" */
        .no-results {
            text-align: center;
            padding: 3rem 2rem;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.05);
            border: 1px dashed #ddd;
        }
        .no-results p {
            font-size: 1.1rem;
            color: #555;
            margin-bottom: 1.5rem;
        }
        .btn-primary {
            display: inline-block;
            padding: 0.8rem 1.5rem;
            background: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s;
        }
        .btn-primary:hover {
            background-color: #0056b3;
            transform: translateY(-2px);
            box-shadow: 0 5px 10px rgba(0,123,255,0.2);
        }

        /* Styles pour les boutons d'action */
        .ticket-actions {
            margin-top: 1rem;
        }
        .btn-action {
            display: block;
            width: 100%;
            padding: 0.6rem 0.5rem;
            border: none;
            border-radius: 8px;
            font-size: 0.9rem;
            font-weight: 700;
            text-align: center;
            text-decoration: none;
            box-sizing: border-box; 
            transition: all 0.3s ease;
        }
        .btn-pay {
            background-color: #ffc107; /* Jaune "payer" */
            color: #333;
        }
        .btn-pay:hover {
            background-color: #e0a800;
        }
        .btn-view {
            background-color: #28a745; /* Vert "succès" */
            color: #fff;
        }
        .btn-view:hover {
            background-color: #218838;
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
                <a href="/dashboard">🏠 Tableau de bord</a>
                <a href="/search">🚌 Chercher un voyage</a>
                <a href="/my-reservations" class="active">🎟️ Mes réservations</a>
                <a href="/profile">👤 Mon profil</a>
            </nav>
            <div class="sidebar-footer">
                <a href="/logout" class="logout-link">Se déconnecter</a>
            </div>
        </aside>

        <main class="main-content">
            
            <header class="main-header">
                <h1>Mes réservations</h1>
            </header>
            
            <ul class="reservation-list">
                
                <?php if (empty($commandes)): // <-- MODIFIÉ ?>
                    <li class="no-results">
                        <p>Vous n'avez aucune réservation pour le moment.</p>
                        <a href="/search" class="btn-primary">Faire une réservation</a>
                    </li>
                <?php else: ?>
                    
                    <?php foreach ($commandes as $cmd): // <-- MODIFIÉ ?>
                        <li class="ticket">
                            <div class="ticket-main">
                                <div class="ticket-header">
                                    <h3>
                                        <?= htmlspecialchars($cmd['ville_depart']) ?> 
                                        <span class="arrow">→</span> 
                                        <?= htmlspecialchars($cmd['ville_arrivee']) ?>
                                    </h3>
                                </div>
                                <div class="ticket-details">
                                    <div class="detail-item">
                                        <span>📅</span>
                                        <p>
                                            <strong>Date</strong>
                                            <?= htmlspecialchars(date('d M Y \à H:i', strtotime($cmd['date_depart']))) ?>
                                        </p>
                                    </div>
                                    <div class="detail-item">
                                        <span>🪑</span>
                                        <p>
                                            <strong>Siège(s)</strong>
                                            N° <?= htmlspecialchars(implode(', N°', $cmd['sieges'])) ?>
                                        </p>
                                    </div>
                                </div>
                            </div>
                           <div class="ticket-aside">
                                <p class="ticket-price">
                                    <?= htmlspecialchars(number_format($cmd['montant_total'], 0, ',', ' ')) ?> XAF
                                </p>
                                
                                <?php 
                                    $statut = $cmd['statut']; // <-- MODIFIÉ
                                    $classe = 'status-en-attente'; // Défaut
                                    if ($statut === 'PAYEE') $classe = 'status-payee';
                                    if ($statut === 'ANNULEE') $classe = 'status-annulee';
                                    // Le statut 'TERMINEE' n'est pas sur la commande,
                                    // mais on peut le laisser au cas où
                                ?>
                                <span class="status <?= $classe ?>">
                                    <?= htmlspecialchars(ucfirst(strtolower(str_replace('_', ' ', $statut)))) ?>
                                </span>

                                <div class="ticket-actions">
                                    <?php if ($statut === 'EN_ATTENTE'): ?>
                                        <a href="/payment/show/<?= $cmd['id_commande'] ?>" class="btn-action btn-pay">
                                            Payer maintenant
                                        </a>
                                    <?php elseif ($statut === 'PAYEE'): ?>
                                        <a href="/ticket/view/<?= $cmd['id_commande'] ?>" class="btn-action btn-view">
                                            Voir le ticket
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </li>
                    <?php endforeach; ?>
                    
                <?php endif; ?>
                
            </ul>
        </main>

    </div>
</body>
</html>