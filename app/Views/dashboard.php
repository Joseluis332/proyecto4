<?php
// app/Views/admin/dashboard.php

// Las variables pasadas desde el controlador están disponibles aquí
// Por ejemplo, $titulo y $mensaje desde AdminController::dashboard()

// Puedes usar la sintaxis de PHP para mostrar variables
?>

<h1><?php echo htmlspecialchars($titulo ?? 'Dashboard'); ?></h1>
<p><?php echo htmlspecialchars($mensaje ?? 'Contenido del dashboard.'); ?></p>

<div class="card mt-4">
    <div class="card-header">
        Panel de Administración
    </div>
    <div class="card-body">
        <h5 class="card-title">Opciones Rápidas:</h5>
        <ul>
            <li><a href="/proyecto_pnfi/public/admin/usuarios">Gestionar Usuarios</a> (si creas esta ruta y controlador/vista)</li>
            <li><a href="/proyecto_pnfi/public/admin/cursos">Gestionar Cursos</a> (si creas esta ruta y controlador/vista)</li>
            <li><a href="/proyecto_pnfi/public/admin/reportes">Ver Reportes</a> (si creas esta ruta y controlador/vista)</li>
        </ul>
        <p class="card-text">Aquí verás un resumen de la actividad y opciones de administración del sistema.</p>
        <?php // if (isset($totalUsuarios)): ?>
            <?php // endif; ?>
    </div>
</div>