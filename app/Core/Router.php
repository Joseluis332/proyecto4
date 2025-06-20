<?php

namespace App\Core;

use PDO; // Importa la clase PDO nativa de PHP
use App\Core\View; // Importa la clase View para renderizar vistas, incluyendo errores
use App\Core\MiddlewareInterface; // Importa la interfaz de Middleware

class Router {
    protected array $routes = []; // Almacena las rutas definidas en la aplicación
    protected array $params = []; // Almacena los parámetros extraídos de la URL (ej. {id})
    protected ?PDO $pdo; // Conexión a la base de datos que se inyectará a los controladores

    /**
     * Constructor del Router. Recibe la conexión PDO.
     * @param PDO|null $pdo La instancia de PDO para la conexión a la base de datos.
     */
    public function __construct(?PDO $pdo = null) {
        $this->pdo = $pdo;
    }

    /**
     * Define una ruta GET (HTTP GET request).
     * @param string $uri La URI a coincidir (ej. '/usuarios/{id}/editar').
     * @param string $controllerAction El controlador y método (ej. 'UsuarioController@edit').
     * @param array $middlewares Array de nombres de clases Middleware (ej. ['AuthMiddleware']).
     */
    public function get(string $uri, string $controllerAction, array $middlewares = []): void {
        $this->add($uri, $controllerAction, 'GET', $middlewares);
    }

    /**
     * Define una ruta POST (HTTP POST request).
     * @param string $uri La URI a coincidir.
     * @param string $controllerAction El controlador y método.
     * @param array $middlewares Array de nombres de clases Middleware.
     */
    public function post(string $uri, string $controllerAction, array $middlewares = []): void {
        $this->add($uri, $controllerAction, 'POST', $middlewares);
    }

    /**
     * Añade una ruta a la tabla de enrutamiento interna.
     * Convierte la URI en una expresión regular para manejar parámetros dinámicos.
     * @param string $uri La URI original (ej. '/usuarios/{id}').
     * @param string $controllerAction El string 'ControllerName@actionMethod'.
     * @param string $method El método HTTP (GET, POST, etc.).
     * @param array $middlewares Array de nombres de clases Middleware (ej. ['AuthMiddleware', 'RoleMiddleware:admin']).
     */
    protected function add(string $uri, string $controllerAction, string $method, array $middlewares = []): void {
        // Escapa las barras para su uso en expresiones regulares
        $uri = preg_replace('/\//', '\\/', $uri);

        // Reemplaza los marcadores de posición de parámetros {nombre_parametro}
        // con grupos de captura nombrados en la expresión regular.
        // (?P<nombre_parametro>[^\/]+) significa "captura cualquier caracter excepto '/' y nómbralo 'nombre_parametro'"
        $uri = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '(?P<\1>[^\/]+)', $uri);

        // Añade delimitadores de inicio (^) y fin ($) y hace la coincidencia insensible a mayúsculas/minúsculas (i)
        $uri = '/^' . $uri . '$/i';

