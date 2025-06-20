<?php
// C:\xampp\htdocs\proyecto_pnfi\app\Views\proyectos\create.php

// Recuperar datos antiguos y errores de sesión
$old_input = $old_input ?? [];
$errors = $errors ?? [];

// Helper para obtener el valor antiguo o vacío
function old($key, $default = '') {
    global $old_input; // Acceder a la variable global
    return htmlspecialchars($old_input[$key] ?? $default);
}

// Helper para mostrar errores
function error($key) {
    global $errors; // Acceder a la variable global
    return isset($errors[$key]) ? '<div class="invalid-feedback d-block">' . htmlspecialchars($errors[$key]) . '</div>' : '';
}
?>

<h1 class="mb-4">Registrar Nuevo Proyecto</h1>

<form action="/proyecto_pnfi/public/admin/proyectos" method="POST">
    <!-- Número de Proyecto -->
    <div class="mb-3">
        <label for="numero_proyecto" class="form-label">Número de Proyecto:</label>
        <input type="text" class="form-control <?php echo isset($errors['numero_proyecto']) ? 'is-invalid' : ''; ?>"
               id="numero_proyecto" name="numero_proyecto" value="<?php echo old('numero_proyecto'); ?>" required>
        <?php echo error('numero_proyecto'); ?>
    </div>

    <!-- Nombre del Proyecto -->
    <div class="mb-3">
        <label for="nombre" class="form-label">Nombre del Proyecto:</label>
        <input type="text" class="form-control <?php echo isset($errors['nombre']) ? 'is-invalid' : ''; ?>"
               id="nombre" name="nombre" value="<?php echo old('nombre'); ?>" required>
        <?php echo error('nombre'); ?>
    </div>

    <!-- Descripción -->
    <div class="mb-3">
        <label for="descripcion" class="form-label">Descripción:</label>
        <textarea class="form-control <?php echo isset($errors['descripcion']) ? 'is-invalid' : ''; ?>"
                  id="descripcion" name="descripcion" rows="5" required><?php echo old('descripcion'); ?></textarea>
        <?php echo error('descripcion'); ?>
    </div>

    <!-- Fecha de Inicio -->
    <div class="mb-3">
        <label for="fecha_inicio" class="form-label">Fecha de Inicio:</label>
        <input type="date" class="form-control <?php echo isset($errors['fecha_inicio']) ? 'is-invalid' : ''; ?>"
               id="fecha_inicio" name="fecha_inicio" value="<?php echo old('fecha_inicio'); ?>" required>
        <?php echo error('fecha_inicio'); ?>
    </div>

    <!-- Fecha Fin Estimada (Opcional) -->
    <div class="mb-3">
        <label for="fecha_fin_estimada" class="form-label">Fecha Fin Estimada (Opcional):</label>
        <input type="date" class="form-control <?php echo isset($errors['fecha_fin_estimada']) ? 'is-invalid' : ''; ?>"
               id="fecha_fin_estimada" name="fecha_fin_estimada" value="<?php echo old('fecha_fin_estimada'); ?>">
        <?php echo error('fecha_fin_estimada'); ?>
    </div>

    <!-- Estado del Proyecto -->
    <div class="mb-3">
        <label for="estado" class="form-label">Estado:</label>
        <select class="form-select <?php echo isset($errors['estado']) ? 'is-invalid' : ''; ?>" id="estado" name="estado" required>
            <option value="">Seleccione un estado</option>
            <option value="activo" <?php echo old('estado') == 'activo' ? 'selected' : ''; ?>>Activo</option>
            <option value="culminado" <?php echo old('estado') == 'culminado' ? 'selected' : ''; ?>>Culminado</option>
            <option value="pendiente" <?php echo old('estado') == 'pendiente' ? 'selected' : ''; ?>>Pendiente</option>
            <option value="cancelado" <?php echo old('estado') == 'cancelado' ? 'selected' : ''; ?>>Cancelado</option>
        </select>
        <?php echo error('estado'); ?>
    </div>

    <!-- Tutor Docente -->
    <div class="mb-3">
        <label for="id_tutor_docente_fk" class="form-label">Tutor Docente:</label>
        <select class="form-select <?php echo isset($errors['id_tutor_docente_fk']) ? 'is-invalid' : ''; ?>" id="id_tutor_docente_fk" name="id_tutor_docente_fk" required>
            <option value="">Seleccione un tutor</option>
            <?php foreach ($docentes as $docente): ?>
                <option value="<?php echo htmlspecialchars($docente['id_docente']); ?>"
                    <?php echo old('id_tutor_docente_fk') == $docente['id_docente'] ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($docente['nombre'] . ' ' . $docente['apellido']); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <?php echo error('id_tutor_docente_fk'); ?>
    </div>

    <!-- Comunidad -->
    <div class="mb-3">
        <label for="id_comunidad_fk" class="form-label">Comunidad:</label>
        <select class="form-select <?php echo isset($errors['id_comunidad_fk']) ? 'is-invalid' : ''; ?>" id="id_comunidad_fk" name="id_comunidad_fk" required>
            <option value="">Seleccione una comunidad</option>
            <?php foreach ($comunidades as $comunidad): ?>
                <option value="<?php echo htmlspecialchars($comunidad['id_comunidad']); ?>"
                    <?php echo old('id_comunidad_fk') == $comunidad['id_comunidad'] ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($comunidad['nombre']); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <?php echo error('id_comunidad_fk'); ?>
    </div>

    <!-- Estudiantes (Selector Múltiple) -->
    <div class="mb-3">
        <label for="estudiantes" class="form-label">Estudiantes (no asignados):</label>
        <select class="form-select" id="estudiantes" name="estudiantes[]" multiple size="5">
            <?php foreach ($estudiantesNoAsignados as $estudiante): ?>
                <option value="<?php echo htmlspecialchars($estudiante['id_estudiante']); ?>"
                    <?php echo in_array($estudiante['id_estudiante'], (array)old('estudiantes', [])) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($estudiante['nombre'] . ' ' . $estudiante['apellido'] . ' (C.I.: ' . $estudiante['cedula'] . ')'); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <div class="form-text">Mantén Ctrl/Cmd para seleccionar múltiples estudiantes. Solo se muestran los estudiantes sin proyecto asignado.</div>
    </div>

    <button type="submit" class="btn btn-success">Registrar Proyecto</button>
    <a href="/proyecto_pnfi/public/admin/proyectos" class="btn btn-secondary">Cancelar</a>
</form>