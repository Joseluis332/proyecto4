<?php

namespace App\Models;

use PDO; // Necesario para el constructor de BaseModel
use PDOException; // Importar PDOException para manejo de errores

/**
 * Clase Docente
 *
 * Representa la tabla 'docentes' en la base de datos.
 * Hereda de BaseModel para operaciones CRUD básicas.
 */
class Docente extends BaseModel
{
    /**
     * @var string Nombre de la tabla asociada al modelo.
     */
    protected string $table = 'docentes';

    /**
     * @var string Nombre de la clave primaria de la tabla.
     */
    protected string $primaryKey = 'id_docente';

    /**
     * @var array Campos de la tabla que pueden ser llenados masivamente.
     */
    protected array $fillable = ['id_usuario_fk', 'cargo', 'especialidad', 'fecha_ingreso'];

    /**
     * @var array Reglas de validación para los campos del modelo.
     */
    protected array $validationRules = [
        'id_usuario_fk' => 'required|integer',
        'especialidad' => 'required|max:100'
        // Añade más reglas de validación si es necesario para otros campos
    ];

    /**
     * Constructor de la clase Docente.
     *
     * @param PDO $pdo Instancia de la conexión a la base de datos.
     */
    public function __construct(PDO $pdo)
    {
        // Llama al constructor de la clase padre (BaseModel) para inicializar la conexión PDO
        parent::__construct($pdo);
    }

    /**
     * Obtiene los datos del usuario asociado a este docente.
     * Esto asume que existe un modelo Usuario para buscar la información.
     *
     * @return array|false Los datos del usuario asociado o false si no se encuentra.
     */
    public function getUsuario(): array|false
    {
        // Se instancia el modelo Usuario usando la misma conexión PDO
        $usuarioModel = new Usuario($this->pdo);
        // Se busca el usuario por la clave foránea id_usuario_fk
        return $usuarioModel->find($this->id_usuario_fk);
    }

    /**
     * Obtiene todos los docentes con el nombre y apellido del usuario asociado.
     * Esto es útil para poblar selectores en formularios.
     *
     * @return array Array de docentes con su ID, nombre y apellido del usuario.
     */
    public function getAllWithUserNames(): array
    {
        try {
            // Se realiza un JOIN con la tabla 'usuarios' para obtener el nombre y apellido
            $stmt = $this->pdo->prepare("SELECT d.id_docente, u.nombre, u.apellido FROM docentes d JOIN usuarios u ON d.id_usuario_fk = u.id_usuario ORDER BY u.nombre, u.apellido ASC");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener docentes con nombres de usuario: " . $e->getMessage());
            return []; // Retorna un array vacío en caso de error
        }
    }
}