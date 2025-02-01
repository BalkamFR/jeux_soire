<?php
// Récupération de l'URI de la requête
$request = $_SERVER['REQUEST_URI'];

// Simple système de routage selon la route
switch ($request) {
    case '/' :
        // Page d'accueil
        require __DIR__ . '/home.php';
        break;
    case '/home' :
        require __DIR__ . '/views/home.php';
        break;
    default:
        // Page non trouvée (404)
        http_response_code(404);
        require __DIR__ . '/views/404.php';
        break;
}
