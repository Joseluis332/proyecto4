<?php
// C:\xampp\htdocs\proyecto_pnfi\app\Views\docentes\index.php

// Mostrar mensajes flash si existen
if (!empty($success_message)) {
    echo '<div class="alert alert-success">' . htmlspecialchars($success_message) . '</div>';
}
if (!empty($error_message)) {
    echo '<div class="alert alert-danger">' . htmlspecialchars($error_message) . '</div>';
}
?>

<h1 class="mb-4">Gestión de Docentes</h1>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h3>Listado de Docentes</h3>
    <a href="/proyecto_pnfi/public/admin/docentes/crear" class="btn btn-primary">Registrar Nuevo Docente</a>
</div>

<?php if (empty($docentes)): ?>
    <div class="alert alert-info" role="alert">
        No hay docentes registrados en el sistema.
    </div>
<?php else: ?>
    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead class="table-dark">
                <tr>
                    <th>Cédula</th>
                    <th>Nombre Completo</th>
                    <th>Correo</th>
                    <th>Cargo</th>
                    <th>Especialidad</th>
                    <th>Fecha Ingreso</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($docentes as $docente): ?>
                <tr>
                    <td><?php echo htmlspecialchars($docente['usuario_cedula'] ?? 'N/A'); ?></td>
                    <td><?php echo htmlspecialchars($docente['usuario_nombre'] . ' ' . $docente['usuario_apellido']); ?></td>
                    <td><?php echo htmlspecialchars($docente['usuario_correo'] ?? 'N/A'); ?></td>
                    <td><?php echo htmlspecialchars($docente['cargo'] ?? 'N/A'); ?></td>
                    <td><?php echo htmlspecialchars($docente['especialidad'] ?? 'N/A'); ?></td>
                    <td><?php echo htmlspecialchars($docente['fecha_ingreso'] ?? 'N/A'); ?></td>
                    <td>
                        <a href="/proyecto_pnfi/public/admin/docentes/ver/<?php echo htmlspecialchars($docente['id_docente']); ?>" class="btn btn-info btn-sm me-1" title="Ver Detalles">
                            <i class="fas fa-eye"></i> Ver
                        </a>
                        <a href="/proyecto_pnfi/public/admin/docentes/editar/<?php echo htmlspecialchars($docente['id_docente']); ?>" class="btn btn-warning btn-sm me-1" title="Editar Docente">
                            <i class="fas fa-edit"></i> Editar
                        </a>
                        <form action="/proyecto_pnfi/public/admin/docentes/eliminar/<?php echo htmlspecialchars($docente['id_docente']); ?>" method="POST" class="d-inline" onsubmit="return confirm('¿Está seguro de que desea eliminar este docente? Esta acción no se puede deshacer si no tiene proyectos asignados.');">
                            <button type="submit" class="btn btn-danger btn-sm" title="Eliminar Docente">
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

<!-- Font Awesome para iconos (asegúrate de que esté en tu main.php o aquí) -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
