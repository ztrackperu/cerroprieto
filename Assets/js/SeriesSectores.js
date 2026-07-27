/**
 * Series históricas Cerro Prieto — API vía proxy SeriesSectores/series/{sector}
 * Starcool: relés no se grafican; CO2/O2 eje Y izquierdo 0–100 %; humedad eje Y1 derecho 0–100 %.
 * Starcool: spanGaps para no cortar la línea en nulos; SET CO1–3 / O1–3 como líneas horizontales de referencia (%).
 * Atmósfera: Power no se grafica; Y1 temperaturas, Y2 %, Y3 ventilación CFM; por defecto Set Point, Suministro y Retorno.
 * Madurador: ver ver_madurador.md — Y temp (mad_2–10), Y1 % (mad_11,13–16), Y2 ppm (mad_18–19); sombreado Power (mad_1=1); tabla incluye todo mad_*.
 */
(function () {
    const sector = typeof window.SERIES_SECTOR === 'string' ? window.SERIES_SECTOR : 'starcool_cerro_prieto';
    const labelsMap = window.SERIES_LABELS && typeof window.SERIES_LABELS === 'object' ? window.SERIES_LABELS : {};
    /** Solo usuario de sesión zgroup (PHP): botón Procesar y panel de análisis SET */
    const allowStarcoolSetAnalysis =
        typeof window.SERIES_ALLOW_SET_ANALYSIS !== 'undefined' && window.SERIES_ALLOW_SET_ANALYSIS === true;
    const isStarcool = sector === 'starcool_cerro_prieto';
    const isAtmosfera = sector === 'atmosfera_controlada';
    const isMadurador = sector === 'madurador';

    /** Relés Starcool — no entran al gráfico */
    const STARCOOL_RELE_KEYS = new Set(['st_10', 'st_11', 'st_12', 'st_13', 'st_14', 'st_15']);

    /** Atmósfera: Power (ac_1) no entra al gráfico */
    const ATMOSFERA_POWER_KEY = 'ac_1';

    /** Temperaturas °C → eje y1 */
    const ATMOSFERA_TEMP_KEYS = new Set(['ac_2', 'ac_3', 'ac_4', 'ac_5', 'ac_6', 'ac_7', 'ac_8', 'ac_9', 'ac_10']);
    /** Porcentajes → eje y2 */
    const ATMOSFERA_PCT_KEYS = new Set(['ac_11', 'ac_13', 'ac_14', 'ac_15', 'ac_16']);
    /** Ventilación CFM → eje y3 */
    const ATMOSFERA_VENT_KEY = 'ac_12';

    /** Eje Y2 (gases / %): rango fijo para lectura uniforme */
    const ATMOSFERA_PCT_AXIS_MIN = 0;
    const ATMOSFERA_PCT_AXIS_MAX = 100;

    /** Por defecto visibles en el gráfico: Set Point, Suministro, Retorno */
    const ATMOSFERA_DEFAULT_ON = new Set(['ac_2', 'ac_3', 'ac_4']);

    /** Grupos UI (orden). defaultKeys: marcados al cargar */
    const ATMOSFERA_GROUPS = [
        {
            id: 'temp',
            label: 'Temperaturas (°C) — eje Y1',
            keys: ['ac_2', 'ac_3', 'ac_4', 'ac_5', 'ac_6', 'ac_7', 'ac_8', 'ac_9', 'ac_10'],
            defaultKeys: ['ac_2', 'ac_3', 'ac_4']
        },
        {
            id: 'pct',
            label: 'Porcentajes — eje Y2',
            keys: ['ac_11', 'ac_13', 'ac_14', 'ac_15', 'ac_16'],
            defaultKeys: []
        },
        {
            id: 'vent',
            label: 'Ventilación — eje Y3',
            keys: ['ac_12'],
            defaultKeys: []
        }
    ];

    /** Madurador — ver ver_madurador.md y Config/Funciones/madurador_campos.php (etiquetas vía SERIES_LABELS) */
    const MADURADOR_POWER_KEY = 'mad_1';
    const MADURADOR_ETILENO_NIVEL_KEY = 'mad_18';
    const MADURADOR_ETILENO_SET_KEY = 'mad_19';

    const MADURADOR_TEMP_KEYS = new Set([
        'mad_2', 'mad_3', 'mad_4', 'mad_5', 'mad_6', 'mad_7', 'mad_8', 'mad_9', 'mad_10'
    ]);
    const MADURADOR_PCT_KEYS = new Set(['mad_11', 'mad_13', 'mad_14', 'mad_15', 'mad_16']);
    const MADURADOR_ETILENO_PPM_KEYS = new Set([MADURADOR_ETILENO_NIVEL_KEY, MADURADOR_ETILENO_SET_KEY]);
    /** Solo tabla / export — no entran al gráfico */
    const MADURADOR_TABLE_ONLY_KEYS = new Set(['mad_12', 'mad_17', 'mad_20', 'mad_21', 'mad_22']);

    const MADURADOR_CHART_KEY_ORDER = [
        'mad_2', 'mad_3', 'mad_4', 'mad_5', 'mad_6', 'mad_7', 'mad_8', 'mad_9', 'mad_10',
        'mad_11', 'mad_13', 'mad_14', 'mad_15', 'mad_16',
        MADURADOR_ETILENO_NIVEL_KEY, MADURADOR_ETILENO_SET_KEY
    ];

    const MADURADOR_ETILENO_MIN = 0;
    const MADURADOR_ETILENO_MAX = 300;
    const MADURADOR_ETILENO_PLACEHOLDER = { mad_18: 1, mad_19: 20 };

    const MADURADOR_PCT_AXIS_MIN = 0;
    const MADURADOR_PCT_AXIS_MAX = 100;

    /** Por defecto visible: nivel de etileno (mad_18) */
    const MADURADOR_DEFAULT_ON = new Set([MADURADOR_ETILENO_NIVEL_KEY]);
    const MADURADOR_UI_SCHEMA = 2;

    const MADURADOR_GROUPS = [
        {
            id: 'temp',
            label: 'Temperaturas (°C) — eje Y',
            keys: ['mad_2', 'mad_3', 'mad_4', 'mad_5', 'mad_6', 'mad_7', 'mad_8', 'mad_9', 'mad_10'],
            defaultKeys: []
        },
        {
            id: 'pct',
            label: 'Porcentajes — eje Y1',
            keys: ['mad_11', 'mad_13', 'mad_14', 'mad_15', 'mad_16'],
            defaultKeys: []
        },
        {
            id: 'etileno',
            label: 'Etileno (ppm) — eje Y2',
            keys: [MADURADOR_ETILENO_NIVEL_KEY, MADURADOR_ETILENO_SET_KEY],
            defaultKeys: [MADURADOR_ETILENO_NIVEL_KEY]
        }
    ];

    function maduradorEsClaveGraficable(key) {
        return MADURADOR_CHART_KEY_ORDER.indexOf(key) !== -1;
    }

    function maduradorEtilenoValorSuprimido(key, v) {
        if (!MADURADOR_ETILENO_PPM_KEYS.has(key)) return false;
        if (v === null || v === undefined || v === '') return false;
        const n = Number(v);
        if (!Number.isFinite(n)) return true;
        if (n > MADURADOR_ETILENO_MAX) return true;
        if (key === MADURADOR_ETILENO_NIVEL_KEY && n === MADURADOR_ETILENO_PLACEHOLDER.mad_18) return true;
        if (key === MADURADOR_ETILENO_SET_KEY && n === MADURADOR_ETILENO_PLACEHOLDER.mad_19) return true;
        return false;
    }

    /** @type {boolean[]|null} picos mad_18 sin tendencia (vecinos inmediatos) */
    let maduradorEtilenoSpikeMask = null;
    /** @type {boolean[]|null} filas con mad_1 distinto de 0, 1 o vacío/null */
    let maduradorFilaPowerInvalidaMask = null;

    /** Power válido para vista: 0, 1 o sin dato (null / vacío). */
    function maduradorPowerValido(v) {
        if (v === null || v === undefined || v === '') return true;
        const n = Number(v);
        if (Number.isFinite(n) && (n === 0 || n === 1)) return true;
        return false;
    }

    function rebuildMaduradorInvalidRowMask(seriesObj) {
        maduradorFilaPowerInvalidaMask = null;
        if (!seriesObj || !Object.prototype.hasOwnProperty.call(seriesObj, MADURADOR_POWER_KEY)) return;
        const power = seriesObj[MADURADOR_POWER_KEY];
        if (!Array.isArray(power)) return;
        maduradorFilaPowerInvalidaMask = power.map(function (v) {
            return !maduradorPowerValido(v);
        });
    }

    function maduradorFilaSuprimida(rowIdx) {
        return (
            isMadurador &&
            maduradorFilaPowerInvalidaMask &&
            maduradorFilaPowerInvalidaMask[rowIdx] === true
        );
    }

    function maduradorNumeroSerie(v) {
        if (v === null || v === undefined || v === '') return null;
        const n = Number(v);
        return Number.isFinite(n) ? n : null;
    }

    /** Lecturas ≤ este valor (ppm) se usan como referencia de tendencia local */
    const MADURADOR_ETILENO_BASELINE_MAX = 40;
    const MADURADOR_SPIKE_WINDOW = 5;

    function maduradorBaselineVecinos(arr, i, radio) {
        const lows = [];
        for (let j = Math.max(0, i - radio); j <= Math.min(arr.length - 1, i + radio); j++) {
            if (j === i) continue;
            const v = maduradorNumeroSerie(arr[j]);
            if (v !== null && v <= MADURADOR_ETILENO_BASELINE_MAX) lows.push(v);
        }
        return lows;
    }

    /**
     * Pico en mad_18 vs tendencia local (±5 muestras). Detecta ráfagas 92.2, 92.4 entre ~15 ppm
     * aunque los vecinos inmediatos también sean altos o null.
     */
    function maduradorEtilenoPicoSinTendencia(arr, i) {
        if (!arr || i < 0 || i >= arr.length) return false;
        const c = maduradorNumeroSerie(arr[i]);
        if (c === null) return false;

        const lows = maduradorBaselineVecinos(arr, i, MADURADOR_SPIKE_WINDOW);
        if (lows.length >= 1) {
            lows.sort(function (a, b) {
                return a - b;
            });
            const baseline = lows[Math.floor(lows.length / 2)];
            if (c >= baseline + 20 && c >= baseline * 2.3) return true;
            if (c >= 42 && baseline <= MADURADOR_ETILENO_BASELINE_MAX) return true;
            return false;
        }

        if (c >= 42) {
            for (let j = Math.max(0, i - 2); j <= Math.min(arr.length - 1, i + 2); j++) {
                if (j === i) continue;
                const v = maduradorNumeroSerie(arr[j]);
                if (v !== null && v >= 40) return true;
            }
        }
        return false;
    }

    function rebuildMaduradorSpikeMask(seriesObj) {
        maduradorEtilenoSpikeMask = null;
        if (!seriesObj || !Object.prototype.hasOwnProperty.call(seriesObj, MADURADOR_ETILENO_NIVEL_KEY)) return;
        const arr = seriesObj[MADURADOR_ETILENO_NIVEL_KEY];
        if (!Array.isArray(arr)) return;

        const mask = arr.map(function (_, i) {
            return maduradorEtilenoPicoSinTendencia(arr, i);
        });

        for (let i = 0; i < arr.length; i++) {
            if (mask[i]) continue;
            const c = maduradorNumeroSerie(arr[i]);
            if (c === null || c < 40) continue;
            for (let j = Math.max(0, i - 2); j <= Math.min(arr.length - 1, i + 2); j++) {
                if (mask[j]) {
                    mask[i] = true;
                    break;
                }
            }
        }

        maduradorEtilenoSpikeMask = mask;
    }

    function maduradorCeldaSuprimida(key, v, rowIdx) {
        if (maduradorFilaSuprimida(rowIdx)) return true;
        if (isMadurador && MADURADOR_ETILENO_PPM_KEYS.has(key) && maduradorEtilenoValorSuprimido(key, v)) {
            return true;
        }
        if (
            isMadurador &&
            key === MADURADOR_ETILENO_NIVEL_KEY &&
            maduradorEtilenoSpikeMask &&
            maduradorEtilenoSpikeMask[rowIdx]
        ) {
            return true;
        }
        return false;
    }

    function yValueMaduradorChart(key, v, rowIdx) {
        if (!maduradorEsClaveGraficable(key)) return null;
        if (maduradorCeldaSuprimida(key, v, rowIdx)) return null;
        if (v === null || v === undefined || v === '') return null;
        const y = Number(v);
        return Number.isFinite(y) ? y : null;
    }

    function maduradorYAxisId(key) {
        if (MADURADOR_TEMP_KEYS.has(key)) return 'y';
        if (MADURADOR_PCT_KEYS.has(key)) return 'y1';
        if (MADURADOR_ETILENO_PPM_KEYS.has(key)) return 'y2';
        return 'y';
    }

    function maduradorSortKeys(a, b) {
        const na = parseInt(String(a).replace('mad_', ''), 10);
        const nb = parseInt(String(b).replace('mad_', ''), 10);
        return (Number.isFinite(na) ? na : 0) - (Number.isFinite(nb) ? nb : 0);
    }

    /** Claves que pueden dibujarse en el gráfico (presentes en la respuesta) */
    function maduradorGraphKeys(seriesObj) {
        return MADURADOR_CHART_KEY_ORDER.filter(function (k) {
            return Object.prototype.hasOwnProperty.call(seriesObj, k);
        });
    }

    /** Todas las mad_* de la API (incl. power y solo-tabla) para tabla/export */
    function maduradorTableKeys(seriesObj) {
        return Object.keys(seriesObj)
            .filter(function (k) {
                return /^mad_\d+$/.test(k);
            })
            .sort(maduradorSortKeys);
    }

    /** Sombreado suave cuando mad_1 (Power) = 1 */
    const maduradorPowerShadePlugin = {
        id: 'maduradorPowerShade',
        beforeDatasetsDraw: function (chart) {
            if (!isMadurador || !lastPayload || !lastPayload.data) return;
            const power = lastPayload.data.series[MADURADOR_POWER_KEY];
            const fechas = lastPayload.data.fechas;
            if (!Array.isArray(power) || !Array.isArray(fechas) || fechas.length === 0) return;
            const xScale = chart.scales.x;
            if (!xScale || !chart.chartArea) return;
            const ctx = chart.ctx;
            const top = chart.chartArea.top;
            const h = chart.chartArea.bottom - top;
            ctx.save();
            ctx.fillStyle = 'rgba(25, 135, 84, 0.07)';
            for (let i = 0; i < fechas.length; i++) {
                if (maduradorFilaSuprimida(i)) continue;
                if (Number(power[i]) !== 1) continue;
                const t0 = new Date(fechas[i]).getTime();
                let t1;
                if (i + 1 < fechas.length) {
                    t1 = new Date(fechas[i + 1]).getTime();
                } else if (i > 0) {
                    t1 = t0 + (t0 - new Date(fechas[i - 1]).getTime());
                } else {
                    t1 = t0 + 60000;
                }
                const x0 = xScale.getPixelForValue(t0);
                const x1 = xScale.getPixelForValue(t1);
                const left = Math.min(x0, x1);
                const w = Math.max(Math.abs(x1 - x0), 4);
                ctx.fillRect(left, top, w, h);
            }
            ctx.restore();
        }
    };

    /** Grupos para UI (orden visual) */
    const STARCOOL_GROUPS = [
        { id: 'co2', label: 'CO2', keys: ['st_1', 'st_2', 'st_3'], defaultGroupOn: true },
        { id: 'o2', label: 'O2', keys: ['st_4', 'st_5', 'st_6'], defaultGroupOn: true },
        { id: 'humedad', label: 'Humedad', keys: ['st_7', 'st_8', 'st_9'], defaultGroupOn: false }
    ];

    /** Eje Y izquierdo (CO₂ / O₂ y líneas SET): escala fija % */
    const STARCOOL_MAIN_AXIS_MIN = 0;
    const STARCOOL_MAIN_AXIS_MAX = 100;

    /** Referencias horizontales (%), misma clave que la serie a comparar */
    const STARCOOL_SETPOINT_DEFS = [
        { key: 'st_1', label: 'SET CO1' },
        { key: 'st_2', label: 'SET CO2' },
        { key: 'st_3', label: 'SET CO3' },
        { key: 'st_4', label: 'SET O1' },
        { key: 'st_5', label: 'SET O2' },
        { key: 'st_6', label: 'SET O3' }
    ];

    /** Valores tecleados en inputs SET (string); persiste entre recargas de datos */
    const starcoolSetValues = {};
    let starcoolSetInputDebounce = null;

    const COLORS = [
        '#0d6efd', '#198754', '#dc3545', '#fd7e14', '#6f42c1', '#20c997', '#6610f2', '#d63384',
        '#0dcaf0', '#ffc107', '#845ef7', '#51cf66', '#ff922b', '#212529'
    ];

    let chartInstance = null;
    let tableVisible = false;
    let lastPayload = null;
    /** @type {Record<string, boolean>} */
    let starcoolVisibility = {};
    /** @type {Record<string, boolean>} */
    let atmosferaVisibility = {};
    /** @type {Record<string, boolean>} */
    let maduradorVisibility = {};

    const el = {
        loader: document.getElementById('seriesLoader'),
        alert: document.getElementById('seriesAlert'),
        chartWrap: document.getElementById('seriesChartWrap'),
        canvas: document.getElementById('seriesChartCanvas'),
        meta: document.getElementById('seriesMeta'),
        tableWrap: document.getElementById('seriesTableWrap'),
        tableHead: document.getElementById('seriesTableHead'),
        tableBody: document.getElementById('seriesTableBody'),
        btnConsultar: document.getElementById('seriesBtnConsultar'),
        btnUlt12: document.getElementById('seriesBtnUltimas12'),
        btnTabla: document.getElementById('seriesBtnTabla'),
        btnTablaText: document.getElementById('seriesBtnTablaText'),
        start: document.getElementById('seriesStart'),
        end: document.getElementById('seriesEnd'),
        starcoolPanel: document.getElementById('seriesStarcoolControls'),
        starcoolBody: document.getElementById('seriesStarcoolControlsBody'),
        atmosferaPanel: document.getElementById('seriesAtmosferaControls'),
        atmosferaBody: document.getElementById('seriesAtmosferaControlsBody'),
        maduradorPanel: document.getElementById('seriesMaduradorControls'),
        maduradorBody: document.getElementById('seriesMaduradorControlsBody'),
        exportToolbar: document.getElementById('seriesExportToolbar'),
        exportToolbarTable: document.getElementById('seriesExportToolbarTable'),
        exportXlsx: document.getElementById('seriesExportXlsx'),
        exportCsv: document.getElementById('seriesExportCsv'),
        exportPdf: document.getElementById('seriesExportPdf'),
        exportJson: document.getElementById('seriesExportJson'),
        starcoolStatsWrap: document.getElementById('seriesStarcoolStatsWrap'),
        starcoolStatsBody: document.getElementById('seriesStarcoolStatsBody')
    };

    function showLoader(on) {
        if (el.loader) el.loader.classList.toggle('d-none', !on);
    }

    function showAlert(msg, kind) {
        if (!el.alert) return;
        el.alert.className = 'alert alert-' + (kind || 'warning');
        el.alert.textContent = msg;
        el.alert.classList.remove('d-none');
    }

    function hideAlert() {
        if (el.alert) el.alert.classList.add('d-none');
    }

    function localInputToApiFormat(value) {
        if (!value) return '';
        const d = new Date(value);
        if (Number.isNaN(d.getTime())) return '';
        const dd = String(d.getDate()).padStart(2, '0');
        const mm = String(d.getMonth() + 1).padStart(2, '0');
        const yyyy = d.getFullYear();
        const hh = String(d.getHours()).padStart(2, '0');
        const mi = String(d.getMinutes()).padStart(2, '0');
        const ss = String(d.getSeconds()).padStart(2, '0');
        return dd + '-' + mm + '-' + yyyy + '_' + hh + '-' + mi + '-' + ss;
    }

    function serieLabel(key) {
        const m = labelsMap[key];
        if (m && m.label) {
            return m.unit ? m.label + ' (' + m.unit + ')' : m.label;
        }
        return key;
    }

    function isStarcoolHumedadKey(key) {
        return key === 'st_7' || key === 'st_8' || key === 'st_9';
    }

    function parseStarcoolSetValue(key) {
        const raw = starcoolSetValues[key];
        if (raw === undefined || raw === null || String(raw).trim() === '') return null;
        const n = Number(String(raw).replace(',', '.'));
        return Number.isFinite(n) ? n : null;
    }

    function appendStarcoolSetpointRow(seriesObj) {
        if (!el.starcoolBody) return;
        const anyCo2o2 = STARCOOL_SETPOINT_DEFS.some(function (d) {
            return Object.prototype.hasOwnProperty.call(seriesObj, d.key);
        });
        if (!anyCo2o2) return;

        const wrap = document.createElement('div');
        wrap.className = 'col-12 mt-2';
        const inner = document.createElement('div');
        inner.className = 'border rounded p-2 bg-white';
        inner.innerHTML =
            '<p class="small fw-semibold mb-2 mb-md-1">Referencias SET (% CO₂ / O₂) — línea horizontal para comparar con las lecturas</p>' +
            '<p class="small text-muted mb-2 d-none d-md-block">Deje vacío para ocultar. Mismo color que el sensor, trazo discontinuo.</p>';

        const grid = document.createElement('div');
        grid.className = 'row g-2 align-items-end';

        STARCOOL_SETPOINT_DEFS.forEach(function (def) {
            if (!Object.prototype.hasOwnProperty.call(seriesObj, def.key)) return;
            const col = document.createElement('div');
            col.className = 'col-6 col-md-4 col-lg-2';
            const lab = document.createElement('label');
            lab.className = 'form-label small text-muted mb-0';
            lab.setAttribute('for', 'starcool-set-' + def.key);
            lab.textContent = def.label + ' (%)';
            const inp = document.createElement('input');
            inp.type = 'number';
            inp.step = 'any';
            inp.className = 'form-control form-control-sm';
            inp.id = 'starcool-set-' + def.key;
            inp.dataset.starcoolSetKey = def.key;
            inp.placeholder = '—';
            inp.value =
                starcoolSetValues[def.key] !== undefined && starcoolSetValues[def.key] !== null
                    ? String(starcoolSetValues[def.key])
                    : '';

            function applySetFromInput() {
                const v = inp.value.trim();
                if (v === '') {
                    delete starcoolSetValues[def.key];
                } else {
                    starcoolSetValues[def.key] = v;
                }
            }
            inp.addEventListener('change', function () {
                applySetFromInput();
                refreshStarcoolChart();
            });
            inp.addEventListener('input', function () {
                applySetFromInput();
                if (starcoolSetInputDebounce) clearTimeout(starcoolSetInputDebounce);
                starcoolSetInputDebounce = setTimeout(function () {
                    starcoolSetInputDebounce = null;
                    refreshStarcoolChart();
                }, 300);
            });

            col.appendChild(lab);
            col.appendChild(inp);
            grid.appendChild(col);
        });

        inner.appendChild(grid);

        if (allowStarcoolSetAnalysis) {
            const rowBtn = document.createElement('div');
            rowBtn.className = 'row g-2 mt-2';
            const colBtn = document.createElement('div');
            colBtn.className = 'col-12 d-flex flex-wrap align-items-center gap-2';
            const btnProc = document.createElement('button');
            btnProc.type = 'button';
            btnProc.className = 'btn btn-primary btn-sm';
            btnProc.textContent = 'Procesar';
            btnProc.setAttribute('aria-label', 'Procesar comparación SET vs lecturas');
            btnProc.addEventListener('click', function () {
                runStarcoolSetpointAnalysis();
            });
            const hintProc = document.createElement('small');
            hintProc.className = 'text-muted';
            hintProc.textContent =
                'Compara cada SET con su sensor (CO1↔sensor 1, …). Banda en rango: ±10 % del SET. Pulse tras cargar datos y definir SET.';
            colBtn.appendChild(btnProc);
            colBtn.appendChild(hintProc);
            rowBtn.appendChild(colBtn);
            inner.appendChild(rowBtn);
        }

        wrap.appendChild(inner);
        el.starcoolBody.appendChild(wrap);
    }

    function buildStarcoolReferenceDatasets(fechas, colorOrder) {
        if (!fechas || fechas.length === 0) return [];
        const t0 = new Date(fechas[0]);
        const t1 = new Date(fechas[fechas.length - 1]);
        if (Number.isNaN(t0.getTime()) || Number.isNaN(t1.getTime())) return [];

        const out = [];
        STARCOOL_SETPOINT_DEFS.forEach(function (def) {
            const num = parseStarcoolSetValue(def.key);
            if (num == null) return;
            const ci = colorIndexForKey(def.key, colorOrder);
            const baseColor = COLORS[ci % COLORS.length];
            out.push({
                label: def.label + ' (ref. %)',
                data: [
                    { x: t0, y: num },
                    { x: t1, y: num }
                ],
                yAxisID: 'y',
                borderColor: baseColor,
                backgroundColor: 'transparent',
                borderDash: [10, 5],
                pointRadius: 0,
                pointHoverRadius: 0,
                borderWidth: 2,
                tension: 0,
                spanGaps: true,
                order: 10
            });
        });
        return out;
    }

    function parseFechaMs(t) {
        const d = new Date(t);
        const x = d.getTime();
        return Number.isFinite(x) ? x : null;
    }

    function formatDurationMs(ms) {
        if (!Number.isFinite(ms) || ms <= 0) return '0 s';
        const sec = Math.floor(ms / 1000);
        const h = Math.floor(sec / 3600);
        const m = Math.floor((sec % 3600) / 60);
        const s = sec % 60;
        const parts = [];
        if (h > 0) parts.push(h + ' h');
        if (m > 0) parts.push(m + ' min');
        if (s > 0 || parts.length === 0) parts.push(s + ' s');
        return parts.join(' ');
    }

    function fmtStatNum(v, dec) {
        const d = dec != null ? dec : 3;
        if (v === null || v === undefined || !Number.isFinite(v)) return '—';
        return Number(v).toFixed(d);
    }

    /**
     * Estadísticas de la serie vs setpoint. Banda en rango: [set×0,9 , set×1,1].
     * Tiempo en banda: suma de (t[i+1]-t[i]) solo si ambas lecturas consecutivas están en banda.
     */
    function computeSetVsSeriesStats(fechas, arr, setpoint) {
        const low = setpoint * 0.9;
        const high = setpoint * 1.1;
        const vals = [];
        const times = [];
        const nMax = Math.min(fechas.length, arr.length);
        for (let i = 0; i < nMax; i++) {
            const raw = arr[i];
            if (raw === null || raw === undefined || raw === '') continue;
            const v = Number(raw);
            if (!Number.isFinite(v)) continue;
            const tms = parseFechaMs(fechas[i]);
            if (tms == null) continue;
            vals.push(v);
            times.push(tms);
        }
        const n = vals.length;
        if (n === 0) return null;

        let sum = 0;
        for (let j = 0; j < n; j++) sum += vals[j];
        const mean = sum / n;

        let sumSq = 0;
        for (let j = 0; j < n; j++) {
            const d = vals[j] - mean;
            sumSq += d * d;
        }
        const stdev = n > 1 ? Math.sqrt(sumSq / (n - 1)) : 0;

        let minV = vals[0];
        let maxV = vals[0];
        for (let j = 1; j < n; j++) {
            if (vals[j] < minV) minV = vals[j];
            if (vals[j] > maxV) maxV = vals[j];
        }

        let timeInBandMs = 0;
        for (let i = 0; i < n - 1; i++) {
            const v0 = vals[i];
            const v1 = vals[i + 1];
            if (v0 >= low && v0 <= high && v1 >= low && v1 <= high) {
                timeInBandMs += Math.max(0, times[i + 1] - times[i]);
            }
        }

        const totalSpanMs = Math.max(0, times[n - 1] - times[0]);
        const pctTimeInBand = totalSpanMs > 0 ? (timeInBandMs / totalSpanMs) * 100 : 0;

        let samplesInBand = 0;
        for (let j = 0; j < n; j++) {
            if (vals[j] >= low && vals[j] <= high) samplesInBand++;
        }
        const pctSamplesInBand = (samplesInBand / n) * 100;

        let sumAbs = 0;
        for (let j = 0; j < n; j++) sumAbs += Math.abs(vals[j] - setpoint);
        const meanAbsErr = sumAbs / n;
        const meanRelErrPct =
            setpoint !== 0 && Number.isFinite(setpoint) ? (meanAbsErr / Math.abs(setpoint)) * 100 : null;

        const cvPct = mean !== 0 && Number.isFinite(mean) ? (stdev / Math.abs(mean)) * 100 : null;

        return {
            n: n,
            mean: mean,
            stdev: stdev,
            minV: minV,
            maxV: maxV,
            setpoint: setpoint,
            low: low,
            high: high,
            diffMeanVsSet: mean - setpoint,
            faltaSubir: setpoint - mean,
            timeInBandMs: timeInBandMs,
            totalSpanMs: totalSpanMs,
            pctTimeInBand: pctTimeInBand,
            samplesInBand: samplesInBand,
            pctSamplesInBand: pctSamplesInBand,
            meanAbsErr: meanAbsErr,
            meanRelErrPct: meanRelErrPct,
            cvPct: cvPct
        };
    }

    function hideStarcoolStatsPanel() {
        if (el.starcoolStatsWrap) el.starcoolStatsWrap.classList.add('d-none');
        if (el.starcoolStatsBody) el.starcoolStatsBody.innerHTML = '';
    }

    function runStarcoolSetpointAnalysis() {
        if (!allowStarcoolSetAnalysis) return;
        if (!isStarcool || !lastPayload || !lastPayload.data) {
            showAlert('No hay datos cargados. Consulte un rango primero.', 'warning');
            return;
        }
        if (!el.starcoolStatsBody || !el.starcoolStatsWrap) return;

        let hasAnySet = false;
        STARCOOL_SETPOINT_DEFS.forEach(function (d) {
            if (parseStarcoolSetValue(d.key) != null) hasAnySet = true;
        });
        if (!hasAnySet) {
            showAlert('Indique al menos un SET (CO1–CO3 u O1–O3) para comparar con las lecturas.', 'warning');
            return;
        }

        const d = lastPayload.data;
        const fechas = d.fechas;
        const seriesObj = d.series;

        const thead = document.createElement('thead');
        thead.className = 'table-light';
        thead.innerHTML =
            '<tr>' +
            '<th>Serie</th>' +
            '<th class="text-end">SET (%)</th>' +
            '<th class="text-end">Banda ±10%</th>' +
            '<th class="text-end">n</th>' +
            '<th class="text-end">Promedio</th>' +
            '<th class="text-end">Δ prom. vs SET</th>' +
            '<th class="text-end">Ajuste vs SET</th>' +
            '<th class="text-end">Tiempo en banda</th>' +
            '<th class="text-end">% tiempo en banda</th>' +
            '<th class="text-end">% muestras en banda</th>' +
            '<th class="text-end">σ (desv. est.)</th>' +
            '<th class="text-end">CV %</th>' +
            '<th class="text-end">Err. abs. medio</th>' +
            '<th class="text-end">Err. rel. medio</th>' +
            '<th class="text-end">Mín</th>' +
            '<th class="text-end">Máx</th>' +
            '</tr>';

        const tbody = document.createElement('tbody');
        let rowCount = 0;

        STARCOOL_SETPOINT_DEFS.forEach(function (def) {
            const sp = parseStarcoolSetValue(def.key);
            if (sp == null) return;
            if (!Object.prototype.hasOwnProperty.call(seriesObj, def.key)) {
                const tr = document.createElement('tr');
                tr.innerHTML =
                    '<td>' +
                    serieLabel(def.key) +
                    ' <small class="text-muted">(' +
                    def.label +
                    ')</small></td>' +
                    '<td colspan="15" class="text-warning">No hay serie de datos para este canal.</td>';
                tbody.appendChild(tr);
                rowCount++;
                return;
            }
            const stats = computeSetVsSeriesStats(fechas, seriesObj[def.key], sp);
            if (!stats) {
                const tr = document.createElement('tr');
                tr.innerHTML =
                    '<td>' +
                    serieLabel(def.key) +
                    '</td>' +
                    '<td class="text-end">' +
                    fmtStatNum(sp, 3) +
                    '</td>' +
                    '<td colspan="14" class="text-muted">Sin valores numéricos en el rango.</td>';
                tbody.appendChild(tr);
                rowCount++;
                return;
            }

            const adj =
                Math.abs(stats.faltaSubir) < 1e-9
                    ? 'En el set (prom.)'
                    : stats.faltaSubir > 0
                      ? 'Falta subir ~' + fmtStatNum(stats.faltaSubir, 3) + ' % (prom.)'
                      : 'Exceso ~' + fmtStatNum(-stats.faltaSubir, 3) + ' % (prom.)';

            const tr = document.createElement('tr');
            tr.innerHTML =
                '<td>' +
                serieLabel(def.key) +
                ' <small class="text-muted">(' +
                def.label +
                ')</small></td>' +
                '<td class="text-end">' +
                fmtStatNum(stats.setpoint, 3) +
                '</td>' +
                '<td class="text-end small">' +
                fmtStatNum(stats.low, 2) +
                ' – ' +
                fmtStatNum(stats.high, 2) +
                '</td>' +
                '<td class="text-end">' +
                stats.n +
                '</td>' +
                '<td class="text-end">' +
                fmtStatNum(stats.mean, 3) +
                '</td>' +
                '<td class="text-end">' +
                (stats.diffMeanVsSet >= 0 ? '+' : '') +
                fmtStatNum(stats.diffMeanVsSet, 3) +
                '</td>' +
                '<td class="text-end small">' +
                adj +
                '</td>' +
                '<td class="text-end text-nowrap">' +
                formatDurationMs(stats.timeInBandMs) +
                '</td>' +
                '<td class="text-end">' +
                fmtStatNum(stats.pctTimeInBand, 1) +
                '</td>' +
                '<td class="text-end">' +
                fmtStatNum(stats.pctSamplesInBand, 1) +
                '</td>' +
                '<td class="text-end">' +
                fmtStatNum(stats.stdev, 4) +
                '</td>' +
                '<td class="text-end">' +
                (stats.cvPct != null ? fmtStatNum(stats.cvPct, 2) : '—') +
                '</td>' +
                '<td class="text-end">' +
                fmtStatNum(stats.meanAbsErr, 4) +
                '</td>' +
                '<td class="text-end">' +
                (stats.meanRelErrPct != null ? fmtStatNum(stats.meanRelErrPct, 2) : '—') +
                '</td>' +
                '<td class="text-end">' +
                fmtStatNum(stats.minV, 3) +
                '</td>' +
                '<td class="text-end">' +
                fmtStatNum(stats.maxV, 3) +
                '</td>';
            tbody.appendChild(tr);
            rowCount++;
        });

        el.starcoolStatsBody.innerHTML = '';
        const note = document.createElement('p');
        note.className = 'small text-muted mb-2';
        note.innerHTML =
            'Se usan todas las muestras numéricas del rango. <strong>Tiempo en banda</strong>: suma de intervalos entre muestras consecutivas donde <em>ambas</em> lecturas están entre SET×0,9 y SET×1,1. <strong>% tiempo en banda</strong> respecto al intervalo total (primera→última muestra válida).';
        el.starcoolStatsBody.appendChild(note);

        const wrapTbl = document.createElement('div');
        wrapTbl.className = 'table-responsive';
        const table = document.createElement('table');
        table.className = 'table table-sm table-bordered table-hover align-middle mb-0';
        table.appendChild(thead);
        table.appendChild(tbody);
        wrapTbl.appendChild(table);
        el.starcoolStatsBody.appendChild(wrapTbl);

        if (rowCount > 0) {
            el.starcoolStatsWrap.classList.remove('d-none');
            el.starcoolStatsWrap.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }
    }

    function atmosferaYAxisId(key) {
        if (ATMOSFERA_TEMP_KEYS.has(key)) return 'y1';
        if (ATMOSFERA_PCT_KEYS.has(key)) return 'y2';
        if (key === ATMOSFERA_VENT_KEY) return 'y3';
        return 'y1';
    }

    function maduradorSeriesKeys(seriesObj) {
        return maduradorGraphKeys(seriesObj);
    }

    /** Orden fijo para colores (sin Power) */
    const ATMOSFERA_CHART_KEY_ORDER = [
        'ac_2',
        'ac_3',
        'ac_4',
        'ac_5',
        'ac_6',
        'ac_7',
        'ac_8',
        'ac_9',
        'ac_10',
        'ac_11',
        'ac_13',
        'ac_14',
        'ac_15',
        'ac_16',
        'ac_12'
    ];

    function tieneDatosUtiles(payload) {
        const d = payload && payload.data;
        if (!d) return false;
        const f = d.fechas;
        if (!Array.isArray(f) || f.length === 0) return false;
        const s = d.series;
        if (!s || typeof s !== 'object') return false;
        return Object.keys(s).length > 0;
    }

    function buildQuery(startApi, endApi) {
        const p = new URLSearchParams();
        if (startApi) p.set('start_date', startApi);
        if (endApi) p.set('end_date', endApi);
        const q = p.toString();
        return q ? '?' + q : '';
    }

    async function fetchSeries(startApi, endApi) {
        const url = base_url + 'SeriesSectores/series/' + encodeURIComponent(sector) + buildQuery(startApi, endApi);
        const res = await fetch(url, { method: 'GET', headers: { Accept: 'application/json' } });
        if (!res.ok) throw new Error('HTTP ' + res.status);
        return res.json();
    }

    function destroyChart() {
        if (chartInstance) {
            chartInstance.destroy();
            chartInstance = null;
        }
    }

    const MESES_CORTO = ['ene', 'feb', 'mar', 'abr', 'may', 'jun', 'jul', 'ago', 'sep', 'oct', 'nov', 'dic'];

    /** Valor numérico (ms) del tick anterior en el array que entrega Chart.js */
    function tickValueAt(ticks, index) {
        if (!ticks || index < 1) return null;
        const t = ticks[index - 1];
        if (t == null) return null;
        if (typeof t === 'object' && t.value != null) return t.value;
        return t;
    }

    /**
     * Eje tiempo: solo hora; en el primer tick tras cambio de día natural (respecto al tick anterior), día + mes (ej. 23 mar).
     */
    function timeAxisTickLabel(value, index, ticks) {
        const d = new Date(value);
        if (Number.isNaN(d.getTime())) return '';
        const prevMs = tickValueAt(ticks, index);
        const prev = prevMs != null ? new Date(prevMs) : null;
        const dayChanged =
            prev &&
            !Number.isNaN(prev.getTime()) &&
            (d.getFullYear() !== prev.getFullYear() ||
                d.getMonth() !== prev.getMonth() ||
                d.getDate() !== prev.getDate());
        if (dayChanged) {
            return d.getDate() + ' ' + MESES_CORTO[d.getMonth()];
        }
        return d.toLocaleTimeString('es-MX', { hour: 'numeric', minute: '2-digit', hour12: true });
    }

    function timeScaleXAxis() {
        return {
            type: 'time',
            title: { display: true, text: 'Tiempo' },
            ticks: {
                maxRotation: 45,
                autoSkip: true,
                callback: function (value, index, ticks) {
                    return timeAxisTickLabel(value, index, ticks);
                }
            }
        };
    }

    function initStarcoolVisibility(seriesObj) {
        starcoolVisibility = {};
        Object.keys(seriesObj)
            .sort()
            .forEach(function (k) {
                if (STARCOOL_RELE_KEYS.has(k)) return;
                let def = false;
                STARCOOL_GROUPS.forEach(function (g) {
                    if (g.keys.indexOf(k) !== -1) def = g.defaultGroupOn;
                });
                starcoolVisibility[k] = def;
            });
    }

    function renderStarcoolControls(seriesObj) {
        if (!isStarcool || !el.starcoolPanel || !el.starcoolBody) return;
        el.starcoolBody.innerHTML = '';
        const keysPresent = Object.keys(seriesObj).filter(function (k) {
            return !STARCOOL_RELE_KEYS.has(k);
        });
        if (keysPresent.length === 0) {
            el.starcoolPanel.classList.add('d-none');
            return;
        }
        el.starcoolPanel.classList.remove('d-none');

        const row = document.createElement('div');
        row.className = 'row g-3';

        STARCOOL_GROUPS.forEach(function (group) {
            const keysInData = group.keys.filter(function (k) {
                return Object.prototype.hasOwnProperty.call(seriesObj, k);
            });
            if (keysInData.length === 0) return;

            const col = document.createElement('div');
            col.className = 'col-md-4';
            const card = document.createElement('div');
            card.className = 'border rounded p-2 h-100 bg-light';

            const masterId = 'starcool-grp-' + group.id;
            const allOn = keysInData.every(function (k) {
                return starcoolVisibility[k];
            });
            const master = document.createElement('div');
            master.className = 'form-check fw-semibold mb-2';
            master.innerHTML =
                '<input class="form-check-input" type="checkbox" id="' +
                masterId +
                '" ' +
                (allOn ? 'checked' : '') +
                '> <label class="form-check-label" for="' +
                masterId +
                '">' +
                group.label +
                ' (grupo)</label>';
            card.appendChild(master);

            keysInData.forEach(function (key) {
                const cid = 'starcool-serie-' + key;
                const wrap = document.createElement('div');
                wrap.className = 'form-check form-check-sm ms-2 mb-1';
                const cb = document.createElement('input');
                cb.type = 'checkbox';
                cb.className = 'form-check-input';
                cb.id = cid;
                cb.dataset.serieKey = key;
                cb.checked = !!starcoolVisibility[key];
                const lab = document.createElement('label');
                lab.className = 'form-check-label small';
                lab.setAttribute('for', cid);
                lab.textContent = serieLabel(key);
                wrap.appendChild(cb);
                wrap.appendChild(lab);
                card.appendChild(wrap);

                cb.addEventListener('change', function () {
                    starcoolVisibility[key] = cb.checked;
                    syncStarcoolGroupMaster(group.id, keysInData);
                    refreshStarcoolChart();
                });
            });

            const masterInput = master.querySelector('input');
            masterInput.addEventListener('change', function () {
                const on = masterInput.checked;
                keysInData.forEach(function (k) {
                    starcoolVisibility[k] = on;
                    const c = el.starcoolBody.querySelector('input[data-serie-key="' + k + '"]');
                    if (c) c.checked = on;
                });
                refreshStarcoolChart();
            });

            col.appendChild(card);
            row.appendChild(col);
            syncStarcoolGroupMaster(group.id, keysInData);
        });

        el.starcoolBody.appendChild(row);
        appendStarcoolSetpointRow(seriesObj);
    }

    function syncStarcoolGroupMaster(groupId, keysInData) {
        const master = el.starcoolBody && el.starcoolBody.querySelector('#starcool-grp-' + groupId);
        if (!master) return;
        const allOn = keysInData.every(function (k) {
            return starcoolVisibility[k];
        });
        const noneOn = keysInData.every(function (k) {
            return !starcoolVisibility[k];
        });
        master.checked = allOn;
        master.indeterminate = !allOn && !noneOn;
    }

    function getStarcoolChartKeys(seriesObj) {
        return Object.keys(seriesObj)
            .sort()
            .filter(function (k) {
                return !STARCOOL_RELE_KEYS.has(k) && starcoolVisibility[k];
            });
    }

    function colorIndexForKey(key, orderedKeys) {
        const idx = orderedKeys.indexOf(key);
        return idx >= 0 ? idx : 0;
    }

    function initAtmosferaVisibility(seriesObj) {
        atmosferaVisibility = {};
        Object.keys(seriesObj)
            .sort()
            .forEach(function (k) {
                if (k === ATMOSFERA_POWER_KEY) return;
                atmosferaVisibility[k] = ATMOSFERA_DEFAULT_ON.has(k);
            });
    }

    function getAtmosferaChartKeys(seriesObj) {
        return Object.keys(seriesObj)
            .sort()
            .filter(function (k) {
                return k !== ATMOSFERA_POWER_KEY && atmosferaVisibility[k];
            });
    }

    function syncAtmosferaGroupMaster(groupId, keysInData) {
        const master = el.atmosferaBody && el.atmosferaBody.querySelector('#atmosfera-grp-' + groupId);
        if (!master) return;
        const allOn = keysInData.every(function (k) {
            return atmosferaVisibility[k];
        });
        const noneOn = keysInData.every(function (k) {
            return !atmosferaVisibility[k];
        });
        master.checked = allOn;
        master.indeterminate = !allOn && !noneOn;
    }

    function renderAtmosferaControls(seriesObj) {
        if (!isAtmosfera || !el.atmosferaPanel || !el.atmosferaBody) return;
        el.atmosferaBody.innerHTML = '';
        const keysPresent = Object.keys(seriesObj).filter(function (k) {
            return k !== ATMOSFERA_POWER_KEY;
        });
        if (keysPresent.length === 0) {
            el.atmosferaPanel.classList.add('d-none');
            return;
        }
        el.atmosferaPanel.classList.remove('d-none');

        const row = document.createElement('div');
        row.className = 'row g-3';

        ATMOSFERA_GROUPS.forEach(function (group) {
            const keysInData = group.keys.filter(function (k) {
                return Object.prototype.hasOwnProperty.call(seriesObj, k);
            });
            if (keysInData.length === 0) return;

            const col = document.createElement('div');
            col.className = 'col-md-4';
            const card = document.createElement('div');
            card.className = 'border rounded p-2 h-100 bg-light';

            const masterId = 'atmosfera-grp-' + group.id;
            const allOn = keysInData.every(function (k) {
                return atmosferaVisibility[k];
            });
            const master = document.createElement('div');
            master.className = 'form-check fw-semibold mb-2';
            master.innerHTML =
                '<input class="form-check-input" type="checkbox" id="' +
                masterId +
                '" ' +
                (allOn ? 'checked' : '') +
                '> <label class="form-check-label" for="' +
                masterId +
                '">' +
                group.label +
                ' (grupo)</label>';
            card.appendChild(master);

            keysInData.forEach(function (key) {
                const cid = 'atmosfera-serie-' + key;
                const wrap = document.createElement('div');
                wrap.className = 'form-check form-check-sm ms-2 mb-1';
                const cb = document.createElement('input');
                cb.type = 'checkbox';
                cb.className = 'form-check-input';
                cb.id = cid;
                cb.dataset.serieKey = key;
                cb.checked = !!atmosferaVisibility[key];
                const lab = document.createElement('label');
                lab.className = 'form-check-label small';
                lab.setAttribute('for', cid);
                lab.textContent = serieLabel(key);
                wrap.appendChild(cb);
                wrap.appendChild(lab);
                card.appendChild(wrap);

                cb.addEventListener('change', function () {
                    atmosferaVisibility[key] = cb.checked;
                    syncAtmosferaGroupMaster(group.id, keysInData);
                    refreshAtmosferaChart();
                });
            });

            const masterInput = master.querySelector('input');
            masterInput.addEventListener('change', function () {
                const on = masterInput.checked;
                keysInData.forEach(function (k) {
                    atmosferaVisibility[k] = on;
                    const c = el.atmosferaBody.querySelector('input[data-serie-key="' + k + '"]');
                    if (c) c.checked = on;
                });
                refreshAtmosferaChart();
            });

            col.appendChild(card);
            row.appendChild(col);
            syncAtmosferaGroupMaster(group.id, keysInData);
        });

        el.atmosferaBody.appendChild(row);
    }

    function refreshAtmosferaChart() {
        if (!lastPayload || !isAtmosfera) return;
        renderChart(lastPayload);
    }

    function initMaduradorVisibility(seriesObj) {
        maduradorVisibility = {};
        maduradorGraphKeys(seriesObj).forEach(function (k) {
            let on = MADURADOR_DEFAULT_ON.has(k);
            MADURADOR_GROUPS.forEach(function (g) {
                if (g.defaultKeys && g.defaultKeys.indexOf(k) !== -1) {
                    on = true;
                }
            });
            maduradorVisibility[k] = on;
        });
    }

    function getMaduradorChartKeys(seriesObj) {
        return maduradorSeriesKeys(seriesObj).filter(function (k) {
            return maduradorVisibility[k];
        });
    }

    function syncMaduradorGroupMaster(groupId, keysInData) {
        const master = el.maduradorBody && el.maduradorBody.querySelector('#madurador-grp-' + groupId);
        if (!master) return;
        const allOn = keysInData.every(function (k) {
            return maduradorVisibility[k];
        });
        const noneOn = keysInData.every(function (k) {
            return !maduradorVisibility[k];
        });
        master.checked = allOn;
        master.indeterminate = !allOn && !noneOn;
    }

    function renderMaduradorControls(seriesObj) {
        if (!isMadurador || !el.maduradorPanel || !el.maduradorBody) return;
        el.maduradorBody.innerHTML = '';
        const keysPresent = maduradorSeriesKeys(seriesObj);
        if (keysPresent.length === 0) {
            el.maduradorPanel.classList.add('d-none');
            return;
        }
        el.maduradorPanel.classList.remove('d-none');

        const row = document.createElement('div');
        row.className = 'row g-3';

        MADURADOR_GROUPS.forEach(function (group) {
            const keysInData = group.keys.filter(function (k) {
                return Object.prototype.hasOwnProperty.call(seriesObj, k);
            });
            if (keysInData.length === 0) return;

            const col = document.createElement('div');
            col.className = 'col-lg-4 col-md-6';
            const card = document.createElement('div');
            card.className = 'border rounded p-2 h-100 bg-light';

            const masterId = 'madurador-grp-' + group.id;
            const allOn = keysInData.every(function (k) {
                return maduradorVisibility[k];
            });
            const master = document.createElement('div');
            master.className = 'form-check fw-semibold mb-2';
            master.innerHTML =
                '<input class="form-check-input" type="checkbox" id="' +
                masterId +
                '" ' +
                (allOn ? 'checked' : '') +
                '> <label class="form-check-label" for="' +
                masterId +
                '">' +
                group.label +
                ' (grupo)</label>';
            card.appendChild(master);

            keysInData.forEach(function (key) {
                const cid = 'madurador-serie-' + key;
                const wrap = document.createElement('div');
                wrap.className = 'form-check form-check-sm ms-2 mb-1';
                const cb = document.createElement('input');
                cb.type = 'checkbox';
                cb.className = 'form-check-input';
                cb.id = cid;
                cb.dataset.serieKey = key;
                cb.checked = !!maduradorVisibility[key];
                const lab = document.createElement('label');
                lab.className = 'form-check-label small';
                lab.setAttribute('for', cid);
                lab.textContent = serieLabel(key);
                wrap.appendChild(cb);
                wrap.appendChild(lab);
                card.appendChild(wrap);

                cb.addEventListener('change', function () {
                    maduradorVisibility[key] = cb.checked;
                    syncMaduradorGroupMaster(group.id, keysInData);
                    refreshMaduradorChart();
                });
            });

            const masterInput = master.querySelector('input');
            masterInput.addEventListener('change', function () {
                const on = masterInput.checked;
                keysInData.forEach(function (k) {
                    maduradorVisibility[k] = on;
                    const c = el.maduradorBody.querySelector('input[data-serie-key="' + k + '"]');
                    if (c) c.checked = on;
                });
                refreshMaduradorChart();
            });

            col.appendChild(card);
            row.appendChild(col);
            syncMaduradorGroupMaster(group.id, keysInData);
        });

        el.maduradorBody.appendChild(row);
    }

    function refreshMaduradorChart() {
        if (!lastPayload || !isMadurador) return;
        renderChart(lastPayload);
    }

    function refreshStarcoolChart() {
        if (!lastPayload || !isStarcool) return;
        renderChart(lastPayload);
    }

    function renderChart(payload) {
        if (!el.canvas || typeof Chart === 'undefined') return;
        destroyChart();
        const d = payload.data;
        const fechas = d.fechas;
        const seriesObj = d.series;

        if (isStarcool) {
            const chartKeys = getStarcoolChartKeys(seriesObj);
            const colorOrder = Object.keys(seriesObj)
                .sort()
                .filter(function (k) {
                    return !STARCOOL_RELE_KEYS.has(k);
                });
            const hasHumedadVisible = chartKeys.some(isStarcoolHumedadKey);
            const refDatasets = buildStarcoolReferenceDatasets(fechas, colorOrder);

            if (chartKeys.length === 0 && refDatasets.length === 0) {
                const ctx = el.canvas.getContext('2d');
                chartInstance = new Chart(ctx, {
                    type: 'line',
                    data: { datasets: [] },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            title: {
                                display: true,
                                text: 'Marque al menos una serie en el panel superior o indique un SET CO/O2'
                            }
                        }
                    }
                });
                return;
            }

            const datasets = chartKeys.map(function (key) {
                const arr = seriesObj[key];
                const pts = fechas.map(function (t, i) {
                    const v = arr[i];
                    const y = v === null || v === undefined || v === '' ? null : Number(v);
                    return {
                        x: new Date(t),
                        y: Number.isFinite(y) ? y : null
                    };
                });
                const ci = colorIndexForKey(key, colorOrder);
                return {
                    label: serieLabel(key),
                    data: pts,
                    yAxisID: isStarcoolHumedadKey(key) ? 'y1' : 'y',
                    borderColor: COLORS[ci % COLORS.length],
                    backgroundColor: 'transparent',
                    tension: 0.15,
                    spanGaps: true,
                    pointRadius: fechas.length > 80 ? 0 : 2,
                    borderWidth: 1.5,
                    order: 0
                };
            });
            refDatasets.forEach(function (ds) {
                datasets.push(ds);
            });

            const scales = {
                x: timeScaleXAxis(),
                y: {
                    type: 'linear',
                    position: 'left',
                    min: STARCOOL_MAIN_AXIS_MIN,
                    max: STARCOOL_MAIN_AXIS_MAX,
                    title: { display: true, text: 'CO2 / O2 (%)' },
                    grid: { drawOnChartArea: true }
                }
            };
            if (hasHumedadVisible) {
                scales.y1 = {
                    type: 'linear',
                    position: 'right',
                    min: 0,
                    max: 100,
                    title: { display: true, text: 'Humedad (%)' },
                    grid: { drawOnChartArea: false }
                };
            }

            const ctx = el.canvas.getContext('2d');
            chartInstance = new Chart(ctx, {
                type: 'line',
                data: { datasets: datasets },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: { mode: 'index', intersect: false },
                    plugins: {
                        legend: { position: 'bottom', labels: { boxWidth: 12, font: { size: 10 } } },
                        tooltip: {
                            callbacks: {
                                title: function (items) {
                                    if (!items.length) return '';
                                    const x = items[0].parsed.x;
                                    return x != null ? new Date(x).toLocaleString() : '';
                                }
                            }
                        }
                    },
                    scales: scales
                }
            });
            return;
        }

        if (isAtmosfera) {
            const chartKeys = getAtmosferaChartKeys(seriesObj);
            const colorOrder = ATMOSFERA_CHART_KEY_ORDER.filter(function (k) {
                return Object.prototype.hasOwnProperty.call(seriesObj, k) && k !== ATMOSFERA_POWER_KEY;
            });
            const hasY1 = chartKeys.some(function (k) {
                return atmosferaYAxisId(k) === 'y1';
            });
            const hasY2 = chartKeys.some(function (k) {
                return atmosferaYAxisId(k) === 'y2';
            });
            const hasY3 = chartKeys.some(function (k) {
                return atmosferaYAxisId(k) === 'y3';
            });

            if (chartKeys.length === 0) {
                const ctx = el.canvas.getContext('2d');
                chartInstance = new Chart(ctx, {
                    type: 'line',
                    data: { datasets: [] },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            title: {
                                display: true,
                                text: 'Marque al menos una serie en el panel superior'
                            }
                        }
                    }
                });
                return;
            }

            const datasets = chartKeys.map(function (key) {
                const arr = seriesObj[key];
                const pts = fechas.map(function (t, i) {
                    const v = arr[i];
                    const y = v === null || v === undefined || v === '' ? null : Number(v);
                    return {
                        x: new Date(t),
                        y: Number.isFinite(y) ? y : null
                    };
                });
                const ci = colorIndexForKey(key, colorOrder);
                return {
                    label: serieLabel(key),
                    data: pts,
                    yAxisID: atmosferaYAxisId(key),
                    borderColor: COLORS[ci % COLORS.length],
                    backgroundColor: 'transparent',
                    tension: 0.15,
                    spanGaps: false,
                    pointRadius: fechas.length > 80 ? 0 : 2,
                    borderWidth: 1.5
                };
            });

            const scales = {
                x: timeScaleXAxis()
            };
            if (hasY1) {
                scales.y1 = {
                    type: 'linear',
                    position: 'left',
                    title: { display: true, text: 'Temperatura (°C)' },
                    grid: { drawOnChartArea: true }
                };
            }
            if (hasY2) {
                scales.y2 = {
                    type: 'linear',
                    position: 'right',
                    min: ATMOSFERA_PCT_AXIS_MIN,
                    max: ATMOSFERA_PCT_AXIS_MAX,
                    title: { display: true, text: 'Gases / humedad (%)' },
                    grid: { drawOnChartArea: false }
                };
            }
            if (hasY3) {
                scales.y3 = {
                    type: 'linear',
                    position: 'right',
                    title: { display: true, text: 'Ventilación (CFM)' },
                    grid: { drawOnChartArea: false }
                };
            }

            const ctx = el.canvas.getContext('2d');
            chartInstance = new Chart(ctx, {
                type: 'line',
                data: { datasets: datasets },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: { mode: 'index', intersect: false },
                    plugins: {
                        legend: { position: 'bottom', labels: { boxWidth: 12, font: { size: 10 } } },
                        tooltip: {
                            callbacks: {
                                title: function (items) {
                                    if (!items.length) return '';
                                    const x = items[0].parsed.x;
                                    return x != null ? new Date(x).toLocaleString() : '';
                                }
                            }
                        }
                    },
                    scales: scales
                }
            });
            return;
        }

        if (isMadurador) {
            const chartKeys = getMaduradorChartKeys(seriesObj);
            const colorOrder = MADURADOR_CHART_KEY_ORDER.filter(function (k) {
                return Object.prototype.hasOwnProperty.call(seriesObj, k);
            });
            const hasTemp = chartKeys.some(function (k) {
                return maduradorYAxisId(k) === 'y';
            });
            const hasPct = chartKeys.some(function (k) {
                return maduradorYAxisId(k) === 'y1';
            });
            const hasPpm = chartKeys.some(function (k) {
                return maduradorYAxisId(k) === 'y2';
            });

            if (chartKeys.length === 0) {
                const ctx = el.canvas.getContext('2d');
                chartInstance = new Chart(ctx, {
                    type: 'line',
                    data: { datasets: [] },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            title: {
                                display: true,
                                text: 'Marque al menos una serie en el panel superior (por defecto: Nivel de etileno)'
                            }
                        }
                    }
                });
                return;
            }

            const datasets = chartKeys.map(function (key) {
                const arr = seriesObj[key];
                const pts = fechas.map(function (t, i) {
                    const y = yValueMaduradorChart(key, arr[i], i);
                    return { x: new Date(t), y: y };
                });
                const ci = colorIndexForKey(key, colorOrder);
                const isSetPpm = key === MADURADOR_ETILENO_SET_KEY;
                return {
                    label: serieLabel(key),
                    data: pts,
                    yAxisID: maduradorYAxisId(key),
                    madKey: key,
                    borderColor: COLORS[ci % COLORS.length],
                    backgroundColor: 'transparent',
                    tension: 0.15,
                    spanGaps: true,
                    pointRadius: fechas.length > 80 ? 0 : 2,
                    borderWidth: isSetPpm ? 2 : 1.5,
                    borderDash: isSetPpm ? [6, 4] : undefined
                };
            });

            let maxPpmVisible = 0;
            chartKeys.forEach(function (key) {
                if (!MADURADOR_ETILENO_PPM_KEYS.has(key)) return;
                const arr = seriesObj[key];
                for (let i = 0; i < fechas.length; i++) {
                    const y = yValueMaduradorChart(key, arr[i], i);
                    if (y != null && y > maxPpmVisible) maxPpmVisible = y;
                }
            });

            const scales = { x: timeScaleXAxis() };
            if (hasTemp) {
                scales.y = {
                    type: 'linear',
                    position: 'left',
                    title: { display: true, text: 'Temperatura (°C)' },
                    grid: { drawOnChartArea: true }
                };
            }
            if (hasPct) {
                scales.y1 = {
                    type: 'linear',
                    position: 'right',
                    min: MADURADOR_PCT_AXIS_MIN,
                    max: MADURADOR_PCT_AXIS_MAX,
                    title: { display: true, text: '%' },
                    grid: { drawOnChartArea: false }
                };
            }
            if (hasPpm) {
                const y2Max =
                    maxPpmVisible > 0
                        ? Math.min(MADURADOR_ETILENO_MAX, Math.max(25, Math.ceil(maxPpmVisible * 1.35)))
                        : MADURADOR_ETILENO_MAX;
                scales.y2 = {
                    type: 'linear',
                    position: 'right',
                    min: MADURADOR_ETILENO_MIN,
                    max: y2Max,
                    title: { display: true, text: 'Etileno (ppm)' },
                    grid: { drawOnChartArea: false }
                };
            }

            const chartOpts = {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                plugins: {
                    legend: { position: 'bottom', labels: { boxWidth: 12, font: { size: 10 } } },
                    tooltip: {
                        callbacks: {
                            title: function (items) {
                                if (!items.length) return '';
                                const x = items[0].parsed.x;
                                return x != null ? new Date(x).toLocaleString() : '';
                            },
                            label: function (item) {
                                const val = item.parsed.y;
                                if (val == null || Number.isNaN(val)) return item.dataset.label + ': —';
                                const ds = item.chart.data.datasets[item.datasetIndex];
                                const key = ds && ds.madKey ? ds.madKey : null;
                                if (key && MADURADOR_ETILENO_PPM_KEYS.has(key)) {
                                    return item.dataset.label + ': ' + val + ' ppm';
                                }
                                if (key && MADURADOR_PCT_KEYS.has(key)) {
                                    return item.dataset.label + ': ' + val + ' %';
                                }
                                if (key && MADURADOR_TEMP_KEYS.has(key)) {
                                    return item.dataset.label + ': ' + val + ' °C';
                                }
                                return item.dataset.label + ': ' + val;
                            }
                        }
                    }
                },
                scales: scales
            };
            if (hasPct && hasPpm) {
                chartOpts.layout = { padding: { right: 8 } };
            }

            const ctx = el.canvas.getContext('2d');
            chartInstance = new Chart(ctx, {
                type: 'line',
                data: { datasets: datasets },
                options: chartOpts,
                plugins: [maduradorPowerShadePlugin]
            });
            return;
        }

        /* Otros sectores: comportamiento anterior (una sola Y) */
        const keys = Object.keys(seriesObj).sort();
        const datasets = keys.map(function (key, idx) {
            const arr = seriesObj[key];
            const pts = fechas.map(function (t, i) {
                const v = arr[i];
                const y = v === null || v === undefined || v === '' ? null : Number(v);
                return {
                    x: new Date(t),
                    y: Number.isFinite(y) ? y : null
                };
            });
            return {
                label: serieLabel(key),
                data: pts,
                borderColor: COLORS[idx % COLORS.length],
                backgroundColor: 'transparent',
                tension: 0.15,
                spanGaps: false,
                pointRadius: fechas.length > 80 ? 0 : 2,
                borderWidth: 1.5
            };
        });

        const ctx2 = el.canvas.getContext('2d');
        chartInstance = new Chart(ctx2, {
            type: 'line',
            data: { datasets: datasets },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                plugins: {
                    legend: { position: 'bottom', labels: { boxWidth: 12, font: { size: 10 } } },
                    tooltip: {
                        callbacks: {
                            title: function (items) {
                                if (!items.length) return '';
                                const x = items[0].parsed.x;
                                return x != null ? new Date(x).toLocaleString() : '';
                            }
                        }
                    }
                },
                scales: {
                    x: timeScaleXAxis(),
                    y: {
                        title: { display: true, text: 'Valor' }
                    }
                }
            }
        });
    }

    function sanitizeSeriesFilename(base) {
        const s = String(base || 'series').replace(/[^a-zA-Z0-9_-]/g, '_');
        return s || 'series';
    }

    function seriesExportTimestamp() {
        const d = new Date();
        const pad = function (n) {
            return String(n).padStart(2, '0');
        };
        return (
            d.getFullYear() +
            '-' +
            pad(d.getMonth() + 1) +
            '-' +
            pad(d.getDate()) +
            '_' +
            pad(d.getHours()) +
            pad(d.getMinutes()) +
            pad(d.getSeconds())
        );
    }

    /**
     * Misma estructura que la tabla en pantalla (valores crudos; vacío si null).
     * @returns {{ headers: string[], rows: string[][] } | null}
     */
    function buildSeriesTableMatrix() {
        if (!lastPayload || !lastPayload.data) return null;
        const d = lastPayload.data;
        const fechas = d.fechas;
        const seriesObj = d.series;
        if (!Array.isArray(fechas) || fechas.length === 0) return null;
        const keys = isMadurador ? maduradorTableKeys(seriesObj) : Object.keys(seriesObj).sort();
        if (keys.length === 0) return null;
        const headers = ['Fecha / hora'].concat(
            keys.map(function (k) {
                return serieLabel(k);
            })
        );
        const rows = fechas
            .map(function (t, rowIdx) {
                if (isMadurador && maduradorFilaSuprimida(rowIdx)) return null;
                const row = [String(t)];
                keys.forEach(function (key) {
                    const v = seriesObj[key][rowIdx];
                    if (isMadurador && maduradorCeldaSuprimida(key, v, rowIdx)) {
                        row.push('_');
                    } else {
                        row.push(v === null || v === undefined ? '' : String(v));
                    }
                });
                return row;
            })
            .filter(function (row) {
                return row !== null;
            });
        return { headers: headers, rows: rows };
    }

    function exportSeriesTableCsv(matrix, fileBase, ts) {
        const lines = [matrix.headers].concat(matrix.rows);
        const csv = lines
            .map(function (row) {
                return row
                    .map(function (cell) {
                        const s = String(cell != null ? cell : '');
                        if (/[",\n\r]/.test(s)) return '"' + s.replace(/"/g, '""') + '"';
                        return s;
                    })
                    .join(',');
            })
            .join('\r\n');
        const blob = new Blob(['\ufeff' + csv], { type: 'text/csv;charset=utf-8;' });
        const a = document.createElement('a');
        a.href = URL.createObjectURL(blob);
        a.download = sanitizeSeriesFilename(fileBase) + '_' + ts + '.csv';
        a.click();
        setTimeout(function () {
            URL.revokeObjectURL(a.href);
        }, 0);
    }

    function exportSeriesTableXlsx(matrix, fileBase, ts) {
        if (typeof XLSX === 'undefined') {
            showAlert('No se cargó la librería Excel. Recargue la página.', 'warning');
            return;
        }
        const aoa = [matrix.headers].concat(matrix.rows);
        const ws = XLSX.utils.aoa_to_sheet(aoa);
        const wb = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(wb, ws, 'Series');
        XLSX.writeFile(wb, sanitizeSeriesFilename(fileBase) + '_' + ts + '.xlsx');
    }

    function exportSeriesTablePdf(matrix, fileBase, ts) {
        const jspdf = window.jspdf;
        if (!jspdf || typeof jspdf.jsPDF !== 'function') {
            showAlert('No se cargó la librería PDF. Recargue la página.', 'warning');
            return;
        }
        const Doc = jspdf.jsPDF;
        const doc = new Doc({ orientation: 'landscape', unit: 'mm', format: 'a4' });
        const title = 'Series históricas — ' + sector;
        doc.setFontSize(11);
        doc.text(title, 14, 12);
        const sub =
            lastPayload && lastPayload.data && lastPayload.data.puntos != null
                ? lastPayload.data.puntos + ' puntos'
                : '';
        if (sub) {
            doc.setFontSize(9);
            doc.setTextColor(100);
            doc.text(sub, 14, 17);
            doc.setTextColor(0);
        }
        if (typeof doc.autoTable === 'function') {
            doc.autoTable({
                head: [matrix.headers],
                body: matrix.rows,
                startY: sub ? 20 : 16,
                styles: { fontSize: 6, cellPadding: 1 },
                headStyles: { fillColor: [13, 110, 253] },
                margin: { left: 10, right: 10 }
            });
        } else {
            doc.setFontSize(8);
            let y = 25;
            matrix.rows.forEach(function (r) {
                doc.text(r.join(' | '), 10, y);
                y += 4;
            });
        }
        doc.save(sanitizeSeriesFilename(fileBase) + '_' + ts + '.pdf');
    }

    function buildSeriesExportPayload() {
        if (!lastPayload || !lastPayload.data) return null;
        const d = lastPayload.data;
        const fechas = d.fechas;
        const seriesObj = d.series;
        if (!Array.isArray(fechas) || fechas.length === 0 || !seriesObj) return null;

        const keys = isMadurador ? maduradorTableKeys(seriesObj) : Object.keys(seriesObj).sort();
        const seriesRaw = {};
        const seriesProcessed = {};
        const labels = {};

        keys.forEach(function (key) {
            labels[key] = serieLabel(key);
            const rawArr = seriesObj[key];
            seriesRaw[key] = rawArr;
            seriesProcessed[key] = fechas.map(function (_, rowIdx) {
                const v = rawArr[rowIdx];
                if (isMadurador && maduradorCeldaSuprimida(key, v, rowIdx)) return null;
                if (v === null || v === undefined || v === '') return null;
                const n = Number(v);
                return Number.isFinite(n) ? n : v;
            });
        });

        const payload = {
            sector: sector,
            imei: d.imei || null,
            timezone_datos: d.timezone_datos || null,
            start_date: d.start_date || null,
            end_date: d.end_date || null,
            puntos: fechas.length,
            fechas: fechas,
            labels: labels,
            series_raw: seriesRaw,
            series_processed: seriesProcessed
        };

        if (isMadurador) {
            payload.madurador_procesamiento = {
                etileno_nivel_key: MADURADOR_ETILENO_NIVEL_KEY,
                power_key: MADURADOR_POWER_KEY,
                power_valido: '0, 1 o null/vacío',
                placeholders_ppm: MADURADOR_ETILENO_PLACEHOLDER,
                filtro_picos_vecinos: true,
                picos_mad_18_indices: maduradorEtilenoSpikeMask
                    ? maduradorEtilenoSpikeMask
                          .map(function (flag, i) {
                              return flag ? i : -1;
                          })
                          .filter(function (i) {
                              return i >= 0;
                          })
                    : [],
                filas_power_invalido_indices: maduradorFilaPowerInvalidaMask
                    ? maduradorFilaPowerInvalidaMask
                          .map(function (flag, i) {
                              return flag ? i : -1;
                          })
                          .filter(function (i) {
                              return i >= 0;
                          })
                    : []
            };
        }
        return payload;
    }

    function exportSeriesJson() {
        const payload = buildSeriesExportPayload();
        if (!payload) {
            showAlert('No hay datos para exportar en JSON.', 'warning');
            return;
        }
        const ts = seriesExportTimestamp();
        const json = JSON.stringify(payload, null, 2);
        const blob = new Blob([json], { type: 'application/json;charset=utf-8;' });
        const a = document.createElement('a');
        a.href = URL.createObjectURL(blob);
        a.download = sanitizeSeriesFilename(sector) + '_' + ts + '.json';
        a.click();
        setTimeout(function () {
            URL.revokeObjectURL(a.href);
        }, 0);
    }

    function exportSeriesTable(fmt) {
        const matrix = buildSeriesTableMatrix();
        if (!matrix) {
            showAlert('No hay datos en la tabla para exportar.', 'warning');
            return;
        }
        const fileBase = sector;
        const ts = seriesExportTimestamp();
        const f = (fmt || '').toLowerCase();
        if (f === 'csv') {
            exportSeriesTableCsv(matrix, fileBase, ts);
        } else if (f === 'xlsx') {
            exportSeriesTableXlsx(matrix, fileBase, ts);
        } else if (f === 'pdf') {
            exportSeriesTablePdf(matrix, fileBase, ts);
        }
    }

    function setSeriesExportToolbarsVisible(on) {
        if (el.exportToolbar) el.exportToolbar.classList.toggle('d-none', !on);
        if (el.exportToolbarTable) el.exportToolbarTable.classList.toggle('d-none', !on);
    }

    function renderTable(payload) {
        if (!el.tableHead || !el.tableBody) return;
        const d = payload.data;
        const fechas = d.fechas;
        const seriesObj = d.series;
        const keys = isMadurador ? maduradorTableKeys(seriesObj) : Object.keys(seriesObj).sort();

        el.tableHead.innerHTML = '';
        const th0 = document.createElement('th');
        th0.textContent = 'Fecha / hora';
        th0.className = 'text-nowrap';
        el.tableHead.appendChild(th0);
        keys.forEach(function (key) {
            const th = document.createElement('th');
            th.textContent = serieLabel(key);
            th.className = 'text-nowrap small';
            if (isStarcool && STARCOOL_RELE_KEYS.has(key)) {
                th.classList.add('text-secondary');
            }
            if (isAtmosfera && key === ATMOSFERA_POWER_KEY) {
                th.classList.add('text-secondary');
            }
            el.tableHead.appendChild(th);
        });

        el.tableBody.innerHTML = '';
        fechas.forEach(function (t, rowIdx) {
            if (isMadurador && maduradorFilaSuprimida(rowIdx)) return;
            const tr = document.createElement('tr');
            const td0 = document.createElement('td');
            td0.textContent = t;
            td0.className = 'text-nowrap small';
            tr.appendChild(td0);
            keys.forEach(function (key) {
                const td = document.createElement('td');
                const v = seriesObj[key][rowIdx];
                if (isMadurador && maduradorCeldaSuprimida(key, v, rowIdx)) {
                    td.textContent = '_';
                } else {
                    td.textContent = v === null || v === undefined ? '—' : String(v);
                }
                tr.appendChild(td);
            });
            el.tableBody.appendChild(tr);
        });
    }

    function setMeta(payload) {
        if (!el.meta) return;
        const d = payload.data;
        const parts = [];
        if (d.timezone_datos) parts.push(d.timezone_datos);
        if (d.puntos != null) parts.push(d.puntos + ' puntos');
        if (d.start_date && d.end_date) parts.push(d.start_date + ' → ' + d.end_date);
        el.meta.textContent = parts.join(' · ');
    }

    function applySuccess(payload) {
        lastPayload = payload;
        hideAlert();
        setMeta(payload);

        if (isStarcool) {
            initStarcoolVisibility(payload.data.series);
            renderStarcoolControls(payload.data.series);
            if (el.atmosferaPanel) el.atmosferaPanel.classList.add('d-none');
            if (el.maduradorPanel) el.maduradorPanel.classList.add('d-none');
        } else if (isAtmosfera) {
            initAtmosferaVisibility(payload.data.series);
            renderAtmosferaControls(payload.data.series);
            if (el.starcoolPanel) el.starcoolPanel.classList.add('d-none');
            if (el.maduradorPanel) el.maduradorPanel.classList.add('d-none');
        } else if (isMadurador) {
            rebuildMaduradorInvalidRowMask(payload.data.series);
            rebuildMaduradorSpikeMask(payload.data.series);
            initMaduradorVisibility(payload.data.series);
            renderMaduradorControls(payload.data.series);
            if (el.starcoolPanel) el.starcoolPanel.classList.add('d-none');
            if (el.atmosferaPanel) el.atmosferaPanel.classList.add('d-none');
            const madKeys = Object.keys(payload.data.series).filter(function (k) {
                return /^mad_\d+$/.test(k);
            });
            console.info(
                '[Madurador] schema v' +
                    MADURADOR_UI_SCHEMA +
                    ' · series API: ' +
                    madKeys.length +
                    ' · graficables: ' +
                    maduradorGraphKeys(payload.data.series).length +
                    ' (etileno mad_18/mad_19)'
            );
        } else {
            if (el.starcoolPanel) el.starcoolPanel.classList.add('d-none');
            if (el.atmosferaPanel) el.atmosferaPanel.classList.add('d-none');
            if (el.maduradorPanel) el.maduradorPanel.classList.add('d-none');
        }

        renderChart(payload);
        renderTable(payload);
        if (isStarcool && allowStarcoolSetAnalysis) hideStarcoolStatsPanel();
        if (el.chartWrap) el.chartWrap.classList.remove('d-none');
        setSeriesExportToolbarsVisible(true);
        if (el.btnTabla) {
            el.btnTabla.style.display = '';
            if (!tableVisible && el.tableWrap) el.tableWrap.classList.add('d-none');
        }
    }

    function applyEmpty(message, usedRange) {
        destroyChart();
        lastPayload = null;
        maduradorEtilenoSpikeMask = null;
        if (el.starcoolPanel) el.starcoolPanel.classList.add('d-none');
        if (el.atmosferaPanel) el.atmosferaPanel.classList.add('d-none');
        if (el.maduradorPanel) el.maduradorPanel.classList.add('d-none');
        if (el.chartWrap) el.chartWrap.classList.add('d-none');
        if (el.tableWrap) el.tableWrap.classList.add('d-none');
        if (el.btnTabla) el.btnTabla.style.display = 'none';
        if (allowStarcoolSetAnalysis) hideStarcoolStatsPanel();
        setSeriesExportToolbarsVisible(false);
        if (el.tableBody) el.tableBody.innerHTML = '';
        if (el.tableHead) el.tableHead.innerHTML = '';
        if (el.meta) el.meta.textContent = '';

        let msg =
            message ||
            'No hay datos en el rango solicitado' +
                (usedRange ? '' : ' (por defecto: últimas 12 horas)') +
                '.';
        showAlert(msg + ' Utilice el calendario de inicio y fin y pulse «Consultar rango».', 'warning');
    }

    async function runLoad(startApi, endApi) {
        showLoader(true);
        hideAlert();
        try {
            const payload = await fetchSeries(startApi, endApi);
            const usedRange = !!(startApi || endApi);
            if (tieneDatosUtiles(payload)) {
                applySuccess(payload);
            } else {
                const msg = payload && payload.message ? String(payload.message) : '';
                applyEmpty(msg, usedRange);
            }
        } catch (e) {
            console.error(e);
            applyEmpty('Error de red al consultar series.', !!(startApi || endApi));
        } finally {
            showLoader(false);
        }
    }

    function onConsultar() {
        const s = el.start && el.start.value ? localInputToApiFormat(el.start.value) : '';
        const e = el.end && el.end.value ? localInputToApiFormat(el.end.value) : '';
        if ((s && !e) || (!s && e)) {
            showAlert('Indique ambas fechas (inicio y fin) o ninguna para usar las últimas 12 horas.', 'danger');
            return;
        }
        runLoad(s, e);
    }

    function onUlt12() {
        if (el.start) el.start.value = '';
        if (el.end) el.end.value = '';
        runLoad('', '');
    }

    function onToggleTabla() {
        tableVisible = !tableVisible;
        if (el.tableWrap) el.tableWrap.classList.toggle('d-none', !tableVisible);
        if (el.btnTablaText) {
            el.btnTablaText.textContent = tableVisible ? 'Ocultar tabla' : 'Ver tabla';
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        if (el.btnConsultar) el.btnConsultar.addEventListener('click', onConsultar);
        if (el.btnUlt12) el.btnUlt12.addEventListener('click', onUlt12);
        if (el.btnTabla) el.btnTabla.addEventListener('click', onToggleTabla);
        if (el.exportXlsx) el.exportXlsx.addEventListener('click', function () { exportSeriesTable('xlsx'); });
        if (el.exportCsv) el.exportCsv.addEventListener('click', function () { exportSeriesTable('csv'); });
        if (el.exportPdf) el.exportPdf.addEventListener('click', function () { exportSeriesTable('pdf'); });
        if (el.exportJson) el.exportJson.addEventListener('click', exportSeriesJson);
        document.addEventListener('click', function (e) {
            const btnJson = e.target.closest('.series-export-json');
            if (btnJson) {
                exportSeriesJson();
                return;
            }
            const btn = e.target.closest('.series-export-table');
            if (!btn) return;
            const fmt = btn.getAttribute('data-series-fmt');
            if (fmt) exportSeriesTable(fmt);
        });
        onUlt12();
    });
})();
