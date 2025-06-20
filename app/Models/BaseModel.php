<?php

namespace App\Models;

use PDO;
use PDOException;

/**
 * Clase BaseModel abstracta para todos los modelos de la aplicación.
 * Proporciona métodos CRUD básicos y validación.
 * Implementa __get y __set para acceso dinámico a propiedades/datos del modelo.
 */
abstract class BaseModel
{
    /**
     * @var PDO Instancia de la conexión a la base de datos.
     */
    protected PDO $pdo;

    /**
     * @var string Nombre de la tabla asociada al modelo. Debe ser definido por las clases hijas.
     */
    protected string $table;

    /**
     * @var string Nombre de la clave primaria de la tabla. Debe ser definido por las clases hijas.
     */
    protected string $primaryKey;

    /**
     * @var array Campos de la tabla que pueden ser llenados masivamente. Debe ser definido por las clases hijas.
     */
    protected array $fillable = [];

    /**
     * @var array Reglas de validación para los campos del modelo. Debe ser definido por las clases hijas.
     */
    protected array $validationRules = [];

    /**
     * @var array Almacena los datos del modelo de forma interna.
     */
    protected array $data = [];

    /**
     * Constructor de la clase BaseModel.
     *
     * @param PDO $pdo Instancia de la conexión a la base de datos.
     */
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Método mágico para obtener el valor de una propiedad.
     * Permite acceder a los datos almacenados en $this->data como si fueran propiedades del objeto.
     *
     * @param string $name Nombre de la propiedad.
     * @return mixed Valor de la propiedad.
     */
    public function __get(string $name)
    {
        if (array_key_exists($name, $this->data)) {
            return $this->data[$name];
        }
        return null;
    }

    /**
     * Método mágico para establecer el valor de una propiedad.
     * Almacena los valores en $this->data.
     *
     * @param string $name Nombre de la propiedad.
     * @param mixed $value Valor a establecer.
     */
    public function __set(string $name, mixed $value): void
    {
        $this->data[$name] = $value;
    }

    /**
     * Método mágico para verificar si una propiedad existe.
     *
     * @param string $name Nombre de la propiedad.
     * @return bool True si la propiedad existe, false en caso contrario.
     */
    public function __isset(string $name): bool
    {
        return array_key_exists($name, $this->data);
    }

    /**
     * Valida los datos proporcionados contra las reglas de validación del modelo.
     *
     * @param array $data Los datos a validar.
     * @return array Un array asociativo de errores, o vacío si no hay errores.
     */
    public function validate(array $data): array
    {
        $errors = [];
        foreach ($this->validationRules as $field => $rulesString) {
            $rules = explode('|', $rulesString);
            $value = $data[$field] ?? null;

            foreach ($rules as $rule) {
                $ruleName = $rule;
                $ruleParam = null;
                if (str_contains($rule, ':')) {
                    [$ruleName, $ruleParam] = explode(':', $rule, 2);
                }

                switch ($ruleName) {
                    case 'required':
                        if (empty($value) && $value !== 0 && $value !== '0') {
                            $errors[$field] = "El campo '{$field}' es obligatorio.";
                        }
                        break;
                    case 'integer':
                        if (!is_null($value) && !filter_var($value, FILTER_VALIDATE_INT)) {
                            $errors[$field] = "El campo '{$field}' debe ser un número entero válido.";
                        }
                        break;
                    case 'max':
                        if (!is_null($value) && strlen((string)$value) > (int)$ruleParam) { // Cast a string para strlen
                            $errors[$field] = "El campo '{$field}' no debe exceder los {$ruleParam} caracteres.";
                        }
                        break;
                    case 'unique':
                        list($table, $column, $excludeId) = array_pad(explode(',', $ruleParam), 3, null);
                        if (is_null($table)) $table = $this->table;
                        if (is_null($column)) $column = $field;

                        $sql = "SELECT COUNT(*) FROM {$table} WHERE {$column} = ?";
                        $params = [$value];
                        // Si se está actualizando un registro existente, se excluye su propio ID
                        if ($excludeId === null && isset($data[$this->primaryKey])) {
                            $sql .= " AND {$this->primaryKey} != ?";
                            $params[] = $data[$this->primaryKey];
                        } elseif ($excludeId !== null) { // Si se especificó un ID para excluir
                             $sql .= " AND {$this->primaryKey} != ?";
                             $params[] = $excludeId;
                        }

                        $stmt = $this->pdo->prepare($sql);
                        $stmt->execute($params);
                        if ($stmt->fetchColumn() > 0) {
                            $errors[$field] = "El valor para '{$field}' ya existe.";
                        }
                        break;
                    case 'date':
                        if (!is_null($value) && !\DateTime::createFromFormat('Y-m-d', (string)$value)) { // Cast a string
                            $errors[$field] = "El campo '{$field}' debe ser una fecha válida (YYYY-MM-DD).";
                        }
                        break;
                    case 'nullable':
                        break; // Esta regla solo indica que el campo puede ser nulo/vacío si no es 'required'
                    case 'after_or_equal':
                        if (!is_null($value) && isset($data[$ruleParam])) {
                            try {
                                $date1 = new \DateTime((string)$value);
                                $date2 = new \DateTime((string)$data[$ruleParam]);
                                if ($date1 < $date2) {
                                    $errors[$field] = "La fecha '{$field}' debe ser igual o posterior a '{$ruleParam}'.";
                                }
                            } catch (\Exception $e) {
                                $errors[$field] = "Formato de fecha inválido para '{$field}' o '{$ruleParam}'.";
                            }
                        }
                        break;
                    case 'in':
                        $allowedValues = explode(',', $ruleParam);
                        if (!is_null($value) && !in_array($value, $allowedValues)) {
                            $errors[$field] = "El campo '{$field}' debe ser uno de los siguientes: " . implode(', ', $allowedValues) . ".";
                        }
                        break;
                    case 'email':
                        if (!is_null($value) && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                            $errors[$field] = "El campo '{$field}' debe ser una dirección de correo electrónico válida.";
                        }
                        break;
                    case 'min':
                        if (!is_null($value) && strlen((string)$value) < (int)$ruleParam) {
                            $errors[$field] = "El campo '{$field}' debe tener al menos {$ruleParam} caracteres.";
                        }
                        break;
                }

                if (isset($errors[$field])) {
                    break;
                }
            }
        }
        return $errors;
    }

