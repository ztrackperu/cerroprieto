<?php

/**
 * Definición canónica mad_1..mad_22 (ver ver_madurador.md).
 *
 * @return array<string, array{label: string, unit: string, tipo: string, icon: string}>
 */
function mapa_campos_madurador_completo(): array
{
    return [
        'mad_1' => ['label' => 'Power', 'unit' => '', 'tipo' => 'power', 'icon' => 'bi-power'],
        'mad_2' => ['label' => 'Setpoint temperatura', 'unit' => 'C°', 'tipo' => 'setpoint', 'icon' => 'bi-sliders'],
        'mad_3' => ['label' => 'Sensor Suministro', 'unit' => 'C°', 'tipo' => 'temp', 'icon' => 'bi-wind'],
        'mad_4' => ['label' => 'Sensor Retorno', 'unit' => 'C°', 'tipo' => 'temp', 'icon' => 'bi-arrow-return-left'],
        'mad_5' => ['label' => 'Sensor Evaporador', 'unit' => 'C°', 'tipo' => 'temp', 'icon' => 'bi-cloud-haze2'],
        'mad_6' => ['label' => 'Sensor Condensador', 'unit' => 'C°', 'tipo' => 'temp', 'icon' => 'bi-thermometer-half'],
        'mad_7' => ['label' => 'Sensor USDA 1', 'unit' => 'C°', 'tipo' => 'temp', 'icon' => 'bi-thermometer-low'],
        'mad_8' => ['label' => 'Sensor USDA 2', 'unit' => 'C°', 'tipo' => 'temp', 'icon' => 'bi-thermometer-low'],
        'mad_9' => ['label' => 'Sensor USDA 3', 'unit' => 'C°', 'tipo' => 'temp', 'icon' => 'bi-thermometer-low'],
        'mad_10' => ['label' => 'Sensor USDA 4', 'unit' => 'C°', 'tipo' => 'temp', 'icon' => 'bi-thermometer-low'],
        'mad_11' => ['label' => 'Nivel de humedad', 'unit' => '%', 'tipo' => 'porcentaje', 'icon' => 'bi-droplet'],
        'mad_12' => ['label' => 'Nivel de ventilación', 'unit' => 'CFM', 'tipo' => 'cfm', 'icon' => 'bi-fan'],
        'mad_13' => ['label' => 'Nivel de CO2', 'unit' => '%', 'tipo' => 'porcentaje', 'icon' => 'bi-cloud'],
        'mad_14' => ['label' => 'Nivel de O2', 'unit' => '%', 'tipo' => 'porcentaje', 'icon' => 'bi-circle-half'],
        'mad_15' => ['label' => 'Set de humedad', 'unit' => '%', 'tipo' => 'porcentaje', 'icon' => 'bi-moisture'],
        'mad_16' => ['label' => 'Set de CO2', 'unit' => '%', 'tipo' => 'porcentaje', 'icon' => 'bi-percent'],
        'mad_17' => ['label' => 'Horas programadas maduración', 'unit' => 'h', 'tipo' => 'horas', 'icon' => 'bi-clock-history'],
        'mad_18' => ['label' => 'Nivel de etileno', 'unit' => 'ppm', 'tipo' => 'ppm', 'icon' => 'bi-activity'],
        'mad_19' => ['label' => 'Setpoint etileno', 'unit' => 'ppm', 'tipo' => 'ppm', 'icon' => 'bi-bullseye'],
        'mad_20' => ['label' => 'Horas restantes maduración', 'unit' => 'h', 'tipo' => 'horas', 'icon' => 'bi-hourglass-split'],
        'mad_21' => ['label' => 'Minutos restantes maduración', 'unit' => 'min', 'tipo' => 'minutos', 'icon' => 'bi-stopwatch'],
        'mad_22' => ['label' => 'Segundos restantes maduración', 'unit' => 's', 'tipo' => 'segundos', 'icon' => 'bi-stopwatch-fill'],
    ];
}

/**
 * Etiquetas para gráficos (SeriesSectores).
 *
 * @return array<string, array{label: string, unit: string}>
 */
function mapa_etiquetas_madurador_series(): array
{
    $out = [];
    foreach (mapa_campos_madurador_completo() as $key => $def) {
        $out[$key] = ['label' => $def['label'], 'unit' => $def['unit']];
    }
    return $out;
}
