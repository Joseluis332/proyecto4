<?php

namespace App\Models;

use PDO; // Necesario para el constructor de BaseModel
use PDOException; // Importar para manejo de excepciones específicas de PDO

/**
 * Clase Estudiante
 *
 * Representa la tabla 'estudiantes' en la base de datos.
 * Hereda de BaseModel para operaciones CRUD básicas.
 */
class Estudiante extends BaseModel
{
    /**
     * @var string Nombre de la tabla asociada al modelo.
     */
    protected string $table = 'estudiantes';

    /**
     * @var string Nombre de la clave primaria de la tabla.
     */
    protected string $primaryKey = 'id_estudiante';

    /**
     * @var array Campos de la tabla que pueden ser llenados masivamente.
     */
    protected array $fillable = ['id_usuario_fk', 'carrera', 'semestre', 'fecha_matricula', 'id_proyecto_fk'];

    /**
     * @var array Reglas de validación para los campos del modelo.
     */
    protected array $validationRules = [
        'id_usuario_fk' => 'required|integer',
        'carrera' => 'required|max:100',
        'semestre' => 'required|integer'
        // Añade más reglas de validación si es necesario
    ];

    /**
     * Constructor de la clase Estudiante.
     *
     * @param PDO $pdo Instancia de la conexión a la base de datos.
     */
    public function __construct(PDO $pdo)
    {
        // Llama al constructor de la clase padre (BaseModel)
        parent::__construct($pdo);
    }

    /**
     * Obtiene los datos del usuario asociado a este estudiante.
     *
     * @return array|false Los datos del usuario o false si no se encuentra.
     */
    public function getUsuario(): array|false
    {
        $usuarioModel = new Usuario($this->pdo); // Instancia el modelo Usuario
        return $usuarioModel->find($this->id_usuario_fk); // Busca el usuario por su ID
    }

    /**
     * Busca estudiantes por un campo específico.
     * Incluye datos del usuario asociado (nombre, apellido, cédula).
     *
     * @param string $field El nombre del campo por el cual buscar (ej. 'id_proyecto_fk').
     * @param mixed $value El valor a buscar.
     * @return array Lista de estudiantes que coinciden, con sus detalles de usuario.
     */
    public function findBy(string $field, mixed $value): array
    {
        $sql = "SELECT e.*, u.nombre, u.apellido, u.cedula
                FROM {$this->table} e
                JOIN usuarios u ON e.id_usuario_fk = u.id_usuario
                WHERE e.{$field} = ?";
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$value]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al buscar estudiantes por {$field}: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtiene todos los estudiantes que no están asignados a ningún proyecto (id_proyecto_fk es NULL).
     * Incluye nombre, apellido y cédula del usuario asociado para facilitar la selección.
     *
     * @return array Array de estudiantes no asignados.
     */
    public function getAllUnassigned(): array
    {
        $sql = "SELECT e.id_estudiante, u.cedula, u.nombre, u.apellido
                FROM estudiantes e
                JOIN usuarios u ON e.id_usuario_fk = u.id_usuario
                WHERE e.id_proyecto_fk IS NULL
                ORDER BY u.nombre, u.apellido ASC"; // Ordenar para una mejor visualización
        
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener estudiantes no asignados: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtiene todos los estudiantes con su información de usuario combinada.
     * Útil para listados completos.
     *
     * @return array Array de estudiantes con todos los detalles de usuario y estudiante.
     */
    public function getAllWithUserDetails(): array
    {
        $sql = "SELECT e.id_estudiante, e.carrera, e.semestre, e.fecha_matricula, e.id_proyecto_fk,
                       u.cedula, u.nombre, u.apellido, u.correo, u.telefono, u.direccion, u.rol, u.estado
                FROM estudiantes e
                JOIN usuarios u ON e.id_usuario_fk = u.id_usuario
                ORDER BY u.nombre, u.apellido ASC";
        
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener todos los estudiantes con detalles de usuario: " . $e->getMessage());
            return [];
        }
    }
}