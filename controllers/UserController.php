<?php

function UserController() {
    $players = getAllPlayersFromUser($_SESSION['unique_key_user']);
    require __DIR__ . '/../views/add_player.php';
}

function deletePlayerAction() {
    if($_GET['name_player'] != getnameUser($_SESSION['unique_key_user']))
    {
        deletePlayer($_SESSION['unique_key_user'], $_GET['name_player']);
        $players = getAllPlayersFromUser($_SESSION['unique_key_user']);
        header('Content-Type: application/json');
        echo json_encode([
             'status'  => 'success',
             'players' => $players
        ]);
    }
    exit;
}

function groupe_player()
{
    require __DIR__ . '/../views/add_player.php';
}

function addPlayerAction() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $unique_key_user = $_SESSION['unique_key_user'];
        $name_player = $_POST['name_player'] ?? '';
        $score_player = 0;
        
        try {
            createPlayer($unique_key_user, $name_player, $score_player);
            $players = getAllPlayersFromUser($_SESSION['unique_key_user']);
            
            header('Content-Type: application/json');
            echo json_encode([
                'status' => 'success',
                'players' => $players
            ]);
            exit;
        } catch (Exception $e) {
            header('Content-Type: application/json');
            echo json_encode([
                'status' => 'error',
                'message' => "Erreur lors de l'ajout du joueur"
            ]);
            exit;
        }
    }
}

function getPlayersAction() {
    $players = getAllPlayersFromUser($_SESSION['unique_key_user']);
    header('Content-Type: application/json');
    error_log('Players: ' . print_r($players, true));
    echo json_encode([
        'status' => 'success',
        'players' => $players
    ]);
    exit;
}

