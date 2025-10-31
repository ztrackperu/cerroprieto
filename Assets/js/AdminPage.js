
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
        TABLA_ESTADO: "AdminPage/TablaEstadoDispositivos",
        REGISTRAR: "AdminPage/registrar"
    },
    ICONS: {
        UP: "<i class='bi bi-arrow-up-short me-2 align-items-center mb-1 text-success value-icon'></i>",
        DOWN: "<i class='bi bi-arrow-down-short me-2 align-items-center mb-1 text-danger value-icon'></i>",
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

    actualizar(campo, telemetriaId, valor) {
        const valorAnterior = this.valores[campo][telemetriaId];
        this.valores[campo][telemetriaId] = valor;
        
        if (valorAnterior === undefined) return null;
        if (valor > valorAnterior) return 'up';
        if (valor < valorAnterior) return 'down';
        return null;
    }

    obtenerTendencia(campo, telemetriaId, valor) {
        return this.actualizar(campo, telemetriaId, valor);
    }
}

const estado = new EstadoDispositivos();

// ==========================================
// UTILIDADES Y HELPERS
// ==========================================
const Utils = {
    async fetchData(endpoint, method = 'GET') {
        try {
            const response = await fetch(CONFIG.BASE_URL + endpoint, { method });
            if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
            return await response.json();
        } catch (error) {
            console.error(`Error fetching ${endpoint}:`, error);
            throw error;
        }
    },

    formatearValor(valor, unidad = '') {
        if (valor === null || valor === undefined) return 'NA';
        return `${valor}${unidad}`;
    },

    getIcono(tendencia) {
        if (!tendencia) return '';
        return tendencia === 'up' ? CONFIG.ICONS.UP : CONFIG.ICONS.DOWN;
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
        this.camposConfig = [
            { campo: 'ethylene', selector: 'ethyleno', formato: 'ppm', iconSelector: 'eti_icon' },
            { campo: 'co2_reading', selector: 'co2', formato: '%', iconSelector: 'co2_icon', validacion: this.validarCO2 },
            { campo: 'temp_supply', selector: 'supply', formato: 'F°', iconSelector: 'supply_icon', usarCampo: 'temp_supply_1' },
            { campo: 'return_air', selector: 'return', formato: 'F°', iconSelector: 'return_icon' },
            { campo: 'relative_humidity', selector: 'humidity', formato: '%', iconSelector: 'humidity_icon' },
            { campo: 'ripener_prueba', selector: 'i_hours', formato: '', iconSelector: 'i_hours_icon' },
            { campo: 'avl', selector: 'avl', formato: 'CFM', iconSelector: 'avl_icon' },
            { campo: 'compress_coil_1', selector: 'compressor', formato: 'F°', iconSelector: 'compressor_icon' },
            { campo: 'evaporation_coil', selector: 'evaporator', formato: 'F°', iconSelector: 'evaporator_icon' },
            { campo: 'ambient_air', selector: 'ambient_air', formato: 'F°', iconSelector: 'ambient_air_icon' },
            { campo: 'defrost_prueba', selector: 'pwd', formato: '', iconSelector: 'pwd_icon' },
            { campo: 'stateProcess', selector: 'proceso', formato: '', iconSelector: 'proceso_icon' },
            { campo: 'controlling_mode', selector: 'c_mode', formato: '', iconSelector: 'c_mode_icon' },
            { campo: 'cargo_1_temp', selector: 'usda_1', formato: 'F°', iconSelector: 'usda_1_icon' },
            { campo: 'cargo_2_temp', selector: 'usda_2', formato: 'F°', iconSelector: 'usda_2_icon' }
        ];
    }

    validarCO2(valor) {
        return (valor >= 0 && valor <= 30) ? valor : 'NA';
    }

    actualizarTarjeta(datos) {
        const telemetriaId = datos.telemetria_id;
        
        // Actualizar fecha
        $(`#fechita_${telemetriaId}`).text(datos.ultima_fecha);
        
        // Actualizar campos con batch DOM updates
        const actualizaciones = [];
        
        this.camposConfig.forEach(config => {
            const valorCampo = config.usarCampo ? datos[config.usarCampo] : datos[config.campo];
            const valor = config.validacion ? config.validacion(valorCampo) : valorCampo;
            const tendencia = estado.obtenerTendencia(config.campo, telemetriaId, datos[config.campo]);
            
            // Agregar actualizaciones al batch
            actualizaciones.push({
                selector: `#${config.selector}_${telemetriaId}`,
                valor: Utils.formatearValor(valor, config.formato)
            });
            
            if (config.iconSelector && tendencia) {
                actualizaciones.push({
                    selector: `#${config.iconSelector}_${telemetriaId}`,
                    html: Utils.getIcono(tendencia)
                });
            }
        });
        
        // Aplicar todas las actualizaciones en batch
        requestAnimationFrame(() => {
            actualizaciones.forEach(update => {
                if (update.html) {
                    $(update.selector).html(update.html);
                } else {
                    $(update.selector).text(update.valor);
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
            console.log('Dispositivos cargados:', data);
            
            const contenidoPrincipal = document.getElementById('contenidoPrincipal');
            //contenidoExtra
            const contenidoExtra = document.getElementById('contenidoExtra');
            
            if (contenidoExtra) {
                //console.log(data.text_ok);
                //console.log("aqui estamos en adminpage js");
                console.log(data.text_ok);
                contenidoExtra.innerHTML = data.text_ok;
            }
            if (contenidoPrincipal) {
                //contenidoPrincipal.innerHTML = data.text;
            }
            
            await this.verificarEstadoDispositivos();
        } catch (error) {
            console.error('Error cargando dispositivos:', error);
            alert('Error al cargar los dispositivos. Por favor, recarga la página.');
        }
    }

    async obtenerActualizacionesLive() {
        try {
            const result = await Utils.fetchData(CONFIG.ENDPOINTS.LIVE_DATA);
            
            if (result && result.length > 0) {
                // Procesar en batch para mejor rendimiento
                result.forEach(res => this.actualizadorTarjetas.actualizarTarjeta(res));
                console.log(`Actualización live: ${result.length} dispositivos`);
            }
            
            return result;
        } catch (error) {
            console.error('Error obteniendo datos live:', error);
            return [];
        }
    }

    async verificarEstadoDispositivos() {
        try {
            const result = await Utils.fetchData(CONFIG.ENDPOINTS.TABLA_ESTADO);
            console.log('Estado dispositivos:', result);
            
            if (result && result.length !== 0 && result.estado) {
                const hayProblemas = result.estado.some(item => 
                    item.estado === 'WAIT' || item.estado === 'OFFLINE'
                );
                
                if (hayProblemas) {
                    this.mostrarModalDispositivos(result.text);
                }
            }
        } catch (error) {
            console.error('Error verificando estado:', error);
        }
    }

    mostrarModalDispositivos(contenidoHTML) {
        const contenidoD = document.getElementById('contenidoDispositivos');
        if (contenidoD) {
            contenidoD.innerHTML = contenidoHTML;
            $('#modalDispositivos').modal('show');
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
        console.log('Inicializando aplicación...');
        
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