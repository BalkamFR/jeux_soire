CREATE DATABASE jeux_soire;

CREATE TABLE jeux_soiree_user (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    unique_key_user VARCHAR(255) UNIQUE NOT NULL,
    name_user VARCHAR(100) NOT NULL,
    email_user VARCHAR(255) UNIQUE NOT NULL,
    avatar_user VARCHAR(255),
    password_user VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE jeux_soiree_name_game (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    name_game VARCHAR(100) NOT NULL
);

CREATE TABLE jeux_soiree_player (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    unique_key_user VARCHAR(255),
    name_player VARCHAR(100) NOT NULL,
    FOREIGN KEY (unique_key_user) REFERENCES jeux_soiree_user(unique_key_user)
);

CREATE TABLE jeux_soiree_score (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    unique_key_user VARCHAR(255),
    name_player VARCHAR(100) NOT NULL,
    score_player INT NOT NULL,
    score_game INT NOT NULL,
    FOREIGN KEY (unique_key_user) REFERENCES jeux_soiree_user(unique_key_user)
);

CREATE TABLE jeux_soiree_alcool_drink (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    unique_key_alcool_drink VARCHAR(255) UNIQUE NOT NULL,
    name_alcool_drink VARCHAR(100) NOT NULL,
    alcohol_percentage DECIMAL(4,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

