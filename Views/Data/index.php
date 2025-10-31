<?php include "Views/templates/navbar.php"; ?>
<div class="px-2 py-2">
    <div class="col-12">
        <div class="row">
            <div class="col-12 col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <!--CONTENEDOR -->
                        <h1 class="fw-bold fs-4" id="titleData"></h1>
                        <div class="table-responsive mt-5">
                            <table class="table table-bordered table-hover" style="width:100%" id="tblDatos">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Created At</th>
                                        <th>Set Point</th>
                                        <th>Return Air</th>
                                        <th>Temp Supply</th>
                                        <th>Relative Humidity</th>
                                        <th>Cargo 1</th>
                                        <th>Ambient Air</th>
                                        <th>Evaporation Coil</th>
                                        <th>Power State</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Aquí irán tus filas de datos -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include "Views/templates/footer.php"; ?>