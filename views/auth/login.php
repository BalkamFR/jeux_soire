<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion</title>
</head>
<body>
    <h1>Connexion</h1>
    
    <?php if (isset($error)): ?>
        <div class="error"><?php echo $error; ?></div>
    <?php endif; ?>

    <form method="POST" action="/login">
        <div>
            <label for="unique_key_user">Identifiant :</label>
            <input type="text" id="unique_key_user" name="unique_key_user" required>
        </div>
        
        <div>
            <label for="password_user">Mot de passe :</label>
            <input type="password" id="password_user" name="password_user" required>
        </div>
        
        <button type="submit">Se connecter</button>
    </form>
    
    <p>Pas encore de compte ? <a href="/register">S'inscrire</a></p>
</body>
</html> 