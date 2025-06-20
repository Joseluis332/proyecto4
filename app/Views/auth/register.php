<?php use App\Core\SessionManager; ?>

<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">
        <div class="card">
            <div class="card-header text-center">
                <h3>Registrarse</h3>
            </div>
            <div class="card-body">
                <?php if (SessionManager::get('error_message')): ?>
                    <div class="alert alert-danger" role="alert">
                        <?php echo htmlspecialchars(SessionManager::get('error_message')); SessionManager::remove('error_message'); ?>
                    </div>
                <?php endif; ?>
                <?php if (SessionManager::get('success_message')): ?>
                    <div class="alert alert-success" role="alert">
                        <?php echo htmlspecialchars(SessionManager::get('success_message')); SessionManager::remove('success_message'); ?>
                    </div>
                <?php endif; ?>

                <form action="/proyecto_pnfi/public/register" method="POST">
                    <div class="mb-3">
                        <label for="nombre" class="form-label">Nombre</label>
                        <input type="text" class="form-control" id="nombre" name="nombre" required>
                    </div>
                    <div class="mb-3">
                        <label for="apellido" class="form-label">Apellido</label>
                        <input type="text" class="form-control" id="apellido" name="apellido" required>
                    </div>
                    <div class="mb-3">
                        <label for="cedula" class="form-label">Cédula</label>
                        <input type="text" class="form-control" id="cedula" name="cedula" required>
                    </div>
                    <div class="mb-3">
                        <label for="correo" class="form-label">Correo Electrónico</label>
                        <input type="email" class="form-control" id="correo" name="correo" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Contraseña</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <div class="mb-3">
                        <label for="confirm_password" class="form-label">Confirmar Contraseña</label>
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                    </div>
                    <div class="mb-3">
                        <label for="rol" class="form-label">Rol</label>
                        <select class="form-select" id="rol" name="rol" required>
                            <option value="estudiante">Estudiante</option>
                            <option value="docente">Docente</option>
                            <option value="administrador">Administrador</option>
                        </select>
                    </div>
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">Registrarse</button>
                    </div>
                </form>
                <p class="mt-3 text-center">
                    ¿Ya tienes una cuenta? <a href="/proyecto_pnfi/public/login">Inicia Sesión</a>
                </p>
            </div>
        </div>
    </div>
</div>