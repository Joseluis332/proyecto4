<?php
// app/Views/users/index.php

// Acceder a las variables pasadas desde el controlador: $titulo, $users, $success_message, $error_message
$titulo = $titulo ?? 'Gestión de Usuarios'; // Valor por defecto si no se pasa
$users = $users ?? []; // Asegurarse de que $users sea un array

?>

<div class="container">
    <h1><?php echo htmlspecialchars($titulo); ?></h1>

    <?php if (!empty($success_message)): ?>
        <div class="alert alert-success" role="alert">
            <?php echo htmlspecialchars($success_message); ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($error_message)): ?>
        <div class="alert alert-danger" role="alert">
            <?php echo htmlspecialchars($error_message); ?>
        </div>
    <?php endif; ?>

    <a href="/proyecto_pnfi/public/admin/usuarios/crear" class="btn btn-primary mb-3">Crear Nuevo Usuario</a>

    <?php if (empty($users)): ?>
        <p>No hay usuarios registrados.</p>
    <?php else: ?>
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Cédula</th>
                    <th>Nombre</th>
                    <th>Apellido</th>
                    <th>Correo</th>
                    <th>Teléfono</th>
                    <th>Dirección</th> <th>Rol</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($user['id_usuario'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($user['cedula'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($user['nombre'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($user['apellido'] ?? ''); ?></td> <td><?php echo htmlspecialchars($user['correo'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($user['telefono'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($user['direccion'] ?? ''); ?></td> <td><?php echo htmlspecialchars($user['rol'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($user['estado'] ?? ''); ?></td>
                        <td>
                            <a href="/proyecto_pnfi/public/admin/usuarios/editar/<?php echo htmlspecialchars($user['id_usuario'] ?? ''); ?>" class="btn btn-sm btn-info">Editar</a>

                            <?php if (($user['estado'] ?? '') === 'activo'): ?>
                            <form action="/proyecto_pnfi/public/admin/usuarios/eliminar/<?php echo htmlspecialchars($user['id_usuario'] ?? ''); ?>" method="POST" style="display:inline-block;">
                                 <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('¿Estás seguro de desactivar a este usuario?');">Desactivar</button>
                            </form>
                        <?php else: ?>
                            <form action="/proyecto_pnfi/public/admin/usuarios/activar/<?php echo htmlspecialchars($user['id_usuario'] ?? ''); ?>" method="POST" style="display:inline-block;">
                                 <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('¿Estás seguro de activar a este usuario?');">Activar</button>
                             </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>