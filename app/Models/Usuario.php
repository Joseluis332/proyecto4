<?php

namespace App\Models;

use PDO;
use PDOException;

/**
 * Clase Usuario
 *
 * Representa la tabla 'usuarios' en la base de datos.
 * Hereda de BaseModel para operaciones CRUD básicas.
 */
class Usuario extends BaseModel
{
    /**
     * @var string Nombre de la tabla asociada al modelo.
     */
    protected string $table = 'usuarios';

    /**
     * @var string Nombre de la clave primaria de la tabla.
     */
    protected string $primaryKey = 'id_usuario';

    /**
     * @var array Campos de la tabla que pueden ser llenados masivamente.
     */
    protected array $fillable = [
        'cedula', 'nombre', 'apellido', 'correo', 'telefono', 'direccion',
        'password', 'rol', 'estado'
    ];

    /**
     * @var array Reglas de validación para los campos del modelo.
     */
    protected array $validationRules = [
        'cedula' => 'required|max:20|unique:usuarios,cedula',
        'nombre' => 'required|max:100',
        'apellido' => 'required|max:100',
        'correo' => 'required|email|max:150|unique:usuarios,correo',
        'password' => 'required|min:8',
        'rol' => 'required|in:administrador,docente,estudiante,secretaria',
        'estado' => 'required|in:activo,inactivo'
    ];

    /**
     * Constructor de la clase Usuario.
     *
     * @param PDO $pdo Instancia de la conexión a la base de datos.
     */
    public function __construct(PDO $pdo)
    {
        parent::__construct($pdo);
    }

    /**
     * Busca un usuario por su cédula.
     *
     * @param string $cedula La cédula del usuario.
     * @return Usuario|false Una instancia del modelo Usuario con los datos, o false si no se encuentra.
     */
    public function findByCedula(string $cedula): self|false
    {
        return $this->findWhere('cedula', $cedula); // Usa findWhere del BaseModel
    }

    /**
     * Busca un usuario por su correo electrónico.
     *
     * @param string $correo El correo electrónico del usuario.
     * @return Usuario|false Una instancia del modelo Usuario con los datos, o false si no se encuentra.
     */
    public function findByCorreo(string $correo): self|false
    {
        return $this->findWhere('correo', $correo); // Usa findWhere del BaseModel
    }

    /**
     * Cambia la contraseña de un usuario.
     *
     * @param int $id ID del usuario.
     * @param string $newPassword Nueva contraseña (se hashea automáticamente).
     * @return bool True si la contraseña fue actualizada, false en caso contrario.
     */
    public function changePassword(int $id, string $newPassword): bool
    {
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        return $this->update($id, ['password' => $hashedPassword]);
    }

    /**
     * Obtiene todos los usuarios que aún no están asignados como docentes o estudiantes.
     * Esto asume que `id_usuario_fk` en `docentes` y `estudiantes` es UNIQUE.
     *
     * @return array Array de usuarios que no son docentes ni estudiantes.
     */
    public function getUnassignedUsers(): array
    {
        $sql = "SELECT u.id_usuario, u.cedula, u.nombre, u.apellido, u.correo, u.rol
                FROM usuarios u
                LEFT JOIN docentes d ON u.id_usuario = d.id_usuario_fk
                LEFT JOIN estudiantes e ON u.id_usuario = e.id_usuario_fk
                WHERE d.id_docente IS NULL AND e.id_estudiante IS NULL
                ORDER BY u.nombre, u.apellido ASC";
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener usuarios no asignados: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtiene un usuario específico por su ID con sus detalles de rol.
     * Si es docente o estudiante, incluye esa información.
     *
     * @param int $id El ID del usuario.
     * @return array|false Los datos del usuario con detalles adicionales o false si no se encuentra.
     */
    public function getUserDetails(int $id): array|false
    {
        $user = $this->find($id);
        if (!$user) {
            return false;
        }

        $details = $user->data;

        if ($details['rol'] === 'docente') {
            $docenteModel = new Docente($this->pdo);
            $docente = $docenteModel->findWhere('id_usuario_fk', $details['id_usuario']);
            if ($docente) {
                $details['docente_info'] = $docente->data;
            }
        } elseif ($details['rol'] === 'estudiante') {
            $estudianteModel = new Estudiante($this->pdo);
            $estudiante = $estudianteModel->findWhere('id_usuario_fk', $details['id_usuario']);
            if ($estudiante) {
                $details['estudiante_info'] = $estudiante->data;
            }
        }
        
        return $details;
    }
}
