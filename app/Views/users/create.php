<?php
// app/Views/users/create.php

$titulo = $titulo ?? 'Crear Nuevo Usuario';
$error_message = $error_message ?? '';
// Podrías añadir lógica para 'old_input' si quieres que los campos mantengan los valores si hay un error
?>

<div class="container mt-4">
    <h1><?php echo htmlspecialchars($titulo); ?></h1>

    <?php if (!empty($error_message)): ?>
        <div class="alert alert-danger" role="alert">
            <?php echo htmlspecialchars($error_message); ?>
        </div>
    <?php endif; ?>

    <form action="/proyecto_pnfi/public/admin/usuarios" method="POST">
        <div class="mb-3">
            <label for="cedula" class="form-label">Cédula:</label>
            <input type="text" class="form-control" id="cedula" name="cedula" required>
        </div>
        <div class="mb-3">
            <label for="nombre" class="form-label">Nombre:</label>
            <input type="text" class="form-control" id="nombre" name="nombre" required>
        </div>
        <div class="mb-3">
            <label for="apellido" class="form-label">Apellido:</label>
            <input type="text" class="form-control" id="apellido" name="apellido" required>
        </div>
        <div class="mb-3">
            <label for="correo" class="form-label">Correo:</label>
            <input type="email" class="form-control" id="correo" name="correo" required>
        </div>
        <div class="mb-3">
            <label for="telefono" class="form-label">Teléfono:</label>
            <input type="text" class="form-control" id="telefono" name="telefono">
        </div>
        <div class="mb-3">
            <label for="direccion" class="form-label">Dirección:</label>
            <input type="text" class="form-control" id="direccion" name="direccion">
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Contraseña:</label>
            <input type="password" class="form-control" id="password" name="password" required>
        </div>
        <div class="mb-3">
            <label for="rol" class="form-label">Rol:</label>
            <select class="form-select" id="rol" name="rol" required>
                <option value="">Seleccione un Rol</option>
                <option value="administrador">Administrador</option>
                <option value="Maestro">Maestro</option>
                <option value="Estudiante">Estudiante</option>
                </select>
        </div>
        <div class="mb-3">
            <label for="estado" class="form-label">Estado:</label>
            <select class="form-select" id="estado" name="estado" required>
                <option value="activo">Activo</option>
                <option value="inactivo">Inactivo</option>
            </select>
        </div>

        <button type="submit" class="btn btn-success">Guardar Usuario</button>
        <a href="/proyecto_pnfi/public/admin/usuarios" class="btn btn-secondary">Cancelar</a>
    </form>
</div>