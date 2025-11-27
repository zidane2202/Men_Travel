<?php
// Fichier: server.php (à la racine)
require __DIR__ . '/vendor/autoload.php'; 

// ... le reste de votre code ...

// Maintenant ça va marcher :
$router = new \Bramus\Router\Router();
$uri = urldecode(
    parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? ''
);

// Gère les fichiers statiques (CSS, JS, Images dans /public)
// Si vous n'avez pas de dossier /public, cette partie peut être ignorée,
// mais elle est cruciale pour l'avenir.
if ($uri !== '/' && file_exists(__DIR__ . '/public' . $uri)) {
    return false; // Sert le fichier statique
}

// Gère les fichiers de la racine (ex: /favicon.ico)
if (file_exists(__DIR__ . $uri)) {
    return false; // Sert le fichier statique
}

// Toutes les autres requêtes vont au routeur
require_once __DIR__ . '/index.php';
