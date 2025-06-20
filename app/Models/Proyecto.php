<?php

namespace App\Models;

use PDO; // Necesario para el constructor de BaseModel
use PDOException; // Importar PDOException para manejo de errores

/**
 * Clase Proyecto
 *
 * Representa la tabla 'proyectos' en la base de datos.
 * Hereda de BaseModel para operaciones CRUD básicas.
 */
class Proyecto extends BaseModel
{
    /**
     * @var string Nombre de la tabla asociada al modelo.
     */
    protected string $table = 'proyectos';

    /**
     * @var string Nombre de la clave primaria de la tabla.
     */
    protected string $primaryKey = 'id_proyecto';

    /**
     * @var array Campos de la tabla que pueden ser llenados masivamente.
     */
    protected array $fillable = [
        'numero_proyecto', 'nombre', 'descripcion',
        'fecha_inicio', 'fecha_fin_estimada', 'estado',
        'id_tutor_docente_fk', 'id_comunidad_fk'
    ];

    /**
     * @var array Reglas de validación para los campos del modelo.
     */
    protected array $validationRules = [
        'numero_proyecto' => 'required|max:50|unique:proyectos,numero_proyecto', // unique para el número de proyecto
        'nombre' => 'required|max:255',
        'descripcion' => 'required',
        'fecha_inicio' => 'required|date',
        'fecha_fin_estimada' => 'nullable|date|after_or_equal:fecha_inicio',
        'estado' => 'required|in:activo,culminado,pendiente,cancelado', // Ajustado a los valores CHECK de tu BD
        'id_tutor_docente_fk' => 'required|integer',
        'id_comunidad_fk' => 'required|integer'
    ];

    /**
     * Constructor de la clase Proyecto.
     *
     * @param PDO $pdo Instancia de la conexión a la base de datos.
     */
    public function __construct(PDO $pdo)
    {
        parent::__construct($pdo);
    }

    /**
     * Asocia estudiantes a este proyecto.
     * Esto actualizará el id_proyecto_fk en la tabla 'estudiantes'
     * para cada estudiante dado.
     *
     * @param array $studentIds Array de IDs de estudiantes a asociar.
     * @param int $projectId ID del proyecto al que se asociarán los estudiantes.
     * @return bool True si la operación fue exitosa, false en caso contrario.
     */
    public function associateStudents(array $studentIds, int $projectId): bool
    {
        if (empty($studentIds) || $projectId <= 0) {
            return false;
        }

        // Preparamos la consulta para actualizar el id_proyecto_fk de los estudiantes
        $placeholders = implode(',', array_fill(0, count($studentIds), '?'));
        $sql = "UPDATE estudiantes SET id_proyecto_fk = ? WHERE id_estudiante IN ($placeholders)";

        try {
            $stmt = $this->pdo->prepare($sql);
            // Los parámetros son el ID del proyecto primero, luego los IDs de los estudiantes
            $params = array_merge([$projectId], $studentIds);
            return $stmt->execute($params);
        } catch (PDOException $e) {
            error_log("Error al asociar estudiantes al proyecto: " . $e->getMessage());
            return false;
        }
    }


    /**
     * Desasocia estudiantes de este proyecto (poniendo id_proyecto_fk a NULL).
     * Útil si un estudiante ya no está en este proyecto.
     *
     * @param array $studentIds Array de IDs de estudiantes a desasociar.
     * @return bool True si la operación fue exitosa, false en caso contrario.
     */
    public function disassociateStudents(array $studentIds): bool
    {
        if (empty($studentIds)) {
            return true; // No hay estudiantes para desasociar
        }

        $placeholders = implode(',', array_fill(0, count($studentIds), '?'));
        $sql = "UPDATE estudiantes SET id_proyecto_fk = NULL WHERE id_estudiante IN ($placeholders)";

        try {
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute($studentIds);
        } catch (PDOException $e) {
            error_log("Error al desasociar estudiantes del proyecto: " . $e->getMessage());
            return false;
        }
    }


    /**
     * Obtiene los estudiantes asociados a este proyecto.
     *
     * @return array Lista de estudiantes.
     */
    public function getStudents(): array
    {
        $estudianteModel = new Estudiante($this->pdo);
        // Suponemos que Estudiante model tiene un método para buscar por FK
        // Y que 'id_proyecto' existe como propiedad en la instancia actual de Proyecto
        return $estudianteModel->findBy('id_proyecto_fk', $this->id_proyecto);
    }

    /**
     * Obtiene el tutor docente asociado a este proyecto.
     *
     * @return array|false Datos del docente o false si no se encuentra.
     */
    public function getTutor(): array|false
    {
        $docenteModel = new Docente($this->pdo);
        // Suponemos que 'id_tutor_docente_fk' existe como propiedad en la instancia actual de Proyecto
        return $docenteModel->find($this->id_tutor_docente_fk);
    }

    /**
     * Obtiene la comunidad asociada a este proyecto.
     *
     * @return array|false Datos de la comunidad o false si no se encuentra.
     */
    public function getComunidad(): array|false
    {
        $comunidadModel = new Comunidad($this->pdo);
        // Suponemos que 'id_comunidad_fk' existe como propiedad en la instancia actual de Proyecto
        return $comunidadModel->find($this->id_comunidad_fk);
    }
    
    /**
     * Obtiene todos los proyectos con información combinada de tutor y comunidad.
     *
     * @param string $status Estado para filtrar ('activo', 'culminado', 'pendiente', 'cancelado').
     * @return array Lista de proyectos.
     */
    public function getAllProjects(string $status = 'activo'): array
    {
        // Usamos el campo 'estado' de tu tabla `proyectos` para filtrar
        $sql = "SELECT p.*, d.cargo as tutor_cargo, u.nombre as tutor_nombre, u.apellido as tutor_apellido, c.nombre as comunidad_nombre
                FROM proyectos p
                JOIN docentes d ON p.id_tutor_docente_fk = d.id_docente
                JOIN usuarios u ON d.id_usuario_fk = u.id_usuario
                JOIN comunidades c ON p.id_comunidad_fk = c.id_comunidad
                WHERE p.estado = ?
                ORDER BY p.fecha_creacion DESC";
        
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$status]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener todos los proyectos con detalles: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtiene un proyecto por su ID con información combinada de tutor y comunidad.
     *
     * @param int $id El ID del proyecto.
     * @return array|false Datos del proyecto o false si no se encuentra.
     */
    public function getProjectByIdWithDetails(int $id): array|false
    {
        $sql = "SELECT p.*, d.cargo as tutor_cargo, u.nombre as tutor_nombre, u.apellido as tutor_apellido, c.nombre as comunidad_nombre
                FROM proyectos p
                JOIN docentes d ON p.id_tutor_docente_fk = d.id_docente
                JOIN usuarios u ON d.id_usuario_fk = u.id_usuario
                JOIN comunidades c ON p.id_comunidad_fk = c.id_comunidad
                WHERE p.id_proyecto = ?";
        
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener proyecto por ID con detalles: " . $e->getMessage());
            return false;
        }
    }
}
