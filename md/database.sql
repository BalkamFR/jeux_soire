CREATE DATABASE jeux_soire;

-- Création de la table `jeux_soiree_user`
CREATE TABLE jeux_soiree_user (
    id INT PRIMARY KEY AUTO_INCREMENT,
    unique_key_user VARCHAR(255) UNIQUE NOT NULL,
    score_user INT DEFAULT 0,
    name_user VARCHAR(100) NOT NULL,
    email_user VARCHAR(100) UNIQUE NOT NULL,
    avatar_user VARCHAR(255),
    password_user VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Création de la table `jeux_soiree_name_game`
CREATE TABLE jeux_soiree_name_game (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name_game VARCHAR(100) NOT NULL
);

-- Création de la table `jeux_soiree_player`
CREATE TABLE jeux_soiree_player (
    id INT PRIMARY KEY AUTO_INCREMENT,
    unique_key_user VARCHAR(255),
    avatar_user VARCHAR(255)
    name_player VARCHAR(100) NOT NULL,
    score_player INT DEFAULT 0,
    FOREIGN KEY (unique_key_user) REFERENCES jeux_soiree_user(unique_key_user)
);

-- Création de la table `jeux_soiree_score`
CREATE TABLE jeux_soiree_score (
    id INT PRIMARY KEY AUTO_INCREMENT,
    unique_key_user VARCHAR(255),
    name_player VARCHAR(100) NOT NULL,
    score_game INT DEFAULT 0,
    FOREIGN KEY (unique_key_user) REFERENCES jeux_soiree_user(unique_key_user)
);

-- Création de la table `jeux_soiree_alcool_drink`
CREATE TABLE jeux_soiree_alcool_drink (
    id INT PRIMARY KEY AUTO_INCREMENT,
    unique_key_alcool_drink VARCHAR(255) UNIQUE NOT NULL,
    name_alcool_drink VARCHAR(100) NOT NULL,
    alcohol_percentage DECIMAL(5,2),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Création de la table `jeux_soiree_game_session`
CREATE TABLE jeux_soiree_game_session (
    id INT PRIMARY KEY AUTO_INCREMENT,
    game_id INT,
    name_game VARCHAR(100) NOT NULL,
    unique_key_user VARCHAR(255),
    place_game_max INT,
    place_game_current INT DEFAULT 0,
    status ENUM('en cours', 'terminé') DEFAULT 'en cours',
    start_time DATETIME,
    end_time DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (game_id) REFERENCES jeux_soiree_name_game(id),
    FOREIGN KEY (unique_key_user) REFERENCES jeux_soiree_user(unique_key_user)
);

-- Création de la table `jeux_soiree_undercover`
CREATE TABLE jeux_soiree_undercover (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_create_id INT,
    word_undercover VARCHAR(100) NOT NULL,
    word_dif VARCHAR(100),
    word_theme VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_create_id) REFERENCES jeux_soiree_user(id)
);

-- Création de la table `jeux_soiree_undercover_game`
CREATE TABLE jeux_soiree_undercover_game (
    id INT PRIMARY KEY AUTO_INCREMENT,
    unique_key_user VARCHAR(255),
    word_undercover VARCHAR(200) NOT NULL,
    name_player VARCHAR(100) NOT NULL,
    status ENUM('actif', 'inactif') DEFAULT 'actif',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (unique_key_user) REFERENCES jeux_soiree_user(unique_key_user)
);

-- Création de la table pour les votes Undercover
CREATE TABLE jeux_soiree_undercover_votes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    unique_key_user VARCHAR(255),
    game_id INT,
    voter_name VARCHAR(100) NOT NULL,
    voted_player VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (unique_key_user) REFERENCES jeux_soiree_user(unique_key_user)
);

-- Ajouter cette table pour gérer le statut du jeu
CREATE TABLE jeux_soiree_game_status (
    id INT PRIMARY KEY AUTO_INCREMENT,
    unique_key_user VARCHAR(255),
    current_turn INT DEFAULT 0,
    game_phase ENUM('reveal', 'vote') DEFAULT 'reveal',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (unique_key_user) REFERENCES jeux_soiree_user(unique_key_user)
);

CREATE TABLE IF NOT EXISTS roulette_history (
    id INT AUTO_INCREMENT PRIMARY KEY,
    unique_key_user VARCHAR(255) NOT NULL,
    number INT NOT NULL,
    color VARCHAR(10) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_user (unique_key_user)
);