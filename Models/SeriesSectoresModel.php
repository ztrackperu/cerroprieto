<?php

class SeriesSectoresModel extends Query
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * GET TermoKing/CerroPrietoSeries/{imei}/{sector}?start_date=&end_date=
     * Formato fechas opcional: DD-MM-YYYY_HH-mm-ss
     */
    public function fetchCerroPrietoSeries(string $sector, string $startDate = '', string $endDate = ''): string
    {
        if (!defined('url_nueva') || url_nueva === '') {
            return json_encode([
                'code' => 503,
                'message' => 'URL_NUEVA no configurada',
                'data' => null,
            ], JSON_UNESCAPED_UNICODE);
        }

        $imei = defined('cerro_prieto_series_imei') ? cerro_prieto_series_imei : '860389053949943';
        $base = rtrim(url_nueva, '/');
        $url = $base . '/TermoKing/CerroPrietoSeries/' . rawurlencode((string) $imei) . '/' . rawurlencode($sector);

        $qs = [];
        if ($startDate !== '') {
            $qs[] = 'start_date=' . rawurlencode($startDate);
        }
        if ($endDate !== '') {
            $qs[] = 'end_date=' . rawurlencode($endDate);
        }
        if ($qs !== []) {
            $url .= '?' . implode('&', $qs);
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 12);
        curl_setopt($ch, CURLOPT_TIMEOUT, 45);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Accept: application/json']);

        $body = curl_exec($ch);
        $errno = curl_errno($ch);
        $err = curl_error($ch);
        $code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($errno !== 0) {
            return json_encode([
                'code' => 502,
                'message' => 'Error al consultar series: ' . $err,
                'data' => null,
            ], JSON_UNESCAPED_UNICODE);
        }

        if ($body === false || $body === '') {
            return json_encode([
                'code' => $code ?: 502,
                'message' => 'Respuesta vacía del servicio de series',
                'data' => null,
            ], JSON_UNESCAPED_UNICODE);
        }

        $decoded = json_decode($body, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return json_encode([
                'code' => 502,
                'message' => 'JSON inválido del servicio de series',
                'data' => null,
            ], JSON_UNESCAPED_UNICODE);
        }

        return json_encode($decoded, JSON_UNESCAPED_UNICODE);
    }
}
