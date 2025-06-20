<?php

namespace App\Controllers; // El namespace debe coincidir con la ubicación de la carpeta

use PDO; // Importa la clase PDO nativa

abstract class BaseController {
    protected ?PDO $pdo; // Para almacenar la conexión PDO

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    // Puedes añadir métodos comunes aquí que todos tus controladores puedan usar.
    // Por ejemplo, para renderizar vistas (aunque ya tenemos App\Core\View)
    // o para realizar redirecciones, validar datos, etc.
}