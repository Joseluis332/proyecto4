<?php

namespace App\Core;

/**
 * Clase SessionManager
 *
 * Encapsula la lógica de manejo de sesiones PHP.
 */
class SessionManager
{
    /**
     * Inicia la sesión PHP si no ha sido iniciada.
     */
    public static function start(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Establece un valor en la sesión.
     *
     * @param string $key La clave del valor.
     * @param mixed $value El valor a almacenar.
     */
    public static function set(string $key, mixed $value): void
    {
        $_SESSION[$key] = $value;
    }

    /**
     * Obtiene un valor de la sesión.
     *
     * @param string $key La clave del valor.
     * @param mixed $default Valor por defecto si la clave no existe.
     * @return mixed El valor almacenado, o el valor por defecto.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        return $_SESSION[$key] ?? $default;
    }

    /**
     * Elimina un valor de la sesión.
     *
     * @param string $key La clave del valor a eliminar.
     */
    public static function unset(string $key): void
    {
        if (isset($_SESSION[$key])) {
            unset($_SESSION[$key]);
        }
    }

    /**
     * Destruye toda la sesión.
     */
    public static function destroy(): void
    {
        $_SESSION = [];
        session_unset();
        session_destroy();
    }

    /**
     * Verifica si el usuario ha iniciado sesión.
     *
     * @return bool True si el 'user_id' está en la sesión, false en caso contrario.
     */
    public static function isLoggedIn(): bool
    {
        return self::get('user_id') !== null;
    }

    /**
     * Establece un mensaje flash (se mostrará una vez y luego se eliminará).
     *
     * @param string $key La clave del mensaje (ej. 'success_message', 'error_message', 'errors', 'old_input').
     * @param mixed $value El contenido del mensaje (puede ser string o array que se serializará).
     */
    public static function setFlash(string $key, mixed $value): void
    {
        self::set('flash_' . $key, $value); // Ahora $value puede ser de cualquier tipo
    }

    /**
     * Obtiene y elimina un mensaje flash.
     * Intenta decodificar JSON si el mensaje parece un JSON.
     *
     * @param string $key La clave del mensaje.
     * @return mixed El mensaje (string o array), o null si no existe.
     */
    public static function getFlash(string $key): mixed
    {
        $flashKey = 'flash_' . $key;
        $value = self::get($flashKey);
        self::unset($flashKey); // Elimina el mensaje después de obtenerlo

        // Intentar decodificar JSON si el valor es una cadena y parece JSON
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            // Si la decodificación fue exitosa y el resultado no es null (excepto para 'null' string)
            // y no hubo errores de JSON, entonces es un JSON válido.
            if (json_last_error() === JSON_ERROR_NONE && !is_null($decoded)) {
                return $decoded;
            }
        }
        return $value; // Devolver el valor original si no es JSON o hubo error
    }
}
