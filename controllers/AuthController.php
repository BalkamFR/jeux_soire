<?php

function loginAction() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $unique_key_user = $_POST['unique_key_user'] ?? '';
        $password_user = $_POST['password_user'] ?? '';

        $user = getUserByUniqueKey($unique_key_user);
        if ($user && password_verify($password_user, $user['password_user'])) {
            $_SESSION['unique_key_user'] = $unique_key_user;
            header('Location: /home');
            exit;
        } else {
            $error = "Identifiants incorrects";
            require __DIR__ . '/../views/auth/login.php';
        }
    } else {
        require __DIR__ . '/../views/auth/login.php';
    }
}

function registerAction() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $unique_key_user = uniqid();
        $name_user = $_POST['name_user'] ?? '';
        $email_user = $_POST['email_user'] ?? '';
        $password_user = $_POST['password_user'] ?? '';

        // Gestion de l'upload de l'avatar
        if (isset($_FILES['avatar_user']) && $_FILES['avatar_user']['error'] === UPLOAD_ERR_OK) {
            $avatarTmpPath = $_FILES['avatar_user']['tmp_name'];
            $avatarName = basename($_FILES['avatar_user']['name']);
            $avatarUploadPath = __DIR__ . '/../uploads/avatars/' . $unique_key_user . '_' . $avatarName;
            move_uploaded_file($avatarTmpPath, $avatarUploadPath);
            $avatar_user = 'uploads/avatars/' . $unique_key_user . '_' . $avatarName;
        } else {
            $avatar_user = 'uploads/avatars/default.png'; // avatar par défaut
        }

        $hashed_password = password_hash($password_user, PASSWORD_DEFAULT);

        try {
            createUser($unique_key_user, $name_user, $email_user, $avatar_user, $hashed_password);
            joinGame($name_user, $unique_key_user, $avatar_user);
            $_SESSION['unique_key_user'] = $unique_key_user;
            header('Location: /home');
            exit;
        } catch (Exception $e) {
            $error = "Erreur lors de l'inscription";
            require __DIR__ . '/../views/auth/register.php';
        }
    } else {
        require __DIR__ . '/../views/auth/register.php';
    }
}

function logoutAction() {
    session_destroy();
    header('Location: /login');
    exit;
}


