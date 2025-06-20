<?php

namespace App\Config; // <-- ¡IMPORTANTE! Este es el namespace de la clase

use PDO;
use PDOException;

class Database {
    // Configuración de la base de datos como propiedades estáticas
    private static string $host = 'localhost'; // O la IP de tu servidor de base de datos
    private static string $dbName = 'proyecto'; // Nombre de tu base de datos
    private static string $username = 'root'; // Tu usuario de base de datos
    private static string $password = ''; // Tu contraseña de base de datos (vacío si no tienes)
    private static ?PDO $pdo = null; // Instancia de PDO estática

    // Ya no necesitas un constructor si todas las propiedades son estáticas y la conexión es estática.
    // public function __construct($host, $db_name, $username, $password) { ... }

    /**
     * Obtiene la única instancia de la conexión PDO (Singleton estático).
     * @return PDO La conexión PDO.
     * @throws PDOException Si la conexión falla.
     */
    public static function getConnection(): PDO { // <-- ¡AHORA ES ESTÁTICO!
        if (self::$pdo === null) {
            try {
                $dsn = "mysql:host=" . self::$host . ";dbname=" . self::$dbName . ";charset=utf8mb4";
                self::$pdo = new PDO($dsn, self::$username, self::$password, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]);
            } catch (PDOException $e) {
                // Relanza la excepción para que sea capturada en public/index.php
                throw new PDOException("Error de conexión a la base de datos: " . $e->getMessage(), (int)$e->getCode());
            }
        }
        return self::$pdo;
    }
}