    /**
     * Busca un registro por su clave primaria.
     *
     * @param mixed $id El valor de la clave primaria.
     * @return BaseModel|false Una instancia del modelo con los datos, o false si no se encuentra.
     */
    public function find(mixed $id): self|false
    {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM {$this->table} WHERE {$this->primaryKey} = ? LIMIT 1");
            $stmt->execute([$id]);
            $data = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($data) {
                $instance = new static($this->pdo);
                foreach ($data as $key => $value) {
                    $instance->$key = $value;
                }
                return $instance;
            }
            return false;
        } catch (PDOException $e) {
            error_log("Error en find({$id}) para tabla {$this->table}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Busca un registro por un campo específico.
     *
     * @param string $field El nombre del campo por el cual buscar.
     * @param mixed $value El valor a buscar.
     * @return BaseModel|false Una instancia del modelo con los datos, o false si no se encuentra.
     */
    public function findWhere(string $field, mixed $value): self|false
    {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM {$this->table} WHERE {$field} = ? LIMIT 1");
            $stmt->execute([$value]);
            $data = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($data) {
                $instance = new static($this->pdo);
                foreach ($data as $key => $value) {
                    $instance->$key = $value;
                }
                return $instance;
            }
            return false;
        } catch (PDOException $e) {
            error_log("Error en findWhere({$field}, {$value}) para tabla {$this->table}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtiene todos los registros de la tabla.
     *
     * @return array Un array de instancias del modelo.
     */
    public function all(): array
    {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM {$this->table}");
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $models = [];
            foreach ($results as $data) {
                $instance = new static($this->pdo);
                foreach ($data as $key => $value) {
                    $instance->$key = $value;
                }
                $models[] = $instance;
            }
            return $models;
        } catch (PDOException $e) {
            error_log("Error en all() para tabla {$this->table}: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Guarda un nuevo registro en la base de datos.
     *
     * @param array $data Los datos del registro a guardar (solo campos 'fillable').
     * @return int|false El ID del nuevo registro insertado, o false en caso de error.
     */
    public function save(array $data): int|false
    {
        // Filtrar datos para asegurar que solo se inserten campos 'fillable'
        $filteredData = array_intersect_key($data, array_flip($this->fillable));

        $columns = implode(', ', array_keys($filteredData));
        $placeholders = implode(', ', array_fill(0, count($filteredData), '?'));

        $sql = "INSERT INTO {$this->table} ({$columns}) VALUES ({$placeholders})";

        try {
            $stmt = $this->pdo->prepare($sql);
            if ($stmt->execute(array_values($filteredData))) {
                return (int)$this->pdo->lastInsertId();
            }
            return false;
        } catch (PDOException $e) {
            error_log("Error en save() para tabla {$this->table}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Actualiza un registro existente en la base de datos.
     *
     * @param mixed $id El ID del registro a actualizar.
     * @param array $data Los datos a actualizar (solo campos 'fillable').
     * @return bool True si la actualización fue exitosa, false en caso contrario.
     */
    public function update(mixed $id, array $data): bool
    {
        // Filtrar datos para asegurar que solo se actualicen campos 'fillable'
        $filteredData = array_intersect_key($data, array_flip($this->fillable));

        $setParts = [];
        foreach ($filteredData as $column => $value) {
            $setParts[] = "{$column} = ?";
        }
        $setClause = implode(', ', $setParts);

        $sql = "UPDATE {$this->table} SET {$setClause} WHERE {$this->primaryKey} = ?";

        try {
            $stmt = $this->pdo->prepare($sql);
            $params = array_values($filteredData);
            $params[] = $id; // Añadir el ID al final de los parámetros

            return $stmt->execute($params);
        } catch (PDOException $e) {
            error_log("Error en update({$id}) para tabla {$this->table}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Elimina un registro de la base de datos.
     *
     * @param mixed $id El ID del registro a eliminar.
     * @return bool True si la eliminación fue exitosa, false en caso contrario.
     */
    public function delete(mixed $id): bool
    {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM {$this->table} WHERE {$this->primaryKey} = ?");
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            error_log("Error en delete({$id}) para tabla {$this->table}: " . $e->getMessage());
            return false;
        }
    }
}
