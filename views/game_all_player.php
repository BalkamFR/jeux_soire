<?php
// Récupération de la liste des joueurs au chargement initial
$players = getAllPlayersFromUser($_SESSION['unique_key_user']);

// Si une erreur existe (exemple, depuis une précédente soumission), elle pourra être affichée
$error = isset($error) ? $error : null;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Gestion des joueurs</title>
  <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
  <div class="container">
    <h1>Liste des joueurs</h1>
    <h2>Code unique du groupe : <?php echo $_SESSION['game_unique_key']; ?></h2>

    <!-- Affichage de la liste des joueurs -->
    <div class="players-list" id="playersList">
      <!-- La liste des joueurs sera mise à jour ici -->
    </div>

    <div class="links">
      <a href="/home" class="btn btn-secondary">Retour au Jeu</a>
    </div>
  </div>

  <script>
    // Fonction pour mettre à jour la liste des joueurs
    function updatePlayersList() {
        fetch('/get_game_all_player', {
            headers: {
                'Accept': 'application/json'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Erreur réseau');
            }
            return response.json();
        })
        .then(data => {
            const playersList = document.getElementById('playersList');
            let html = '';
            
            if (data.players && Array.isArray(data.players)) {
                data.players.forEach(player => {
                    html += `
                        <div class="player">
                            ${player.name_player}
                        </div>
                    `;
                });
            } else {
                html = '<p>Aucun joueur trouvé</p>';
            }
            
            playersList.innerHTML = html;
        })
        .catch(error => {
            console.error('Erreur:', error);
            document.getElementById('playersList').innerHTML = 
                '<p>Erreur lors du chargement des joueurs</p>';
        });
    }

    // Mettre à jour la liste toutes les 2 secondes
    setInterval(updatePlayersList, 2000);

    // Charger la liste initiale
    updatePlayersList();
  </script>
</body>
</html>
