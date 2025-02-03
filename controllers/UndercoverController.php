<?php

// Ajouter cette ligne au début du fichier pour inclure le modèle
require_once __DIR__ . '/../models/CreateUndercover.php';

// $_SESSION['unique_key_user'] = "1234567890";


function UndercoverView() {
    global $conn;
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $unique_key_user = $_SESSION['unique_key_user'];
        
        if (isset($_POST['start_undercover'])) {
            resetGame($unique_key_user);
            initGameStatus($unique_key_user);
            createUndercover($unique_key_user);
            header('Location: /undercover');
            exit();
        } elseif (isset($_POST['next_player'])) {
            incrementTurn($unique_key_user);
            header('Location: /undercover');
            exit();
        } elseif (isset($_POST['vote'])) {
            submitVote($unique_key_user, $_POST['voter'], $_POST['voted_player']);
            incrementVoteTurn($unique_key_user);
            header('Location: /undercover');
            exit();
        } elseif (isset($_POST['next_round'])) {
            resetForNextRound($unique_key_user);
            header('Location: /undercover');
            exit();
        }
    }
    
    require __DIR__ . '/../views/undercover.php';
}

// Nouvelle fonction pour réinitialiser la partie
function resetGame($unique_key_user) {
    global $conn;
    
    try {
        // Supprimer les anciens votes
        $sql_delete_votes = "DELETE FROM jeux_soiree_undercover_votes WHERE unique_key_user = :unique_key_user";
        $stmt = $conn->prepare($sql_delete_votes);
        $stmt->execute([':unique_key_user' => $unique_key_user]);
        
        // Supprimer les anciennes parties
        $sql_delete_game = "DELETE FROM jeux_soiree_undercover_game WHERE unique_key_user = :unique_key_user";
        $stmt = $conn->prepare($sql_delete_game);
        $stmt->execute([':unique_key_user' => $unique_key_user]);
        
        return true;
    } catch (PDOException $e) {
        return false;
    }
}

// Nouvelle fonction pour vérifier si un joueur a déjà voté
function hasUserVoted($unique_key_user, $voter_name) {
    global $conn;
    
    $sql = "SELECT COUNT(*) as vote_count 
            FROM jeux_soiree_undercover_votes 
            WHERE unique_key_user = :unique_key_user 
            AND voter_name = :voter_name";
    
    $stmt = $conn->prepare($sql);
    $stmt->execute([
        ':unique_key_user' => $unique_key_user,
        ':voter_name' => $voter_name
    ]);
    
    return $stmt->fetch(PDO::FETCH_ASSOC)['vote_count'] > 0;
}

// Fonction pour obtenir les détails des votes
function getVoteDetails($unique_key_user) {
    global $conn;
    
    try {
        $sql = "SELECT v.voted_player, 
                       COUNT(*) as vote_count,
                       GROUP_CONCAT(v.voter_name) as voters
                FROM jeux_soiree_undercover_votes v
                WHERE v.unique_key_user = :unique_key_user
                GROUP BY v.voted_player";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([':unique_key_user' => $unique_key_user]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
        
    } catch (PDOException $e) {
        error_log("Erreur getVoteDetails: " . $e->getMessage());
        return [];
    }
}

// Nouvelle fonction pour désactiver les anciennes parties
function deactivateOldGames($unique_key_user) {
    global $conn;
    
    $sql = "UPDATE jeux_soiree_undercover_game 
            SET status = 'inactif' 
            WHERE unique_key_user = :unique_key_user 
            AND status = 'actif'";
            
    $stmt = $conn->prepare($sql);
    $stmt->execute([':unique_key_user' => $unique_key_user]);
}

