<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Inscription</title>
</head>
<body>
    <h1>Inscription</h1>
    
    <?php if (isset($error)): ?>
        <div class="error"><?php echo $error; ?></div>
    <?php endif; ?>

    <form method="POST" action="/register" enctype="multipart/form-data">
        <div>
            <label for="name_user">Nom :</label>
            <input type="text" id="name_user" name="name_user" required>
        </div>
        
        <div>
            <label for="email_user">Email :</label>
            <input type="email" id="email_user" name="email_user" required>
        </div>
        
        <div>
            <label for="password_user">Mot de passe :</label>
            <input type="password" id="password_user" name="password_user" required>
        </div>
        
        <div>
            <label for="avatar_user">Avatar :</label>
            <input type="file" id="avatar_user" name="avatar_user" accept="image/*">
        </div>
        
        <button type="submit">S'inscrire</button>
    </form>
    
    <p>Déjà un compte ? <a href="/login">Se connecter</a></p>
</body>
</html> 