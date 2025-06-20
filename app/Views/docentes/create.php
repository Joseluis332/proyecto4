<?php
// C:\xampp\htdocs\proyecto_pnfi\app\Views\docentes\create.php

// Recuperar datos antiguos y errores de sesión
$old_input = $old_input ?? [];
$errors = $errors ?? [];

// Helper para obtener el valor antiguo o vacío
function old($key, $default = '') {
    global $old_input;
    return htmlspecialchars($old_input[$key] ?? $default);
}

// Helper para mostrar errores
function error($key) {
    global $errors;
    return isset($errors[$key]) ? '<div class="invalid-feedback d-block">' . htmlspecialchars($errors[$key]) . '</div>' : '';
}
?>

<h1 class="mb-4">Registrar Nuevo Docente</h1>

<form action="/proyecto_pnfi/public/admin/docentes" method="POST">
    <!-- Selección de Usuario (id_usuario_fk) -->
    <div class="mb-3">
        <label for="id_usuario_fk" class="form-label">Usuario:</label>
        <select class="form-select <?php echo isset($errors['id_usuario_fk']) ? 'is-invalid' : ''; ?>" id="id_usuario_fk" name="id_usuario_fk" required>
            <option value="">Seleccione un usuario</option>
            <?php foreach ($unassignedUsers as $user): ?>
                <option value="<?php echo htmlspecialchars($user['id_usuario']); ?>"
                    <?php echo old('id_usuario_fk') == $user['id_usuario'] ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($user['nombre'] . ' ' . $user['apellido'] . ' (C.I.: ' . $user['cedula'] . ' - Rol: ' . $user['rol'] . ')'); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <?php echo error('id_usuario_fk'); ?>
        <div class="form-text">Solo se muestran usuarios que no están asignados como docente o estudiante.</div>
    </div>

    <!-- Cargo -->
    <div class="mb-3">
        <label for="cargo" class="form-label">Cargo:</label>
        <input type="text" class="form-control <?php echo isset($errors['cargo']) ? 'is-invalid' : ''; ?>"
               id="cargo" name="cargo" value="<?php echo old('cargo'); ?>">
        <?php echo error('cargo'); ?>
    </div>

    <!-- Especialidad -->
    <div class="mb-3">
        <label for="especialidad" class="form-label">Especialidad:</label>
        <input type="text" class="form-control <?php echo isset($errors['especialidad']) ? 'is-invalid' : ''; ?>"
               id="especialidad" name="especialidad" value="<?php echo old('especialidad'); ?>" required>
        <?php echo error('especialidad'); ?>
    </div>

    <!-- Fecha de Ingreso -->
    <div class="mb-3">
        <label for="fecha_ingreso" class="form-label">Fecha de Ingreso:</label>
        <input type="date" class="form-control <?php echo isset($errors['fecha_ingreso']) ? 'is-invalid' : ''; ?>"
               id="fecha_ingreso" name="fecha_ingreso" value="<?php echo old('fecha_ingreso'); ?>">
        <?php echo error('fecha_ingreso'); ?>
    </div>

    <button type="submit" class="btn btn-success">Registrar Docente</button>
    <a href="/proyecto_pnfi/public/admin/docentes" class="btn btn-secondary">Cancelar</a>
</form>
