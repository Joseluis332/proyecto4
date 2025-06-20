<
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title ?? 'Iniciar Sesión - Sistema PNFI'; ?></title>
    <!-- Incluye Tailwind CSS (asegúrate de que esté cargado en tu layout principal o aquí si esta vista no usa layout) -->
    <!-- Para esta vista de login, la pondremos directamente para que sea autocontenida -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Opcional: Fuente Inter para consistencia */
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="w-full max-w-md bg-white rounded-lg shadow-xl p-8 space-y-6">
        <h2 class="text-3xl font-bold text-center text-gray-800">Iniciar Sesión</h2>
        <p class="text-center text-gray-600">Accede a tu cuenta de gestión de proyectos</p>

        <?php
        // Mostrar mensajes flash si existen
        if (!empty($success_message)) {
            echo '<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">';
            echo '<strong class="font-bold">¡Éxito!</strong>';
            echo '<span class="block sm:inline ml-2">' . htmlspecialchars($success_message) . '</span>';
            echo '</div>';
        }
        if (!empty($error_message)) {
            echo '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">';
            echo '<strong class="font-bold">¡Error!</strong>';
            echo '<span class="block sm:inline ml-2">' . htmlspecialchars($error_message) . '</span>';
            echo '</div>';
        }
        // Para errores de validación específicos (si tu AuthController maneja errores de validación por campo)
        $errors = $_SESSION['errors'] ?? []; // Asumiendo que guardas errores en $_SESSION['errors']
        if (!empty($errors)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                <strong class="font-bold">¡Atención!</strong>
                <ul class="mt-2 list-disc list-inside">
                    <?php foreach ($errors as $field => $message): ?>
                        <li><?php echo htmlspecialchars($message); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php unset($_SESSION['errors']); // Limpiar errores después de mostrarlos
        endif;
        ?>

        <form action="/proyecto_pnfi/public/login" method="POST" class="space-y-4">
            <div>
                <label for="cedula" class="block text-sm font-medium text-gray-700 mb-1">Cédula</label>
                <input type="text" id="cedula" name="cedula" required
                       class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm <?php echo isset($errors['cedula']) ? 'border-red-500' : ''; ?>"
                       placeholder="Ej. 12345678"
                       value="<?php echo htmlspecialchars($_POST['cedula'] ?? ''); // Repopular el campo ?>">
                <?php if (isset($errors['cedula'])): ?><p class="mt-1 text-sm text-red-600"><?php echo htmlspecialchars($errors['cedula']); ?></p><?php endif; ?>
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Contraseña</label>
                <input type="password" id="password" name="password" required
                       class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm <?php echo isset($errors['password']) ? 'border-red-500' : ''; ?>"
                       placeholder="Ingrese su contraseña">
                <?php if (isset($errors['password'])): ?><p class="mt-1 text-sm text-red-600"><?php echo htmlspecialchars($errors['password']); ?></p><?php endif; ?>
            </div>

            <div class="flex items-center justify-between">
                <a href="#" class="text-sm text-blue-600 hover:text-blue-800">¿Olvidaste tu contraseña?</a>
            </div>

            <button type="submit"
                    class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transform transition-transform duration-200 hover:scale-105">
                Iniciar Sesión
            </button>
        </form>

        <p class="text-center text-sm text-gray-600">
            ¿No tienes una cuenta? 
            <a href="/proyecto_pnfi/public/register" class="font-medium text-blue-600 hover:text-blue-800">Regístrate aquí</a>
        </p>
    </div>
</body>
</html>
