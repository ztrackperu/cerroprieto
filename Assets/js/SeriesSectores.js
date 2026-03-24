/**
 * Series históricas Cerro Prieto — API vía proxy SeriesSectores/series/{sector}
 * Starcool: relés no se grafican; grupos CO2 / O2 / humedad con toggles; humedad en eje Y1 (0–100).
 * Atmósfera: Power no se grafica; Y1 temperaturas, Y2 %, Y3 ventilación CFM; por defecto Set Point, Suministro y Retorno.
 * Madurador: igual; Y4 etileno (ppm) 0–300; hora inyección en Y1; Power no se grafica.
 */
(function () {
    const sector = typeof window.SERIES_SECTOR === 'string' ? window.SERIES_SECTOR : 'starcool_cerro_prieto';
    const labelsMap = window.SERIES_LABELS && typeof window.SERIES_LABELS === 'object' ? window.SERIES_LABELS : {};
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

    /** Madurador: Power (mad_1) no entra al gráfico */
    const MADURADOR_POWER_KEY = 'mad_1';

    const MADURADOR_TEMP_KEYS = new Set(['mad_2', 'mad_3', 'mad_4', 'mad_5', 'mad_6', 'mad_7', 'mad_8', 'mad_9', 'mad_10']);
    const MADURADOR_PCT_KEYS = new Set(['mad_11', 'mad_13', 'mad_14', 'mad_15', 'mad_16']);
    const MADURADOR_VENT_KEY = 'mad_12';
    const MADURADOR_ETILENO_KEY = 'mad_18';
    /** Hora inyección comparte eje Y1 con temperaturas */
    const MADURADOR_HORA_INYECCION_KEY = 'mad_17';

    const MADURADOR_ETILENO_MIN = 0;
    const MADURADOR_ETILENO_MAX = 300;

    const MADURADOR_DEFAULT_ON = new Set(['mad_2', 'mad_3', 'mad_4']);

    const MADURADOR_GROUPS = [
        {
            id: 'temp',
            label: 'Temperaturas / hora inyección — eje Y1',
            keys: ['mad_2', 'mad_3', 'mad_4', 'mad_5', 'mad_6', 'mad_7', 'mad_8', 'mad_9', 'mad_10', 'mad_17'],
            defaultKeys: ['mad_2', 'mad_3', 'mad_4']
        },
        {
            id: 'pct',
            label: 'Porcentajes — eje Y2',
            keys: ['mad_11', 'mad_13', 'mad_14', 'mad_15', 'mad_16'],
            defaultKeys: []
        },
        {
            id: 'vent',
            label: 'Ventilación (Y3) + etileno (Y4, 0–300 ppm)',
            keys: ['mad_12', 'mad_18'],
            defaultKeys: []
        }
    ];

    /** Orden fijo para colores (sin Power) */
    const MADURADOR_CHART_KEY_ORDER = [
        'mad_2',
        'mad_3',
        'mad_4',
        'mad_5',
        'mad_6',
        'mad_7',
        'mad_8',
        'mad_9',
        'mad_10',
        'mad_17',
        'mad_11',
        'mad_13',
        'mad_14',
        'mad_15',
        'mad_16',
        'mad_12',
        'mad_18'
    ];

    /** Grupos para UI (orden visual) */
    const STARCOOL_GROUPS = [
        { id: 'co2', label: 'CO2', keys: ['st_1', 'st_2', 'st_3'], defaultGroupOn: true },
        { id: 'o2', label: 'O2', keys: ['st_4', 'st_5', 'st_6'], defaultGroupOn: true },
        { id: 'humedad', label: 'Humedad', keys: ['st_7', 'st_8', 'st_9'], defaultGroupOn: false }
    ];

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
        maduradorBody: document.getElementById('seriesMaduradorControlsBody')
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

    function atmosferaYAxisId(key) {
        if (ATMOSFERA_TEMP_KEYS.has(key)) return 'y1';
        if (ATMOSFERA_PCT_KEYS.has(key)) return 'y2';
        if (key === ATMOSFERA_VENT_KEY) return 'y3';
        return 'y1';
    }

    function maduradorYAxisId(key) {
        if (MADURADOR_TEMP_KEYS.has(key) || key === MADURADOR_HORA_INYECCION_KEY) return 'y1';
        if (MADURADOR_PCT_KEYS.has(key)) return 'y2';
        if (key === MADURADOR_VENT_KEY) return 'y3';
        if (key === MADURADOR_ETILENO_KEY) return 'y4';
        return 'y1';
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
        Object.keys(seriesObj)
            .sort()
            .forEach(function (k) {
                if (k === MADURADOR_POWER_KEY) return;
                maduradorVisibility[k] = MADURADOR_DEFAULT_ON.has(k);
            });
    }

    function getMaduradorChartKeys(seriesObj) {
        return Object.keys(seriesObj)
            .sort()
            .filter(function (k) {
                return k !== MADURADOR_POWER_KEY && maduradorVisibility[k];
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
        const keysPresent = Object.keys(seriesObj).filter(function (k) {
            return k !== MADURADOR_POWER_KEY;
        });
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
            col.className = 'col-md-4';
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
                    yAxisID: isStarcoolHumedadKey(key) ? 'y1' : 'y',
                    borderColor: COLORS[ci % COLORS.length],
                    backgroundColor: 'transparent',
                    tension: 0.15,
                    spanGaps: false,
                    pointRadius: fechas.length > 80 ? 0 : 2,
                    borderWidth: 1.5
                };
            });

            const scales = {
                x: timeScaleXAxis(),
                y: {
                    type: 'linear',
                    position: 'left',
                    title: { display: true, text: 'CO2 / O2 / otros (%)' },
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
                    title: { display: true, text: '%' },
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
                return Object.prototype.hasOwnProperty.call(seriesObj, k) && k !== MADURADOR_POWER_KEY;
            });
            const hasY1 = chartKeys.some(function (k) {
                return maduradorYAxisId(k) === 'y1';
            });
            const hasY2 = chartKeys.some(function (k) {
                return maduradorYAxisId(k) === 'y2';
            });
            const hasY3 = chartKeys.some(function (k) {
                return maduradorYAxisId(k) === 'y3';
            });
            const hasY4 = chartKeys.some(function (k) {
                return maduradorYAxisId(k) === 'y4';
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
                    yAxisID: maduradorYAxisId(key),
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
                const y1Title =
                    chartKeys.indexOf(MADURADOR_HORA_INYECCION_KEY) !== -1
                        ? 'Temperatura (°C) / hora inyección (h)'
                        : 'Temperatura (°C)';
                scales.y1 = {
                    type: 'linear',
                    position: 'left',
                    title: { display: true, text: y1Title },
                    grid: { drawOnChartArea: true }
                };
            }
            if (hasY2) {
                scales.y2 = {
                    type: 'linear',
                    position: 'right',
                    title: { display: true, text: '%' },
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
            if (hasY4) {
                scales.y4 = {
                    type: 'linear',
                    position: 'right',
                    min: MADURADOR_ETILENO_MIN,
                    max: MADURADOR_ETILENO_MAX,
                    title: { display: true, text: 'Etileno (ppm) 0–300' },
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
                            }
                        }
                    }
                },
                scales: scales
            };
            if (hasY3 && hasY4) {
                chartOpts.layout = { padding: { right: 8 } };
            }

            const ctx = el.canvas.getContext('2d');
            chartInstance = new Chart(ctx, {
                type: 'line',
                data: { datasets: datasets },
                options: chartOpts
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

    function renderTable(payload) {
        if (!el.tableHead || !el.tableBody) return;
        const d = payload.data;
        const fechas = d.fechas;
        const seriesObj = d.series;
        const keys = Object.keys(seriesObj).sort();

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
            if (isMadurador && key === MADURADOR_POWER_KEY) {
                th.classList.add('text-secondary');
            }
            el.tableHead.appendChild(th);
        });

        el.tableBody.innerHTML = '';
        fechas.forEach(function (t, rowIdx) {
            const tr = document.createElement('tr');
            const td0 = document.createElement('td');
            td0.textContent = t;
            td0.className = 'text-nowrap small';
            tr.appendChild(td0);
            keys.forEach(function (key) {
                const td = document.createElement('td');
                const v = seriesObj[key][rowIdx];
                td.textContent = v === null || v === undefined ? '—' : String(v);
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
            initMaduradorVisibility(payload.data.series);
            renderMaduradorControls(payload.data.series);
            if (el.starcoolPanel) el.starcoolPanel.classList.add('d-none');
            if (el.atmosferaPanel) el.atmosferaPanel.classList.add('d-none');
        } else {
            if (el.starcoolPanel) el.starcoolPanel.classList.add('d-none');
            if (el.atmosferaPanel) el.atmosferaPanel.classList.add('d-none');
            if (el.maduradorPanel) el.maduradorPanel.classList.add('d-none');
        }

        renderChart(payload);
        renderTable(payload);
        if (el.chartWrap) el.chartWrap.classList.remove('d-none');
        if (el.btnTabla) {
            el.btnTabla.style.display = '';
            if (!tableVisible && el.tableWrap) el.tableWrap.classList.add('d-none');
        }
    }

    function applyEmpty(message, usedRange) {
        destroyChart();
        lastPayload = null;
        if (el.starcoolPanel) el.starcoolPanel.classList.add('d-none');
        if (el.atmosferaPanel) el.atmosferaPanel.classList.add('d-none');
        if (el.maduradorPanel) el.maduradorPanel.classList.add('d-none');
        if (el.chartWrap) el.chartWrap.classList.add('d-none');
        if (el.tableWrap) el.tableWrap.classList.add('d-none');
        if (el.btnTabla) el.btnTabla.style.display = 'none';
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
        onUlt12();
    });
})();
