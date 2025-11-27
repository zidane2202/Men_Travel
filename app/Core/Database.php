<?php
// Fichier: app/core/Database.php
// Namespace correspondant au dossier (App\Core)
namespace App\Core;

// On importe la classe PDO du PHP global
use PDO;
use PDOException;
class Database {
    // Paramètres de connexion (À adapter si vous avez un mot de passe sur WAMP)
    private $host = "localhost";
    private $db_name = "men_travel";
    private $username = "root";
    private $password = "12345678"; // Laissez vide si vous n'avez pas mis de mot de passe sur WAMP
    public $conn;

    // Méthode pour obtenir la connexion
    public function getConnection() {
        $this->conn = null;

        try {
            // On essaie de se connecter avec PDO
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name, 
                $this->username, 
                $this->password
            );
            
            // Configuration des options PDO pour une meilleure gestion des erreurs
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            // On s'assure que les échanges se font en UTF-8 (pour les accents)
            $this->conn->exec("set names utf8");
            
        } catch(PDOException $exception) {
            // En cas d'erreur, on arrête tout et on affiche le message
            echo "Erreur de connexion : " . $exception->getMessage();
        }

        return $this->conn;
    }
}
?>