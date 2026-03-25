<?php
require_once __DIR__ . '/../../Config/Funciones/series_sector_labels.php';

$urlPath = isset($_GET['url']) ? (string) $_GET['url'] : '';
$seg = explode('/', trim($urlPath, '/'));
$seriesSector = isset($seg[2]) && $seg[2] !== '' ? $seg[2] : 'starcool_cerro_prieto';
if (!in_array($seriesSector, series_sectores_permitidos(), true)) {
    $seriesSector = 'starcool_cerro_prieto';
}
$labelsMap = mapa_etiquetas_series_por_sector($seriesSector);
$pageTitle = series_sector_titulo($seriesSector);
$labelsJson = json_encode($labelsMap, JSON_UNESCAPED_UNICODE);
$usuarioSeries = isset($_SESSION['usuario_ztrack']) ? (string) $_SESSION['usuario_ztrack'] : '';
$seriesAllowSetAnalysis = strtolower(trim($usuarioSeries)) === 'zgroup';
?>
<?php include 'Views/templates/navbar.php'; ?>
<div class="px-2 py-3">
    <div class="container-fluid">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
            <div>
                <h4 class="mb-0 text-uppercase">Series históricas</h4>
                <p class="text-muted small mb-0"><?php echo htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8'); ?> · IMEI <?php echo htmlspecialchars(defined('cerro_prieto_series_imei') ? cerro_prieto_series_imei : '', ENT_QUOTES, 'UTF-8'); ?></p>
            </div>
            <div class="btn-group btn-group-sm" role="group">
                <a href="<?php echo base_url; ?>SeriesSectores/index/starcool_cerro_prieto" class="btn btn-outline-warning<?php echo $seriesSector === 'starcool_cerro_prieto' ? ' active' : ''; ?>">Starcool</a>
                <a href="<?php echo base_url; ?>SeriesSectores/index/atmosfera_controlada" class="btn btn-outline-info<?php echo $seriesSector === 'atmosfera_controlada' ? ' active' : ''; ?>">Atmósfera</a>
                <a href="<?php echo base_url; ?>SeriesSectores/index/madurador" class="btn btn-outline-primary<?php echo $seriesSector === 'madurador' ? ' active' : ''; ?>">Madurador</a>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-body">
                <div class="row g-3 align-items-end">
                    <div class="col-md-4 col-lg-3">
                        <label class="form-label small text-muted mb-1">Inicio (opcional)</label>
                        <input type="datetime-local" class="form-control form-control-sm" id="seriesStart" step="1">
                    </div>
                    <div class="col-md-4 col-lg-3">
                        <label class="form-label small text-muted mb-1">Fin (opcional)</label>
                        <input type="datetime-local" class="form-control form-control-sm" id="seriesEnd" step="1">
                    </div>
                    <div class="col-md-4 col-lg-6 d-flex flex-wrap gap-2">
                        <button type="button" class="btn btn-primary btn-sm" id="seriesBtnConsultar">
                            <i class="bi bi-search me-1"></i> Consultar rango
                        </button>
                        <button type="button" class="btn btn-outline-secondary btn-sm" id="seriesBtnUltimas12">
                            <i class="bi bi-clock-history me-1"></i> Últimas 12 horas
                        </button>
                        <button type="button" class="btn btn-outline-dark btn-sm" id="seriesBtnTabla" style="display:none;">
                            <i class="bi bi-table me-1"></i> <span id="seriesBtnTablaText">Ver tabla</span>
                        </button>
                        <div class="btn-group btn-group-sm d-none" id="seriesExportToolbar" role="group" aria-label="Exportar tabla de datos">
                            <button type="button" class="btn btn-outline-success" id="seriesExportXlsx" title="Descargar Excel">
                                <i class="bi bi-file-earmark-excel me-1"></i>Excel
                            </button>
                            <button type="button" class="btn btn-outline-secondary" id="seriesExportCsv" title="Descargar CSV">
                                <i class="bi bi-filetype-csv me-1"></i>CSV
                            </button>
                            <button type="button" class="btn btn-outline-danger" id="seriesExportPdf" title="Descargar PDF">
                                <i class="bi bi-file-earmark-pdf me-1"></i>PDF
                            </button>
                        </div>
                    </div>
                </div>
                <p class="small text-muted mt-2 mb-0">Sin fechas, el servicio devuelve las últimas 12 horas. Formato enviado al API: <code>DD-MM-YYYY_HH-mm-ss</code>.</p>
            </div>
        </div>

        <div id="seriesAlert" class="alert alert-warning d-none" role="alert"></div>

        <div id="seriesLoader" class="text-center py-5 d-none">
            <div class="spinner-border text-primary" role="status"><span class="visually-hidden">Cargando…</span></div>
        </div>

        <div id="seriesStarcoolControls" class="card mb-3 d-none">
            <div class="card-header py-2">
                <span class="fw-semibold">Series en el gráfico (Starcool)</span>
                <span class="text-muted small ms-2">Relés fuera del gráfico. Eje Y izquierdo (CO₂/O₂) fijo 0–100 %; humedad eje derecho 0–100 %. Nulos empalman. SET CO1–3 / O1–3 en el gráfico.<?php if ($seriesAllowSetAnalysis): ?> Botón <strong>Procesar</strong>: estadísticas vs SET (±10 %).<?php endif; ?></span>
            </div>
            <div class="card-body py-2" id="seriesStarcoolControlsBody"></div>
        </div>

        <div id="seriesAtmosferaControls" class="card mb-3 d-none">
            <div class="card-header py-2">
                <span class="fw-semibold">Series en el gráfico (Atmósfera)</span>
                <span class="text-muted small ms-2">Temperaturas eje izquierdo (Y1); gases/humedad eje derecho (Y2) fijo 0–100 %; ventilación CFM (Y3). Power no se grafica.</span>
            </div>
            <div class="card-body py-2" id="seriesAtmosferaControlsBody"></div>
        </div>

        <div id="seriesMaduradorControls" class="card mb-3 d-none">
            <div class="card-header py-2">
                <span class="fw-semibold">Series en el gráfico (Madurador)</span>
                <span class="text-muted small ms-2">Y1 temperaturas / hora inyección; Y2 %; Y3 ventilación CFM; etileno (ppm) escala 0–300 junto a ventilación (Y4). Power no se grafica.</span>
            </div>
            <div class="card-body py-2" id="seriesMaduradorControlsBody"></div>
        </div>

        <div id="seriesChartWrap" class="card mb-3 d-none">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span class="fw-semibold">Gráfico</span>
                <small class="text-muted" id="seriesMeta"></small>
            </div>
            <div class="card-body" style="min-height: 380px;">
                <canvas id="seriesChartCanvas"></canvas>
            </div>
        </div>

        <?php if ($seriesAllowSetAnalysis): ?>
        <div id="seriesStarcoolStatsWrap" class="card mb-3 d-none border-primary">
            <div class="card-header py-2 bg-primary text-white">
                <span class="fw-semibold"><i class="bi bi-calculator me-1"></i> Análisis SET vs lecturas (Starcool)</span>
            </div>
            <div class="card-body py-3" id="seriesStarcoolStatsBody"></div>
        </div>
        <?php endif; ?>

        <div id="seriesTableWrap" class="card d-none">
            <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
                <span class="fw-semibold">Tabla de datos</span>
                <div class="btn-group btn-group-sm d-none" id="seriesExportToolbarTable" role="group" aria-label="Exportar tabla">
                    <button type="button" class="btn btn-outline-success btn-sm series-export-table" data-series-fmt="xlsx" title="Excel">
                        <i class="bi bi-file-earmark-excel"></i> Excel
                    </button>
                    <button type="button" class="btn btn-outline-secondary btn-sm series-export-table" data-series-fmt="csv" title="CSV">
                        <i class="bi bi-filetype-csv"></i> CSV
                    </button>
                    <button type="button" class="btn btn-outline-danger btn-sm series-export-table" data-series-fmt="pdf" title="PDF">
                        <i class="bi bi-file-earmark-pdf"></i> PDF
                    </button>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive" style="max-height: 480px;">
                    <table class="table table-sm table-striped table-hover mb-0" id="seriesHistoriaTable">
                        <thead class="table-light sticky-top"><tr id="seriesTableHead"></tr></thead>
                        <tbody id="seriesTableBody"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
window.SERIES_SECTOR = <?php echo json_encode($seriesSector, JSON_UNESCAPED_UNICODE); ?>;
window.SERIES_LABELS = <?php echo $labelsJson; ?>;
window.SERIES_ALLOW_SET_ANALYSIS = <?php echo $seriesAllowSetAnalysis ? 'true' : 'false'; ?>;
</script>
<?php include 'Views/templates/footer.php'; ?>
