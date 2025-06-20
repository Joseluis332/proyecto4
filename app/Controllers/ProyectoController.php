<?php

namespace App\Controllers;

use App\Core\View;
use App\Models\Proyecto;
use App\Models\Docente;
use App\Models\Comunidad;
use App\Models\Estudiante;
use App\Models\Usuario; // Necesario si Docente/Estudiante llaman a Usuario::find
use App\Core\SessionManager;
use PDO;
use PDOException;

/**
 * Clase ProyectoController
 *
 * Maneja las operaciones relacionadas con la gestión de proyectos.
 */
class ProyectoController extends Controller
{
    protected Proyecto $proyectoModel;
    protected Docente $docenteModel;
    protected Comunidad $comunidadModel;
    protected Estudiante $estudianteModel;
    protected Usuario $usuarioModel; // Instanciar si es necesario para alguna operación directa, aunque Docente/Estudiante ya lo usan

    /**
     * Constructor del ProyectoController.
     *
     * @param PDO $pdo Instancia de la conexión a la base de datos.
     */
    public function __construct(PDO $pdo)
    {
        parent::__construct($pdo); // Llama al constructor de la clase padre (Controller)

        $this->proyectoModel = new Proyecto($pdo);
        $this->docenteModel = new Docente($pdo);
        $this->comunidadModel = new Comunidad($pdo);
        $this->estudianteModel = new Estudiante($pdo);
        $this->usuarioModel = new Usuario($pdo); // También instanciar si se usa directamente
    }

    /**
     * Muestra la lista de todos los proyectos.
     * Ruta: GET /admin/proyectos
     */
    public function index(): void
    {
        $proyectos = $this->proyectoModel->getAllProjects('activo');

        View::render('proyectos/index', [
            'title' => 'Gestión de Proyectos',
            'proyectos' => $proyectos,
            'success_message' => SessionManager::getFlash('success_message'),
            'error_message' => SessionManager::getFlash('error_message')
        ]);
    }

    /**
     * Muestra el formulario para crear un nuevo proyecto.
     * Ruta: GET /admin/proyectos/crear
     */
    public function create(): void
    {
        $docentes = $this->docenteModel->getAllWithUserNames();
        $comunidades = $this->comunidadModel->getAllNames();
        $estudiantesNoAsignados = $this->estudianteModel->getAllUnassigned();

        View::render('proyectos/create', [
            'title' => 'Registrar Nuevo Proyecto',
            'docentes' => $docentes,
            'comunidades' => $comunidades,
            'estudiantesNoAsignados' => $estudiantesNoAsignados,
            'errors' => SessionManager::getFlash('errors'),
            'old_input' => SessionManager::getFlash('old_input')
        ]);
    }

