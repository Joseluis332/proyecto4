<?php

namespace App\Controllers;

use PDO; // Asegúrate de que esto esté si tu constructor de BaseController lo necesita
use App\Core\View; // Para renderizar la vista

class AdminController extends BaseController {
    // El constructor es importante para inyectar la conexión PDO del Router
    public function __construct(PDO $pdo) {
        parent::__construct($pdo); // Llama al constructor de BaseController
        // Aquí podrías instanciar modelos específicos para el administrador, si los necesitas
        // Por ejemplo: $this->usuarioModel = new \App\Models\Usuario($pdo);
    }

    /**
     * Muestra el dashboard principal del administrador.
     */
    public function dashboard(): void {
        // Aquí podrías cargar datos para el dashboard, por ejemplo:
        // $totalUsuarios = $this->usuarioModel->countAll();
        // $datosParaVista = ['totalUsuarios' => $totalUsuarios];

        // Renderiza la vista del dashboard del administrador
        // La vista se buscará en app/Views/admin/dashboard.php
        View::renderWithLayout('admin/dashboard', [
            'titulo' => 'Dashboard de Administrador',
            'mensaje' => 'Bienvenido al panel de administración.'
        ]);
    }

    // Puedes añadir más métodos para otras funcionalidades de administración (ej. manageUsers, settings)
}