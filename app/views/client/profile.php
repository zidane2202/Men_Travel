<?php
$pageTitle = 'Mon profil';
$client_nom = $_SESSION['client_nom'] ?? 'Client';

// $clientData est passé par le contrôleur
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
        .sidebar-nav a { display: block; color: #dfe9f3; text-decoration: none; padding: 0.9rem 1.5rem; font-size: 1.05rem; transition: background-color 0.3s, padding-left 0.3s; }
        .sidebar-nav a.active { background-color: #004a99; color: #ffffff; font-weight: 600; border-left: 5px solid #007bff; padding-left: calc(1.5rem - 5px); }
        .sidebar-nav a:hover:not(.active) { background-color: #004080; padding-left: 1.8rem; }
        .sidebar-footer { padding: 1.5rem; border-top: 1px solid #004a99; }
        .logout-link { display: block; background-color: #d9534f; color: #fff; text-align: center; padding: 0.75rem; border-radius: 8px; text-decoration: none; font-weight: 600; transition: background-color 0.3s; }
        .logout-link:hover { background-color: #c9302c; }
        .main-content { flex-grow: 1; margin-left: 560px; padding: 2.5rem; animation: fadeIn 0.5s ease-out; }
        .main-header { margin-bottom: 2rem; }
        .main-header h1 { margin: 0; font-size: 2.2rem; font-weight: 700; }
        
        /* Style pour le formulaire de profil */
        .profile-form { max-width: 600px; background: #fff; padding: 2rem; border-radius: 10px; box-shadow: 0 8px 15px rgba(0, 0, 0, 0.05); }
        .form-group { margin-bottom: 1.25rem; }
        .form-group label { display: block; margin-bottom: 0.5rem; font-weight: 600; font-size: 0.9rem; color: #555; }
        .form-group input, .form-group select { width: 100%; padding: 0.85rem; border: 1px solid #ddd; border-radius: 8px; box-sizing: border-box; background-color: #f9f9f9; }
        .form-group input:focus { outline: none; border-color: #007bff; box-shadow: 0 0 8px rgba(0,123,255,0.25); background-color: #fff; }
        .form-group input[readonly] { background-color: #eee; cursor: not-allowed; }
        .form-button { width: auto; padding: 0.9rem 1.5rem; border: none; border-radius: 8px; background-color: #007bff; color: white; font-size: 1rem; font-weight: 700; cursor: pointer; transition: background-color 0.3s; }
        .form-button:hover { background-color: #0056b3; }
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
                <a href="/my-reservations">🎟️ Mes réservations</a>
                <a href="/profile" class="active">👤 Mon profil</a>
            </nav>
            <div class="sidebar-footer">
                <a href="/logout" class="logout-link">Se déconnecter</a>
            </div>
        </aside>

        <main class="main-content">
            <header class="main-header">
                <h1>Mon profil</h1>
            </header>
            
            <form action="/profile/update" method="POST" class="profile-form">
                <?php if (isset($_GET['error'])): ?>
                    <p style="color:red; background:#fdd; border:1px solid #f00; padding:10px; border-radius:8px;">
                        <?= htmlspecialchars($_GET['error']) ?>
                    </p>
                <?php endif; ?>
                <?php if (isset($_GET['success'])): ?>
                    <p style="color:green; background:#dfd; border:1px solid #0a0; padding:10px; border-radius:8px;">
                        <?= htmlspecialchars($_GET['success']) ?>
                    </p>
                <?php endif; ?>
                
                <div class="form-group">
                    <label for="nom">Nom :</label>
                    <input type="text" id="nom" name="nom" value="<?= htmlspecialchars($clientData['nom'] ?? '') ?>" required>
                </div>
                <div class="form-group">
                    <label for="prenom">Prénom :</label>
                    <input type="text" id="prenom" name="prenom" value="<?= htmlspecialchars($clientData['prenom'] ?? '') ?>" required>
                </div>
                <div class="form-group">
                    <label for="email">Email :</label>
                    <input type="email" id="email" name="email" value="<?= htmlspecialchars($clientData['email'] ?? '') ?>" readonly>
                </div>
                <div class="form-group">
                    <label for="telephone">Téléphone :</label>
                    <input type="tel" id="telephone" name="telephone" value="<?= htmlspecialchars($clientData['telephone'] ?? '') ?>" required>
                </div>
                <div class="form-group">
                    <label for="adresse">Adresse (Ville) :</label>
                    <input type="text" id="adresse" name="adresse" value="<?= htmlspecialchars($clientData['adresse'] ?? '') ?>" required>
                </div>
                <div class="form-group">
                    <label for="date_naissance">Date de naissance :</label>
                    <input type="date" id="date_naissance" name="date_naissance" value="<?= htmlspecialchars($clientData['date_naissance'] ?? '') ?>" required>
                </div>

                <button type="submit" class="form-button">Mettre à jour</button>
            </form>
        </main>

    </div></body>
</html>