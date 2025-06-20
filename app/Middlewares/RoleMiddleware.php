<?php
// C:\xampp\htdocs\proyecto_pnfi\app\Middlewares\RoleMiddleware.php

namespace App\Middlewares;

use App\Core\SessionManager;
use App\Core\MiddlewareInterface;

class RoleMiddleware implements MiddlewareInterface {
    protected string $requiredRole; // Propiedad para almacenar el rol requerido

    public function __construct(string $requiredRole) { // <--- ¡AÑADIDO Y CRÍTICO!
        $this->requiredRole = $requiredRole;
    }

    public function handle(): bool { // <--- CORREGIDO: handle() sin parámetros
        // Verifica si el usuario está logueado (AuthMiddleware ya debería haberlo hecho, pero es buena práctica)
        if (!SessionManager::isLoggedIn()) {
            header('Location: /proyecto_pnfi/public/login');
            exit();
        }

        // Obtiene el rol del usuario de la sesión
        $userRole = SessionManager::get('user_rol');

        // Depuración: Verifica el rol del usuario y el rol requerido
        error_log("DEBUG RoleMiddleware: Rol de usuario en sesión: " . ($userRole ?? 'N/D'));
        error_log("DEBUG RoleMiddleware: Rol requerido para la ruta: " . $this->requiredRole); // <--- Usa la propiedad

        // Si el rol del usuario no coincide con el rol requerido para esta ruta
        if ($userRole !== $this->requiredRole) { // <--- Usa la propiedad
            error_log("DEBUG RoleMiddleware: Acceso denegado. Rol de usuario '" . ($userRole ?? 'N/D') . "' no coincide con el rol requerido '" . $this->requiredRole . "'.");

            // Redirige a la página de acceso denegado
            SessionManager::setFlash('error_message', 'No tienes permisos para acceder a esta sección.');
            header('Location: /proyecto_pnfi/public/access-denied');
            exit();
        }

        // Si el rol coincide, permite el acceso
        error_log("DEBUG RoleMiddleware: Acceso permitido.");
        return true;
    }
}