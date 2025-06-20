<?php

namespace App\Models;

use PDO; // Necesario para el constructor de BaseModel
use PDOException; // Importar PDOException para manejo de errores

/**
 * Clase Comunidad
 *
 * Representa la tabla 'comunidades' en la base de datos.
 * Hereda de BaseModel para operaciones CRUD básicas.
 */
class Comunidad extends BaseModel
{
    /**
     * @var string Nombre de la tabla asociada al modelo.
     */
    protected string $table = 'comunidades';

    /**
     * @var string Nombre de la clave primaria de la tabla.
     */
    protected string $primaryKey = 'id_comunidad';

    /**
     * @var array Campos de la tabla que pueden ser llenados masivamente.
     */
    protected array $fillable = [
        'rif', 'nombre', 'ubicacion_estado', 'ubicacion_municipio',
        'ubicacion_parroquia', 'ubicacion_calle_avenida',
        'ubicacion_codigo_postal', 'id_responsable_fk',
        'correo_contacto', 'telefono_contacto'
    ];

    /**
     * @var array Reglas de validación para los campos del modelo.
     */
    protected array $validationRules = [
        'rif' => 'required|max:20',
        'nombre' => 'required|max:150',
        'ubicacion_estado' => 'required|max:100',
        'id_responsable_fk' => 'required|integer'
        // Añade más reglas de validación si es necesario
    ];

    /**
     * Constructor de la clase Comunidad.
     *
     * @param PDO $pdo Instancia de la conexión a la base de datos.
     */
    public function __construct(PDO $pdo)
    {
        // Llama al constructor de la clase padre (BaseModel)
        parent::__construct($pdo);
    }

    /**
     * Obtiene solo el ID y el nombre de todas las comunidades.
     * Útil para poblar selectores en formularios.
     *
     * @return array Array de comunidades con solo su ID y nombre.
     */
    public function getAllNames(): array
    {
        try {
            $stmt = $this->pdo->prepare("SELECT id_comunidad, nombre FROM comunidades ORDER BY nombre ASC");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener nombres de comunidades: " . $e->getMessage());
            return []; // Retorna un array vacío en caso de error
        }
    }
}