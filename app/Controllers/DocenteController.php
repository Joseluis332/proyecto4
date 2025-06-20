<?php

namespace App\Controllers;

use App\Core\View;
use App\Models\Docente;
use App\Models\Usuario;
use App\Models\Estudiante; // Necesario para la validación en store/update
use App\Models\Proyecto; // Necesario para la validación en delete
use App\Core\SessionManager;
use PDO;
use PDOException;

/**
 * Clase DocenteController
 *
 * Maneja las operaciones CRUD para los docentes.
 */
class DocenteController extends Controller
{
    protected Docente $docenteModel;
    protected Usuario $usuarioModel;
    protected Estudiante $estudianteModel; // <--- ¡DECLARACIÓN AÑADIDA!
    protected Proyecto $proyectoModel; // <--- ¡DECLARACIÓN AÑADIDA!

    /**
     * Constructor del DocenteController.
     *
     * @param PDO $pdo Instancia de la conexión a la base de datos.
     */
    public function __construct(PDO $pdo)
    {
        parent::__construct($pdo);
        $this->docenteModel = new Docente($pdo);
        $this->usuarioModel = new Usuario($pdo);
        $this->estudianteModel = new Estudiante($pdo); // <--- ¡INSTANCIA AÑADIDA!
        $this->proyectoModel = new Proyecto($pdo); // <--- ¡INSTANCIA AÑADIDA!
    }

    /**
     * Muestra la lista de todos los docentes.
     * Ruta: GET /admin/docentes
     */
    public function index(): void
    {
        $docentes = $this->docenteModel->all();
        $docentesWithDetails = [];
        foreach ($docentes as $docente) {
            $docenteData = $docente->data;
            $usuario = $this->usuarioModel->find($docente->id_usuario_fk); // Acceso directo a la propiedad gracias a __get
            if ($usuario) {
                $docenteData['usuario_nombre'] = $usuario->nombre;
                $docenteData['usuario_apellido'] = $usuario->apellido;
                $docenteData['usuario_cedula'] = $usuario->cedula;
                $docenteData['usuario_correo'] = $usuario->correo;
            } else {
                $docenteData['usuario_nombre'] = 'N/A';
                $docenteData['usuario_apellido'] = 'N/A';
                $docenteData['usuario_cedula'] = 'N/A';
                $docenteData['usuario_correo'] = 'N/A';
            }
            $docentesWithDetails[] = $docenteData;
        }

        View::render('docentes/index', [
            'title' => 'Gestión de Docentes',
            'docentes' => $docentesWithDetails,
            'success_message' => SessionManager::getFlash('success_message'),
            'error_message' => SessionManager::getFlash('error_message')
        ]);
    }

    /**
     * Muestra el formulario para crear un nuevo docente.
     * Ruta: GET /admin/docentes/crear
     */
    public function create(): void
    {
        $unassignedUsers = $this->usuarioModel->getUnassignedUsers();

        View::render('docentes/create', [
            'title' => 'Registrar Nuevo Docente',
            'unassignedUsers' => $unassignedUsers,
            'errors' => SessionManager::getFlash('errors'),
            'old_input' => SessionManager::getFlash('old_input')
        ]);
    }

