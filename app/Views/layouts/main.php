<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title ?? 'Sistema PNFI'; ?></title>
    <link rel="stylesheet" href="/proyecto_pnfi/public/css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" xintegrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <!-- ¡AÑADE ESTO para Tailwind CSS si lo tienes en main.php! -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Opcional: Fuente Inter para consistencia */
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>
<body>
    <?php
    // --- ¡NUEVAS LÍNEAS DE DEPURACIÓN AQUÍ! ---
    error_log("DEBUG MAIN: main.php cargado.");
    error_log("DEBUG MAIN: ID de sesión actual en main.php: " . session_id());
    error_log("DEBUG MAIN: session_status() en main.php: " . session_status());
    error_log("DEBUG MAIN: Is Logged In? " . ( \App\Core\SessionManager::isLoggedIn() ? 'TRUE' : 'FALSE' ) );
    error_log("DEBUG MAIN: user_id de sesión en main.php: " . (\App\Core\SessionManager::get('user_id') ?? 'NULL'));
    error_log("DEBUG MAIN: user_nombre de sesión en main.php: " . (\App\Core\SessionManager::get('user_nombre') ?? 'NULL'));
    error_log("DEBUG MAIN: user_rol de sesión en main.php: " . (\App\Core\SessionManager::get('user_rol') ?? 'NULL'));
    error_log("DEBUG MAIN: Contenido de _SESSION en main.php: " . print_r($_SESSION, true));
    // --- FIN NUEVAS LÍNEAS DE DEPURACIÓN ---
    ?>
    <header>
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <div class="container-fluid">
                <a class="navbar-brand" href="/proyecto_pnfi/public/">Sistema PNFI</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                        <li class="nav-item">
                            <a class="nav-link active" aria-current="page" href="/proyecto_pnfi/public/">Inicio</a>
                        </li>

                        <?php
                        // Las llamadas a SessionManager usan el Fully Qualified Class Name (FQCN)
                        if (\App\Core\SessionManager::isLoggedIn()): // Esta es la condición que estamos depurando
                        ?>

                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownUsuarios" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    Usuarios
                                </a>
                                <ul class="dropdown-menu" aria-labelledby="navbarDropdownUsuarios">
                                    <li><h6 class="dropdown-header">Gestión de Usuarios</h6></li>
                                    <li><a class="dropdown-item" href="/proyecto_pnfi/public/admin/usuarios">Listar Usuarios</a></li>
                                    <li><a class="dropdown-item" href="/proyecto_pnfi/public/admin/usuarios/crear">Crear Nuevo Usuario</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="#">Roles y Permisos</a></li>
                                </ul>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link" href="/proyecto_pnfi/public/admin/proyectos">Proyectos</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="/proyecto_pnfi/public/admin/docentes">Docentes</a>
                            </li>
                            <!-- Añadir enlace para Estudiantes aquí cuando esté listo -->
                            <!-- <li class="nav-item">
                                <a class="nav-link" href="/proyecto_pnfi/public/admin/estudiantes">Estudiantes</a>
                            </li> -->

                        <?php endif; ?>
                    </ul>

                    <ul class="navbar-nav ms-auto">
                        <?php if (\App\Core\SessionManager::isLoggedIn()): ?>
                            <li class="nav-item">
                                <span class="nav-link text-white">
                                    Bienvenido, **<?php echo \App\Core\SessionManager::get('user_nombre') ?? 'Usuario'; ?>**
                                </span>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="/proyecto_pnfi/public/logout">Cerrar Sesión</a>
                            </li>
                        <?php else: ?>
                            <li class="nav-item">
                                <a class="nav-link" href="/proyecto_pnfi/public/login">Iniciar Sesión</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="/proyecto_pnfi/public/register">Registrarse</a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <main class="container mt-4">
        <?php echo $viewContent; ?>
    </main>

    <footer class="mt-5 p-3 bg-light text-center">
        <p>&copy; <?php echo date('Y'); ?> UPTAEB - PNFI. Todos los derechos reservados.</p>
    </footer>

    <!-- SCRIPTS DE JAVASCRIPT -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" xintegrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eE+R7onYmP4W" crossorigin="anonymous"></script>
    <script src="/proyecto_pnfi/public/js/main.js"></script>

    <!-- Script de inicialización manual para Bootstrap Dropdowns si es necesario -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var dropdownElement = document.getElementById('navbarDropdownUsuarios');
            if (dropdownElement) {
                var bsDropdown = bootstrap.Dropdown.getInstance(dropdownElement) || new bootstrap.Dropdown(dropdownElement);
            }
        });
    </script>
</body>
</html>