function getUndercoverWords() {
    if (!isset($_SESSION['unique_key_user'])) {
        return [];
    }
    
    $unique_key_user = $_SESSION['unique_key_user'];
    global $conn;
    
    $sql = "SELECT name_player, word_undercover, status 
            FROM jeux_soiree_undercover_game 
            WHERE unique_key_user = :unique_key_user";
    
    $stmt = $conn->prepare($sql);
    $stmt->execute([':unique_key_user' => $unique_key_user]);
    
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Fonction pour soumettre un vote
function submitVote($unique_key_user, $voter, $voted_player) {
    global $conn;
    
    try {
        $sql = "INSERT INTO jeux_soiree_undercover_votes 
                (unique_key_user, voter_name, voted_player) 
                VALUES (:unique_key_user, :voter_name, :voted_player)";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':unique_key_user' => $unique_key_user,
            ':voter_name' => $voter,
            ':voted_player' => $voted_player
        ]);
        
        // Vérifier si assez de votes pour éliminer
        checkPlayerElimination($unique_key_user, $voted_player);
        
        return true;
    } catch (PDOException $e) {
        error_log("Erreur submitVote: " . $e->getMessage());
        return false;
    }
}

function checkPlayerElimination($unique_key_user, $voted_player) {
    global $conn;
    
    try {
        // Compter le nombre total de joueurs actifs
        $sql_active = "SELECT COUNT(*) as active_count 
                      FROM jeux_soiree_undercover_game 
                      WHERE unique_key_user = :unique_key_user 
                      AND status = 'actif'";
        
        $stmt = $conn->prepare($sql_active);
        $stmt->execute([':unique_key_user' => $unique_key_user]);
        $active_count = $stmt->fetch(PDO::FETCH_ASSOC)['active_count'];
        
        // Compter les votes pour le joueur
        $sql_votes = "SELECT COUNT(*) as vote_count 
                     FROM jeux_soiree_undercover_votes 
                     WHERE unique_key_user = :unique_key_user 
                     AND voted_player = :voted_player";
        
        $stmt = $conn->prepare($sql_votes);
        $stmt->execute([
            ':unique_key_user' => $unique_key_user,
            ':voted_player' => $voted_player
        ]);
        $vote_count = $stmt->fetch(PDO::FETCH_ASSOC)['vote_count'];
        
        // Si plus de la moitié des joueurs ont voté pour ce joueur
        if ($vote_count > ($active_count / 2)) {
            // Éliminer le joueur
            $sql_eliminate = "UPDATE jeux_soiree_undercover_game 
                            SET status = 'inactif' 
                            WHERE unique_key_user = :unique_key_user 
                            AND name_player = :player_name";
            
            $stmt = $conn->prepare($sql_eliminate);
            $stmt->execute([
                ':unique_key_user' => $unique_key_user,
                ':player_name' => $voted_player
            ]);
            
            // Vérifier si la partie est terminée
            checkGameEnd($unique_key_user);
        }
        
        return true;
    } catch (PDOException $e) {
        error_log("Erreur checkPlayerElimination: " . $e->getMessage());
        return false;
    }
}

function checkGameEnd($unique_key_user) {
    global $conn;
    
    try {
        // Récupérer les joueurs actifs
        $sql = "SELECT g.name_player, g.word_undercover, u.word_dif 
                FROM jeux_soiree_undercover_game g
                JOIN jeux_soiree_undercover u ON g.word_undercover = u.word_undercover 
                WHERE g.unique_key_user = :unique_key_user 
                AND g.status = 'actif'";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([':unique_key_user' => $unique_key_user]);
        $active_players = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (count($active_players) <= 1) {
            endGame($unique_key_user, 'undercover');
            return true;
        }
        
        // Vérifier si tous les joueurs restants ont le même mot
        $first_word = $active_players[0]['word_undercover'];
        $all_same = true;
        foreach ($active_players as $player) {
            if ($player['word_undercover'] !== $first_word) {
                $all_same = false;
                break;
            }
        }
        
        if ($all_same) {
            if ($first_word === $active_players[0]['word_dif']) {
                endGame($unique_key_user, 'undercover');
            } else {
                endGame($unique_key_user, 'citizens');
            }
            return true;
        }
        
        return false;
    } catch (PDOException $e) {
        error_log("Erreur checkGameEnd: " . $e->getMessage());
        return false;
    }
}

