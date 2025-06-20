<!-- app/Views/errors/404.php -->
<div class="container text-center py-5">
    <h1 class="display-4 text-danger"><?php echo htmlspecialchars($title ?? '404 No Encontrado'); ?></h1>
    <p class="lead"><?php echo htmlspecialchars($message ?? 'La página que solicitaste no pudo ser encontrada. Por favor, verifica la URL e intenta de nuevo.'); ?></p>
    <a href="/proyecto_pnfi/public/" class="btn btn-primary btn-lg mt-3">Ir a la página de inicio</a>
</div>