let modalComando = 0;
let sp_temp_esquema = document.getElementById('sp_temp_esquema');
let sp_co2_esquema = document.getElementById('sp_co2_esquema');
let sp_humidity_esquema = document.getElementById('sp_humidity_esquema');
let sp_o2_esquema = document.getElementById('sp_o2_esquema');

/** Sector activo en el flujo modal (sufijo id, ej. "14872" o "demo") */
let controlActiveSid = null;

let spTemperatureInput = {};
let spCO2Input = {};
let spHumidityInput = {};
let spO2Input = {};
let elpower_state = {};
let elavl = {};

function alertas(msg, icono) {
    Swal.fire({
        position: 'top-end',
        icon: icono,
        title: msg,
        showConfirmButton: false,
        timer: 3000
    });
}

let contenidoControl = document.getElementById('contenidoControl');

function parseSpTempPrev(val) {
    if (val === 'NA' || val === '' || val === undefined) return 0;
    const n = parseFloat(String(val).replace('+', ''));
    return Number.isFinite(n) ? n : 0;
}

async function procesarTmp(sid) {
    controlActiveSid = sid;
    const valor_anterior = parseSpTempPrev($('#tmp_SP_a_' + sid).val());
    const valor_cambiar = $('#tmp_SP_' + sid).val();
    const trama = valor_anterior + '|' + valor_cambiar + '|3';
    const response = await fetch(base_url + 'Control/ProcesarModal/' + trama, { method: 'GET' });
    const result = await response.json();
    sp_temp_esquema.innerHTML = result.data;
    $('#procesarTMP').modal('show');
}

function closeProcesarTmp() {
    const sid = controlActiveSid;
    if (!sid) return;
    $('#btnProcesarTMP_' + sid + ' .closeTMP').attr('hidden', true);
    $('#btnProcesarTMP_' + sid + ' .procesarTMP').attr('hidden', true);
    $('#tmp_SP_' + sid).val(spTemperatureInput[sid]);
}

async function procesarCO2(sid) {
    controlActiveSid = sid;
    const valor_anterior = parseInt($('#co2_SP_a_' + sid).val(), 10) || 0;
    const valor_cambiar = $('#co2_SP_' + sid).val();
    const trama = valor_anterior + '|' + valor_cambiar + '|2';
    const response = await fetch(base_url + 'Control/ProcesarModal/' + trama, { method: 'GET' });
    const result = await response.json();
    sp_co2_esquema.innerHTML = result.data;
    $('#procesarCO2').modal('show');
}

function closeProcesarCO2() {
    const sid = controlActiveSid;
    if (!sid) return;
    $('#btnProcesarCO2_' + sid + ' .closeCO2').attr('hidden', true);
    $('#btnProcesarCO2_' + sid + ' .procesarCO2').attr('hidden', true);
    $('#co2_SP_' + sid).val(spCO2Input[sid]);
}

async function procesarHumidity(sid) {
    controlActiveSid = sid;
    const valor_anterior = parseInt($('#humidity_SP_a_' + sid).val(), 10) || 0;
    const valor_cambiar = $('#humidity_SP_' + sid).val();
    const trama = valor_anterior + '|' + valor_cambiar + '|2';
    const response = await fetch(base_url + 'Control/ProcesarModal/' + trama, { method: 'GET' });
    const result = await response.json();
    sp_humidity_esquema.innerHTML = result.data;
    $('#procesarHumidity').modal('show');
}

function closeProcesarHumidity() {
    const sid = controlActiveSid;
    if (!sid) return;
    $('#btnProcesarHumidity_' + sid + ' .closeHumidity').attr('hidden', true);
    $('#btnProcesarHumidity_' + sid + ' .procesarHumidity').attr('hidden', true);
    $('#humidity_SP_' + sid).val(spHumidityInput[sid]);
}

async function procesarO2(sid) {
    controlActiveSid = sid;
    const valor_anterior = parseFloat($('#o2_SP_a_' + sid).val()) || 0;
    const valor_cambiar = $('#o2_SP_' + sid).val();
    const trama = valor_anterior + '|' + valor_cambiar + '|2';
    const response = await fetch(base_url + 'Control/ProcesarModal/' + trama, { method: 'GET' });
    const result = await response.json();
    sp_o2_esquema.innerHTML = result.data;
    $('#procesarO2').modal('show');
}

