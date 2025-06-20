<?php
// app/Views/users/edit.php

$titulo = $titulo ?? 'Editar Usuario';
$user = $user ?? null; // Asegurarse de que $user exista
$error_message = $error_message ?? '';

if (!$user) {
    // Esto no debería ocurrir si el controlador maneja bien el caso de usuario no encontrado,
    // pero es una salvaguarda.
    echo '<div class="alert alert-danger" role="alert">Usuario no encontrado para editar.</div>';
    return; // Detiene la ejecución de la vista
}
?>

<div class="container mt-4">
    <h1><?php echo htmlspecialchars($titulo); ?>: <?php echo htmlspecialchars($user['nombre'] . ' ' . $user['apellido']); ?></h1>

    <?php if (!empty($error_message)): ?>
        <div class="alert alert-danger" role="alert">
            <?php echo htmlspecialchars($error_message); ?>
        </div>
    <?php endif; ?>

    <form action="/proyecto_pnfi/public/admin/usuarios/actualizar/<?php echo htmlspecialchars($user['id_usuario']); ?>" method="POST">
        <input type="hidden" name="id_usuario" value="<?php echo htmlspecialchars($user['id_usuario']); ?>">

        <div class="mb-3">
            <label for="cedula" class="form-label">Cédula:</label>
            <input type="text" class="form-control" id="cedula" name="cedula" value="<?php echo htmlspecialchars($user['cedula'] ?? ''); ?>" required>
        </div>
        <div class="mb-3">
            <label for="nombre" class="form-label">Nombre:</label>
            <input type="text" class="form-control" id="nombre" name="nombre" value="<?php echo htmlspecialchars($user['nombre'] ?? ''); ?>" required>
        </div>
        <div class="mb-3">
            <label for="apellido" class="form-label">Apellido:</label>
            <input type="text" class="form-control" id="apellido" name="apellido" value="<?php echo htmlspecialchars($user['apellido'] ?? ''); ?>" required>
        </div>
        <div class="mb-3">
            <label for="correo" class="form-label">Correo:</label>
            <input type="email" class="form-control" id="correo" name="correo" value="<?php echo htmlspecialchars($user['correo'] ?? ''); ?>" required>
        </div>
        <div class="mb-3">
            <label for="telefono" class="form-label">Teléfono:</label>
            <input type="text" class="form-control" id="telefono" name="telefono" value="<?php echo htmlspecialchars($user['telefono'] ?? ''); ?>">
        </div>
        <div class="mb-3">
            <label for="direccion" class="form-label">Dirección:</label>
            <input type="text" class="form-control" id="direccion" name="direccion" value="<?php echo htmlspecialchars($user['direccion'] ?? ''); ?>">
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Nueva Contraseña (dejar en blanco para no cambiar):</label>
            <input type="password" class="form-control" id="password" name="password">
            <small class="form-text text-muted">Deja este campo vacío si no deseas cambiar la contraseña.</small>
        </div>
        <div class="mb-3">
            <label for="rol" class="form-label">Rol:</label>
            <select class="form-select" id="rol" name="rol" required>
                <option value="">Seleccione un Rol</option>
                <option value="administrador" <?php echo (isset($user['rol']) && $user['rol'] == 'administrador') ? 'selected' : ''; ?>>Administrador</option>
                <option value="Maestro" <?php echo (isset($user['rol']) && $user['rol'] == 'Maestro') ? 'selected' : ''; ?>>Maestro</option>
                <option value="Estudiante" <?php echo (isset($user['rol']) && $user['rol'] == 'Estudiante') ? 'selected' : ''; ?>>Estudiante</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="estado" class="form-label">Estado:</label>
            <select class="form-select" id="estado" name="estado" required>
                <option value="activo" <?php echo (isset($user['estado']) && $user['estado'] == 'activo') ? 'selected' : ''; ?>>Activo</option>
                <option value="inactivo" <?php echo (isset($user['estado']) && $user['estado'] == 'inactivo') ? 'selected' : ''; ?>>Inactivo</option>
            </select>
        </div>

        <button type="submit" class="btn btn-success">Actualizar Usuario</button>
        <a href="/proyecto_pnfi/public/admin/usuarios" class="btn btn-secondary">Cancelar</a>
    </form>
</div>