    /**
     * Procesa el envío del formulario para almacenar un nuevo docente.
     * Ruta: POST /admin/docentes
     */
    public function store(): void
    {
        $data = [
            'id_usuario_fk' => filter_input(INPUT_POST, 'id_usuario_fk', FILTER_SANITIZE_NUMBER_INT),
            'cargo' => filter_input(INPUT_POST, 'cargo', FILTER_SANITIZE_STRING),
            'especialidad' => filter_input(INPUT_POST, 'especialidad', FILTER_SANITIZE_STRING),
            'fecha_ingreso' => filter_input(INPUT_POST, 'fecha_ingreso', FILTER_SANITIZE_STRING)
        ];

        $errors = $this->docenteModel->validate($data);

        if (!empty($data['id_usuario_fk'])) {
            $usuario = $this->usuarioModel->find($data['id_usuario_fk']);
            if (!$usuario) {
                $errors['id_usuario_fk'] = 'El usuario seleccionado no existe.';
            } else {
                // Verificar si este usuario ya es docente o estudiante utilizando findWhere
                $existingDocente = $this->docenteModel->findWhere('id_usuario_fk', $data['id_usuario_fk']);
                $existingEstudiante = $this->estudianteModel->findWhere('id_usuario_fk', $data['id_usuario_fk']);
                if ($existingDocente || $existingEstudiante) {
                    $errors['id_usuario_fk'] = 'Este usuario ya está asignado como docente o estudiante.';
                }
            }
        }

        if (!empty($errors)) {
            SessionManager::setFlash('errors', $errors);
            SessionManager::setFlash('old_input', $_POST);
            header('Location: /proyecto_pnfi/public/admin/docentes/crear');
            exit();
        }

        try {
            $this->pdo->beginTransaction();
            $newDocenteId = $this->docenteModel->save($data);

            if ($newDocenteId) {
                $usuario = $this->usuarioModel->find($data['id_usuario_fk']);
                if ($usuario && $usuario->rol !== 'docente') {
                    $this->usuarioModel->update($data['id_usuario_fk'], ['rol' => 'docente']);
                }

                $this->pdo->commit();
                SessionManager::setFlash('success_message', 'Docente registrado exitosamente.');
                header('Location: /proyecto_pnfi/public/admin/docentes');
                exit();
            } else {
                throw new PDOException("Fallo al guardar el docente en la base de datos.");
            }
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            error_log("Error al registrar docente: " . $e->getMessage());
            SessionManager::setFlash('error_message', 'Error al registrar el docente. Inténtelo de nuevo.');
            SessionManager::setFlash('old_input', $_POST);
            header('Location: /proyecto_pnfi/public/admin/docentes/crear');
            exit();
        }
    }

    /**
     * Muestra los detalles de un docente específico.
     * Ruta: GET /admin/docentes/ver/{id}
     *
     * @param int $id El ID del docente.
     */
    public function show(int $id): void
    {
        $docente = $this->docenteModel->find($id);

        if (!$docente) {
            SessionManager::setFlash('error_message', 'Docente no encontrado.');
            header('Location: /proyecto_pnfi/public/admin/docentes');
            exit();
        }

        $usuario = $this->usuarioModel->find($docente->id_usuario_fk);

        View::render('docentes/show', [
            'title' => 'Detalles del Docente',
            'docente' => $docente->data,
            'usuario' => $usuario ? $usuario->data : null
        ]);
    }

    /**
     * Muestra el formulario para editar un docente existente.
     * Ruta: GET /admin/docentes/editar/{id}
     *
     * @param int $id El ID del docente a editar.
     */
    public function edit(int $id): void
    {
        $docente = $this->docenteModel->find($id);

        if (!$docente) {
            SessionManager::setFlash('error_message', 'Docente no encontrado para editar.');
            header('Location: /proyecto_pnfi/public/admin/docentes');
            exit();
        }

        $currentUsuario = $this->usuarioModel->find($docente->id_usuario_fk);
        $unassignedUsers = $this->usuarioModel->getUnassignedUsers();

        // Si el usuario actual del docente no está en la lista de no asignados, lo añadimos
        $found = false;
        foreach ($unassignedUsers as $user) {
            if ($currentUsuario && $user['id_usuario'] === $currentUsuario->id_usuario) {
                $found = true;
                break;
            }
        }
        if (!$found && $currentUsuario) {
            $unassignedUsers[] = $currentUsuario->data;
        }
        
        $old_input = SessionManager::getFlash('old_input') ?? $docente->data;

        View::render('docentes/edit', [
            'title' => 'Editar Docente',
            'docente' => $docente->data,
            'unassignedUsers' => $unassignedUsers,
            'errors' => SessionManager::getFlash('errors'),
            'old_input' => $old_input
        ]);
    }

