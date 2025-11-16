<?php $pageTitle = 'Connexion - Men Travel'; ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    
    <style>
        /* Animation de fondu à l'entrée */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Animation pour les messages */
        @keyframes slideIn {
            from { opacity: 0; transform: translateY(-5px); }
            to { opacity: 1; transform: translateY(0); }
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            background-color: #f4f7f6;
            color: #333;
            margin: 0;
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 95vh;
        }

        .form-container {
            background-color: #ffffff;
            padding: 2.5rem;
            border-radius: 12px;
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.07);
            width: 100%;
            max-width: 450px;
            box-sizing: border-box;
            animation: fadeIn 0.6s ease-out;
        }

        .form-container h2 {
            text-align: center;
            color: #0056b3; /* Bleu Men Travel */
            margin-top: 0;
            margin-bottom: 1.5rem;
            font-weight: 700;
        }

        .form-group {
            margin-bottom: 1.25rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            font-size: 0.9rem;
            color: #555;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 0.85rem;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-sizing: border-box; /* Important */
            transition: border-color 0.3s, box-shadow 0.3s;
        }

        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #007bff;
            box-shadow: 0 0 8px rgba(0,123,255,0.25);
        }

        .form-button {
            width: 100%;
            padding: 0.9rem;
            border: none;
            border-radius: 8px;
            background-color: #007bff;
            color: white;
            font-size: 1rem;
            font-weight: 700;
            cursor: pointer;
            transition: background-color 0.3s, transform 0.1s;
        }

        .form-button:hover {
            background-color: #0056b3;
        }
        .form-button:active {
            transform: scale(0.98);
        }

        /* Messages d'erreur/succès */
        .message {
            padding: 0.9rem;
            border-radius: 8px;
            margin-bottom: 1rem;
            text-align: center;
            font-size: 0.9rem;
            animation: slideIn 0.3s ease-out;
        }
        .error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .form-link {
            text-align: center;
            margin-top: 1.5rem;
            display: block;
            color: #007bff;
            text-decoration: none;
            transition: color 0.3s;
        }
        .form-link:hover {
            color: #0056b3;
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <div class="form-container">
        
        <form action="/login" method="POST">
            <h2>Connexion Client</h2>

            <?php if (isset($_GET['error'])): ?>
                <p class="message error"><?= htmlspecialchars($_GET['error']) ?></p>
            <?php endif; ?>
            <?php if (isset($_GET['success'])): ?>
                <p class="message success"><?= htmlspecialchars($_GET['success']) ?></p>
            <?php endif; ?>

            <div class="form-group">
                <label for="email">Email :</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="mot_de_passe">Mot de passe :</label>
                <input type="password" id="mot_de_passe" name="mot_de_passe" required>
            </div>

            <button type="submit" class="form-button">Se connecter</button>
            <a href="/register" class="form-link">Pas de compte ? Inscrivez-vous</a>
        </form>

    </div></body>
</html>