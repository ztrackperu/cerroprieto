<?php include "Views/templates/navbar.php"; ?>
<div class="px-2 py-2">
    <div class="col-12">
        <div class="row">
            <div class="col-12 col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h1 class="fw-bold fs-4">Homogenization</h1>
                        <form id="formProcessH" class="formProcessH" onsubmit="frmProcessH(event);">
                            <div class="row mt-4">
                                <div class="col-12 col-md-6 col-lg-4">
                                    <label for="lblTmp_homogenization">Temperature</label>
                                </div>
                                <div class="col-12 col-md-6 col-lg-4 text-center">
                                    <input id="tmpInput_homogenization" class="form-control text-center" type="text"  name="temperature_homogenization" value="0" required>
                                </div>
                            </div>
                            <div class="row mt-4">
                                <div class="col-12 col-md-6 col-lg-4">
                                    <label for="lblHmy_homogenization">Humidity</label>
                                </div>
                                <div class="col-12 col-md-6 col-lg-4 text-center">
                                    <input id="hmyInput_homogenization" class="form-control text-center" type="text" name="humidity_homogenization" value="0" required> 
                                </div>
                            </div>
                            <h1 class="fw-bold fs-4 mt-4">Ripener</h1>
                            <div class="border">
                            <div class="row mt-4">
                                <div class="col-12 col-md-6 col-lg-4">
                                    <label for="lblTmp">SP Temperature</label>
                                </div>
                                <div class="col-12 col-md-6 col-lg-4 text-center">
                                    <input id="tmpInput" class="form-control text-center" type="text"  name="spTemperature" value="0" required>
                                </div>
                            </div>
                            <div class="row mt-4">
                                <div class="col-12 col-md-6 col-lg-4">
                                    <label for="lblEthy">SP Ethylene</label>
                                </div>
                                <div class="col-12 col-md-6 col-lg-4 text-center">
                                    <input id="ethyInput" class="form-control text-center" type="text" name="spEthylene" value="0" required> 
                                </div>
                            </div>
                            <div class="row mt-4">
                                <div class="col-12 col-md-6 col-lg-4">
                                    <label for="lblCo2">SP CO2</label>
                                </div>
                                <div class="col-12 col-md-6 col-lg-4 text-center">
                                    <input id="co2Input" class="form-control text-center" type="text" name="spCo2" value="0" required> 
                                </div>
                            </div>
                            <div class="row mt-4">
                                <div class="col-12 col-md-6 col-lg-4">
                                    <label for="lblHumidity">SP Humidity</label>
                                </div>
                                <div class="col-12 col-md-6 col-lg-4 text-center">
                                    <input id="hmInput" class="form-control text-center" type="text" name="spHumidity" value="0" required> 
                                </div>
                            </div>
                            <div class="row mt-4">
                                <div class="col-12 col-md-6 col-lg-4">
                                    <label for="lblIHours">I. Hours</label>
                                </div>
                                <div class="col-12 col-md-6 col-lg-4 text-center">
                                    <input id="ihoursInput" class="form-control text-center" type="text" name="iHours" value="0" required> 
                                </div>
                            </div>
                            <div class="row mt-4">
                                <div class="col-12">
                                    <div class="d-flex justify-content-between">
                                        <label for="process">Process</label>
                                        <button class="btn btn-success btn-process px-2 py-2" id="btnAddProcess" type="submit">
                                            <i class="bi bi-patch-check-fill fs-6"></i>
                                                Start Process
                                        </button>
                                    </div>
                                </div>
                            </div>
                            </div>
                            
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="px-2 py-2">
    <div class="col-12">
        <div class="row">
            <div class="col-12 col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h1 class="fw-bold fs-4">Ripener</h1>
                        <form id="formProcess" class="formProcessR" onsubmit="frmProcess(event);">
                            <div class="row mt-4">
                                <div class="col-12 col-md-6 col-lg-4">
                                    <label for="lblTmp">SP Temperature</label>
                                </div>
                                <div class="col-12 col-md-6 col-lg-4 text-center">
                                    <input id="tmpInput" class="form-control text-center" type="text"  name="spTemperature" value="0" required>
                                </div>
                            </div>
                            <div class="row mt-4">
                                <div class="col-12 col-md-6 col-lg-4">
                                    <label for="lblEthy">SP Ethylene</label>
                                </div>
                                <div class="col-12 col-md-6 col-lg-4 text-center">
                                    <input id="ethyInput" class="form-control text-center" type="text" name="spEthylene" value="0" required> 
                                </div>
                            </div>
                            <div class="row mt-4">
                                <div class="col-12 col-md-6 col-lg-4">
                                    <label for="lblCo2">SP CO2</label>
                                </div>
                                <div class="col-12 col-md-6 col-lg-4 text-center">
                                    <input id="co2Input" class="form-control text-center" type="text" name="spCo2" value="0" required> 
                                </div>
                            </div>
                            <div class="row mt-4">
                                <div class="col-12 col-md-6 col-lg-4">
                                    <label for="lblHumidity">SP Humidity</label>
                                </div>
                                <div class="col-12 col-md-6 col-lg-4 text-center">
                                    <input id="hmInput" class="form-control text-center" type="text" name="spHumidity" value="0" required> 
                                </div>
                            </div>
                            <div class="row mt-4">
                                <div class="col-12 col-md-6 col-lg-4">
                                    <label for="lblIHours">I. Hours</label>
                                </div>
                                <div class="col-12 col-md-6 col-lg-4 text-center">
                                    <input id="ihoursInput" class="form-control text-center" type="text" name="iHours" value="0" required> 
                                </div>
                            </div>
                            <div class="row mt-4">
                                <div class="col-12">
                                    <div class="d-flex justify-content-between">
                                        <label for="process">Process</label>
                                        <button class="btn btn-success btn-process px-2 py-2" id="btnAddProcess" type="submit">
                                            <i class="bi bi-patch-check-fill fs-6"></i>
                                                Start Process
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="px-2 py-2">
    <div class="col-12">
        <div class="row">
            <div class="col-12 col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h1 class="fw-bold fs-4">Ventilation</h1>
                        <form id="formProcessV" class="formProcessV" onsubmit="frmProcessV(event);">
                            <div class="row mt-4">
                                <div class="col-12 col-md-6 col-lg-4">
                                    <label for="lblFan">Fun</label>
                                </div>
                                <div class="col-12 col-md-6 col-lg-4 text-center">
                                    <select class="form-select">
                                        <option value="0">Select</option>
                                        <option value="1">100%</option>
                                        <option value="2">50%</option>
                                    </select>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="px-2 py-2">
    <div class="col-12">
        <div class="row">
            <div class="col-12 col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h1 class="fw-bold fs-4">Cooling</h1>
                        <form id="formProcessC" class="formProcessC" onsubmit="frmProcessC(event);">
                            <div class="row mt-4">
                                <div class="col-12 col-md-6 col-lg-4">
                                    <label for="lblProductTmp">Product Temperature</label>
                                </div>
                                <div class="col-12 col-md-6 col-lg-4 text-center">
                                    <input id="pTmpInput" class="form-control text-center" type="text" name="pTmpInput" value="0" required> 
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!--MODAL -->
<div class='modal fade' id='strtProcess' tabindex='-1' aria-labelledby='my-modal-title' aria-hidden='true'>
    <div class='modal-dialog'>
        <div class='modal-content'>
            <div class='modal-header'>
                <h5 class='modal-title' id='title'>Validate</h5>
                <button class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
            </div>
            <div class='modal-body'>
                <div class="mt-2">
                    <div class="row">
                        <div class="col-12 text-center">
                            <h5>Do you want to start the ripening process?</h5>
                            <div class="card py-2 px-2">
                                <div class="card-body">
                                    <div class="col-12 mt-2">
                                        <div class="row">
                                            <div class="col-6">
                                                <label for="lblSptmp">SP Temperature</label>
                                            </div>
                                            <div class="col-6">
                                                <input id="validateTMP" class="form-control text-center" type="text"  name="validateTMP" value="" readonly>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 mt-2">
                                        <div class="row">
                                            <div class="col-6">
                                                <label for="lblSpethy">SP Ethylene</label>
                                            </div>
                                            <div class="col-6">
                                                <input id="validateEthy" class="form-control text-center" type="text"  name="validateEthy" value="" readonly>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 mt-2">
                                        <div class="row">
                                            <div class="col-6">
                                                <label for="lblSpco2">SP Co2</label>
                                            </div>
                                            <div class="col-6">
                                                <input id="validateCo2" class="form-control text-center" type="text"  name="validateCo2" value="" readonly>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 mt-2">
                                        <div class="row">
                                            <div class="col-6">
                                                <label for="lblSpHm">SP Humidity</label>
                                            </div>
                                            <div class="col-6">
                                                <input id="validateHm" class="form-control text-center" type="text"  name="validateHm" value="" readonly>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 mt-2">
                                        <div class="row">
                                            <div class="col-6">
                                                <label for="lblIjHours">Injection Hours</label>
                                            </div>
                                            <div class="col-6">
                                                <input id="validateIH" class="form-control text-center" type="text"  name="validateIH" value="" readonly>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 text-center gap-2 mt-2">
                        <button type="button" class="btn btn-success col-3" onclick="btnProcesar()">Yes</button>
                        <button type="button" class="btn btn-danger col-3 clean_inputTMP" data-bs-dismiss="modal">No</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include "Views/templates/footer.php"; ?>