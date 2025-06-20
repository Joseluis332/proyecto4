<?php

// 1. Incluir el autoloader de Composer
require_once __DIR__ . '/../vendor/autoload.php';

// 2. Cargar variables de entorno (si usas phpdotenv)
// Asegúrate de tener vlucas/phpdotenv instalado via Composer
if (file_exists(__DIR__ . '/../.env')) {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
    $dotenv->load();
}

// 3. Iniciar la Sesión - ¡ESTO DEBE ESTAR AQUÍ, MUY AL PRINCIPIO!
use App\Core\SessionManager;
SessionManager::start(); // <-- ¡IMPORTANTE!

// Importa tus clases principales
use App\Core\Router;
use App\Config\Database;

// Importa tus controladores (asegúrate de que todos están aquí)
use App\Controllers\Auth\AuthController;
use App\Controllers\HomeController;
use App\Controllers\AdminController;
use App\Controllers\UserController;
use App\Controllers\MaestroController;
use App\Controllers\ErrorController;
use App\Controllers\ProyectoController;
use App\Controllers\DocenteController;


// 4. Establecer la conexión a la Base de Datos
try {
    $pdo = Database::getConnection();
} catch (\PDOException $e) {
    error_log("Error de conexión a la base de datos: " . $e->getMessage());
    http_response_code(500);
    echo "<h1>Error Interno del Servidor</h1>";
    echo "<p>No se pudo establecer conexión con la base de datos. Por favor, inténtelo de nuevo más tarde.</p>";
    exit();
}

// 5. Instanciar el Router
$router = new Router($pdo);

// 6. Definir las Rutas de la Aplicación
// Rutas de Autenticación
$router->get('/login', 'AuthController@showLoginForm');
$router->post('/login', 'AuthController@login');
$router->get('/register', 'AuthController@showRegisterForm');
$router->post('/register', 'AuthController@register');
$router->get('/logout', 'AuthController@logout', ['AuthMiddleware']);

// Rutas Protegidas (requieren autenticación)
$router->get('/', 'HomeController@index', ['AuthMiddleware']); // Opcional: si la raíz requiere login
$router->get('/dashboard', 'HomeController@dashboard', ['AuthMiddleware']);
$router->get('/profile', 'HomeController@profile', ['AuthMiddleware']);

// Rutas de Administración (requieren autenticación y rol de administrador)
$router->get('/admin/dashboard', 'AdminController@dashboard', ['AuthMiddleware', 'RoleMiddleware:administrador']);
// Rutas de Usuarios
$router->get('/admin/usuarios', 'UserController@index', ['AuthMiddleware', 'RoleMiddleware:administrador']);
$router->get('/admin/usuarios/crear', 'UserController@create', ['AuthMiddleware', 'RoleMiddleware:administrador']);
$router->post('/admin/usuarios', 'UserController@store', ['AuthMiddleware', 'RoleMiddleware:administrador']);
$router->get('/admin/usuarios/editar/{id}', 'UserController@edit', ['AuthMiddleware', 'RoleMiddleware:administrador']);
$router->post('/admin/usuarios/actualizar/{id}', 'UserController@update', ['AuthMiddleware', 'RoleMiddleware:administrador']);
$router->post('/admin/usuarios/eliminar/{id}', 'UserController@destroy', ['AuthMiddleware', 'RoleMiddleware:administrador']);
$router->post('/admin/usuarios/activar/{id}', 'UserController@activateUser', ['AuthMiddleware', 'RoleMiddleware:administrador']);

// Rutas de Proyectos
$router->get('/admin/proyectos', 'ProyectoController@index', ['AuthMiddleware', 'RoleMiddleware:administrador']);
$router->get('/admin/proyectos/crear', 'ProyectoController@create', ['AuthMiddleware', 'RoleMiddleware:administrador']);
$router->post('/admin/proyectos', 'ProyectoController@store', ['AuthMiddleware', 'RoleMiddleware:administrador']);
$router->get('/admin/proyectos/ver/{id}', 'ProyectoController@show', ['AuthMiddleware', 'RoleMiddleware:administrador']);
$router->post('/admin/proyectos/eliminar/{id}', 'ProyectoController@delete', ['AuthMiddleware', 'RoleMiddleware:administrador']);

// Rutas de Docentes
$router->get('/admin/docentes', 'DocenteController@index', ['AuthMiddleware', 'RoleMiddleware:administrador']);
$router->get('/admin/docentes/crear', 'DocenteController@create', ['AuthMiddleware', 'RoleMiddleware:administrador']);
$router->post('/admin/docentes', 'DocenteController@store', ['AuthMiddleware', 'RoleMiddleware:administrador']);
$router->get('/admin/docentes/ver/{id}', 'DocenteController@show', ['AuthMiddleware', 'RoleMiddleware:administrador']);
$router->get('/admin/docentes/editar/{id}', 'DocenteController@edit', ['AuthMiddleware', 'RoleMiddleware:administrador']);
$router->post('/admin/docentes/actualizar/{id}', 'DocenteController@update', ['AuthMiddleware', 'RoleMiddleware:administrador']);
$router->post('/admin/docentes/eliminar/{id}', 'DocenteController@delete', ['AuthMiddleware', 'RoleMiddleware:administrador']);


// Rutas de Maestro (ejemplo: si existieran)
$router->get('/maestro/perfil', 'MaestroController@profile', ['AuthMiddleware', 'RoleMiddleware:Maestro']);
$router->get('/maestro/cursos', 'MaestroController@courses', ['AuthMiddleware', 'RoleMiddleware:Maestro']);

// Ruta para acceso denegado (cuando un middleware restringe el acceso)
$router->get('/access-denied', 'ErrorController@accessDenied');

// 7. Despachar la Solicitud
$router->dispatch();