function closeProcesarO2() {
    const sid = controlActiveSid;
    if (!sid) return;
    $('#btnProcesarO2_' + sid + ' .closeO2').attr('hidden', true);
    $('#btnProcesarO2_' + sid + ' .procesarO2').attr('hidden', true);
    $('#o2_SP_' + sid).val(spO2Input[sid]);
}

function closePower(sid) {
    const s = sid || controlActiveSid;
    if (s) {
        $('#btnPower_' + s + ' .closepower').attr('hidden', true);
        $('#btnPower_' + s + ' .procesarpower').attr('hidden', true);
        $('#select_power_' + s).val(elpower_state[s]);
    }
}

function closeAVL(sid) {
    const s = sid || controlActiveSid;
    if (s) {
        $('#btnAVL_' + s + ' .closeavl').attr('hidden', true);
        $('#btnAVL_' + s + ' .procesaravl').attr('hidden', true);
        $('#select_avl_ok_' + s).val(elavl[s]);
    }
}

function touchSpinSafe($el, options) {
    const raw = $el.val();
    if (raw === 'NA' || raw === '' || raw === undefined) return;
    $el.TouchSpin(options);
}

function initSectorControls(sid) {
    touchSpinSafe($('#tmp_SP_' + sid), {
        min: -40,
        max: 104,
        step: 0.1,
        decimals: 1,
        boostat: 5,
        maxboostedstep: 10,
        postfix: '°F'
    });
    $('#tmp_SP_' + sid).on('change', function () {
        const btn =
            "<div class='d-flex justify-content-center gap-2'><button type='button' class='btn procesarTMP' style='background-color: #032338; color: white;' onclick='procesarTmp(\"" +
            sid +
            "\")'>Submit</button><button type='button' class='btn btn-danger closeTMP' onclick='closeProcesarTmp()'>Cancel</button></div>";
        $('#btnProcesarTMP_' + sid).html(btn);
    });

    touchSpinSafe($('#co2_SP_' + sid), {
        min: 0,
        max: 30,
        step: 0.1,
        decimals: 1,
        boostat: 5,
        maxboostedstep: 10,
        postfix: '%'
    });
    $('#co2_SP_' + sid).on('change', function () {
        const btn =
            "<div class='d-flex justify-content-center gap-2'><button type='button' class='btn procesarCO2' style='background-color: #032338; color: white;' onclick='procesarCO2(\"" +
            sid +
            "\")'>Submit</button><button type='button' class='btn btn-danger closeCO2' onclick='closeProcesarCO2()'>Cancel</button></div>";
        $('#btnProcesarCO2_' + sid).html(btn);
    });

    touchSpinSafe($('#humidity_SP_' + sid), {
        min: 0,
        max: 100,
        step: 1,
        decimals: 0,
        boostat: 5,
        maxboostedstep: 10,
        postfix: '%'
    });
    $('#humidity_SP_' + sid).on('change', function () {
        const btn =
            "<div class='d-flex justify-content-center gap-2'><button type='button' class='btn procesarHumidity' style='background-color: #032338; color: white;' onclick='procesarHumidity(\"" +
            sid +
            "\")'>Submit</button><button type='button' class='btn btn-danger closeHumidity' onclick='closeProcesarHumidity()'>Cancel</button></div>";
        $('#btnProcesarHumidity_' + sid).html(btn);
    });

    touchSpinSafe($('#o2_SP_' + sid), {
        min: 0,
        max: 25,
        step: 0.1,
        decimals: 1,
        boostat: 5,
        maxboostedstep: 10,
        postfix: '%'
    });
    $('#o2_SP_' + sid).on('change', function () {
        const btn =
            "<div class='d-flex justify-content-center gap-2'><button type='button' class='btn procesarO2' style='background-color: #032338; color: white;' onclick='procesarO2(\"" +
            sid +
            "\")'>Submit</button><button type='button' class='btn btn-danger closeO2' onclick='closeProcesarO2()'>Cancel</button></div>";
        $('#btnProcesarO2_' + sid).html(btn);
    });
}

