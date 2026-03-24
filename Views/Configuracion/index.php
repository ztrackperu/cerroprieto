<?php include "Views/templates/navbar.php"; ?>
<div class="app-title px-2">
    <div>
        <h1><i class="fa fa-cog"></i> Configuración</h1>
    </div>
</div>
<div class="px-2 pb-4">
    <?php if (!empty($data) && is_array($data)): ?>
        <div class="alert alert-info">Datos de configuración cargados. (Edición: implementar formulario según modelo de negocio.)</div>
    <?php else: ?>
        <div class="alert alert-warning">No hay registro en la tabla <code>configuracion</code> o la consulta falló.</div>
    <?php endif; ?>
    <a href="<?php echo base_url; ?>Configuracion/admin" class="btn btn-secondary">Panel admin</a>
</div>
<?php include "Views/templates/footer.php"; ?>
