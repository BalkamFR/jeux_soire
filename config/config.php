<?php

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'jeux_soire');

/**
 * Fonction pour établir une connexion à la base de données avec PDO
 *
 * @return PDO Connexion à la base de données
 */
function connectDb() {
    $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8';
    try {
        $pdo = new PDO($dsn, DB_USER, DB_PASS);
        // Activer les exceptions en cas d'erreur
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch (PDOException $e) {
        die("Connexion échouée: " . $e->getMessage());
    }
}