<?php include "Views/templates/navbar.php"; ?>
<div class="px-2 py-4">
    <div class="col-md-5 mx-auto">
        <div class="card">
            <div class="card-header text-center bg-primary">
                <h4 class="text-white mb-0">No tienes permisos</h4>
            </div>
            <div class="card-body text-center">
                <a href="<?php echo base_url; ?>Configuracion/admin" class="btn btn-danger">Regresar</a>
            </div>
        </div>
    </div>
</div>
<?php include "Views/templates/footer.php"; ?>
