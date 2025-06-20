<?php
// C:\xampp\htdocs\proyecto_pnfi\app\Views\proyectos\show.php
?>

<h1 class="mb-4">Detalles del Proyecto</h1>

<?php if (empty($proyecto)): ?>
    <div class="alert alert-warning" role="alert">
        No se encontró el proyecto.
    </div>
<?php else: ?>
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0"><?php echo htmlspecialchars($proyecto['nombre']); ?> (Nº: <?php echo htmlspecialchars($proyecto['numero_proyecto']); ?>)</h4>
        </div>
        <div class="card-body">
            <p><strong>Descripción:</strong> <?php echo nl2br(htmlspecialchars($proyecto['descripcion'])); ?></p>
            <p><strong>Estado:</strong> <span class="badge 
                <?php 
                    switch ($proyecto['estado']) {
                        case 'activo': echo 'bg-success'; break;
                        case 'culminado': echo 'bg-info'; break;
                        case 'pendiente': echo 'bg-warning text-dark'; break;
                        case 'cancelado': echo 'bg-danger'; break;
                        default: echo 'bg-secondary'; break;
                    }
                ?>">
                <?php echo htmlspecialchars(ucfirst($proyecto['estado'])); ?>
            </span></p>
            <p><strong>Fecha de Inicio:</strong> <?php echo htmlspecialchars($proyecto['fecha_inicio']); ?></p>
            <p><strong>Fecha Fin Estimada:</strong> <?php echo htmlspecialchars($proyecto['fecha_fin_estimada'] ?? 'N/A'); ?></p>
            <p><strong>Tutor Docente:</strong> <?php echo htmlspecialchars($proyecto['tutor_nombre'] . ' ' . $proyecto['tutor_apellido'] . ' (Cargo: ' . $proyecto['tutor_cargo'] . ')'); ?></p>
            <p><strong>Comunidad:</strong> <?php echo htmlspecialchars($proyecto['comunidad_nombre']); ?></p>
            <p><strong>Fecha de Creación:</strong> <?php echo htmlspecialchars($proyecto['fecha_creacion']); ?></p>
            <p><strong>Última Actualización:</strong> <?php echo htmlspecialchars($proyecto['fecha_actualizacion']); ?></p>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header bg-secondary text-white">
            <h5 class="mb-0">Estudiantes Asignados</h5>
        </div>
        <div class="card-body">
            <?php if (empty($estudiantesAsociados)): ?>
                <div class="alert alert-info">No hay estudiantes asignados a este proyecto.</div>
            <?php else: ?>
                <ul class="list-group">
                    <?php foreach ($estudiantesAsociados as $estudiante): ?>
                        <li class="list-group-item">
                            <?php echo htmlspecialchars($estudiante['nombre'] . ' ' . $estudiante['apellido'] . ' (C.I.: ' . $estudiante['cedula'] . ') - Carrera: ' . $estudiante['carrera'] . ' - Semestre: ' . $estudiante['semestre']); ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    </div>

    <div class="d-flex justify-content-end">
        <a href="/proyecto_pnfi/public/admin/proyectos" class="btn btn-secondary me-2">Volver al Listado</a>
        <!-- Puedes añadir un botón de editar aquí si lo implementas -->
        <!-- <a href="/proyecto_pnfi/public/admin/proyectos/editar/<?php echo htmlspecialchars($proyecto['id_proyecto']); ?>" class="btn btn-warning">Editar Proyecto</a> -->
    </div>

<?php endif; ?>
