<?php

function createUndercover($unique_key_user) {
    global $conn;
    
    try {
        // Récupérer un mot aléatoire
        $sql = "SELECT word_undercover, word_dif 
                FROM jeux_soiree_undercover 
                ORDER BY RAND() LIMIT 1";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $word = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$word) {
            // Si pas de mot trouvé, utiliser des mots par défaut
            $word = [
                'word_undercover' => 'chat',
                'word_dif' => 'chien'
            ];
        }
        
        // Récupérer tous les joueurs
        $sql_players = "SELECT name_player 
                       FROM jeux_soiree_player 
                       WHERE unique_key_user = :unique_key_user";
        $stmt = $conn->prepare($sql_players);
        $stmt->execute([':unique_key_user' => $unique_key_user]);
        $players = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (empty($players)) {
            return false;
        }
        
        // Choisir un joueur au hasard pour être l'undercover
        $undercover_index = array_rand($players);
        
        // Insérer pour chaque joueur
        foreach ($players as $index => $player) {
            $word_to_use = ($index === $undercover_index) ? $word['word_dif'] : $word['word_undercover'];
            
            $sql_insert = "INSERT INTO jeux_soiree_undercover_game 
                          (unique_key_user, name_player, word_undercover, status) 
                          VALUES (:unique_key_user, :name_player, :word_undercover, 'actif')";
            
            $stmt = $conn->prepare($sql_insert);
            $stmt->execute([
                ':unique_key_user' => $unique_key_user,
                ':name_player' => $player['name_player'],
                ':word_undercover' => $word_to_use
            ]);
        }
        
        return true;
    } catch (PDOException $e) {
        // Pour le débogage
        error_log("Erreur dans createUndercover: " . $e->getMessage());
        return false;
    }
}

