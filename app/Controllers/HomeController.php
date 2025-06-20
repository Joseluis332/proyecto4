<?php

namespace App\Controllers;

use App\Core\View;
use App\Core\SessionManager;
use PDO;

class HomeController extends Controller
{
    /**
     * Constructor del HomeController.
     * @param PDO $pdo Instancia de la conexión a la base de datos.
     */
    public function __construct(PDO $pdo)
    {
        parent::__construct($pdo);
    }

    /**
     * Muestra la página de inicio.
     * Ruta: GET /
     */
    public function index(): void
    {
        // Aquí no hay problemas de autenticación para la página principal
        View::render('home/index', ['title' => 'Bienvenido al Sistema PNFI']); // <-- CORRECCIÓN AQUÍ
    }

    /**
     * Muestra el dashboard del usuario logueado.
     * Ruta: GET /dashboard
     */
    public function dashboard(): void
    {
        $userName = SessionManager::get('user_nombre');
        $userRole = SessionManager::get('user_rol');

        View::render('dashboard', [
            'title' => 'Dashboard del Usuario',
            'user_name' => $userName,
            'user_role' => $userRole
        ]);
    }

    /**
     * Muestra el perfil del usuario.
     * Ruta: GET /profile
     */
    public function profile(): void
    {
        View::render('profile', ['title' => 'Mi Perfil']);
    }
}
