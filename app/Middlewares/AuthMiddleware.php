<?php
// C:\xampp\htdocs\proyecto_pnfi\app\Middlewares\AuthMiddleware.php

namespace App\Middlewares;

use App\Core\MiddlewareInterface; // ¡CAMBIADO A App\Interfaces!
use App\Core\SessionManager;

class AuthMiddleware implements MiddlewareInterface {
    public function handle( ): bool {
        if (!SessionManager::isLoggedIn()) {
            SessionManager::setFlash('error_message', 'Debes iniciar sesión para acceder a esta página.');
            header('Location: /proyecto_pnfi/public/login');
            exit();
        }
        return true;
    }
}