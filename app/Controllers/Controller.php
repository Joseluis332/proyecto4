<?php

namespace App\Controllers;

use PDO; // Necesario para la inyección de la conexión a la base de datos

/**
 * Clase base abstracta para todos los controladores.
 * Proporciona acceso a la conexión PDO.
 */
abstract class Controller
{
    /**
     * @var PDO Instancia de la conexión a la base de datos.
     */
    protected PDO $pdo;

    /**
     * Constructor de la clase Controller.
     *
     * @param PDO $pdo Instancia de la conexión a la base de datos.
     */
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }
}
