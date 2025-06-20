<?php
// C:\xampp\htdocs\proyecto_pnfi\app\Views\proyectos\index.php

// Mostrar mensajes flash si existen
if (!empty($success_message)) {
    echo '<div class="alert alert-success">' . htmlspecialchars($success_message) . '</div>';
}
if (!empty($error_message)) {
    echo '<div class="alert alert-danger">' . htmlspecialchars($error_message) . '</div>';
}
?>

<h1 class="mb-4">Gestión de Proyectos</h1>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h3>Listado de Proyectos Activos</h3>
    <a href="/proyecto_pnfi/public/admin/proyectos/crear" class="btn btn-primary">Registrar Nuevo Proyecto</a>
</div>

<?php if (empty($proyectos)): ?>
    <div class="alert alert-info" role="alert">
        No hay proyectos activos registrados en el sistema.
    </div>
<?php else: ?>
    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead class="table-dark">
                <tr>
                    <th>Número</th>
                    <th>Nombre</th>
                    <th>Descripción</th>
                    <th>Fecha Inicio</th>
                    <th>Fecha Fin Estimada</th>
                    <th>Estado</th>
                    <th>Tutor</th>
                    <th>Comunidad</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($proyectos as $proyecto): ?>
                <tr>
                    <td><?php echo htmlspecialchars($proyecto['numero_proyecto']); ?></td>
                    <td><?php echo htmlspecialchars($proyecto['nombre']); ?></td>
                    <td><?php echo htmlspecialchars(substr($proyecto['descripcion'], 0, 100)) . (strlen($proyecto['descripcion']) > 100 ? '...' : ''); ?></td>
                    <td><?php echo htmlspecialchars($proyecto['fecha_inicio']); ?></td>
                    <td><?php echo htmlspecialchars($proyecto['fecha_fin_estimada'] ?? 'N/A'); ?></td>
                    <td><?php echo htmlspecialchars($proyecto['estado']); ?></td>
                    <td><?php echo htmlspecialchars($proyecto['tutor_nombre'] . ' ' . $proyecto['tutor_apellido']); ?></td>
                    <td><?php echo htmlspecialchars($proyecto['comunidad_nombre']); ?></td>
                    <td>
                        <a href="/proyecto_pnfi/public/admin/proyectos/ver/<?php echo htmlspecialchars($proyecto['id_proyecto']); ?>" class="btn btn-info btn-sm me-1" title="Ver Detalles">
                            <i class="fas fa-eye"></i> Ver
                        </a>
                        <!-- Puedes añadir un botón de editar si lo implementas -->
                        <!-- <a href="/proyecto_pnfi/public/admin/proyectos/editar/<?php echo htmlspecialchars($proyecto['id_proyecto']); ?>" class="btn btn-warning btn-sm me-1" title="Editar Proyecto">
                            <i class="fas fa-edit"></i> Editar
                        </a> -->
                        <form action="/proyecto_pnfi/public/admin/proyectos/eliminar/<?php echo htmlspecialchars($proyecto['id_proyecto']); ?>" method="POST" class="d-inline" onsubmit="return confirm('¿Está seguro de que desea eliminar lógicamente este proyecto?');">
                            <!-- Si usas un campo oculto para el método DELETE HTTP -->
                            <!-- <input type="hidden" name="_method" value="DELETE"> -->
                            <button type="submit" class="btn btn-danger btn-sm" title="Eliminar Lógicamente">
                                <i class="fas fa-trash-alt"></i> Eliminar
                            </button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>

<!-- Font Awesome para iconos (opcional, si aún no lo tienes en tu layout principal) -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
