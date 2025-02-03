<?php
// Récupération de l'URI de la requête
session_start();
require __DIR__ . '/config/config.php';
$request = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$conn = connectDb();

require __DIR__ . '/models/UserModel.php';
require __DIR__ . '/models/GameModel.php';
require __DIR__ . '/controllers/UserController.php';
require __DIR__ . '/controllers/UndercoverController.php';
require __DIR__ . '/controllers/AuthController.php';
require __DIR__ . '/controllers/HomeController.php';
require __DIR__ . '/controllers/RouletteController.php';

// Middleware d'authentification
function checkAuth() {
    if (!isset($_SESSION['unique_key_user'])) {
        header('Location: /login');
        exit;
    }
}

// Routes publiques (pas besoin d'authentification)
$public_routes = ['/login', '/register', '/logout'];

// Vérifier si la route actuelle nécessite une authentification
if (!in_array($request, $public_routes)) {
    checkAuth();
}

// Simple système de routage selon la route
switch ($request) {
    case '/' :
        homeAction();
        break;    
    case '/undercover' :
        UndercoverView();
        break;
    case '/start_undercover' :
        startUndercoverAction();
        break;
    case '/login':
        loginAction();
        break;
    case '/get_players':
        getPlayersAction();
        break;    
    case '/register':
        registerAction();
        break;
    case '/home':
        homeAction();
        break;    
    case '/add_player':
        addPlayerAction();
        break;
    case '/groupe_player':
        groupe_player();
        break;
    case '/get_game_all_player':
        get_game_all_player();
        break;    
    case '/logout':
        logoutAction();
        break;
    case '/game_all_player':
        game_all_player();
        break;
    case '/game':
        UserController();
        break;
    case '/rejoindre':
        rejoindreAction();
        break;
    case '/rejoindre_form':
        rejoindreFormAction();
        break;
    case '/roulette':
        rouletteView();
        break;
    case '/delete_player':
        deletePlayerAction();
        break;
    default:
        // Page non trouvée (404)
        http_response_code(404);
        require __DIR__ . '/views/404.php';
        break;
}
