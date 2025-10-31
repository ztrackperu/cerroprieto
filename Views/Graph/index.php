<?php include "Views/templates/navbar.php"; ?>
<div class="px-2 py-2">
    <div class="col-12">
        <div class="row">
            <div class="col-12 col-lg-12">
                <div class="card">
                    <div class="card-body">

                        <div class="d-flex flex-wrap justify-content-center mt-5">
                            
                        <h1 align="center" id="tituloGrafica">TEST123456-7</h1>



                        <div class="row justify-content-center " style="padding: 5px; margin-top:1px;">
                            <div class="col-8 col-lg-3 align-self-end" style="margin-top:5px;" >
                                <h5 >Search by Date :</h5>
                            </div>
                            <div class="col-4 col-lg-2 align-self-end" style="padding-right: 15px; margin-top:5px;">
                                <select class="form-select" aria-label="Default select example" id="temp_c_f">
                                    <option value=0 selected>C°</option>
                                    <option value=1 >F°</option>
                                </select>
                            </div>
                            <div class="col-6 col-lg-2" style="padding-left: 15px; margin-top:10px;">
                                <h5 ><strong>From :</strong></h5>
                                <input class='form-control'  id="fechaInicial" type="datetime-local">	
                            </div>
                            <div class="col-6 col-lg-2" style="padding-right: 15px;margin-top:10px;">
                                <h5 ><strong>To :</strong></h5>
                                <input class='form-control' id="fechaFin" type="datetime-local">
                            </div>
                            <div class="col-12 col-lg-2 align-self-center d-grid" style="margin-top:5px;">
                                <button type="button"  id="fechaPer" onclick="procesarFecha()" class="btn btn-primary btn-lg">Search </button>
                            </div>
                        </div>
                        <!--<div class="container "> -->
                        <div id="legend-container" class="container" style="padding-left: 2px;padding-right: 2px;"></div> 
                        <!--</div> -->
                        <div class="container">
  <div class="row">
    <div class="col-1">
    </div>
    <div class="col-14">
    <canvas align ="center" id="graficaFinal" style="" width="1400" height="800"></canvas>
    </div>
    <div class="col-1">
    </div>
  </div>
</div>

                        </div>
                        <a id="bajarGraph" class="btn btn-outline-success btn-lg btn-block">DOWNLOAD GRAPH</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include "Views/templates/footer.php"; ?>