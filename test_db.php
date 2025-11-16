<?php
// Fichier: test_db.php
require_once 'app/core/Database.php';

$database = new Database();
$db = $database->getConnection();

if ($db) {
    echo "Succès ! Connecté à la base de données 'men_travel'.";
} else {
    echo "Échec de la connexion.";
}
?>