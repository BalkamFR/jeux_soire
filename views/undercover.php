<?php
error_log("Début de la vue undercover.php");
checkSession(); // Appel de la nouvelle fonction de vérification

if (!isset($_SESSION['unique_key_user'])) {
    echo '<div style="color: red; padding: 10px;">Erreur: Utilisateur non connecté</div>';
}

$players_words = getUndercoverWords();
if (empty($players_words)) {
    echo '<div style="color: orange; padding: 10px;">Aucun joueur trouvé ou partie non démarrée</div>';
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Undercover</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Arial', sans-serif;
        }

        body {
            background: #1a1a1a;
            color: white;
            padding: 20px;
            min-height: 100vh;
        }

        .game-container {
            max-width: 600px;
            margin: 0 auto;
        }

        .start-button {
            width: 100%;
            padding: 20px;
            background: linear-gradient(45deg, #ff4d4d, #ff8080);
            color: white;
            border: none;
            border-radius: 15px;
            font-size: 20px;
            font-weight: bold;
            margin: 20px 0;
            box-shadow: 0 4px 15px rgba(255, 77, 77, 0.3);
            cursor: pointer;
            transition: transform 0.2s;
        }

        .start-button:active {
            transform: scale(0.98);
        }

        .player-card {
            background: #2a2a2a;
            padding: 20px;
            border-radius: 15px;
            margin: 20px 0;
            text-align: center;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        .player-name {
            color: #ff8080;
            font-size: 18px;
            margin-bottom: 10px;
        }

        .player-word {
            font-size: 32px;
            color: #fff;
            font-weight: bold;
            margin: 15px 0;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }

        .vote-section {
            background: #2a2a2a;
            padding: 20px;
            border-radius: 15px;
            margin-top: 20px;
        }

        .vote-title {
            font-size: 24px;
            color: #ff8080;
            margin-bottom: 20px;
            text-align: center;
        }

        .vote-form select {
            width: 100%;
            padding: 15px;
            font-size: 18px;
            background: #3a3a3a;
            color: white;
            border: 2px solid #ff8080;
            border-radius: 10px;
            margin-bottom: 15px;
            appearance: none;
        }

        .vote-button {
            width: 100%;
            padding: 15px;
            background: #ff8080;
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 18px;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.3s;
        }

        .vote-button:hover {
            background: #ff4d4d;
        }

        .already-voted {
            background: #3a3a3a;
            color: #ff8080;
            padding: 15px;
            border-radius: 10px;
            text-align: center;
            font-size: 18px;
            margin: 15px 0;
        }

        .vote-status {
            margin-top: 20px;
        }

        .vote-status h3 {
            color: #ff8080;
            margin-bottom: 15px;
            text-align: center;
        }

        .vote-count {
            background: #3a3a3a;
            padding: 15px;
            border-radius: 10px;
            margin: 10px 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .eliminated-badge {
            background: #ff4d4d;
            color: white;
            padding: 8px 15px;
            border-radius: 20px;
            font-size: 14px;
            margin-top: 10px;
            display: inline-block;
        }

        .voting-prompt {
            background: #2a2a2a;
            padding: 20px;
            border-radius: 15px;
            margin-bottom: 20px;
        }

        .player-status {
            font-size: 14px;
            color: #888;
            margin-bottom: 10px;
        }

        .results-section {
            background: #2a2a2a;
            padding: 20px;
            border-radius: 15px;
            margin-top: 20px;
        }

        .results-section h2 {
            color: #ff8080;
            text-align: center;
            margin-bottom: 20px;
        }

        .vote-result {
            background: #3a3a3a;
            padding: 15px;
            border-radius: 10px;
            margin: 10px 0;
        }

        .player-votes {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 5px;
        }

        .player-name {
            font-size: 18px;
            color: white;
        }

        .vote-count {
            font-weight: bold;
            color: #ff8080;
        }

        .voters {
            font-size: 14px;
            color: #888;
        }

        .eliminated-announcement {
            background: #ff4d4d;
            color: white;
            padding: 15px;
            border-radius: 10px;
            text-align: center;
            font-size: 20px;
            margin: 20px 0;
        }

        .next-round-button {
            width: 100%;
            padding: 15px;
            background: #4CAF50;
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 18px;
            cursor: pointer;
            margin-top: 20px;
            transition: all 0.3s ease;
        }

        .next-round-button:hover {
            background: #45a049;
            transform: translateY(-2px);
        }

        .next-round-button:active {
            transform: translateY(0);
        }

        .vote-results {
            margin-top: 20px;
            padding: 15px;
            background: #2a2a2a;
            border-radius: 10px;
        }

        .vote-count {
            background: #333;
            padding: 10px;
            margin: 5px 0;
            border-radius: 8px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .player-status {
            font-size: 0.9em;
            color: #aaa;
            margin-bottom: 10px;
        }

        .vote-button, .next-round-button {
            width: 100%;
            padding: 12px;
            background: #4CAF50;
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: bold;
            cursor: pointer;
            margin-top: 10px;
        }

        .vote-button:hover, .next-round-button:hover {
            background: #45a049;
        }

        .eliminated-player {
            background: #ff4d4d;
            padding: 15px;
            border-radius: 10px;
            margin: 20px 0;
            color: white;
        }

        .player-info {
            background: rgba(0,0,0,0.2);
            padding: 10px;
            border-radius: 8px;
            margin-top: 10px;
        }

        .player-name {
            font-size: 18px;
            font-weight: bold;
            margin: 5px 0;
        }

        .player-role, .player-word {
            margin: 5px 0;
        }

        .game-over {
            background: #4CAF50;
            padding: 20px;
            border-radius: 10px;
            margin: 20px 0;
            text-align: center;
            color: white;
        }

        .winner-announcement {
            font-size: 24px;
            margin: 10px 0;
            font-weight: bold;
        }

        .voting-prompt {
            background: #2a2a2a;
            padding: 20px;
            border-radius: 15px;
            margin-bottom: 20px;
        }

        .vote-form {
            margin-top: 15px;
        }

        .vote-form select {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 5px;
            background: #333;
            color: white;
            border: 1px solid #444;
        }

        .vote-button {
            background: #4CAF50;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            width: 100%;
        }

        .vote-button:hover {
            background: #45a049;
        }

        .eliminated-player {
            background: #ff4d4d;
            padding: 15px;
            border-radius: 10px;
            margin: 20px 0;
            color: white;
        }

        .player-info {
            background: rgba(0,0,0,0.2);
            padding: 10px;
            border-radius: 8px;
            margin-top: 10px;
        }

        .game-over {
            background: #4CAF50;
            padding: 20px;
            border-radius: 10px;
            margin: 20px 0;
            text-align: center;
            color: white;
        }

        .next-round-button {
            width: 100%;
            padding: 15px;
            background: #4CAF50;
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 18px;
            cursor: pointer;
            margin-top: 20px;
            transition: all 0.3s ease;
        }

        .next-round-button:hover {
            background: #45a049;
            transform: translateY(-2px);
        }

        .final-results {
            margin-top: 20px;
            padding: 15px;
            background: #2a2a2a;
            border-radius: 10px;
        }

        .player-role {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px;
            margin: 5px 0;
            border-radius: 8px;
            background: #333;
            color: white;
        }

        .player-role.undercover {
            background: #ff4d4d;
        }

        .player-role.citoyen {
            background: #4CAF50;
        }

        .player-role.eliminated {
            opacity: 0.7;
            text-decoration: line-through;
        }

        .role-badge {
            padding: 5px 10px;
            border-radius: 15px;
            font-weight: bold;
            background: rgba(0,0,0,0.2);
        }

        .player-word {
            font-style: italic;
            background: rgba(0,0,0,0.2);
            padding: 5px 10px;
            border-radius: 8px;
        }

        .vote-button, .next-round-button {
            width: 100%;
            padding: 12px;
            background: #4CAF50;
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: bold;
            cursor: pointer;
            margin-top: 10px;
            transition: all 0.3s ease;
        }

        .vote-button:hover, .next-round-button:hover {
            background: #45a049;
        }

        .words-reveal {
            margin-top: 20px;
            padding: 15px;
            background: #2a2a2a;
            border-radius: 10px;
        }

        .player-word {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px;
            margin: 5px 0;
            background: #333;
            border-radius: 8px;
            color: white;
        }

        .player-name {
            font-weight: bold;
        }

        .word {
            background: rgba(0,0,0,0.2);
            padding: 5px 10px;
            border-radius: 5px;
        }

        .vote-results {
            margin-top: 20px;
            padding: 15px;
            background: #2a2a2a;
            border-radius: 10px;
        }

        .vote-count {
            background: #333;
            padding: 10px;
            margin: 5px 0;
            border-radius: 8px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: white;
        }

        .player-status {
            font-size: 0.9em;
            color: #aaa;
            margin-bottom: 10px;
        }

        .vote-button, .next-round-button {
            width: 100%;
            padding: 12px;
            background: #4CAF50;
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: bold;
            cursor: pointer;
            margin-top: 10px;
        }

        .vote-button:hover, .next-round-button:hover {
            background: #45a049;
        }

        .eliminated-players {
            margin-top: 20px;
            padding: 15px;
            background: #2a2a2a;
            border-radius: 10px;
        }

        .player-word {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px;
            margin: 5px 0;
            background: #333;
            border-radius: 8px;
            color: white;
        }

        .player-name {
            font-weight: bold;
        }

        .word {
            background: rgba(0,0,0,0.2);
            padding: 5px 10px;
            border-radius: 5px;
        }

        .eliminated-section {
            margin-top: 20px;
            padding: 15px;
            background: #2a2a2a;
            border-radius: 10px;
        }

        .eliminated-player {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px;
            margin: 5px 0;
            background: #333;
            border-radius: 8px;
            color: white;
        }

        .player-name {
            font-weight: bold;
        }

        .player-word {
            background: rgba(0,0,0,0.2);
            padding: 5px 10px;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="game-container">
        <?php
        $game_status = getCurrentTurn($_SESSION['unique_key_user']);
        $players = getUndercoverWords();
        $current_turn = $game_status['current_turn'] ?? 0;
        $game_phase = $game_status['game_phase'] ?? 'reveal';
        
        if (empty($players)): ?>
            <!-- Bouton de démarrage -->
            <form method="POST" action="">
                <button type="submit" name="start_undercover" class="start-button">
                    🎮 Nouvelle Partie
                </button>
            </form>
        <?php else: ?>
            <?php if ($game_phase === 'reveal'): ?>
                <!-- Phase de révélation des mots -->
                <div class="player-card">
                    <div class="player-name">
                        Au tour de <?php echo htmlspecialchars($players[$current_turn]['name_player']); ?>
                    </div>
                    <div class="player-word">
                        Ton mot : <?php echo htmlspecialchars($players[$current_turn]['word_undercover']); ?>
                    </div>
                    <form method="POST" action="">
                        <button type="submit" name="next_player" class="vote-button">
                            Joueur suivant →
                        </button>
                    </form>
                </div>
            <?php elseif ($game_phase === 'results'): ?>
                <div class="results-section">
                    <h2>Résultats du vote</h2>
                    <?php 
                    $vote_details = getVoteDetails($_SESSION['unique_key_user']);
                    $eliminated_player = '';
                    $max_votes = 0;
                    
                    ?>
                    <div class="vote-results">
                        <h3>📊 Résultats des votes</h3>
                        <?php foreach ($vote_details as $vote): ?>
                            <div class="vote-count">
                                <span><?php echo htmlspecialchars($vote['voted_player']); ?></span>
                                <strong><?php echo $vote['vote_count']; ?> vote(s)</strong>
                            </div>
                            <div class="player-status">
                                Votants : <?php echo htmlspecialchars($vote['voters']); ?>
                            </div>
                            <div class="vote-result">
                                <div class="player-votes">
                                    <span class="player-name"><?php echo htmlspecialchars($vote['voted_player']); ?></span>
                                    <span class="vote-count"><?php echo $vote['vote_count']; ?> vote(s)</span>
                                </div>
                                <div class="voters">
                                    Votants : <?php echo htmlspecialchars($vote['voters']); ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <?php if ($eliminated_player): ?>
                        <div class="eliminated-announcement">
                            <?php echo htmlspecialchars($eliminated_player); ?> a été éliminé !
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" action="" class="next-round-form">
                        <button type="submit" name="next_round" class="next-round-button">
                            Tour Suivant →
                        </button>
                    </form>
                </div>
            <?php else: ?>
                <!-- Phase de vote -->
                <?php
                $active_players = array_values(array_filter($players, function($player) {
                    return $player['status'] === 'actif';
                }));
                
                if (!empty($active_players)):
                    $current_voter = $active_players[$current_turn]['name_player'];
                ?>
                    <div class="vote-section">
                        <?php 
                        $vote_details = getVoteDetails($_SESSION['unique_key_user']);

                        $current_voter = getCurrentVoter($_SESSION['unique_key_user']);
                        $eliminated = getEliminatedPlayers($_SESSION['unique_key_user']);
                        ?>

                        <!-- Interface de vote -->
                        <?php if ($current_voter): ?>
                            <div class="voting-prompt">
                                <h3>🗳️ C'est au tour de <?php echo htmlspecialchars($current_voter); ?> de voter</h3>
                                
                                <form method="POST" action="" class="vote-form">
                                    <input type="hidden" name="voter" value="<?php echo htmlspecialchars($current_voter); ?>">
                                    <select name="voted_player" required>
                                        <option value="">Choisir qui éliminer...</option>
                                        <?php 
                                        $votable_players = getActivePlayersForVoting($_SESSION['unique_key_user'], $current_voter);
                                        foreach ($votable_players as $player): ?>
                                            <option value="<?php echo htmlspecialchars($player['name_player']); ?>">
                                                <?php echo htmlspecialchars($player['name_player']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <button type="submit" name="vote" class="vote-button">Voter</button>
                                </form>
                            </div>
                        <?php endif; ?>

                        <!-- Résultats des votes -->
                        <div class="vote-results">
                            <h3>📊 Résultats des votes</h3>
                            <?php foreach ($vote_details as $vote): ?>
                                <div class="vote-count">
                                    <span><?php echo htmlspecialchars($vote['voted_player']); ?></span>
                                    <strong><?php echo $vote['vote_count']; ?> vote(s)</strong>
                                </div>
                                <div class="player-status">
                                    Votants : <?php echo htmlspecialchars($vote['voters']); ?>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <!-- Joueurs éliminés -->
                        <?php if (!empty($eliminated)): ?>
              
                            <div class="eliminated-section">
                                <h3>🎯 Mots des joueurs</h3>
                                <?php foreach ($active_players as $player): ?>
                                    <div class="eliminated-player">
                                        <span class="player-name"><?php echo htmlspecialchars($player['name_player']); ?></span>
                                        <span class="player-word">Mot : <?php echo htmlspecialchars($player['word_undercover']); ?></span>
                                    </div>
                                <?php endforeach; ?>
                                <?php foreach ($eliminated as $player): ?>
                                    <div class="eliminated-player">
                                        <span class="player-name"><?php echo htmlspecialchars($player['name_player']); ?></span>
                                        <span class="player-word">Mot : <?php echo htmlspecialchars($player['word_undercover']); ?></span>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($eliminated)): ?>
                            <form method="POST" action="" class="next-round-form">
                                <button type="submit" name="next_round" class="next-round-button">
                                    Tour suivant →
                                </button>
                            </form>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</body>
</html>