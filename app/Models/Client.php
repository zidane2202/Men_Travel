<?php
// Fichier: app/models/Client.php
// Namespace correspondant au dossier (App\Models)
namespace App\Models;

// On importe la classe PDO
use PDO;
class Client {
    private $conn;
    private $table_name = "clients";

    // Propriétés du client
    public $id_client;
    public $nom;
    public $prenom;
    public $date_naissance;
    public $sexe;
    public $email;
    public $telephone;
    public $adresse;
    public $mot_de_passe;

public function __construct(PDO $db) { // <-- L'erreur vient de là
    $this->conn = $db;
    }

    /**
     * Vérifie si un email existe déjà dans la base de données.
     */
    public function emailExists() {
        $query = "SELECT id_client FROM " . $this->table_name . " WHERE email = :email LIMIT 0,1";
        
        $stmt = $this->conn->prepare($query);
        
        // "Sanitize" (nettoyer) l'email
        // CORRIGÉ : $this->email au lieu de $this.email
        $this->email = htmlspecialchars(strip_tags($this->email));
        $stmt->bindParam(':email', $this->email);
        
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            return true; // L'email existe
        }
        return false; // L'email n'existe pas
    }

    /**
     * Crée un nouveau client (Inscription).
     */
    public function register() {
        // 1. Vérifier si l'email existe déjà
        if ($this->emailExists()) {
            return false; // Échec, l'email est déjà pris
        }

        // 2. Requête d'insertion
        $query = "INSERT INTO " . $this->table_name . "
                  SET nom=:nom, prenom=:prenom, date_naissance=:date_naissance, sexe=:sexe, 
                      email=:email, telephone=:telephone, adresse=:adresse, mot_de_passe=:mot_de_passe";

        $stmt = $this->conn->prepare($query);

        // 3. Nettoyer les données (strip_tags et htmlspecialchars)
        // CORRIGÉ : $this->... au lieu de $this....
        $this->nom = htmlspecialchars(strip_tags($this->nom));
        $this->prenom = htmlspecialchars(strip_tags($this->prenom));
        $this->date_naissance = htmlspecialchars(strip_tags($this->date_naissance));
        $this->sexe = htmlspecialchars(strip_tags($this->sexe));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->telephone = htmlspecialchars(strip_tags($this->telephone));
        $this->adresse = htmlspecialchars(strip_tags($this->adresse));

        // 4. HACHAGE DU MOT DE PASSE
        // CORRIGÉ : $this->... au lieu de $this....
        $this->mot_de_passe = password_hash($this->mot_de_passe, PASSWORD_BCRYPT);

        // 5. Lier les paramètres
        $stmt->bindParam(':nom', $this->nom);
        $stmt->bindParam(':prenom', $this->prenom);
        $stmt->bindParam(':date_naissance', $this->date_naissance);
        $stmt->bindParam(':sexe', $this->sexe);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':telephone', $this->telephone);
        $stmt->bindParam(':adresse', $this->adresse);
        $stmt->bindParam(':mot_de_passe', $this->mot_de_passe);

        // 6. Exécuter la requête
        if ($stmt->execute()) {
            return true; // Succès
        }

        return false; // Échec
    }
    
    /**
     * Tente de connecter un client.
     */
    public function login() {
        // 1. Requête pour trouver l'utilisateur par email
        $query = "SELECT id_client, nom, prenom, mot_de_passe 
                  FROM " . $this->table_name . " 
                  WHERE email = :email 
                  LIMIT 0,1";
        
        $stmt = $this->conn->prepare($query);

        // 2. Nettoyer l'email
        // CORRIGÉ : $this->... au lieu de $this....
        $this->email = htmlspecialchars(strip_tags($this->email));
        $stmt->bindParam(':email', $this->email);

        // 3. Exécuter
        $stmt->execute();
        
        // 4. Vérifier si l'utilisateur existe
        if ($stmt->rowCount() == 1) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            // 5. VÉRIFIER LE MOT DE PASSE
            // CORRIGÉ : $this->... au lieu de $this....
            if (password_verify($this->mot_de_passe, $row['mot_de_passe'])) {
                // Le mot de passe est correct !
                // On stocke les infos de l'utilisateur dans l'objet
                // CORRIGÉ : $this->... au lieu de $this....
                $this->id_client = $row['id_client'];
                $this->nom = $row['nom'];
                $this->prenom = $row['prenom'];
                return true;
            }
        }
        
        // 6. Si l'email n'existe pas ou si le mdp est faux
        return false;
    }

    /**
     * Récupère les infos d'un client par son ID.
     */
    public function findById($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id_client = :id LIMIT 0,1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        if ($stmt->rowCount() == 1) {
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
        return null;
    }
    public function update($id, $nom, $prenom, $telephone, $adresse, $date_naissance) {
        
        $query = "UPDATE " . $this->table_name . "
                  SET 
                      nom = :nom, 
                      prenom = :prenom, 
                      telephone = :telephone, 
                      adresse = :adresse, 
                      date_naissance = :date_naissance
                  WHERE 
                      id_client = :id_client";

        $stmt = $this->conn->prepare($query);

        // Nettoyage des données
        $nom = htmlspecialchars(strip_tags($nom));
        $prenom = htmlspecialchars(strip_tags($prenom));
        $telephone = htmlspecialchars(strip_tags($telephone));
        $adresse = htmlspecialchars(strip_tags($adresse));
        $date_naissance = htmlspecialchars(strip_tags($date_naissance));
        $id = htmlspecialchars(strip_tags($id));

        // Liaison des paramètres
        $stmt->bindParam(':nom', $nom);
        $stmt->bindParam(':prenom', $prenom);
        $stmt->bindParam(':telephone', $telephone);
        $stmt->bindParam(':adresse', $adresse);
        $stmt->bindParam(':date_naissance', $date_naissance);
        $stmt->bindParam(':id_client', $id);

        // Exécution
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }
public function findAll() {
        $query = "SELECT 
                    id_client, nom, prenom, email, telephone, date_inscription
                FROM " . $this->table_name . "
                ORDER BY date_inscription DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
} // Fin de la classe Clien

?>