function endGame($unique_key_user, $winner) {
    global $conn;
    
    try {
        // Mettre à jour le statut de la partie
        $sql = "UPDATE jeux_soiree_game_status 
                SET game_phase = 'ended', 
                    winner = :winner 
                WHERE unique_key_user = :unique_key_user";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':unique_key_user' => $unique_key_user,
            ':winner' => $winner
        ]);
        
        // Désactiver tous les joueurs
        $sql = "UPDATE jeux_soiree_undercover_game 
                SET status = 'inactif' 
                WHERE unique_key_user = :unique_key_user";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([':unique_key_user' => $unique_key_user]);
        
        return true;
    } catch (PDOException $e) {
        error_log("Erreur endGame: " . $e->getMessage());
        return false;
    }
}

// Fonction pour obtenir les votes actuels
function getCurrentVotes($unique_key_user) {
    global $conn;
    
    $sql = "SELECT voted_player, COUNT(*) as vote_count 
            FROM jeux_soiree_undercover_votes 
            WHERE unique_key_user = :unique_key_user 
            GROUP BY voted_player";
    
    $stmt = $conn->prepare($sql);
    $stmt->execute([':unique_key_user' => $unique_key_user]);
    
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function initGameStatus($unique_key_user) {
    global $conn;
    
    try {
        // D'abord, supprimer l'ancien statut s'il existe
        $sql_delete = "DELETE FROM jeux_soiree_game_status WHERE unique_key_user = :unique_key_user";
        $stmt = $conn->prepare($sql_delete);
        $stmt->execute([':unique_key_user' => $unique_key_user]);
        
        // Insérer le nouveau statut
        $sql_insert = "INSERT INTO jeux_soiree_game_status 
                      (unique_key_user, current_turn, game_phase) 
                      VALUES (:unique_key_user, 0, 'reveal')";
        $stmt = $conn->prepare($sql_insert);
        $stmt->execute([':unique_key_user' => $unique_key_user]);
        
        return true;
    } catch (PDOException $e) {
        error_log("Erreur initGameStatus: " . $e->getMessage());
        return false;
    }
}

function getCurrentTurn($unique_key_user) {
    global $conn;
    
    try {
        $sql = "SELECT current_turn, game_phase 
                FROM jeux_soiree_game_status 
                WHERE unique_key_user = :unique_key_user";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([':unique_key_user' => $unique_key_user]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$result) {
            initGameStatus($unique_key_user);
            return ['current_turn' => 0, 'game_phase' => 'reveal'];
        }
        
        return $result;
    } catch (PDOException $e) {
        error_log("Erreur getCurrentTurn: " . $e->getMessage());
        return ['current_turn' => 0, 'game_phase' => 'reveal'];
    }
}

function incrementTurn($unique_key_user) {
    global $conn;
    
    try {
        $current = getCurrentTurn($unique_key_user);
        $players = getUndercoverWords();
        
        if (empty($players)) {
            return false;
        }
        
        $next_turn = ($current['current_turn'] + 1) % count($players);
        
        // Si on a fait le tour de tous les joueurs
        if ($next_turn === 0) {
            // Passer à la phase de vote
            $sql = "UPDATE jeux_soiree_game_status 
                    SET current_turn = 0, 
                    game_phase = 'vote' 
                    WHERE unique_key_user = :unique_key_user";
        } else {
            // Continuer avec le joueur suivant
            $sql = "UPDATE jeux_soiree_game_status 
                    SET current_turn = :turn 
                    WHERE unique_key_user = :unique_key_user";
        }
        
        $stmt = $conn->prepare($sql);
        $params = [':unique_key_user' => $unique_key_user];
        if ($next_turn !== 0) {
            $params[':turn'] = $next_turn;
        }
        $stmt->execute($params);
        
        return true;
    } catch (PDOException $e) {
        error_log("Erreur incrementTurn: " . $e->getMessage());
        return false;
    }
}

