<!-- app/Views/errors/500.php -->
<div class="container text-center py-5">
    <h1 class="display-4 text-warning"><?php echo htmlspecialchars($title ?? '500 Error Interno del Servidor'); ?></h1>
    <p class="lead"><?php echo htmlspecialchars($message ?? 'Ha ocurrido un error inesperado. Estamos trabajando para solucionarlo.'); ?></p>
    <a href="/proyecto_pnfi/public/" class="btn btn-secondary btn-lg mt-3">Volver a la página de inicio</a>
</div>