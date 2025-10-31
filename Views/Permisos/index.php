<?php include "Views/templates/navbar.php"; ?>
<p></p>
<div class="app-title">
    <div>
        <h1><i class="fa fa-dashboard"></i> Permisos</h1>
    </div>
</div>
<button class="btn btn-primary mb-2" type="button" onclick="frmPermiso();">+<i class="fa fa-plus"></i></button>
<div class="row">
    <div class="col-lg-12">
        <div class="tile">
            <div class="tile-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="tblPermisos">
                        <thead class="thead-dark">
                            <tr>
                                <th>Id</th>
                                <th>Permiso</th> 
                                <th>Tipo</th>
                                <th>Estado</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="nuevoPermiso" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="my-modal-title"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h5 class="modal-title text-white" id="title">Nuevo Permiso</h5>
                <button class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="post" id="frmPermiso">
                    <div class="form-group">
                        <label for="Permiso">Permiso</label>
                        <input type="hidden" id="id" name="id">
                        <input id="Permiso" class="form-control" type="text" name="Permiso" placeholder="Permiso">
                    </div>

                    <button class="btn btn-primary" type="button" onclick="registrarPermiso(event);"
                        id="btnAccion">Registrar</button>
                    <button class="btn btn-danger" type="button" data-bs-dismiss="modal">Cancelar</button>
                </form>
            </div>
        </div>
    </div>
</div>
<div id="permisos" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="my-modal-title" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h5 class="modal-title text-white">Asignar Permisos</h5>
                <button class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="frmPermisos">
                </form>
            </div>
        </div>
    </div>
</div>
<?php include "Views/templates/footer.php"; ?>