function setCurrentTurn($unique_key_user, $turn) {
    global $conn;
    
    try {
        $sql = "UPDATE jeux_soiree_game_status 
                SET current_turn = :turn 
                WHERE unique_key_user = :unique_key_user";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':turn' => $turn,
            ':unique_key_user' => $unique_key_user
        ]);
        
        return true;
    } catch (PDOException $e) {
        error_log("Erreur setCurrentTurn: " . $e->getMessage());
        return false;
    }
}

function incrementVoteTurn($unique_key_user) {
    global $conn;
    
    try {
        $current = getCurrentTurn($unique_key_user);
        $active_players = array_filter(getUndercoverWords(), function($player) {
            return isset($player['status']) && $player['status'] === 'actif';
        });
        
        if (empty($active_players)) {
            return false;
        }
        
        $next_turn = ($current['current_turn'] + 1) % count($active_players);
        
        // Mettre à jour le tour
        setCurrentTurn($unique_key_user, $next_turn);
        
        // Si tous les joueurs ont voté, vérifier l'élimination
        if ($next_turn === 0) {
            processVoteResults($unique_key_user);
        }
        
        return true;
    } catch (PDOException $e) {
        error_log("Erreur incrementVoteTurn: " . $e->getMessage());
        return false;
    }
}

function getActivePlayersForVoting($unique_key_user, $current_voter) {
    global $conn;
    
    try {
        // Récupérer tous les joueurs actifs sauf le votant actuel
        $sql = "SELECT name_player 
                FROM jeux_soiree_undercover_game 
                WHERE unique_key_user = :unique_key_user 
                AND status = 'actif' 
                AND name_player != :current_voter 
                ORDER BY name_player ASC";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':unique_key_user' => $unique_key_user,
            ':current_voter' => $current_voter
        ]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Erreur getActivePlayersForVoting: " . $e->getMessage());
        return [];
    }
}

function getCurrentVoter($unique_key_user) {
    global $conn;
    
    try {
        // Vérifier s'il reste des joueurs actifs qui n'ont pas voté
        $sql = "SELECT g.name_player 
                FROM jeux_soiree_undercover_game g 
                LEFT JOIN jeux_soiree_undercover_votes v 
                ON g.name_player = v.voter_name 
                AND v.unique_key_user = g.unique_key_user 
                WHERE g.unique_key_user = :unique_key_user 
                AND g.status = 'actif' 
                AND v.voter_name IS NULL 
                ORDER BY g.name_player ASC 
                LIMIT 1";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([':unique_key_user' => $unique_key_user]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result ? $result['name_player'] : null;
    } catch (PDOException $e) {
        error_log("Erreur getCurrentVoter: " . $e->getMessage());
        return null;
    }
}

function getGameResults($unique_key_user) {
    global $conn;
    
    try {
        // Récupérer les joueurs éliminés et leurs mots, similaire à getVoteDetails
        $sql = "SELECT g.name_player, g.word_undercover, g.word_dif, g.status
                FROM jeux_soiree_undercover_game g
                WHERE g.unique_key_user = :unique_key_user
                AND g.status = 'inactif'
                ORDER BY g.name_player ASC";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([':unique_key_user' => $unique_key_user]);
        $eliminated_players = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Ajouter des logs pour le débogage
        error_log("Joueurs éliminés trouvés : " . print_r($eliminated_players, true));
        
        return $eliminated_players;
        
    } catch (PDOException $e) {
        error_log("Erreur getGameResults: " . $e->getMessage());
        return [];
    }
}