document.addEventListener('DOMContentLoaded', async function () {
    try {
        const response = await fetch(base_url + 'Control/ControlContent', { method: 'GET' });
        const payload = await response.json();
        const rows = Array.isArray(payload.data) ? payload.data : [];
        contenidoControl.innerHTML = payload.text || '';

        const panels = document.querySelectorAll('.control-sector-panel');
        panels.forEach(function (panel, idx) {
            const sid = panel.getAttribute('data-telemetria-id');
            if (!sid) return;
            const row = rows[idx] || {};

            const selPower = document.getElementById('select_power_' + sid);
            const selAvl = document.getElementById('select_avl_ok_' + sid);
            const iconPower = document.getElementById('power_icon_' + sid);
            if (selPower) {
                selPower.value = String(row.power_state != null ? row.power_state : 0);
                if (iconPower) {
                    $(iconPower).removeClass('text-success text-danger');
                    if (selPower.value === '1') $(iconPower).addClass('text-success');
                    else $(iconPower).addClass('text-danger');
                }
            }
            if (selAvl) {
                if (row.avl === 0) selAvl.value = '0';
                else selAvl.value = '1';
            }

            let spCo2 = $('#co2_SP_' + sid).val();
            const spCo2n = parseFloat(spCo2);
            if (!Number.isFinite(spCo2n) || spCo2n > 30 || spCo2n < 0) spCo2 = 'NA';

            spTemperatureInput[sid] = $('#tmp_SP_' + sid).val();
            spCO2Input[sid] = spCo2;
            spHumidityInput[sid] = $('#humidity_SP_' + sid).val();
            spO2Input[sid] = $('#o2_SP_' + sid).val();
            elpower_state[sid] = selPower ? selPower.value : '0';
            elavl[sid] = selAvl ? selAvl.value : '0';

            initSectorControls(sid);
        });

        $(document).on('click', '.clean_inputTMP', function () {
            closeProcesarTmp();
        });
        $(document).on('click', '.clean_inputCO2', function () {
            closeProcesarCO2();
        });
        $(document).on('click', '.clean_inputHumidity', function () {
            closeProcesarHumidity();
        });
        $(document).on('click', '.clean_inputO2', function () {
            closeProcesarO2();
        });
    } catch (err) {
        console.error(err);
        alertas('No se pudo cargar el panel de control.', 'error');
    }

    contenidoControl.addEventListener('change', function (e) {
        const t = e.target;
        if (!t.id) return;
        if (t.id.startsWith('select_power_')) {
            const sid = t.getAttribute('data-sector') || t.id.replace('select_power_', '');
            controlActiveSid = sid;
            const btnPower =
                "<div class='d-flex justify-content-center gap-2'><button type='button' class='btn procesarpower' style='background-color: #032338; color: white;' onclick='procesarPower(\"" +
                sid +
                "\")'>Power</button><button type='button' class='btn btn-danger closepower' onclick='closePower(\"" +
                sid +
                "\")'>Cancel</button></div>";
            $('#btnPower_' + sid).html(btnPower);
        }
        if (t.id.startsWith('select_avl_ok_')) {
            const sid = t.getAttribute('data-sector') || t.id.replace('select_avl_ok_', '');
            controlActiveSid = sid;
            const btnAVL =
                "<div class='d-flex justify-content-center gap-2'><button type='button' class='btn procesaravl' style='background-color: #032338; color: white;' onclick='procesarAVL(\"" +
                sid +
                "\")'>AVL</button><button type='button' class='btn btn-danger closeavl' onclick='closeAVL(\"" +
                sid +
                "\")'>Cancel</button></div>";
            $('#btnAVL_' + sid).html(btnAVL);
        }
    });
});

async function btnProcesarTMP() {
    const sid = controlActiveSid;
    if (!sid) return;
    const SP_Setpoint = $('#tmp_SP_' + sid).val();
    if (SP_Setpoint && SP_Setpoint !== 'NA') {
        const trama = SP_Setpoint;
        const response = await fetch(base_url + 'Control/ComandoTemperatura/' + trama, { method: 'GET' });
        const result = await response.json();
        const analizar = JSON.parse(result.data);
        const mensaje = analizar.estado == 1 ? ['success', result.mensaje] : ['error', 'control on standby, please wait ...'];
        alertas(mensaje[1], mensaje[0]);
        setTimeout(function () {
            window.location.href = base_url + 'AdminPage';
        }, 2000);
    }
}

