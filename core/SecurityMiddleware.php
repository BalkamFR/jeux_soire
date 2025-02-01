<?php
class SecurityMiddleware {
    public static function checkAuth() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit();
        }
    }
} 