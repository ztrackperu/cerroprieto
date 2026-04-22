
// ==========================================
// CONFIGURACIÓN Y CONSTANTES
// ==========================================
const CONFIG = {
    BASE_URL: base_url,
    INTERVALS: {
        LIVE_DATA: 30000,      // 30 segundos
        VIEW_HANDLERS: 1000    // 1 segundo
    },
    ENDPOINTS: {
        LISTA_DISPOSITIVOS: "AdminPage/ListaDispositivoEmpresa",
        LIVE_DATA: "AdminPage/LiveData",
        REGISTRAR: "AdminPage/registrar"
    },
    ICONS: {
        UP: "<i class='bi bi-arrow-up-short me-2 align-items-center mb-1 text-success value-icon'></i>",
        DOWN: "<i class='bi bi-arrow-down-short me-2 align-items-center mb-1 text-danger value-icon'></i>",
        STABLE: "<i class='bi bi-arrow-left-right me-2 align-items-center mb-1 text-primary value-icon'></i>",
        VIEW_LESS: "<i class='ri-arrow-up-circle-line view-less text-danger fs-2'></i>",
        VIEW_MORE: "<button type='button' class='btn btn-primary btn-sm view-more'>View More</button>"
    }
};
// ==========================================
// ESTADO GLOBAL DE LA APLICACIÓN
// ==========================================
class EstadoDispositivos {
    constructor() {
        this.valores = {
            ethylene: {},
            co2_reading: {},
            temp_supply: {},
            return_air: {},
            relative_humidity: {},
            ripener_prueba: {},
            avl: {},
            compress_coil_1: {},
            evaporation_coil: {},
            ambient_air: {},
            defrost_prueba: {},
            stateProcess: {},
            controlling_mode: {},
            cargo_1_temp: {},
            cargo_2_temp: {}
        };
    }

    /**
     * Actualiza el último valor numérico conocido y devuelve la tendencia respecto al ciclo anterior.
     * 'init' = primera muestra; 'stable' = sin cambio numérico.
     */
    actualizarNumerico(campo, telemetriaId, valor) {
        const valorAnterior = this.valores[campo][telemetriaId];
        this.valores[campo][telemetriaId] = valor;
        if (valorAnterior === undefined) return 'init';
        if (valor > valorAnterior) return 'up';
        if (valor < valorAnterior) return 'down';
        return 'stable';
    }

    actualizarTexto(campo, telemetriaId, valor) {
        const valorAnterior = this.valores[campo][telemetriaId];
        this.valores[campo][telemetriaId] = valor;
        if (valorAnterior === undefined) return 'init';
        if (String(valor) > String(valorAnterior)) return 'up';
        if (String(valor) < String(valorAnterior)) return 'down';
        return 'stable';
    }
}

const estado = new EstadoDispositivos();

