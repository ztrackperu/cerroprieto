<?php

require_once __DIR__ . '/../Config/Funciones/series_sector_labels.php';

class SeriesSectores extends Controller
{
    public function __construct()
    {
        if (empty($_SESSION['activo_ztrack'])) {
            header('location: ' . base_url);
            exit;
        }
        parent::__construct();
    }

    public function index($param = '')
    {
        $sector = is_string($param) ? trim($param) : '';
        if ($sector === '' || !in_array($sector, series_sectores_permitidos(), true)) {
            $sector = 'starcool_cerro_prieto';
        }
        $this->views->getView($this, 'index');
    }

    /**
     * Proxy JSON: SeriesSectores/series/{sector}?start_date=&end_date=
     */
    public function series($param = '')
    {
        $sector = is_string($param) ? trim($param) : '';
        if ($sector === '' || !in_array($sector, series_sectores_permitidos(), true)) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode([
                'code' => 400,
                'message' => 'Sector no válido',
                'data' => null,
            ], JSON_UNESCAPED_UNICODE);
            die();
        }

        $start = isset($_GET['start_date']) ? trim((string) $_GET['start_date']) : '';
        $end = isset($_GET['end_date']) ? trim((string) $_GET['end_date']) : '';

        $json = $this->model->fetchCerroPrietoSeries($sector, $start, $end);
        header('Content-Type: application/json; charset=utf-8');
        echo $json;
        die();
    }
}