async function btnProcesarCO2() {
    const sid = controlActiveSid;
    if (!sid) return;
    const SP_Setpoint = $('#co2_SP_' + sid).val();
    if (SP_Setpoint && SP_Setpoint !== 'NA') {
        const trama = SP_Setpoint;
        const response = await fetch(base_url + 'Control/CO2Comando/' + trama, { method: 'GET' });
        const result = await response.json();
        const analizar = JSON.parse(result.data);
        const mensaje = analizar.estado == 1 ? ['success', result.mensaje] : ['error', 'control on standby, please wait ...'];
        alertas(mensaje[1], mensaje[0]);
        setTimeout(function () {
            window.location.href = base_url + 'AdminPage';
        }, 2000);
    }
}

async function btnProcesarHumidity() {
    const sid = controlActiveSid;
    if (!sid) return;
    const SP_Setpoint = $('#humidity_SP_' + sid).val();
    if (SP_Setpoint && SP_Setpoint !== 'NA') {
        const trama = SP_Setpoint;
        const response = await fetch(base_url + 'Control/ComandoHumedad/' + trama, { method: 'GET' });
        const result = await response.json();
        const analizar = JSON.parse(result.data);
        const mensaje = analizar.estado == 1 ? ['success', result.mensaje] : ['error', 'control on standby, please wait ...'];
        alertas(mensaje[1], mensaje[0]);
        setTimeout(function () {
            window.location.href = base_url + 'AdminPage';
        }, 2000);
    }
}

async function btnProcesarO2() {
    const sid = controlActiveSid;
    if (!sid) return;
    const SP_Setpoint = $('#o2_SP_' + sid).val();
    if (SP_Setpoint && SP_Setpoint !== 'NA') {
        const trama = SP_Setpoint;
        const response = await fetch(base_url + 'Control/ComandoO2/' + trama, { method: 'GET' });
        const result = await response.json();
        if (!result || !result.data) {
            alertas('Respuesta inválida del servidor', 'error');
            return;
        }
        const analizar = JSON.parse(result.data);
        const mensaje = analizar.estado == 1 ? ['success', result.mensaje] : ['error', 'control on standby, please wait ...'];
        alertas(mensaje[1], mensaje[0]);
        setTimeout(function () {
            window.location.href = base_url + 'AdminPage';
        }, 2000);
    }
}

async function procesarPower(sid) {
    controlActiveSid = sid;
    $('#btnPower_' + sid + ' .closepower').attr('hidden', true);
    $('#btnPower_' + sid + ' .procesarpower').attr('hidden', true);
    const sel = document.getElementById('select_power_' + sid);
    if (!sel || sel.value === elpower_state[sid]) return;
    elpower_state[sid] = sel.value === '0' ? '0' : '1';
    const trama = sel.value === '0' ? 'OFF' : 'ON';
    const response = await fetch(base_url + 'Control/ComandoPower/' + trama, { method: 'GET' });
    const result = await response.json();
    const analizar = JSON.parse(result.data);
    const mensaje = analizar.estado == 1 ? ['success', result.mensaje] : ['error', 'control on standby, please wait ...'];
    alertas(mensaje[1], mensaje[0]);
    setTimeout(function () {
        window.location.href = base_url + 'AdminPage';
    }, 2000);
}

async function procesarAVL(sid) {
    controlActiveSid = sid;
    const sel = document.getElementById('select_avl_ok_' + sid);
    if (!sel || sel.value === elavl[sid]) return;
    const trama = sel.value === '0' ? 'NO' : 'FULL';
    elavl[sid] = sel.value === '0' ? '0' : '1';
    const response = await fetch(base_url + 'Control/AVLOK/' + trama, { method: 'GET' });
    const result = await response.json();
    const analizar = JSON.parse(result.data);
    const mensaje = analizar.estado == 1 ? ['success', result.mensaje] : ['error', 'control on standby, please wait ...'];
    alertas(mensaje[1], mensaje[0]);
    setTimeout(function () {
        window.location.href = base_url + 'AdminPage';
    }, 2000);
}

async function defrost_p_ok() {
    const SP_Setpoint = 'OK';
    const response = await fetch(base_url + 'Control/DefrostOK/' + SP_Setpoint, { method: 'GET' });
    const result = await response.json();
    const analizar = JSON.parse(result.data);
    const mensaje = analizar.estado == 1 ? ['success', result.mensaje] : ['error', 'control on standby, please wait ...'];
    alertas(mensaje[1], mensaje[0]);
    setTimeout(function () {
        window.location.href = base_url + 'AdminPage';
    }, 2000);
}
