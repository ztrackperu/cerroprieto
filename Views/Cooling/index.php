<?php include "Views/templates/navbar.php"; ?>
<div class="px-2 py-2">
    <div class="col-12">
        <div class="row">
            <div class="col-12 col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h1 class="fw-bold fs-4">Mode Cooling</h1>
                        <form id="formProcess" class="formProcessH" onsubmit="frmProcess(event);">

                            <div class="row mt-4">

                                <div class="col-12 col-md-6 col-lg-6 mt-4 text-center">
                                    <small>Time</small>
                                    <input id="coolingInput" class="form-control text-center" type="text" name="cooling" value="0" required> 
                                </div>
                                <div class="col-12 col-md-6 col-lg-6 mt-4 text-center">
                                    <small>TMP PRODUCT</small>
                                    <input id="tmpProductInput" class="form-control text-center" type="text" name="tmpProduct" value="60" required> 
                                </div>
                            </div>
                            <div class="row mt-4">
                                <div class="col-12">
                                    <div class="d-flex justify-content-between">
                                        <label for="process">Process</label>
                                        <button class="btn btn-success btn-process px-2 py-2" id="btnAddProcess" type="submit">
                                            <i class="bi bi-patch-check-fill fs-6"></i>
                                                Start Cooling
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
                            <h5>Do you want to start the mode cooling?</h5>
                            <div class="card py-2 px-2">
                                <div class="card-body">

                                    <div class="col-12 mt-2">
                                        <div class="row">
                                            <div class="col-6">
                                                <label for="validateC">Cooling(hours)</label>
                                            </div>
                                            <div class="col-6">
                                                <input id="validateC_c" class="form-control text-center" type="text"  name="validateCooling_c" value="" readonly>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 mt-2">
                                        <div class="row">
                                            <div class="col-6">
                                                <label for="validateTP">TMP Product</label>
                                            </div>
                                            <div class="col-6">
                                                <input id="validateTP_c" class="form-control text-center" type="text"  name="validateTmp_c" value="" readonly>
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