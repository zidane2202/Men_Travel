<?php
// Fichier: index.php (à la racine)
session_start();

// 1. Charger l'autoloader (ne change pas)
require_once __DIR__ . '/vendor/autoload.php';

// 2. Importer les classes (grâce à l'autoloading PSR-4)
use App\Controllers\ClientController;
use App\Controllers\VoyageController;
use App\Controllers\TicketController;
use App\Controllers\PaiementController; // <-- AJOUTEZ CETTE LIGNE
use App\Controllers\ReservationController; // <-- AJOUTEZ CETTE LIGNE
use App\Controllers\AdminController; // <-- AJOUTEZ CETTE LIGNE
use App\Controllers\PagesController; // <-- AJOUTEZ CETTE LIGNE
use App\Controllers\AdminVoyageController; // <-- AJOUTEZ CETTE LIGNE
use App\Controllers\AdminPersonnelController; // <-- AJOUTEZ CETTE LIGNE
// 3. Initialiser le routeur
$router = new \Bramus\Router\Router();

// --- DÉFINITION DES ROUTES ---
// C'est la partie la plus importante.
// Nous définissons des routes GET AVANT de lancer run().

// Page d'accueil
$router->get('/', function() {
    (new PagesController())->showHomePage();
});

// -- Routes d'inscription --
$router->get('/register', function() {
    (new ClientController())->showRegisterPage();
});
$router->post('/register', function() {
    (new ClientController())->handleRegister();
});

// -- Routes de connexion --
$router->get('/login', function() {
    (new ClientController())->showLoginPage();
});
$router->post('/login', function() {
    (new ClientController())->handleLogin();
});

// -- Route de déconnexion --
$router->get('/logout', function() {
    (new ClientController())->handleLogout();
});

// -- Route du tableau de bord (protégée) --
$router->get('/dashboard', function() {
    (new ClientController())->showDashboard();
});

// -- Route "Mes réservations" --
$router->get('/my-reservations', function() {
    (new App\Controllers\ClientController())->showMyReservations();
});

// -- Route "Mon profil" --
$router->get('/profile', function() {
    (new App\Controllers\ClientController())->showProfile();
});
// --- FIN DES ROUTES ---
// AJOUTEZ CETTE NOUVELLE ROUTE POUR LE FORMULAIRE
$router->post('/profile/update', function() {
    (new App\Controllers\ClientController())->handleProfileUpdate();
});

$router->get('/reservation/select-seat/(\d+)', function($id_voyage) {
    (new ReservationController())->showSeatSelection($id_voyage);
});

// Traite le formulaire de création de réservation
$router->post('/reservation/create', function() {
    (new ReservationController())->create();
});

$router->get('/payment/show/(\d+)', function($id_reservation) {
    (new PaiementController())->show($id_reservation);
});

$router->post('/payment/process', function() {
    (new PaiementController())->process();
});
$router->get('/search', function() {
    (new VoyageController())->showSearchPage();
});

$router->get('/payment/show/(\d+)', function($id_reservation) {
    (new PaiementController())->show($id_reservation);
});

// Traite la simulation de paiement
$router->post('/payment/process', function() {
    (new PaiementController())->process();
});

$router->get('/ticket/view/(\d+)', function($id_reservation) {
    (new TicketController())->show($id_reservation);
});




// Ce "filtre" s'exécute AVANT toutes les routes qui commencent par /admin/
$router->before('GET|POST', '/admin/.*', function() {
    // Si la route est /admin/login, on laisse passer
    if (strpos($_SERVER['REQUEST_URI'], '/admin/login') !== false) {
        return; // Ne pas bloquer la page de login
    }
    
    // Si la session admin n'existe pas, on bloque
    if (!isset($_SESSION['admin_id'])) {
        header('Location: /admin/login?error=Accès refusé. Veuillez vous connecter.');
        exit();
    }
});

// Routes de Login Admin
$router->get('/admin/login', function() {
    (new AdminController())->showLoginPage();
});
$router->post('/admin/login', function() {
    (new AdminController())->handleLogin();
});

// Routes du Dashboard Admin (protégées par le filtre ci-dessus)
$router->get('/admin/dashboard', function() {
    (new AdminController())->showDashboard();
});
$router->get('/admin/logout', function() {
    (new AdminController())->handleLogout();
});

// Routes Voyages (List & Create)
$router->get('/admin/voyages', function() {
    (new AdminVoyageController())->listVoyages();
});
$router->get('/admin/voyages/create', function() {
    (new AdminVoyageController())->showCreateForm();
});
$router->post('/admin/voyages/store', function() {
    (new AdminVoyageController())->storeVoyage();
});


// Route Manifeste (Passagers)
$router->get('/admin/voyages/manifest/(\d+)', function($id_voyage) { // <-- AJOUTEZ/VÉRIFIEZ CETTE LIGNE
    (new AdminVoyageController())->showManifest($id_voyage);
});
$router->get('/admin/voyages/edit/(\d+)', function($id_voyage) {
    (new AdminVoyageController())->showEditForm($id_voyage);
});
// Route pour traiter la mise à jour (POST)
$router->post('/admin/voyages/update', function() {
    (new AdminVoyageController())->updateVoyage();
});
// Route pour traiter la suppression
$router->post('/admin/voyages/delete', function() {
    (new AdminVoyageController())->deleteVoyage();
});




// Routes Gestion Personnel
$router->get('/admin/employes', function() {
    (new AdminPersonnelController())->listEmployes();
});
$router->get('/admin/employes/create', function() {
    (new AdminPersonnelController())->showCreateForm();
});
$router->post('/admin/employes/store', function() {
    (new AdminPersonnelController())->storeEmploye();
});
$router->get('/admin/employes/edit/(\d+)', function($id_employe) {
    (new AdminPersonnelController())->showEditForm($id_employe);
});
$router->post('/admin/employes/update', function() {
    (new AdminPersonnelController())->updateEmploye();
});
// 4. Lancer le routeur (maintenant qu'il connaît les routes GET)
$router->run();