        // Almacena la ruta en el array de rutas, indexada primero por el método HTTP y luego por el patrón de la URI (regex)
        $this->routes[$method][$uri] = [ // Almacena un array asociativo con action y middlewares
            'controllerAction' => $controllerAction,
            'middlewares' => $middlewares
        ];
    }

    /**
     * Obtiene la URI de la petición actual, limpiándola de la ruta base del proyecto.
     * Esto es crucial para que el enrutador funcione correctamente en subdirectorios.
     * @return string La URI limpia y normalizada (ej. '/login' o '/usuarios/1/editar').
     */
    protected function getUri(): string {
        $uri = $_SERVER['REQUEST_URI'] ?? '/'; // Obtiene la URI completa de la solicitud del servidor
        $uri = strtok($uri, '?'); // Elimina cualquier cadena de consulta (query string)

        // Obtiene la ruta del script ejecutado (ej. /proyecto_pnfi/public/index.php)
        $scriptName = $_SERVER['SCRIPT_NAME'];
        // Obtiene el directorio base de la aplicación (ej. /proyecto_pnfi/public)
        $basePath = dirname($scriptName);

        // Si la URI de la solicitud comienza con la ruta base de la aplicación, la elimina
        // str_starts_with fue introducido en PHP 8.0, si usas una versión anterior, usa strncmp o substr.
        if ($basePath !== '/' && str_starts_with($uri, $basePath)) {
            $uri = substr($uri, strlen($basePath));
        }

        // Si la URI resultante está vacía después de la limpieza, es la raíz de la aplicación
        if ($uri === '') {
            $uri = '/';
        }

        // Elimina barras duplicadas o barras al final de la URI para normalizarla (ej. //login/ -> /login)
        $uri = rtrim($uri, '/');
        if (empty($uri)) { // Si después de rtrim queda vacío (ej. para '/'), asegúrate de que sea '/'
            $uri = '/';
        }

        return $uri;
    }

    /**
     * Despacha la petición actual al controlador y método de acción adecuados.
     * Recorre las rutas definidas y, si encuentra una coincidencia, ejecuta la lógica asociada.
     */
    public function dispatch(): void {
        $uri = $this->getUri(); // Obtiene la URI limpia (ej. '/login' o '/usuarios/1/editar')
        $method = $_SERVER['REQUEST_METHOD']; // Obtiene el método HTTP de la solicitud (GET, POST, etc.)

        // Descomenta la siguiente línea para depurar la URI y el método que llega al router
        // error_log("DEBUG Router: URI = '" . $uri . "', Method = '" . $method . "'");

        // Verifica si hay rutas definidas para el método HTTP actual (GET, POST, etc.)
        if (!isset($this->routes[$method])) {
            // Si no hay rutas para este método, se maneja como una página no encontrada (404)
            View::renderWithLayout('errors/404', ['title' => 'Método no Soportado', 'message' => 'El método de solicitud HTTP no está permitido para esta URL.']);
            return;
        }

        // Itera sobre las rutas registradas para el método HTTP actual
        // $routePattern será la expresión regular (ej. '/^\\/login$/i')
        // $routeInfo será un array con 'controllerAction' y 'middlewares'
        foreach ($this->routes[$method] as $routePattern => $routeInfo) {
            // Intenta hacer coincidir la URI limpia con el patrón de la ruta (expresión regular)
            if (preg_match($routePattern, $uri, $matches)) {
                $controllerAction = $routeInfo['controllerAction'];
                $middlewares = $routeInfo['middlewares'] ?? [];

                // --- Ejecutar Middlewares ---
                foreach ($middlewares as $middlewareDefinition) {
                    $middlewareClass = $middlewareDefinition;
                    $middlewareParams = [];

                    // Manejar parámetros para middlewares (ej. 'RoleMiddleware:admin')
                    if (strpos($middlewareDefinition, ':') !== false) {
                        list($middlewareClass, $paramString) = explode(':', $middlewareDefinition, 2);
                        // Convertir la cadena de parámetros en un array si hay comas (ej. 'param1,param2')
                        // En tu caso 'admin' es un solo parámetro, pero esta lógica es más robusta.
                        $middlewareParams = array_map('trim', explode(',', $paramString));
                    }
                    
                    // Asume que los middlewares están en el namespace App\Middlewares
                    $fullMiddlewareName = "App\\Middlewares\\" . $middlewareClass;

                    if (!class_exists($fullMiddlewareName)) {
                        error_log("Error interno: El Middleware '{$fullMiddlewareName}' no existe para la ruta '{$uri}'.");
                        View::renderWithLayout('errors/500', ['title' => 'Error Interno del Servidor', 'message' => 'Error en la configuración del middleware.']);
                        return; // Detiene la ejecución
                    }

                    // Instanciar el middleware con sus parámetros si los tiene
                    // IMPORTANTE: ...$middlewareParams descompone el array en argumentos separados para el constructor
                    $middlewareInstance = new $fullMiddlewareName(...$middlewareParams);

                    // Asegurarse de que el middleware implementa la interfaz correcta
                    if (!$middlewareInstance instanceof MiddlewareInterface) {
                        error_log("Error interno: El Middleware '{$fullMiddlewareName}' no implementa MiddlewareInterface.");
                        View::renderWithLayout('errors/500', ['title' => 'Error Interno del Servidor', 'message' => 'Error en la implementación del middleware.']);
                        return; // Detiene la ejecución
                    }

                    // Ejecutar el middleware. Si handle() retorna false, detener la ejecución de la ruta.
                    if (!$middlewareInstance->handle()) {
                        // El middleware ya debería haber manejado la redirección o el error (ej. redirigir a login)
                        return; // Detiene el dispatch
                    }
                }
                // --- Fin de Ejecución de Middlewares ---


                // Si todos los middlewares pasaron, procede con el controlador
                list($controllerName, $actionName) = explode('@', $controllerAction);
                // Asume que los controladores están en el namespace App\Controllers
                $fullControllerName = "App\\Controllers\\" . $controllerName;

                if (!class_exists($fullControllerName)) {
                    error_log("Error interno: El controlador '{$fullControllerName}' no existe para la ruta '{$uri}'.");
                    View::renderWithLayout('errors/500', ['title' => 'Error Interno del Servidor', 'message' => 'Algo salió mal. Por favor, inténtelo de nuevo más tarde.']);
                    return; // Detiene la ejecución
                }

                // Instanciar el controlador, pasando la conexión PDO si está disponible
                // Esto es crucial para que los controladores puedan usar los modelos
                $controller = new $fullControllerName($this->pdo);

                if (!method_exists($controller, $actionName)) {
                    error_log("Error interno: El método '{$actionName}' no existe en el controlador '{$fullControllerName}' para la ruta '{$uri}'.");
                    View::renderWithLayout('errors/500', ['title' => 'Error Interno del Servidor', 'message' => 'Algo salió mal. Por favor, inténtelo de nuevo más tarde.']);
                    return; // Detiene la ejecución
                }

                $params = [];
                // Extrae los parámetros nombrados de la URL (ej. {id})
                // Los grupos de captura nombrados de preg_match se indexan por su nombre
                foreach ($matches as $key => $value) {
                    if (is_string($key)) { // Asegura que solo tomamos los parámetros nombrados
                        $params[$key] = $value;
                    }
                }
                
                // Llama al método de acción del controlador, pasándole los parámetros extraídos de la URL
                call_user_func_array([$controller, $actionName], $params);
                
                return; // Una vez que una ruta coincide y se despacha, detener el procesamiento
            }
        }

        // Si el bucle termina y no se encontró ninguna ruta coincidente con la URI y el método HTTP
        $this->handleNotFound();
    }

    /**
     * Maneja las peticiones que no coinciden con ninguna ruta definida (error 404).
     * Muestra una página de error 404 personalizada.
     */
    protected function handleNotFound(): void {
        header("HTTP/1.0 404 Not Found"); // Establece el código de estado HTTP 404
        View::renderWithLayout('errors/404', ['title' => 'Página no encontrada', 'message' => 'La página que solicitaste no pudo ser encontrada. Por favor, verifica la URL.']);
    }
}