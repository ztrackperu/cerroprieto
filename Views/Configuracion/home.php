<?php include "Views/templates/navbar.php"; ?>
<div class="app-title px-2">
    <div>
        <h1><i class="fa fa-dashboard"></i> Administración</h1>
    </div>
</div>
<div class="px-2 pb-4 row">
    <?php
    if (!empty($data) && is_array($data)) {
        foreach ($data as $clave => $fila) {
            $total = isset($fila['total']) ? (int) $fila['total'] : 0;
            echo '<div class="col-md-4 mb-3"><div class="card"><div class="card-body"><h5 class="card-title text-capitalize">' . htmlspecialchars(str_replace('_', ' ', $clave)) . '</h5><p class="display-6">' . $total . '</p></div></div></div>';
        }
    } else {
        echo '<div class="col-12"><div class="alert alert-secondary">Sin datos de resumen.</div></div>';
    }
    ?>
</div>
<?php include "Views/templates/footer.php"; ?>
