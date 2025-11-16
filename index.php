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
// 3. Initialiser le routeur
$router = new \Bramus\Router\Router();

// --- DÉFINITION DES ROUTES ---
// C'est la partie la plus importante.
// Nous définissons des routes GET AVANT de lancer run().

// Page d'accueil
$router->get('/', function() {
    header('Location: /login');
    exit();
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
// 4. Lancer le routeur (maintenant qu'il connaît les routes GET)
$router->run();