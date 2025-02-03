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
    <h1>Gestion des joueurs </h1>
    <h2>Code unique du groupe : <?php echo $_SESSION['unique_key_user']; ?></h2>

    <?php if ($error): ?>
      <div class="alert alert-danger">
        <?php echo htmlspecialchars($error); ?>
      </div>
    <?php endif; ?>

    <form id="addPlayerForm" action="/add_player" method="POST" class="form">
      <div class="form-group">
        <label for="name_player">Nom du Joueur</label>
        <input type="text" id="name_player" name="name_player" required class="form-control" placeholder="Entrez le nom du joueur">
      </div>
      <button type="submit" class="btn btn-primary">Ajouter un joueur</button>
    </form>

    <div class="links">
      <a href="/home" class="btn btn-secondary">Retour au Jeu</a>
      <a href="/logout" class="btn btn-danger">Déconnexion</a>
    </div>

    <!-- Affichage initial de la liste des joueurs -->
    <div class="players-list" id="playersList">
      <!-- La liste des joueurs sera mise à jour ici -->
    </div>
  </div>

  <script>
    // Fonction pour mettre à jour la liste des joueurs
    function updatePlayersList() {
        fetch('/get_players', {
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
                    const isHost = player.is_host === true;
                    html += `
                        <div class="player" data-name="${player.name_player}">
                            ${player.name_player}
                            ${!isHost ? `<button onclick="deletePlayer('${player.name_player}')">Supprimer</button>` : ''}
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

    // Fonction pour supprimer un joueur
    function deletePlayer(playerName) {
      fetch(`/delete_player?name_player=${encodeURIComponent(playerName)}`)
        .then(response => response.json())
        .then(data => {
          if (data.status === 'success') {
            updatePlayersList();
          }
        })
        .catch(error => console.error('Erreur:', error));
    }

    // Gérer le formulaire d'ajout
    document.getElementById('addPlayerForm').addEventListener('submit', function(e) {
      e.preventDefault();
      const formData = new FormData(this);

      fetch('/add_player', {
        method: 'POST',
        body: formData
      })
      .then(response => response.json())
      .then(data => {
        if (data.status === 'success') {
          this.reset();
          updatePlayersList();
        }
      })
      .catch(error => console.error('Erreur:', error));
    });

    // Mettre à jour la liste toutes les 2 secondes
    setInterval(updatePlayersList, 2000);

    // Charger la liste initiale
    updatePlayersList();

    // Debug initial
    console.log('Script chargé');
  </script>
</body>
</html>
