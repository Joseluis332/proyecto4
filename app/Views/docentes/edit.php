<?php
// C:\xampp\htdocs\proyecto_pnfi\app\Views\docentes\edit.php

// Recuperar datos antiguos y errores de sesión (si existen después de una validación fallida)
// Si no hay old_input, usamos los datos del docente para pre-llenar el formulario
$old_input = $old_input ?? $docente; // $docente viene del controlador si no hay errores previos
$errors = $errors ?? [];

// Helper para obtener el valor antiguo o el valor actual del docente
function old_edit($key, $docenteData, $default = '') {
    global $old_input;
    // Prioriza old_input (lo que el usuario envió por última vez)
    // Luego el valor original del docente
    // Finalmente un valor por defecto
    return htmlspecialchars($old_input[$key] ?? ($docenteData[$key] ?? $default));
}

// Helper para mostrar errores
function error($key) {
    global $errors;
    return isset($errors[$key]) ? '<div class="invalid-feedback d-block">' . htmlspecialchars($errors[$key]) . '</div>' : '';
}
?>

<h1 class="mb-4">Editar Docente</h1>

<form action="/proyecto_pnfi/public/admin/docentes/actualizar/<?php echo htmlspecialchars($docente['id_docente']); ?>" method="POST">
    <!-- Selección de Usuario (id_usuario_fk) -->
    <div class="mb-3">
        <label for="id_usuario_fk" class="form-label">Usuario:</label>
        <select class="form-select <?php echo isset($errors['id_usuario_fk']) ? 'is-invalid' : ''; ?>" id="id_usuario_fk" name="id_usuario_fk" required>
            <option value="">Seleccione un usuario</option>
            <?php foreach ($unassignedUsers as $user): ?>
                <option value="<?php echo htmlspecialchars($user['id_usuario']); ?>"
                    <?php 
                    // Si hay old_input, úsalo. Si no, usa el id_usuario_fk actual del docente
                    $selectedUserId = $old_input['id_usuario_fk'] ?? $docente['id_usuario_fk'];
                    echo ($selectedUserId == $user['id_usuario']) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($user['nombre'] . ' ' . $user['apellido'] . ' (C.I.: ' . $user['cedula'] . ' - Rol: ' . $user['rol'] . ')'); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <?php echo error('id_usuario_fk'); ?>
        <div class="form-text">Solo se muestran usuarios no asignados o el usuario actualmente vinculado a este docente.</div>
    </div>

    <!-- Cargo -->
    <div class="mb-3">
        <label for="cargo" class="form-label">Cargo:</label>
        <input type="text" class="form-control <?php echo isset($errors['cargo']) ? 'is-invalid' : ''; ?>"
               id="cargo" name="cargo" value="<?php echo old_edit('cargo', $docente); ?>">
        <?php echo error('cargo'); ?>
    </div>

    <!-- Especialidad -->
    <div class="mb-3">
        <label for="especialidad" class="form-label">Especialidad:</label>
        <input type="text" class="form-control <?php echo isset($errors['especialidad']) ? 'is-invalid' : ''; ?>"
               id="especialidad" name="especialidad" value="<?php echo old_edit('especialidad', $docente); ?>" required>
        <?php echo error('especialidad'); ?>
    </div>

    <!-- Fecha de Ingreso -->
    <div class="mb-3">
        <label for="fecha_ingreso" class="form-label">Fecha de Ingreso:</label>
        <input type="date" class="form-control <?php echo isset($errors['fecha_ingreso']) ? 'is-invalid' : ''; ?>"
               id="fecha_ingreso" name="fecha_ingreso" value="<?php echo old_edit('fecha_ingreso', $docente); ?>">
        <?php echo error('fecha_ingreso'); ?>
    </div>

    <button type="submit" class="btn btn-success">Actualizar Docente</button>
    <a href="/proyecto_pnfi/public/admin/docentes" class="btn btn-secondary">Cancelar</a>
</form>