    /**
     * Procesa el envío del formulario para almacenar un nuevo proyecto.
     * Ruta: POST /admin/proyectos
     */
    public function store(): void
    {
        $data = [
            'numero_proyecto' => filter_input(INPUT_POST, 'numero_proyecto', FILTER_SANITIZE_STRING),
            'nombre' => filter_input(INPUT_POST, 'nombre', FILTER_SANITIZE_STRING),
            'descripcion' => filter_input(INPUT_POST, 'descripcion', FILTER_SANITIZE_STRING),
            'fecha_inicio' => filter_input(INPUT_POST, 'fecha_inicio', FILTER_SANITIZE_STRING),
            'fecha_fin_estimada' => filter_input(INPUT_POST, 'fecha_fin_estimada', FILTER_SANITIZE_STRING) ?: null,
            'estado' => filter_input(INPUT_POST, 'estado', FILTER_SANITIZE_STRING),
            'id_tutor_docente_fk' => filter_input(INPUT_POST, 'id_tutor_docente_fk', FILTER_SANITIZE_NUMBER_INT),
            'id_comunidad_fk' => filter_input(INPUT_POST, 'id_comunidad_fk', FILTER_SANITIZE_NUMBER_INT),
        ];

        $selectedStudentIds = $_POST['estudiantes'] ?? [];
        if (!is_array($selectedStudentIds)) {
            $selectedStudentIds = [];
        } else {
            $selectedStudentIds = array_map('intval', $selectedStudentIds);
            $selectedStudentIds = array_filter($selectedStudentIds, function($id) { return $id > 0; });
        }

        $errors = $this->proyectoModel->validate($data); // <--- Ahora este método debería existir

        // Validar que los IDs de las FK sean válidos
        // $this->docenteModel->find($id) ahora devolverá una INSTANCIA del modelo o false
        if (!empty($data['id_tutor_docente_fk']) && !$this->docenteModel->find($data['id_tutor_docente_fk'])) {
            $errors['id_tutor_docente_fk'] = 'El tutor seleccionado no es válido.';
        }
        if (!empty($data['id_comunidad_fk']) && !$this->comunidadModel->find($data['id_comunidad_fk'])) {
            $errors['id_comunidad_fk'] = 'La comunidad seleccionada no es válida.';
        }

        if (!empty($errors)) {
            SessionManager::setFlash('errors', $errors);
            SessionManager::setFlash('old_input', $_POST);
            header('Location: /proyecto_pnfi/public/admin/proyectos/crear');
            exit();
        }

        try {
            $this->pdo->beginTransaction();

            $newProjectId = $this->proyectoModel->save($data); // <--- Ahora este método debería existir

            if ($newProjectId) {
                if (!empty($selectedStudentIds)) {
                    $associated = $this->proyectoModel->associateStudents($selectedStudentIds, $newProjectId);
                    if (!$associated) {
                        throw new PDOException("Fallo al asociar estudiantes al proyecto.");
                    }
                }
                $this->pdo->commit();
                SessionManager::setFlash('success_message', 'Proyecto registrado exitosamente.');
                header('Location: /proyecto_pnfi/public/admin/proyectos');
                exit();
            } else {
                throw new PDOException("Fallo al guardar el proyecto en la base de datos.");
            }
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            error_log("Error al registrar proyecto: " . $e->getMessage());
            SessionManager::setFlash('error_message', 'Error al registrar el proyecto. Inténtelo de nuevo.');
            SessionManager::setFlash('old_input', $_POST);
            header('Location: /proyecto_pnfi/public/admin/proyectos/crear');
            exit();
        }
    }

    /**
     * Muestra los detalles de un proyecto específico.
     * Ruta: GET /admin/proyectos/ver/{id}
     *
     * @param int $id El ID del proyecto.
     */
    public function show(int $id): void
    {
        $proyecto = $this->proyectoModel->getProjectByIdWithDetails($id); // <--- Este método debería devolver un array

        if (!$proyecto) {
            SessionManager::setFlash('error_message', 'Proyecto no encontrado.');
            header('Location: /proyecto_pnfi/public/admin/proyectos');
            exit();
        }

        $estudiantesAsociados = $this->estudianteModel->findBy('id_proyecto_fk', $id);

        View::render('proyectos/show', [
            'title' => 'Detalles del Proyecto: ' . htmlspecialchars($proyecto['nombre']),
            'proyecto' => $proyecto,
            'estudiantesAsociados' => $estudiantesAsociados
        ]);
    }

    /**
     * Maneja la eliminación lógica de un proyecto.
     * Ruta: POST /admin/proyectos/eliminar/{id}
     *
     * @param int $id El ID del proyecto a eliminar.
     */
    public function delete(int $id): void
    {
        $proyecto = $this->proyectoModel->find($id); // <--- Ahora este método debería existir y devolver una instancia o false
        if (!$proyecto) {
            SessionManager::setFlash('error_message', 'Proyecto no encontrado para eliminar.');
            header('Location: /proyecto_pnfi/public/admin/proyectos');
            exit();
        }

        try {
            $this->pdo->beginTransaction();

            $estudiantesEnProyecto = $this->estudianteModel->findBy('id_proyecto_fk', $id);
            if (!empty($estudiantesEnProyecto)) {
                $studentIdsToDisassociate = array_column($estudiantesEnProyecto, 'id_estudiante');
                $disassociated = $this->proyectoModel->disassociateStudents($studentIdsToDisassociate);
                if (!$disassociated) {
                    throw new PDOException("Fallo al desasociar estudiantes del proyecto.");
                }
            }

            $updated = $this->proyectoModel->update($id, ['estado' => 'cancelado']); // <--- Ahora este método debería existir

            if ($updated) {
                $this->pdo->commit();
                SessionManager::setFlash('success_message', 'Proyecto eliminado lógicamente y estudiantes desasociados.');
            } else {
                throw new PDOException("Fallo al cambiar el estado del proyecto a 'cancelado'.");
            }
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            error_log("Error al eliminar proyecto lógicamente: " . $e->getMessage());
            SessionManager::setFlash('error_message', 'Error al eliminar el proyecto. Inténtelo de nuevo.');
        }

        header('Location: /proyecto_pnfi/public/admin/proyectos');
        exit();
    }
}
