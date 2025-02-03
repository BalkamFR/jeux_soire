<?php

function getGameByUniqueKey($unique_key_game) {
    $pdo = connectDb();
    $stmt = $pdo->prepare("SELECT * FROM jeux_soiree_game WHERE unique_key_game = :unique_key_game");
    $stmt->execute(['unique_key_game' => $unique_key_game]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function createGame($unique_key_game, $name_game, $description_game, $date_game, $time_game, $location_game, $avatar_game) {
    $pdo = connectDb();
    $stmt = $pdo->prepare("INSERT INTO jeux_soiree_game (unique_key_game, name_game, description_game, date_game, time_game, location_game, avatar_game) VALUES (:unique_key_game, :name_game, :description_game, :date_game, :time_game, :location_game, :avatar_game)");
    $stmt->execute(['unique_key_game' => $unique_key_game, 'name_game' => $name_game, 'description_game' => $description_game, 'date_game' => $date_game, 'time_game' => $time_game, 'location_game' => $location_game, 'avatar_game' => $avatar_game]);
}


function getGameByUniqueKeyUser($unique_key_user) {
    $pdo = connectDb();
    $stmt = $pdo->prepare("SELECT * FROM jeux_soiree_player WHERE unique_key_user = :unique_key_user");
    $stmt->execute(['unique_key_user' => $unique_key_user]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function joinGame($name_player, $unique_key_user, $avatar_user) {
    $pdo = connectDb();
    $stmt = $pdo->prepare("INSERT INTO jeux_soiree_player (name_player, unique_key_user, avatar_user) VALUES (:name_player, :unique_key_user, :avatar_user)");
    $stmt->execute(['name_player' => $name_player, 'unique_key_user' => $unique_key_user, 'avatar_user' => $avatar_user]);
}