// Fonction pour vérifier l'état de la partie
function checkGameStatus($unique_key_user) {
    global $conn;
    
    try {
        $sql = "SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN status = 'actif' THEN 1 ELSE 0 END) as active,
                    SUM(CASE WHEN word_undercover != word_dif AND status = 'actif' THEN 1 ELSE 0 END) as undercover_active
                FROM jeux_soiree_undercover_game 
                WHERE unique_key_user = :unique_key_user";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([':unique_key_user' => $unique_key_user]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
        
    } catch (PDOException $e) {
        error_log("Erreur checkGameStatus: " . $e->getMessage());
        return null;
    }
}

// Modifier la fonction processVoteResults pour utiliser getGameResults
function processVoteResults($unique_key_user) {
    global $conn;
    
    try {
        error_log("Début processVoteResults avec unique_key_user: " . $unique_key_user);
        
        // Vérifier les votes actuels
        $sql = "SELECT * FROM jeux_soiree_undercover_votes WHERE unique_key_user = :unique_key_user";
        $stmt = $conn->prepare($sql);
        $stmt->execute([':unique_key_user' => $unique_key_user]);
        $current_votes = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        error_log("Votes actuels: " . print_r($current_votes, true));
        
        // Trouver le joueur le plus voté
        $sql = "SELECT voted_player, COUNT(*) as vote_count 
                FROM jeux_soiree_undercover_votes 
                WHERE unique_key_user = :unique_key_user 
                GROUP BY voted_player 
                ORDER BY vote_count DESC 
                LIMIT 1";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([':unique_key_user' => $unique_key_user]);
        $most_voted = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($most_voted) {
            // Éliminer le joueur
            $sql = "UPDATE jeux_soiree_undercover_game 
                   SET status = 'inactif' 
                   WHERE unique_key_user = :unique_key_user 
                   AND name_player = :player_name";
            
            $stmt = $conn->prepare($sql);
            $stmt->execute([
                ':unique_key_user' => $unique_key_user,
                ':player_name' => $most_voted['voted_player']
            ]);
            
            // Nettoyer les votes
            $sql = "DELETE FROM jeux_soiree_undercover_votes 
                   WHERE unique_key_user = :unique_key_user";
            
            $stmt = $conn->prepare($sql);
            $stmt->execute([':unique_key_user' => $unique_key_user]);
        }
        
        // Retourner les résultats mis à jour
        return getGameResults($unique_key_user);
        
    } catch (PDOException $e) {
        error_log("Erreur processVoteResults: " . $e->getMessage());
        return null;
    }
}

function getActivePlayersCount($unique_key_user) {
    global $conn;
    
    try {
        $sql = "SELECT COUNT(*) as count 
                FROM jeux_soiree_undercover_game 
                WHERE unique_key_user = :unique_key_user 
                AND status = 'actif'";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([':unique_key_user' => $unique_key_user]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result['count'];
    } catch (PDOException $e) {
        error_log("Erreur getActivePlayersCount: " . $e->getMessage());
        return 0;
    }
}

// Ajouter cette nouvelle table pour gérer les égalités
$sql_create_tiebreaker = "CREATE TABLE IF NOT EXISTS jeux_soiree_tiebreaker (
    id INT AUTO_INCREMENT PRIMARY KEY,
    unique_key_user VARCHAR(255),
    player_name VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

function getTiedPlayers($unique_key_user) {
    global $conn;
    
    try {
        $sql = "SELECT player_name 
                FROM jeux_soiree_tiebreaker 
                WHERE unique_key_user = :unique_key_user";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([':unique_key_user' => $unique_key_user]);
        
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    } catch (PDOException $e) {
        error_log("Erreur getTiedPlayers: " . $e->getMessage());
        return [];
    }
}

