<?php include "Views/templates/navbar.php"; ?>
<div class="px-2 py-2">
    <div class="col-12">
        <div class="row">
            <div class="col-12 col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-center">
                            <img src="<?php echo base_url.'Assets'; ?>/img/contenedor_2.png" alt="Contenedor" class="img_contenedor">
                        </div>
                        <div class="d-flex flex-wrap justify-content-center mt-5" id="contenidoPrincipal">
                         
                            
                            <!--
                            <div class='col-6 col-lg-3'>
                                <div class='text-center'>
                                    <button class='btn btn-primary rounded-circle'>
                                        <i class='ri-arrow-down-circle-line fs-4' id='icon_mostrar_contenido'></i>
                                    </button>
                                </div>
                            </div>-->
                        </div>
                        <div class="d-flex flex-wrap justify-content-center mt-5" id="contenidoExtra"> 
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!--modal para dispositivos -->
<div class="modal fade" id="modalDispositivos" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Device detail</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="contenidoDispositivos">
            </div>
        </div>
    </div>
</div>

<?php include "Views/templates/footer.php"; ?>