// ==========================================
// UTILIDADES Y HELPERS
// ==========================================
const Utils = {
    async fetchData(endpoint, method = 'GET') {
        const url = CONFIG.BASE_URL + endpoint;
        try {
            const response = await fetch(url, { method });
            const text = await response.text();
            if (!response.ok) {
                console.error(`fetch ${endpoint}: HTTP ${response.status}`, text ? text.slice(0, 600) : '');
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            if (!text || !String(text).trim()) {
                console.warn('fetch: cuerpo vacío', url);
                return { data: [], text: '', text_ok: '' };
            }
            let data;
            try {
                data = JSON.parse(text);
            } catch (parseErr) {
                console.error(`JSON inválido en ${endpoint}:`, (text || '').slice(0, 500));
                throw new Error('La respuesta del servidor no es JSON válido (¿error PHP o HTML?).');
            }
            return data;
        } catch (error) {
            console.error(`Error fetching ${endpoint}:`, error);
            throw error;
        }
    },

    formatearValor(valor, unidad = '') {
        if (valor === null || valor === undefined) return 'NA';
        if (typeof valor === 'string' && valor.trim().toUpperCase() === 'NA') return 'NA';
        return `${valor}${unidad}`;
    },

    getIcono(tendencia) {
        if (tendencia === 'up') return CONFIG.ICONS.UP;
        if (tendencia === 'down') return CONFIG.ICONS.DOWN;
        if (tendencia === 'stable' || tendencia === 'init') return CONFIG.ICONS.STABLE;
        return CONFIG.ICONS.STABLE;
    },

    /**
     * Parsea ultima_fecha tal como la devuelve fechaPro(): "HH:MM:SS - DD/MM/YYYY"
     */
    parseFechaProToMs(str) {
        if (!str || typeof str !== 'string') return null;
        const m = str.trim().match(/^(\d{2}):(\d{2}):(\d{2})\s*-\s*(\d{2})\/(\d{2})\/(\d{4})$/);
        if (!m) return null;
        const [, hh, mm, ss, d, mo, y] = m;
        const t = new Date(`${y}-${mo}-${d}T${hh}:${mm}:${ss}`).getTime();
        return Number.isFinite(t) ? t : null;
    },

    horaCortaDesdeFechaPro(str) {
        if (!str || typeof str !== 'string') return '';
        const m = str.trim().match(/^(\d{2}):(\d{2}):(\d{2})\s*-/);
        return m ? `${m[1]}:${m[2]}` : '';
    },

    esValorNa(valor) {
        if (valor === null || valor === undefined) return true;
        if (typeof valor === 'number' && !Number.isFinite(valor)) return true;
        const s = String(valor).trim();
        if (s === '' || s.toUpperCase() === 'NA') return true;
        return false;
    },

    parseComparableNumber(valor) {
        if (Utils.esValorNa(valor)) return null;
        const n = parseFloat(String(valor).replace(/^\+/, ''));
        return Number.isFinite(n) ? n : null;
    }
};

// ==========================================
// MANEJADORES DE VISTA
// ==========================================
class ManejadorVistas {
    constructor() {
        this.inicializarEventos();
    }

    inicializarEventos() {
        // Usar delegación de eventos para mejorar el rendimiento
        $(document).on('click', '.view-more', this.mostrarMas.bind(this));
        $(document).on('click', '.view-less', this.mostrarMenos.bind(this));
    }

    mostrarMas() {
        $('.hide-content').attr('hidden', false);
        $('#change-button').html(CONFIG.ICONS.VIEW_LESS);
    }

    mostrarMenos() {
        $('.hide-content').attr('hidden', true);
        $('#change-button').html(CONFIG.ICONS.VIEW_MORE);
    }
}

// ==========================================
// ACTUALIZADOR DE TARJETAS
// ==========================================
class ActualizadorTarjetas {
    constructor() {
        /** @type {Record<number, number>} ms desde epoch de la última muestra aplicada por dispositivo */
        this.ultimaFechaMsPorDispositivo = {};
        /** @type {Record<number, Record<string, string>>} último texto mostrado por campo (para conservar si llega NA) */
        this.ultimoTextoValor = {};
        this.camposConfig = [
            { campo: 'ethylene', selector: 'ethyleno', formato: ' ppm', iconSelector: 'eti_icon', validacion: this.validarEtilenoPpm },
            { campo: 'co2_reading', selector: 'co2', formato: ' %', iconSelector: 'co2_icon', validacion: this.validarCO2 },
            { campo: 'temp_supply', selector: 'supply', formato: ' F°', iconSelector: 'supply_icon', usarCampo: 'temp_supply_1' },
            { campo: 'return_air', selector: 'return', formato: ' F°', iconSelector: 'return_icon' },
            { campo: 'relative_humidity', selector: 'humidity', formato: ' %', iconSelector: 'humidity_icon' },
            { campo: 'ripener_prueba', selector: 'i_hours', formato: '', iconSelector: 'i_hours_icon' },
            { campo: 'avl', selector: 'avl', formato: ' CFM', iconSelector: 'avl_icon' },
            { campo: 'compress_coil_1', selector: 'compressor', formato: ' F°', iconSelector: 'compressor_icon' },
            { campo: 'evaporation_coil', selector: 'evaporator', formato: ' F°', iconSelector: 'evaporator_icon' },
            { campo: 'ambient_air', selector: 'ambient_air', formato: ' F°', iconSelector: 'ambient_air_icon' },
            { campo: 'defrost_prueba', selector: 'pwd', formato: '', iconSelector: 'pwd_icon' },
            { campo: 'stateProcess', selector: 'proceso', formato: '', iconSelector: 'proceso_icon' },
            { campo: 'controlling_mode', selector: 'c_mode', formato: '', iconSelector: 'c_mode_icon' },
            { campo: 'cargo_1_temp', selector: 'usda_1', formato: ' F°', iconSelector: 'usda_1_icon' },
            { campo: 'cargo_2_temp', selector: 'usda_2', formato: ' F°', iconSelector: 'usda_2_icon' }
        ];
    }

    validarCO2(valor) {
        return (valor >= 0 && valor <= 30) ? valor : 'NA';
    }

    /** Etileno (PPM): no mostrar lecturas estrictamente mayores a 250 (misma regla que val_eti en PHP). */
    validarEtilenoPpm(valor) {
        if (Utils.esValorNa(valor)) {
            return valor;
        }
        const n = parseFloat(String(valor).replace(/^\+/, ''));
        if (!Number.isFinite(n)) {
            return 'NA';
        }
        if (n > 250) {
            return 'NA';
        }
        return valor;
    }

    /**
     * Solo aplica la tanda si ultima_fecha es estrictamente más reciente que la última vista (evita datos viejos).
     */
    actualizarTarjeta(datos) {
        const telemetriaId = datos.telemetria_id;
        const tsMs = Utils.parseFechaProToMs(datos.ultima_fecha);

        if (tsMs !== null) {
            const prevMs = this.ultimaFechaMsPorDispositivo[telemetriaId];
            if (prevMs !== undefined && tsMs <= prevMs) {
                return;
            }
            this.ultimaFechaMsPorDispositivo[telemetriaId] = tsMs;
        }

        $(`#fechita_${telemetriaId}`).text(datos.ultima_fecha);

        const horaCorta = Utils.horaCortaDesdeFechaPro(datos.ultima_fecha);
        const actualizaciones = [];

        this.camposConfig.forEach(config => {
            const valorCampo = config.usarCampo ? datos[config.usarCampo] : datos[config.campo];
            let valor = config.validacion ? config.validacion(valorCampo) : valorCampo;

            const cachePorId = this.ultimoTextoValor[telemetriaId] || (this.ultimoTextoValor[telemetriaId] = {});
            let tendencia;
            let textoMostrar;
            let marcarHoraEnEtiqueta = false;

            if (Utils.esValorNa(valor)) {
                textoMostrar =
                    cachePorId[config.selector] !== undefined ? cachePorId[config.selector] : 'NA';
                tendencia = 'stable';
            } else {
                textoMostrar = Utils.formatearValor(valor, config.formato);
                cachePorId[config.selector] = textoMostrar;

                const num = Utils.parseComparableNumber(valor);
                if (num !== null) {
                    tendencia = estado.actualizarNumerico(config.campo, telemetriaId, num);
                } else {
                    tendencia = estado.actualizarTexto(config.campo, telemetriaId, String(valor));
                }
            }

            if ((tendencia === 'up' || tendencia === 'down') && horaCorta) {
                marcarHoraEnEtiqueta = true;
            }

            actualizaciones.push({
                labelSelector: `#${config.selector}_${telemetriaId}`,
                iconSelector: config.iconSelector ? `#${config.iconSelector}_${telemetriaId}` : null,
                texto: textoMostrar,
                tendencia,
                marcarHoraEnEtiqueta,
                horaCorta
            });
        });

        requestAnimationFrame(() => {
            actualizaciones.forEach(u => {
                const $lab = $(u.labelSelector);
                if ($lab.length === 0) return;

                if (u.marcarHoraEnEtiqueta && u.horaCorta) {
                    $lab.empty();
                    $lab.append(document.createTextNode(u.texto));
                    const cls = u.tendencia === 'up' ? 'text-success' : 'text-danger';
                    $lab.append($('<small>').addClass(`ms-1 fw-normal ${cls}`).text(u.horaCorta));
                } else {
                    $lab.text(u.texto);
                }

                if (u.iconSelector) {
                    const $ic = $(u.iconSelector);
                    if ($ic.length) $ic.html(Utils.getIcono(u.tendencia));
                }
            });
        });
    }
}

// ==========================================
// GESTOR DE DISPOSITIVOS
// ==========================================
class GestorDispositivos {
    constructor() {
        this.actualizadorTarjetas = new ActualizadorTarjetas();
        this.intervalos = new Map();
    }

    async cargarDispositivosIniciales() {
        try {
            const data = await Utils.fetchData(CONFIG.ENDPOINTS.LISTA_DISPOSITIVOS);
            if (!data || typeof data !== 'object') {
                throw new Error('Respuesta de lista de dispositivos no es un objeto JSON.');
            }
            const total = data.total_dispositivos != null ? data.total_dispositivos : (Array.isArray(data.data) ? data.data.length : 0);
            const imeiTr = data.imei_trama != null && String(data.imei_trama).trim() !== '' ? data.imei_trama : '(ninguno)';
            console.log(
                'AdminPage: ' + total + ' dispositivo(s) en lista; trama TermoKing/ConsultarUltimaTrama para IMEI:',
                imeiTr,
                '(text_ok = bloque principal)'
            );
            
            const contenidoPrincipal = document.getElementById('contenidoPrincipal');
            const contenidoExtra = document.getElementById('contenidoExtra');
            
            if (contenidoExtra) {
                const html = data.text_ok != null && data.text_ok !== undefined ? data.text_ok : '';
                contenidoExtra.innerHTML = typeof html === 'string' ? html : String(html);
            }
            /* #contenidoPrincipal oculto en Admin (d-none en vista); no inyectar HTML allí. */
        } catch (error) {
            console.error('Error cargando dispositivos:', error);
            alert('Error al cargar los dispositivos. Por favor, recarga la página.');
        }
    }

    async obtenerActualizacionesLive() {
        try {
            const result = await Utils.fetchData(CONFIG.ENDPOINTS.LIVE_DATA);
            const list = Array.isArray(result) ? result : [];
            if (list.length > 0) {
                list.forEach((res) => this.actualizadorTarjetas.actualizarTarjeta(res));
                console.log(`Actualización live: ${list.length} dispositivos`);
            }
            return list;
        } catch (error) {
            console.error('Error obteniendo datos live:', error);
            return [];
        }
    }

    iniciarActualizacionesPeriodicas() {
        // Limpiar intervalos existentes
        this.detenerActualizaciones();
        
        // Configurar nuevo intervalo para datos live
        this.intervalos.set('liveData', 
            setInterval(() => this.obtenerActualizacionesLive(), CONFIG.INTERVALS.LIVE_DATA)
        );
        
        console.log('Actualizaciones periódicas iniciadas');
    }

    detenerActualizaciones() {
        this.intervalos.forEach(intervalo => clearInterval(intervalo));
        this.intervalos.clear();
    }
}

// ==========================================
// GESTOR DE FORMULARIOS
// ==========================================
class GestorFormularios {
    async registrarRespuesta(e) {
        e.preventDefault();
        
        const frm = document.getElementById("frmRegistrar");
        if (!frm) {
            console.error('Formulario no encontrado');
            return;
        }

        try {
            const formData = new FormData(frm);
            const response = await fetch(CONFIG.BASE_URL + CONFIG.ENDPOINTS.REGISTRAR, {
                method: 'POST',
                body: formData
            });

            if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
            
            const res = await response.json();
            
            frm.reset();
            
            // Recargar tabla si existe
            if (typeof tblFormulario !== 'undefined' && tblFormulario.ajax) {
                tblFormulario.ajax.reload();
            }
            
            // Mostrar alerta si la función existe
            if (typeof alertas === 'function') {
                alertas(res.msg, res.icono);
            }
        } catch (error) {
            console.error('Error registrando respuesta:', error);
            alert('Error al registrar. Por favor, intenta nuevamente.');
        }
    }
}

// ==========================================
// APLICACIÓN PRINCIPAL
// ==========================================
class AplicacionPrincipal {
    constructor() {
        this.gestorDispositivos = new GestorDispositivos();
        this.manejadorVistas = new ManejadorVistas();
        this.gestorFormularios = new GestorFormularios();
    }

    async inicializar() {
        console.log('Luis Inicializando aplicación...');
        
        try {
            // Cargar dispositivos iniciales
            await this.gestorDispositivos.cargarDispositivosIniciales();
            
            // Iniciar actualizaciones periódicas
            this.gestorDispositivos.iniciarActualizacionesPeriodicas();
            
            console.log('Aplicación inicializada correctamente');
        } catch (error) {
            console.error('Error inicializando aplicación:', error);
        }
    }

    destruir() {
        this.gestorDispositivos.detenerActualizaciones();
        console.log('Aplicación destruida');
    }
}

// ==========================================
// INICIALIZACIÓN
// ==========================================
let app;

document.addEventListener("DOMContentLoaded", async function() {
    app = new AplicacionPrincipal();
    await app.inicializar();
});

// Limpiar al cerrar/recargar la página
window.addEventListener('beforeunload', function() {
    if (app) {
        app.destruir();
    }
});

// Exportar función de registro para compatibilidad
window.registrarRespuesta = function(e) {
    if (app && app.gestorFormularios) {
        app.gestorFormularios.registrarRespuesta(e);
    }
}; 