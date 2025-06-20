<?php
// C:\xampp\htdocs\proyecto_pnfi\app\Views\docentes\show.php
?>

<h1 class="mb-4">Detalles del Docente</h1>

<?php if (empty($docente) || empty($usuario)): ?>
    <div class="alert alert-warning" role="alert">
        No se encontró el docente o su información de usuario.
    </div>
<?php else: ?>
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0"><?php echo htmlspecialchars($usuario['nombre'] . ' ' . $usuario['apellido']); ?> (C.I.: <?php echo htmlspecialchars($usuario['cedula']); ?>)</h4>
        </div>
        <div class="card-body">
            <p><strong>Correo:</strong> <?php echo htmlspecialchars($usuario['correo']); ?></p>
            <p><strong>Teléfono:</strong> <?php echo htmlspecialchars($usuario['telefono'] ?? 'N/A'); ?></p>
            <p><strong>Dirección:</strong> <?php echo htmlspecialchars($usuario['direccion'] ?? 'N/A'); ?></p>
            <p><strong>Cargo:</strong> <?php echo htmlspecialchars($docente['cargo'] ?? 'N/A'); ?></p>
            <p><strong>Especialidad:</strong> <?php echo htmlspecialchars($docente['especialidad'] ?? 'N/A'); ?></p>
            <p><strong>Fecha de Ingreso:</strong> <?php echo htmlspecialchars($docente['fecha_ingreso'] ?? 'N/A'); ?></p>
            <p><strong>Rol en Sistema:</strong> <span class="badge bg-info"><?php echo htmlspecialchars(ucfirst($usuario['rol'])); ?></span></p>
            <p><strong>Estado de Usuario:</strong> <span class="badge 
                <?php echo $usuario['estado'] == 'activo' ? 'bg-success' : 'bg-danger'; ?>">
                <?php echo htmlspecialchars(ucfirst($usuario['estado'])); ?>
            </span></p>
            <p><strong>Registrado el:</strong> <?php echo htmlspecialchars($usuario['fecha_creacion']); ?></p>
            <p><strong>Última Actualización (Usuario):</strong> <?php echo htmlspecialchars($usuario['fecha_actualizacion']); ?></p>
        </div>
    </div>

    <div class="d-flex justify-content-end">
        <a href="/proyecto_pnfi/public/admin/docentes" class="btn btn-secondary me-2">Volver al Listado</a>
        <a href="/proyecto_pnfi/public/admin/docentes/editar/<?php echo htmlspecialchars($docente['id_docente']); ?>" class="btn btn-warning">Editar Docente</a>
    </div>

<?php endif; ?>
