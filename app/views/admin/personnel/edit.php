<?php 
// $employeData, $specialisationData sont passés par le contrôleur
$admin_nom = $_SESSION['admin_nom'] ?? 'Admin';
$pageTitle = 'Modifier Employé N°' . ($employeData['id_employe'] ?? 'N/A');

// Préparation des dates pour les champs HTML (YYYY-MM-DD)
$date_naissance_html = date('Y-m-d', strtotime($employeData['date_naissance'] ?? 'now'));
$date_embauche_html = date('Y-m-d', strtotime($employeData['date_embauche'] ?? 'now'));

// Récupération des données de spécialisation avec vérification de l'existence
$permis_numero = $specialisationData['permis_numero'] ?? '';
$specialite = $specialisationData['specialite'] ?? '';

// Liste de tous les postes possibles (identique à create.php)
$postes = ['Chauffeur', 'Mecanicien', 'Comptable', 'AgentReservation', 'Gardien', 'Administrateur'];
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
        .main-content { flex-grow: 1; margin-left: 500px; padding: 2.5rem; animation: fadeIn 0.5s ease-out; }
        .main-header { margin-bottom: 2rem; display: flex; justify-content: space-between; align-items: center; }
        .main-header h1 { margin: 0; font-size: 2.2rem; font-weight: 700; color: #343a40; }
        
        /* Formulaire */
        .form-card { max-width: 700px; background: white; padding: 2rem; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; }
        .form-group { display: flex; flex-direction: column; }
        .form-group label { margin-bottom: 0.5rem; font-weight: 600; font-size: 0.9rem; color: #495057; }
        .form-group input, .form-group select { padding: 0.85rem; border: 1px solid #ddd; border-radius: 5px; }
        .form-actions { grid-column: 1 / 3; text-align: right; margin-top: 1rem; }
        .btn-submit { padding: 0.85rem 1.5rem; border: none; border-radius: 5px; background-color: #007bff; color: white; font-weight: 700; cursor: pointer; }
        .btn-cancel { background-color: #6c757d; color: white; padding: 0.7rem 1.5rem; border-radius: 8px; text-decoration: none; font-weight: 600; }
        .error-message { background-color: #f8d7da; color: #721c24; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; }
        .conditional-field { grid-column: 1 / 3; border-top: 2px dashed #ddd; padding-top: 1rem; margin-top: 1rem; }
    </style>
</head>
<body onload="toggleSpecialization('<?= htmlspecialchars($employeData['poste'] ?? '') ?>')">

    <div class="dashboard-wrapper">
        <aside class="sidebar">
            <div class="sidebar-header"><h2>Men Travel</h2><span style="font-size: 0.9rem;">Panneau d'Administration</span></div>
            <nav class="sidebar-nav">
                <a href="/admin/dashboard">🏠 Tableau de bord</a>
                <a href="/admin/voyages">🚌 Gérer les Voyages</a>
                <a href="/admin/vehicules">🚗 Gérer les Véhicules</a>
                <a href="/admin/reservations">🎟️ Voir les Réservations</a>
                <a href="/admin/clients">👤 Gérer les Clients</a>
                <a href="/admin/employes" class="active">🛠️ Gérer le Personnel</a>
            </nav>
            <div class="sidebar-footer"><a href="/admin/logout" class="logout-link">Se déconnecter</a></div>
        </aside>

        <main class="main-content">
            <header class="main-header">
                <h1>Modifier les informations de l'employé</h1>
                <a href="/admin/employes" class="btn-action btn-cancel" style="background-color: #6c757d;">← Retour</a>
            </header>
            
            <?php if (isset($_GET['error'])): ?>
                <div class="error-message"><?= htmlspecialchars($_GET['error']) ?></div>
            <?php endif; ?>

            <div class="form-card">
                
                <form action="/admin/employes/update" method="POST">
                    <input type="hidden" name="id_employe" value="<?= $employeData['id_employe'] ?? '' ?>">

                    <div class="form-grid">

                        <div class="form-group">
                            <label for="poste">Poste :</label>
                            <select id="poste" name="poste" required onchange="toggleSpecialization(this.value)">
                                <?php foreach ($postes as $p): ?>
                                    <option value="<?= $p ?>" <?= (($employeData['poste'] ?? '') == $p) ? 'selected' : '' ?>>
                                        <?= ucfirst($p) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            </div>

                        <div class="form-group">
                            <label for="nom">Nom :</label>
                            <input type="text" id="nom" name="nom" value="<?= htmlspecialchars($employeData['nom'] ?? '') ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="prenom">Prénom :</label>
                            <input type="text" id="prenom" name="prenom" value="<?= htmlspecialchars($employeData['prenom'] ?? '') ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Email :</label>
                            <input type="email" id="email" name="email" value="<?= htmlspecialchars($employeData['email'] ?? '') ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="telephone">Téléphone :</label>
                            <input type="tel" id="telephone" name="telephone" value="<?= htmlspecialchars($employeData['telephone'] ?? '') ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="date_naissance">Date de Naissance :</label>
                            <input type="date" id="date_naissance" name="date_naissance" value="<?= $date_naissance_html ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="date_embauche">Date d'Embauche :</label>
                            <input type="date" id="date_embauche" name="date_embauche" value="<?= $date_embauche_html ?>" required>
                        </div>
                        
                        <div id="specialization-fields" style="grid-column: 1 / 3;">
                            <div id="chauffeur-fields" class="conditional-field" style="display:none; border-color: #007bff;">
                                <h3>Détails du Chauffeur</h3>
                                <div class="form-group">
                                    <label for="permis_numero">Numéro de Permis :</label>
                                    <input type="text" id="permis_numero" name="permis_numero" value="<?= htmlspecialchars($permis_numero) ?>">
                                </div>
                            </div>
                            <div id="mecanicien-fields" class="conditional-field" style="display:none; border-color: #d9534f;">
                                <h3>Détails du Mécanicien</h3>
                                <div class="form-group">
                                    <label for="specialite">Spécialité :</label>
                                    <input type="text" id="specialite" name="specialite" value="<?= htmlspecialchars($specialite) ?>" placeholder="Ex: Moteurs Diesel">
                                </div>
                            </div>
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn-submit" style="background-color: #007bff;">Enregistrer les modifications</button>
                        </div>
                    </div>
                </form>
            </div>
        </main>
    </div>

    <script>
        function toggleSpecialization(poste) {
            const chauffeur = document.getElementById('chauffeur-fields');
            const mecanicien = document.getElementById('mecanicien-fields');
            const permisInput = document.getElementById('permis_numero');
            const specialiteInput = document.getElementById('specialite');

            // Masquer tout par défaut et retirer l'obligation
            chauffeur.style.display = 'none';
            mecanicien.style.display = 'none';
            permisInput.required = false; 
            specialiteInput.required = false; 

            // Afficher le champ requis et le rendre obligatoire
            if (poste === 'Chauffeur') {
                chauffeur.style.display = 'block';
                permisInput.required = true; 
            } else if (poste === 'Mecanicien') {
                mecanicien.style.display = 'block';
                specialiteInput.required = true; 
            }
        }

        // Exécuter au chargement initial
        document.addEventListener('DOMContentLoaded', function() {
            // Utiliser le poste actuel de l'employé
            const initialPoste = document.getElementById('poste').value;
            toggleSpecialization(initialPoste);
        });
    </script>
</body>
</html>