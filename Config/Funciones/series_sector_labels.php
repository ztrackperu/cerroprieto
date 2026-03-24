<?php

/**
 * Etiquetas alineadas con AdminPage (generales.php: Starcool, Atmósfera, Madurador PLUS).
 *
 * @return array<string, array{label:string, unit:string}>
 */
function mapa_etiquetas_series_por_sector(string $sector): array
{
    $starcool = [
        'st_1' => ['label' => 'CO2 Sensor 1', 'unit' => '%'],
        'st_2' => ['label' => 'CO2 Sensor 2', 'unit' => '%'],
        'st_3' => ['label' => 'CO2 Sensor 3', 'unit' => '%'],
        'st_4' => ['label' => 'O2 Sensor 1', 'unit' => '%'],
        'st_5' => ['label' => 'O2 Sensor 2', 'unit' => '%'],
        'st_6' => ['label' => 'O2 Sensor 3', 'unit' => '%'],
        'st_7' => ['label' => 'Humedad Sensor 1', 'unit' => '%'],
        'st_8' => ['label' => 'Humedad Sensor 2', 'unit' => '%'],
        'st_9' => ['label' => 'Humedad Sensor 3', 'unit' => '%'],
        'st_10' => ['label' => 'Rele 1', 'unit' => ''],
        'st_11' => ['label' => 'Rele 2', 'unit' => ''],
        'st_12' => ['label' => 'Rele 3', 'unit' => ''],
        'st_13' => ['label' => 'Rele 4', 'unit' => ''],
        'st_14' => ['label' => 'Rele 5', 'unit' => ''],
        'st_15' => ['label' => 'Rele 6', 'unit' => ''],
    ];

    $atmosfera = [
        'ac_1' => ['label' => 'Power', 'unit' => ''],
        'ac_2' => ['label' => 'Set Point', 'unit' => 'C°'],
        'ac_3' => ['label' => 'Suministro', 'unit' => 'C°'],
        'ac_4' => ['label' => 'Retorno', 'unit' => 'C°'],
        'ac_5' => ['label' => 'Evaporador', 'unit' => 'C°'],
        'ac_6' => ['label' => 'Condensador', 'unit' => 'C°'],
        'ac_7' => ['label' => 'Sensor 1', 'unit' => 'C°'],
        'ac_8' => ['label' => 'Sensor 2', 'unit' => 'C°'],
        'ac_9' => ['label' => 'Sensor 3', 'unit' => 'C°'],
        'ac_10' => ['label' => 'Sensor 4', 'unit' => 'C°'],
        'ac_11' => ['label' => 'Humedad', 'unit' => '%'],
        'ac_12' => ['label' => 'Ventilacion', 'unit' => 'CFM'],
        'ac_13' => ['label' => 'CO2 Sensor', 'unit' => '%'],
        'ac_14' => ['label' => 'O2 Sensor', 'unit' => '%'],
        'ac_15' => ['label' => 'SP Humedad', 'unit' => '%'],
        'ac_16' => ['label' => 'SP CO2', 'unit' => '%'],
    ];

    $madurador = [
        'mad_1' => ['label' => 'POWER', 'unit' => ''],
        'mad_2' => ['label' => 'Setpoint', 'unit' => 'C°'],
        'mad_3' => ['label' => 'Suministro', 'unit' => 'C°'],
        'mad_4' => ['label' => 'Retorno', 'unit' => 'C°'],
        'mad_5' => ['label' => 'Evaporador', 'unit' => 'C°'],
        'mad_6' => ['label' => 'Condensador', 'unit' => 'C°'],
        'mad_7' => ['label' => 'Sensor 1', 'unit' => 'C°'],
        'mad_8' => ['label' => 'Sensor 2', 'unit' => 'C°'],
        'mad_9' => ['label' => 'Sensor 3', 'unit' => 'C°'],
        'mad_10' => ['label' => 'Sensor 4', 'unit' => 'C°'],
        'mad_11' => ['label' => 'Humedad', 'unit' => '%'],
        'mad_12' => ['label' => 'Ventilación', 'unit' => 'CFM'],
        'mad_13' => ['label' => 'CO2', 'unit' => '%'],
        'mad_14' => ['label' => 'O2', 'unit' => '%'],
        'mad_15' => ['label' => 'Humedad SP', 'unit' => '%'],
        'mad_16' => ['label' => 'CO2 SP', 'unit' => '%'],
        'mad_17' => ['label' => 'Hora inyección', 'unit' => 'h'],
        'mad_18' => ['label' => 'PPM (etileno)', 'unit' => 'ppm'],
    ];

    switch ($sector) {
        case 'starcool_cerro_prieto':
            return $starcool;
        case 'atmosfera_controlada':
            return $atmosfera;
        case 'madurador':
            return $madurador;
        default:
            return [];
    }
}

function series_sectores_permitidos(): array
{
    return ['starcool_cerro_prieto', 'atmosfera_controlada', 'madurador'];
}

function series_sector_titulo(string $sector): string
{
    $t = [
        'starcool_cerro_prieto' => 'Starcool Cerro Prieto',
        'atmosfera_controlada' => 'Atmósfera controlada',
        'madurador' => 'Sistema Madurador',
    ];
    return $t[$sector] ?? $sector;
}
