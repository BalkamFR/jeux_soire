<?php


function getAllPlayersforUser($unique_key_user) {
    $pdo = connectDb();

    // Préparer et exécuter la requête SQL
    $stmt = $pdo->prepare("SELECT * FROM jeux_soiree_user WHERE unique_key_user = :unique_key_user");
    $stmt->execute(['unique_key_user' => $unique_key_user]);

    // Récupérer les résultats
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    return $result;
}

function getnameUser($unique_key_user) {
    $pdo = connectDb();
    $stmt = $pdo->prepare("SELECT name_user FROM jeux_soiree_user WHERE unique_key_user = :unique_key_user");
    $stmt->execute(['unique_key_user' => $unique_key_user]);
    return $stmt->fetch(PDO::FETCH_COLUMN);
}
function getAvatarUser($unique_key_user) {
    $pdo = connectDb();
    $stmt = $pdo->prepare("SELECT avatar_user FROM jeux_soiree_user WHERE unique_key_user = :unique_key_user");
    $stmt->execute(['unique_key_user' => $unique_key_user]);
    return $stmt;
}

function getScoreUser($unique_key_user) {
    $pdo = connectDb();
    $stmt = $pdo->prepare("SELECT score_user FROM jeux_soiree_user WHERE unique_key_user = :unique_key_user");
    $stmt->execute(['unique_key_user' => $unique_key_user]);
    return $stmt;
}


function getUserByUniqueKey($unique_key_user) {
    $pdo = connectDb();
    $stmt = $pdo->prepare("SELECT * FROM jeux_soiree_user WHERE unique_key_user = :unique_key_user");
    $stmt->execute(['unique_key_user' => $unique_key_user]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function connexionUser($unique_key_user, $password_user) {
    $pdo = connectDb();
    $stmt = $pdo->prepare("SELECT * FROM jeux_soiree_user WHERE unique_key_user = :unique_key_user AND password_user = :password_user");
    $stmt->execute(['unique_key_user' => $unique_key_user, 'password_user' => $password_user]);
    if ($stmt->rowCount() > 0) {
        return true;
    } else {
        return false;
    }
}

function createUser($unique_key_user, $name_user, $email_user, $avatar_user, $password_user) {
    $pdo = connectDb();
    $stmt = $pdo->prepare("INSERT INTO jeux_soiree_user (unique_key_user, name_user, email_user, avatar_user, password_user) VALUES (:unique_key_user, :name_user, :email_user, :avatar_user, :password_user)");
    $stmt->execute(['unique_key_user' => $unique_key_user, 'name_user' => $name_user, 'email_user' => $email_user, 'avatar_user' => $avatar_user, 'password_user' => $password_user]);
}

function updateUser($unique_key_user, $name_user, $email_user, $avatar_user, $password_user) {
    $pdo = connectDb();
    $stmt = $pdo->prepare("UPDATE jeux_soiree_user SET name_user = :name_user, email_user = :email_user, avatar_user = :avatar_user, password_user = :password_user WHERE unique_key_user = :unique_key_user");
    $stmt->execute(['unique_key_user' => $unique_key_user, 'name_user' => $name_user, 'email_user' => $email_user, 'avatar_user' => $avatar_user, 'password_user' => $password_user]);
}

function deleteUser($unique_key_user) {
    $pdo = connectDb();
    $stmt = $pdo->prepare("DELETE FROM jeux_soiree_user WHERE unique_key_user = :unique_key_user");
    $stmt->execute(['unique_key_user' => $unique_key_user]);
}

function getAllUsers() {
    $pdo = connectDb();
    $stmt = $pdo->prepare("SELECT * FROM jeux_soiree_user");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function createPlayer($unique_key_user, $name_player, $score_player) {
    $pdo = connectDb();
    $stmt = $pdo->prepare("INSERT INTO jeux_soiree_player (unique_key_user, name_player, score_player) VALUES (:unique_key_user, :name_player, :score_player)");
    $stmt->execute(['unique_key_user' => $unique_key_user, 'name_player' => $name_player, 'score_player' => $score_player]);
}

function getPlayerByUniqueKey($unique_key_user) {
    $pdo = connectDb();
    $stmt = $pdo->prepare("SELECT * FROM jeux_soiree_player WHERE unique_key_user = :unique_key_user");
    $stmt->execute(['unique_key_user' => $unique_key_user]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function getAllPlayersFromUser($unique_key_user) {
    $pdo = connectDb();
    $stmt = $pdo->prepare("SELECT * FROM jeux_soiree_player WHERE unique_key_user = :unique_key_user");
    $stmt->execute(['unique_key_user' => $unique_key_user]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function updatePlayer($unique_key_user, $name_player, $score_player) {
    $pdo = connectDb();
    $stmt = $pdo->prepare("UPDATE jeux_soiree_player SET name_player = :name_player, score_player = :score_player WHERE unique_key_user = :unique_key_user");
    $stmt->execute(['unique_key_user' => $unique_key_user, 'name_player' => $name_player, 'score_player' => $score_player]);
}

function deleteAllPlayer($unique_key_user) {
    $pdo = connectDb();
    $stmt = $pdo->prepare("DELETE FROM jeux_soiree_player WHERE unique_key_user = :unique_key_user");
    $stmt->execute(['unique_key_user' => $unique_key_user]);
}

function deletePlayer($unique_key_user, $name_player) {
    $pdo = connectDb();
    $stmt = $pdo->prepare("DELETE FROM jeux_soiree_player WHERE unique_key_user = :unique_key_user AND name_player = :name_player");
    $stmt->execute(['unique_key_user' => $unique_key_user, 'name_player' => $name_player]);
}

function getAllPlayers() {
    $pdo = connectDb();
    $stmt = $pdo->prepare("SELECT * FROM jeux_soiree_player");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