function resetForNextRound($unique_key_user) {
    global $conn;
    
    try {
        // Supprimer les anciens votes
        $sql_reset_votes = "DELETE FROM jeux_soiree_undercover_votes 
                           WHERE unique_key_user = :unique_key_user";
        
        $stmt = $conn->prepare($sql_reset_votes);
        $stmt->execute([':unique_key_user' => $unique_key_user]);
        
        // Supprimer l'ancienne partie
        $sql_reset_game = "DELETE FROM jeux_soiree_undercover_game 
                          WHERE unique_key_user = :unique_key_user";
        
        $stmt = $conn->prepare($sql_reset_game);
        $stmt->execute([':unique_key_user' => $unique_key_user]);
        
        // Créer une nouvelle partie
        createUndercover($unique_key_user);
        
        // Réinitialiser le statut du jeu
        $sql_reset_status = "UPDATE jeux_soiree_game_status 
                            SET current_turn = 0, 
                                game_phase = 'reveal' 
                            WHERE unique_key_user = :unique_key_user";
        
        $stmt = $conn->prepare($sql_reset_status);
        $stmt->execute([':unique_key_user' => $unique_key_user]);
        
        return true;
    } catch (PDOException $e) {
        error_log("Erreur resetForNextRound: " . $e->getMessage());
        return false;
    }
}

function isVotingComplete($unique_key_user) {
    global $conn;
    
    try {
        // Compter le nombre de joueurs actifs
        $sql_active = "SELECT COUNT(*) as active_count 
                      FROM jeux_soiree_undercover_game 
                      WHERE unique_key_user = :unique_key_user 
                      AND status = 'actif'";
        
        $stmt = $conn->prepare($sql_active);
        $stmt->execute([':unique_key_user' => $unique_key_user]);
        $active_count = $stmt->fetch(PDO::FETCH_ASSOC)['active_count'];
        
        // Compter le nombre de votes
        $sql_votes = "SELECT COUNT(DISTINCT voter_name) as vote_count 
                     FROM jeux_soiree_undercover_votes 
                     WHERE unique_key_user = :unique_key_user";
        
        $stmt = $conn->prepare($sql_votes);
        $stmt->execute([':unique_key_user' => $unique_key_user]);
        $vote_count = $stmt->fetch(PDO::FETCH_ASSOC)['vote_count'];
        
        // Si tous les joueurs actifs ont voté
        return $vote_count >= $active_count;
    } catch (PDOException $e) {
        error_log("Erreur isVotingComplete: " . $e->getMessage());
        return false;
    }
}

function updateGamePhase($unique_key_user, $phase) {
    global $conn;
    
    $sql = "UPDATE jeux_soiree_game_status 
            SET game_phase = :phase 
            WHERE unique_key_user = :unique_key_user";
    
    $stmt = $conn->prepare($sql);
    $stmt->execute([
        ':phase' => $phase,
        ':unique_key_user' => $unique_key_user
    ]);
}

