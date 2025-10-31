<?php include "Views/templates/navbar.php"; ?>
<div class="px-2 py-2">
    <div class="col-12">
        <div class="row">
            <div class="col-12 col-lg-12">
                <div class="card">
                    <div class="card-body py-5">
                        <div class="d-flex justify-content-center">
                            <img src="<?php echo base_url.'Assets'; ?>/img/contenedor_2.png" alt="Contenedor" class="img_contenedor">
                        </div>
                        <div class="d-flex flex-wrap justify-content-center mt-5" id="contenidoControl">
                            <!-- CONTENT CONTROL -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!--MODAL PARA PROCESAR TMP -->
<div class='modal fade' id='procesarTMP' tabindex='-1' aria-labelledby='my-modal-title' aria-hidden='true'>
    <div class='modal-dialog'>
        <div class='modal-content'>
            <div class='modal-header'>
                <h5 class='modal-title' id='title'>SP TEMPERATURE</h5>
                <button class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
            </div>
            <div class='modal-body'>
                <div class="mt-2">
                    <h5 class="text-upppercase text-center">Are you sure you want to edit this parameter?</h5>
                    <p></p>
                    <div id="sp_temp_esquema"></div>
                    <p></p>
                    <div class="col-12 text-center gap-2">
                        <button type="button" class="btn btn-success col-3" onclick="btnProcesarTMP()">Yes</button>
                        <button type="button" class="btn btn-danger col-3 clean_inputTMP" data-bs-dismiss="modal">No</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!--MODAL PARA PROCESAR ETHY -->
<div class='modal fade' id='procesarETHY' tabindex='-1' aria-labelledby='my-modal-title' aria-hidden='true'>
    <div class='modal-dialog'>
        <div class='modal-content'>
            <div class='modal-header'>
                <h5 class='modal-title' id='title'>SP ETHYLENE</h5>
                <button class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
            </div>
            <div class='modal-body'>
                <div class="mt-2">
                    <h5 class="text-upppercase text-center">Are you sure you want to edit this parameter?</h5>
                    <p></p>
                    <div id="sp_ety_esquema"></div>
                    <p></p>
                    <div class="col-12 text-center gap-2">
                        <button type="button" class="btn btn-success col-3" onclick="btnProcesarETHY()">Yes</button>
                        <button type="button" class="btn btn-danger col-3 clean_inputETHY" data-bs-dismiss="modal">No</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!--MODAL PARA PROCESAR CO2 -->
<div class='modal fade' id='procesarCO2' tabindex='-1' aria-labelledby='my-modal-title' aria-hidden='true'>
    <div class='modal-dialog'>
        <div class='modal-content'>
            <div class='modal-header'>
                <h5 class='modal-title' id='title'>SP CO2</h5>
                <button class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
            </div>
            <div class='modal-body'>
                <div class="mt-2">
                    <h5 class="text-upppercase text-center">Are you sure you want to edit this parameter?</h5>
                    <p></p>
                    <div id="sp_co2_esquema"></div>
                    <p></p>
                    <div class="col-12 text-center gap-2">
                        <button type="button" class="btn btn-success col-3" onclick="btnProcesarCO2()">Yes</button>
                        <button type="button" class="btn btn-danger col-3 clean_inputCO2" data-bs-dismiss="modal">No</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!--MODAL PARA PROCESAR HUMIDITY -->
<div class='modal fade' id='procesarHumidity' tabindex='-1' aria-labelledby='my-modal-title' aria-hidden='true'>
    <div class='modal-dialog'>
        <div class='modal-content'>
            <div class='modal-header'>
                <h5 class='modal-title' id='title'>SP HUMIDITY</h5>
                <button class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
            </div>
            <div class='modal-body'>
                <div class="mt-2">
                    <h5 class="text-upppercase text-center">Are you sure you want to edit this parameter?</h5>
                    <p></p>
                    <div id="sp_humidity_esquema"></div>
                    <p></p>
                    <div class="col-12 text-center gap-2">
                        <button type="button" class="btn btn-success col-3" onclick="btnProcesarHumidity()">Yes</button>
                        <button type="button" class="btn btn-danger col-3 clean_inputHumidity" data-bs-dismiss="modal">No</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!--MODAL PARA PROCESAR I HOURS -->
<div class='modal fade' id='procesarIHours' tabindex='-1' aria-labelledby='my-modal-title' aria-hidden='true'>
    <div class='modal-dialog'>
        <div class='modal-content'>
            <div class='modal-header'>
                <h5 class='modal-title' id='title'>INJECTION HOURS</h5>
                <button class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
            </div>
            <div class='modal-body'>
                <div class="mt-2">
                    <h5 class="text-upppercase text-center">Are you sure you want to edit this parameter?</h5>
                    <p></p>
                    <div id="sp_hora_esquema"></div>
                    <p></p>
                    <div class="col-12 text-center gap-2">
                        <button type="button" class="btn btn-success col-3" onclick="btnprocesarIHours()">Yes</button>
                        <button type="button" class="btn btn-danger col-3 clean_inputIHours" data-bs-dismiss="modal">No</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!--MODAL PARA PROCESAR I HOURS -->
<div class='modal fade' id='defrostActivate' tabindex='-1' aria-labelledby='my-modal-title' aria-hidden='true'>
    <div class='modal-dialog'>
        <div class='modal-content'>
            <div class='modal-header'>
                <h5 class='modal-title' id='title'>DEFROST ACTIVE</h5>
                <button class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
            </div>
            <div class='modal-body'>
                <div class="mt-2">
                    <h5 class="text-upppercase text-center">Are you sure you want to edit this parameter?</h5>
                    <p></p>
                    <div id="sp_hora_esquema"></div>
                    <p></p>
                    <div class="col-12 text-center gap-2">
                        <button type="button" class="btn btn-success col-3" id="defrost_p()">Yes</button>
                        <button type="button" class="btn btn-danger col-3 clean_inputIHours" data-bs-dismiss="modal">No</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include "Views/templates/footer.php"; ?>