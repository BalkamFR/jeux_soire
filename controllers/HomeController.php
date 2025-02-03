<?php

function homeAction() {
    require __DIR__ . '/../views/home.php';
}

function rejoindreAction() {
    require __DIR__ . '/../views/rejoindre.php';
}



function game_all_player()
{
    require __DIR__ . '/../views/game_all_player.php';
}


function get_game_all_player() {
    $players = getAllPlayersFromUser($_SESSION['game_unique_key']);
    header('Content-Type: application/json');
    error_log('Players: ' . print_r($players, true));
    echo json_encode([
        'status' => 'success',
        'players' => $players
    ]);
    exit;
}



function rejoindreFormAction() {
    $unique_key_user = $_POST['unique_key_user'];
    $_SESSION['game_unique_key'] = $unique_key_user;
    $name_player2 = getnameUser($_SESSION['unique_key_user'])->fetch(PDO::FETCH_COLUMN);
    $score_player = getScoreUser($_SESSION['unique_key_user'])->fetch(PDO::FETCH_COLUMN);
    $avatar_user = getAvatarUser($_SESSION['unique_key_user'])->fetch(PDO::FETCH_COLUMN);

    joinGame($name_player2, $unique_key_user, $avatar_user);

    header('Location: game_all_player');
}

