<?php

namespace App\Controllers;

use App\Core\View;
use App\Models\Usuario; // Necesitaremos este modelo
use App\Core\SessionManager; // Para mensajes flash
use PDO; // Asegúrate de que PDO esté disponible si tu BaseController lo usa

class UserController extends BaseController {
    protected Usuario $userModel;

    public function __construct(PDO $pdo) {
        parent::__construct($pdo);
        $this->userModel = new Usuario($pdo); // Instanciar el modelo de Usuario
    }

    /**
     * Muestra la lista de todos los usuarios.
     */
    public function index(): void {
        $users = $this->userModel->getAll(); // Método que crearemos en el modelo
        View::renderWithLayout('users/index', [
            'titulo' => 'Gestión de Usuarios',
            'users' => $users,
            'success_message' => SessionManager::getFlash('success_message'),
            'error_message' => SessionManager::getFlash('error_message')
        ]);
    }

    /**
     * Muestra el formulario para crear un nuevo usuario.
     */
    public function create(): void {
        View::renderWithLayout('users/create', [
            'titulo' => 'Crear Nuevo Usuario',
            'error_message' => SessionManager::getFlash('error_message')
        ]);
    }

    /**
     * Procesa los datos del formulario para crear un nuevo usuario.
     */
    public function store(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'cedula' => trim($_POST['cedula'] ?? ''),
                'nombre' => trim($_POST['nombre'] ?? ''),
                'apellido' => trim($_POST['apellido'] ?? ''),
                'correo' => trim($_POST['correo'] ?? ''),
                'telefono' => trim($_POST['telefono'] ?? ''),
                'direccion' => trim($_POST['direccion'] ?? ''),
                'password' => $_POST['password'] ?? '', // Se hasheará
                'rol' => $_POST['rol'] ?? ''
            ];

            // **Validación básica (puedes expandirla)**
            if (empty($data['cedula']) || empty($data['nombre']) || empty($data['apellido']) || empty($data['correo']) || empty($data['password']) || empty($data['rol'])) {
                SessionManager::setFlash('error_message', 'Todos los campos obligatorios deben ser completados.');
                header('Location: /proyecto_pnfi/public/admin/usuarios/crear');
                exit();
            }

            // Verificar si la cédula o el correo ya existen (Implementar en el modelo si es necesario)
            // $existingUserByCedula = $this->userModel->findByCedula($data['cedula']);
            // if ($existingUserByCedula) {
            //     SessionManager::setFlash('error_message', 'La cédula ya está registrada.');
            //     header('Location: /proyecto_pnfi/public/admin/usuarios/crear');
            //     exit();
            // }

            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);

            if ($this->userModel->create($data)) {
                SessionManager::setFlash('success_message', 'Usuario creado exitosamente.');
                header('Location: /proyecto_pnfi/public/admin/usuarios');
                exit();
            } else {
                SessionManager::setFlash('error_message', 'Error al crear el usuario. Revise los datos.');
                header('Location: /proyecto_pnfi/public/admin/usuarios/crear');
                exit();
            }
        }
        // Si no es POST, redirigir o mostrar error
        header('Location: /proyecto_pnfi/public/admin/usuarios');
        exit();
    }

    /**
     * Muestra el formulario para editar un usuario existente.
     * @param int $id El ID del usuario a editar.
     */
    public function edit(int $id): void {
        $user = $this->userModel->findById($id);
        if (!$user) {
            SessionManager::setFlash('error_message', 'Usuario no encontrado.');
            header('Location: /proyecto_pnfi/public/admin/usuarios');
            exit();
        }
        View::renderWithLayout('users/edit', [
            'titulo' => 'Editar Usuario',
            'user' => $user,
            'error_message' => SessionManager::getFlash('error_message')
        ]);
    }

    /**
     * Procesa los datos del formulario para actualizar un usuario existente.
     * @param int $id El ID del usuario a actualizar.
     */
    public function update(int $id): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'cedula' => trim($_POST['cedula'] ?? ''),
                'nombre' => trim($_POST['nombre'] ?? ''),
                'apellido' => trim($_POST['apellido'] ?? ''),
                'correo' => trim($_POST['correo'] ?? ''),
                'telefono' => trim($_POST['telefono'] ?? ''),
                'direccion' => trim($_POST['direccion'] ?? ''),
                'rol' => trim($_POST['rol'] ?? '')
            ];

            // Validación básica
            if (empty($data['cedula']) || empty($data['nombre']) || empty($data['apellido']) || empty($data['correo']) || empty($data['rol'])) {
                SessionManager::setFlash('error_message', 'Todos los campos obligatorios deben ser completados.');
                header('Location: /proyecto_pnfi/public/admin/usuarios/editar/' . $id);
                exit();
            }

            if (!empty($_POST['password'])) {
                $data['password'] = password_hash($_POST['password'], PASSWORD_DEFAULT);
            }

            if ($this->userModel->update($id, $data)) {
                SessionManager::setFlash('success_message', 'Usuario actualizado exitosamente.');
                header('Location: /proyecto_pnfi/public/admin/usuarios');
                exit();
            } else {
                SessionManager::setFlash('error_message', 'Error al actualizar el usuario. Revise los datos.');
                header('Location: /proyecto_pnfi/public/admin/usuarios/editar/' . $id);
                exit();
            }
        }
        header('Location: /proyecto_pnfi/public/admin/usuarios');
        exit();
    }

    /**
     * Elimina un usuario.
     * @param int $id El ID del usuario a eliminar.
     */
    public function destroy(int $id): void {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if ($this->userModel->softDelete($id, 'inactivo')) { // Asegura que se pasa 'inactivo'
            \App\Core\SessionManager::setFlash('success_message', 'Usuario desactivado exitosamente.');
        } else {
            \App\Core\SessionManager::setFlash('error_message', 'Error al desactivar el usuario.');
        }
    }
    header('Location: /proyecto_pnfi/public/admin/usuarios');
    exit();
}

/**
 * Activa lógicamente un usuario (cambia su estado a 'activo').
 * @param int $id El ID del usuario a activar.
 */
public function activateUser(int $id): void {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if ($this->userModel->softDelete($id, 'activo')) { // Llama a softDelete con 'activo'
            \App\Core\SessionManager::setFlash('success_message', 'Usuario activado exitosamente.');
        } else {
            \App\Core\SessionManager::setFlash('error_message', 'Error al activar el usuario.');
        }
    }
    header('Location: /proyecto_pnfi/public/admin/usuarios');
    exit();
}
}