    /**
     * Procesa el envío del formulario para actualizar un docente.
     * Ruta: POST /admin/docentes/actualizar/{id}
     *
     * @param int $id El ID del docente a actualizar.
     */
    public function update(int $id): void
    {
        $docente = $this->docenteModel->find($id);
        if (!$docente) {
            SessionManager::setFlash('error_message', 'Docente no encontrado para actualizar.');
            header('Location: /proyecto_pnfi/public/admin/docentes');
            exit();
        }

        $data = [
            'id_usuario_fk' => filter_input(INPUT_POST, 'id_usuario_fk', FILTER_SANITIZE_NUMBER_INT),
            'cargo' => filter_input(INPUT_POST, 'cargo', FILTER_SANITIZE_STRING),
            'especialidad' => filter_input(INPUT_POST, 'especialidad', FILTER_SANITIZE_STRING),
            'fecha_ingreso' => filter_input(INPUT_POST, 'fecha_ingreso', FILTER_SANITIZE_STRING)
        ];

        // Se pasa el ID del docente actual para que la validación 'unique' lo excluya
        $errors = $this->docenteModel->validate($data + [$this->docenteModel->primaryKey => $id]);

        if (!empty($data['id_usuario_fk'])) {
            $usuario = $this->usuarioModel->find($data['id_usuario_fk']);
            if (!$usuario) {
                $errors['id_usuario_fk'] = 'El usuario seleccionado no existe.';
            } else {
                // Verificar si este usuario ya es docente (excluyendo el docente actual) o estudiante
                $existingDocente = $this->docenteModel->findWhere('id_usuario_fk', $data['id_usuario_fk']);
                $existingEstudiante = $this->estudianteModel->findWhere('id_usuario_fk', $data['id_usuario_fk']);

                if ($existingDocente && $existingDocente->id_docente != $id) {
                    $errors['id_usuario_fk'] = 'Este usuario ya está asignado a otro docente.';
                }
                if ($existingEstudiante) {
                    $errors['id_usuario_fk'] = 'Este usuario ya está asignado como estudiante.';
                }
            }
        }

        if (!empty($errors)) {
            SessionManager::setFlash('errors', $errors);
            SessionManager::setFlash('old_input', $_POST);
            header('Location: /proyecto_pnfi/public/admin/docentes/editar/' . $id);
            exit();
        }

        try {
            $this->pdo->beginTransaction();
            $updated = $this->docenteModel->update($id, $data);

            if ($updated) {
                $usuario = $this->usuarioModel->find($data['id_usuario_fk']);
                if ($usuario && $usuario->rol !== 'docente') {
                    $this->usuarioModel->update($data['id_usuario_fk'], ['rol' => 'docente']);
                }
                
                // Lógica para el usuario anterior si id_usuario_fk cambió
                if ($docente->id_usuario_fk != $data['id_usuario_fk']) {
                    $oldUsuario = $this->usuarioModel->find($docente->id_usuario_fk);
                    // Aquí podrías añadir lógica para cambiar el rol del oldUsuario si ya no tiene asignaciones
                    // Ejemplo: si oldUsuario no es estudiante, pon su rol a 'usuario' o 'inactivo'
                }

                $this->pdo->commit();
                SessionManager::setFlash('success_message', 'Docente actualizado exitosamente.');
                header('Location: /proyecto_pnfi/public/admin/docentes');
                exit();
            } else {
                throw new PDOException("Fallo al actualizar el docente en la base de datos.");
            }
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            error_log("Error al actualizar docente: " . $e->getMessage());
            SessionManager::setFlash('error_message', 'Error al actualizar el docente. Inténtelo de nuevo.');
            SessionManager::setFlash('old_input', $_POST);
            header('Location: /proyecto_pnfi/public/admin/docentes/editar/' . $id);
            exit();
        }
    }

    /**
     * Maneja la eliminación de un docente (eliminación física si no hay dependencia de proyectos).
     * Ruta: POST /admin/docentes/eliminar/{id}
     *
     * @param int $id El ID del docente a eliminar.
     */
    public function delete(int $id): void
    {
        $docente = $this->docenteModel->find($id);
        if (!$docente) {
            SessionManager::setFlash('error_message', 'Docente no encontrado para eliminar.');
            header('Location: /proyecto_pnfi/public/admin/docentes');
            exit();
        }

        try {
            $this->pdo->beginTransaction();

            // Verificar si el docente está asignado a algún proyecto
            $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM proyectos WHERE id_tutor_docente_fk = ?");
            $stmt->execute([$id]);
            $countProyectosAsignados = $stmt->fetchColumn();

            if ($countProyectosAsignados > 0) { // Si el conteo es mayor a 0, significa que hay proyectos
                SessionManager::setFlash('error_message', 'No se puede eliminar el docente porque tiene proyectos asignados. Primero reasigne los proyectos.');
                header('Location: /proyecto_pnfi/public/admin/docentes');
                exit();
            }

            $deleted = $this->docenteModel->delete($id); // <--- delete existe en BaseModel

            if ($deleted) {
                $this->pdo->commit();
                SessionManager::setFlash('success_message', 'Docente eliminado exitosamente.');
            } else {
                throw new PDOException("Fallo al eliminar el docente.");
            }
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            error_log("Error al eliminar docente: " . $e->getMessage());
            SessionManager::setFlash('error_message', 'Error al eliminar el docente. Inténtelo de nuevo.');
        }

        header('Location: /proyecto_pnfi/public/admin/docentes');
        exit();
    }
}
