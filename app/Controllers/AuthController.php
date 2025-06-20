<?php

namespace App\Controllers;

use PDO;
use App\Core\View;
use App\Core\SessionManager;
use App\Models\Usuario;

class AuthController extends Controller {
    private Usuario $usuarioModel;

    public function __construct(PDO $pdo) {
        parent::__construct($pdo);
        $this->usuarioModel = new Usuario($pdo);
    }

    // ... (showLoginForm y login métodos como están, con los logs de sesión) ...

    public function login(): void {
        error_log("DEBUG AUTH: Inicio del método login().");

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $cedula = filter_input(INPUT_POST, 'cedula', FILTER_SANITIZE_STRING) ?? '';
            $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING) ?? '';

            error_log("DEBUG AUTH: Cédula recibida: " . $cedula);

            $usuario = $this->usuarioModel->findByCedula($cedula);
            
            if ($usuario) {
                error_log("DEBUG AUTH: Resultado findByCedula para '" . $cedula . "': " . ($usuario ? 'Usuario encontrado.' : 'Usuario NO encontrado.'));
                error_log("DEBUG AUTH: Datos de usuario (parcial): " . print_r($usuario->data, true));
                error_log("DEBUG AUTH: Contraseña ingresada: " . $password);
                error_log("DEBUG AUTH: Hash de contraseña en DB: " . ($usuario->password ?? 'N/D'));

                if (password_verify($password, $usuario->password)) {
                    SessionManager::set('user_id', $usuario->id_usuario);
                    SessionManager::set('user_cedula', $usuario->cedula);
                    SessionManager::set('user_nombre', $usuario->nombre);
                    SessionManager::set('user_rol', $usuario->rol);

                    error_log("DEBUG AUTH: Contenido de _SESSION DESPUÉS de setear variables: " . print_r($_SESSION, true));
                    error_log("DEBUG AUTH: ID de sesión actual: " . session_id());

                    error_log("DEBUG AUTH: Login exitoso para el usuario: " . $usuario->nombre . " (ID: " . $usuario->id_usuario . ", Rol: " . $usuario->rol . ").");
                    SessionManager::setFlash('success_message', '¡Bienvenido ' . $usuario->nombre . '!');
                    header('Location: /proyecto_pnfi/public/dashboard');
                    exit();
                } else {
                    error_log("DEBUG AUTH: Fallo de autenticación. Contraseña incorrecta para el usuario: " . $cedula);
                    SessionManager::setFlash('error_message', 'Cédula o contraseña incorrectas.');
                    header('Location: /proyecto_pnfi/public/login');
                    exit();
                }
            } else {
                error_log("DEBUG AUTH: Fallo de autenticación. Usuario no encontrado: " . $cedula);
                SessionManager::setFlash('error_message', 'Cédula o contraseña incorrectas.');
                header('Location: /proyecto_pnfi/public/login');
                exit();
            }
        } else {
            error_log("DEBUG AUTH: Solicitud GET recibida en login(), redirigiendo a showLoginForm.");
            header('Location: /proyecto_pnfi/public/login');
            exit();
        }
    }

    // ... (logout y showRegisterForm) ...

    public function register(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'nombre' => filter_input(INPUT_POST, 'nombre', FILTER_SANITIZE_STRING) ?? '',
                'apellido' => filter_input(INPUT_POST, 'apellido', FILTER_SANITIZE_STRING) ?? '',
                'cedula' => filter_input(INPUT_POST, 'cedula', FILTER_SANITIZE_STRING) ?? '',
                'correo' => filter_input(INPUT_POST, 'correo', FILTER_SANITIZE_EMAIL) ?? '',
                'password' => $_POST['password'] ?? '',
                'confirm_password' => $_POST['confirm_password'] ?? '',
                'rol' => filter_input(INPUT_POST, 'rol', FILTER_SANITIZE_STRING) ?? 'estudiante',
                'estado' => 'activo'
            ];

            $errors = [];
            if (empty($data['nombre'])) $errors['nombre'] = 'El nombre es obligatorio.';
            if (empty($data['apellido'])) $errors['apellido'] = 'El apellido es obligatorio.';
            if (empty($data['cedula'])) $errors['cedula'] = 'La cédula es obligatoria.';
            if (empty($data['correo'])) $errors['correo'] = 'El correo es obligatorio.';
            if (empty($data['password'])) $errors['password'] = 'La contraseña es obligatoria.';
            if (empty($data['confirm_password'])) $errors['confirm_password'] = 'La confirmación de contraseña es obligatoria.';

            if ($data['password'] !== $data['confirm_password']) {
                $errors['confirm_password'] = 'Las contraseñas no coinciden.';
            }

            if ($this->usuarioModel->findByCedula($data['cedula'])) {
                $errors['cedula'] = 'Esta cédula ya está registrada.';
            }
            if ($this->usuarioModel->findByCorreo($data['correo'])) {
                $errors['correo'] = 'Este correo ya está registrado.';
            }

            if (!empty($errors)) {
                // Modificación aquí: Serializar el array de errores
                SessionManager::setFlash('errors', json_encode($errors)); // <--- LÍNEA 156 APROX.
                SessionManager::setFlash('old_input', json_encode($data)); // <--- LÍNEA 157 APROX.
                header('Location: /proyecto_pnfi/public/register');
                exit();
            }

            $modelValidationErrors = $this->usuarioModel->validate($data);
            if (!empty($modelValidationErrors)) {
                // Modificación aquí: Serializar el array de errores de validación del modelo
                SessionManager::setFlash('errors', json_encode($modelValidationErrors)); // <--- LÍNEA 166 APROX.
                SessionManager::setFlash('old_input', json_encode($data));
                header('Location: /proyecto_pnfi/public/register');
                exit();
            }
            
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
            unset($data['confirm_password']);

            $userId = $this->usuarioModel->save($data);

            if ($userId) {
                SessionManager::setFlash('success_message', 'Usuario registrado con éxito. Ahora puede iniciar sesión.');
                header('Location: /proyecto_pnfi/public/login');
                exit();
            } else {
                SessionManager::setFlash('error_message', 'Error al registrar el usuario. Inténtelo de nuevo.');
                header('Location: /proyecto_pnfi/public/register');
                exit();
            }
        } else {
            header('Location: /proyecto_pnfi/public/register');
            exit();
        }
    }
}
