<?php
session_start();
require_once 'config/config.php';
require_once 'models/UserModel.php';
require_once 'models/GameModel.php';
require_once 'controllers/UserController.php';
require_once 'core/Router.php';
require_once 'core/Database.php';
// Ajoutez d'autres require_once selon les besoins

$router = new Router();
$router->dispatch($_SERVER['REQUEST_URI']);

// Simple routage basé sur l'URL
$request = $_SERVER['REQUEST_URI'];

switch($request){
    case '/' :
        require __DIR__ . '/views/home.php';
        break;
    case '/register' :
        $userController = new UserController();
        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $userController->register($_POST);
        }
        require __DIR__ . '/views/register.php';
        break;
    // Ajoutez d'autres routes selon les besoins
    default:
        http_response_code(404);
        require __DIR__ . '/views/404.php';
        break;
}
?> 