function checkVoteResults($unique_key_user) {
    global $conn;
    
    try {
        // Récupérer tous les votes
        $sql = "SELECT voted_player, COUNT(*) as vote_count 
                FROM jeux_soiree_undercover_votes 
                WHERE unique_key_user = :unique_key_user 
                GROUP BY voted_player";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([':unique_key_user' => $unique_key_user]);
        $votes = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Vérifier s'il y a égalité
        $max_votes = 0;
        $players_with_max_votes = [];
        
        foreach ($votes as $vote) {
            if ($vote['vote_count'] > $max_votes) {
                $max_votes = $vote['vote_count'];
                $players_with_max_votes = [$vote['voted_player']];
            } elseif ($vote['vote_count'] == $max_votes) {
                $players_with_max_votes[] = $vote['voted_player'];
            }
        }
        
        if (count($players_with_max_votes) > 1) {
            // En cas d'égalité, mettre à jour le statut pour un nouveau vote
            $sql_update = "UPDATE jeux_soiree_game_status 
                          SET game_phase = 'tiebreaker', 
                              tied_players = :tied_players 
                          WHERE unique_key_user = :unique_key_user";
            
            $stmt = $conn->prepare($sql_update);
            $stmt->execute([
                ':unique_key_user' => $unique_key_user,
                ':tied_players' => json_encode($players_with_max_votes)
            ]);
            
            return 'tie';
        } else {
            // Éliminer le joueur avec le plus de votes
            $eliminated_player = $players_with_max_votes[0];
            
            // Vérifier si c'était un undercover
            $sql_check = "SELECT word_undercover 
                         FROM jeux_soiree_undercover_game 
                         WHERE unique_key_user = :unique_key_user 
                         AND name_player = :player_name";
            
            $stmt = $conn->prepare($sql_check);
            $stmt->execute([
                ':unique_key_user' => $unique_key_user,
                ':player_name' => $eliminated_player
            ]);
            $player_word = $stmt->fetch(PDO::FETCH_ASSOC)['word_undercover'];
            
            // Éliminer le joueur
            $sql_eliminate = "UPDATE jeux_soiree_undercover_game 
                            SET status = 'inactif' 
                            WHERE unique_key_user = :unique_key_user 
                            AND name_player = :player_name";
            
            $stmt = $conn->prepare($sql_eliminate);
            $stmt->execute([
                ':unique_key_user' => $unique_key_user,
                ':player_name' => $eliminated_player
            ]);
            
            // Vérifier la condition de victoire
            $sql_active = "SELECT word_undercover 
                          FROM jeux_soiree_undercover_game 
                          WHERE unique_key_user = :unique_key_user 
                          AND status = 'actif'";
            
            $stmt = $conn->prepare($sql_active);
            $stmt->execute([':unique_key_user' => $unique_key_user]);
            $active_words = $stmt->fetchAll(PDO::FETCH_COLUMN);
            
            $all_same = count(array_unique($active_words)) === 1;
            
            if ($all_same) {
                return 'game_over';
            }
            
            // Réinitialiser les votes pour le prochain tour
            $sql_reset = "DELETE FROM jeux_soiree_undercover_votes 
                         WHERE unique_key_user = :unique_key_user";
            
            $stmt = $conn->prepare($sql_reset);
            $stmt->execute([':unique_key_user' => $unique_key_user]);
            
            return 'continue';
        }
    } catch (PDOException $e) {
        error_log("Erreur checkVoteResults: " . $e->getMessage());
        return false;
    }
}

function getEliminatedPlayerInfo($unique_key_user) {
    global $conn;
    
    try {
        // Trouver le joueur avec le plus de votes
        $sql = "SELECT voted_player, COUNT(*) as vote_count 
                FROM jeux_soiree_undercover_votes 
                WHERE unique_key_user = :unique_key_user 
                GROUP BY voted_player 
                ORDER BY vote_count DESC 
                LIMIT 1";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([':unique_key_user' => $unique_key_user]);
        $eliminated = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($eliminated) {
            // Récupérer le rôle du joueur éliminé
            $sql_role = "SELECT word_undercover, word_dif 
                        FROM jeux_soiree_undercover_game 
                        WHERE unique_key_user = :unique_key_user 
                        AND name_player = :player_name";
            
            $stmt = $conn->prepare($sql_role);
            $stmt->execute([
                ':unique_key_user' => $unique_key_user,
                ':player_name' => $eliminated['voted_player']
            ]);
            $player_info = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Déterminer si c'était un undercover
            $is_undercover = ($player_info['word_undercover'] !== $player_info['word_dif']);
            
            return [
                'name' => $eliminated['voted_player'],
                'votes' => $eliminated['vote_count'],
                'is_undercover' => $is_undercover,
                'word' => $is_undercover ? $player_info['word_undercover'] : $player_info['word_dif']
            ];
        }
        
        return null;
    } catch (PDOException $e) {
        error_log("Erreur getEliminatedPlayerInfo: " . $e->getMessage());
        return null;
    }
}

