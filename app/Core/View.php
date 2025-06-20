<?php

namespace App\Core;

/**
 * Clase View
 *
 * Helper para renderizar vistas y gestionar el layout principal.
 */
class View
{
    /**
     * Renderiza una vista con o sin el layout principal.
     *
     * @param string $viewPath La ruta de la vista a renderizar (ej. 'auth/login', 'dashboard').
     * @param array $data Los datos a pasar a la vista.
     */
    public static function render(string $viewPath, array $data = []): void
    {
        // Extraer los datos para que estén disponibles como variables en la vista
        extract($data);

        // Definir la ruta base para el directorio de vistas.
        // __DIR__ es el directorio actual (app/Core)
        // dirname(__DIR__) sube un nivel (a app/)
        // dirname(dirname(__DIR__)) sube dos niveles (a la raíz del proyecto, proyecto_pnfi/)
        // De ahí, construimos la ruta a app/Views/
        $viewsBaseDir = dirname(dirname(__DIR__)) . '/app/Views/';

        // Construir la ruta completa al archivo de vista específico
        $viewFile = $viewsBaseDir . $viewPath . '.php';

        // Verificar si el archivo de vista existe antes de intentar incluirlo
        if (!file_exists($viewFile)) {
            // Manejo de error si la vista no se encuentra
            error_log("Error: La vista '{$viewPath}' no se encuentra en la ruta '{$viewFile}'.");
            http_response_code(500);
            echo "<h1>Error Interno del Servidor</h1>";
            echo "<p>Error: No se encuentra la vista '{$viewPath}' en la ruta '{$viewFile}'.</p>";
            exit();
        }

        // Capturar el contenido de la vista específica
        ob_start();
        require $viewFile; // Usamos require aquí ya que hemos verificado la existencia
        $viewContent = ob_get_clean();

        // Incluir el layout principal (main.php)
        // La ruta al layout principal es conocida y fija
        $layoutFile = $viewsBaseDir . 'layouts/main.php';

        if (!file_exists($layoutFile)) {
            error_log("Error: El layout principal 'layouts/main.php' no se encuentra en la ruta '{$layoutFile}'.");
            http_response_code(500);
            echo "<h1>Error Interno del Servidor</h1>";
            echo "<p>Error: El layout principal no se encuentra.</p>";
            exit();
        }
        
        require $layoutFile; // El layout principal incluirá $viewContent
    }
}