function checkGameResult($unique_key_user) {
    global $conn;
    
    try {
        // Récupérer les joueurs actifs
        $sql = "SELECT word_undercover, word_dif 
                FROM jeux_soiree_undercover_game 
                WHERE unique_key_user = :unique_key_user 
                AND status = 'actif'";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([':unique_key_user' => $unique_key_user]);
        $active_players = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (count($active_players) <= 1) {
            // Vérifier si l'undercover a gagné
            $undercover_won = false;
            foreach ($active_players as $player) {
                if ($player['word_undercover'] !== $player['word_dif']) {
                    $undercover_won = true;
                    break;
                }
            }
            
            return [
                'game_over' => true,
                'winner' => $undercover_won ? 'undercover' : 'citizens'
            ];
        }
        
        return ['game_over' => false];
    } catch (PDOException $e) {
        error_log("Erreur checkGameResult: " . $e->getMessage());
        return ['game_over' => false];
    }
}

function resetVotes($unique_key_user) {
    global $conn;
    
    try {
        $sql = "DELETE FROM jeux_soiree_undercover_votes 
                WHERE unique_key_user = :unique_key_user";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([':unique_key_user' => $unique_key_user]);
        
        return true;
    } catch (PDOException $e) {
        error_log("Erreur resetVotes: " . $e->getMessage());
        return false;
    }
}

function hasEveryoneVoted($unique_key_user) {
    global $conn;
    
    try {
        // Compter les joueurs actifs
        $sql_active = "SELECT COUNT(*) as active_count 
                      FROM jeux_soiree_undercover_game 
                      WHERE unique_key_user = :unique_key_user 
                      AND status = 'actif'";
        
        $stmt = $conn->prepare($sql_active);
        $stmt->execute([':unique_key_user' => $unique_key_user]);
        $active_count = $stmt->fetch(PDO::FETCH_ASSOC)['active_count'];
        
        // Compter les votes
        $sql_votes = "SELECT COUNT(DISTINCT voter_name) as vote_count 
                     FROM jeux_soiree_undercover_votes 
                     WHERE unique_key_user = :unique_key_user";
        
        $stmt = $conn->prepare($sql_votes);
        $stmt->execute([':unique_key_user' => $unique_key_user]);
        $vote_count = $stmt->fetch(PDO::FETCH_ASSOC)['vote_count'];
        
        return $vote_count >= $active_count - 1;
    } catch (PDOException $e) {
        error_log("Erreur hasEveryoneVoted: " . $e->getMessage());
        return false;
    }
}

function hasAllVoted($unique_key_user) {
    global $conn;
    
    try {
        // Compter les joueurs actifs
        $sql = "SELECT COUNT(*) as active_count 
                FROM jeux_soiree_undercover_game 
                WHERE unique_key_user = :unique_key_user 
                AND status = 'actif'";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([':unique_key_user' => $unique_key_user]);
        $active_count = $stmt->fetch(PDO::FETCH_ASSOC)['active_count'];
        
        // Compter les votes uniques
        $sql = "SELECT COUNT(DISTINCT voter_name) as vote_count 
                FROM jeux_soiree_undercover_votes 
                WHERE unique_key_user = :unique_key_user";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([':unique_key_user' => $unique_key_user]);
        $vote_count = $stmt->fetch(PDO::FETCH_ASSOC)['vote_count'];
        
        return $vote_count >= $active_count;
    } catch (PDOException $e) {
        error_log("Erreur hasAllVoted: " . $e->getMessage());
        return false;
    }
}

// Fonction pour vérifier la session
function checkSession() {
    error_log("Session unique_key_user: " . (isset($_SESSION['unique_key_user']) ? $_SESSION['unique_key_user'] : 'non défini'));
    error_log("Session data: " . print_r($_SESSION, true));
}

function getEliminatedPlayers($unique_key_user) {
    global $conn;
    
    try {
        // Utiliser exactement la même structure que getVoteDetails
        $sql = "SELECT 
                    name_player,
                    word_undercover,
                    status
                FROM jeux_soiree_undercover_game 
                WHERE unique_key_user = :unique_key_user 
                AND status = 'inactif'";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([':unique_key_user' => $unique_key_user]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
        
    } catch (PDOException $e) {
        error_log("Erreur getEliminatedPlayers: " . $e->getMessage());
        return [];
    }
}



