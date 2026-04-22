<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'Libraries/PHPMailer/src/Exception.php';
require 'Libraries/PHPMailer/src/PHPMailer.php';
require 'Libraries/PHPMailer/src/SMTP.php';

class AdminPage extends Controller
{
    public function __construct()
    {
        if (empty($_SESSION['activo_ztrack'])) {
            header("location: " . base_url);
        }
        parent::__construct();
    }
    public function index()
    {
		$id_user = $_SESSION['id_ztrack'];
        //$perm = $this->model->verificarPermisos($id_user, "AdminPage");
        //if (!$perm && $id_user != 1) {
            //$this->views->getView($this, "permisos");
            //exit;
        //}
        $this->views->getView($this, "index");
    }
    public function validarCamposCorreoYClave()
    {
        $id_user = $_SESSION['id_usuario'];
        $res = $this->model->validarCamposCorreoYClave($id_user);
        echo json_encode($res);
        die();
    }

    public function registrar()
    {
        $id = strClean($_POST['id']);
        $correo_usuario = strClean($_POST['correo']);
        $clave_correo = strClean($_POST['password']);
        $email_existente = strClean($_POST['correo_admin']);
        $usuario_activo = $_SESSION['id_usuario'];

        if (empty($correo_usuario) || empty($clave_correo)) {
            $msg = array('msg' => 'Ingrese todos sus datos', 'icono' => 'warning');
        } else {
            $data = $this->model->insertarRespuesta($id, $correo_usuario,$usuario_activo);

            if ($data == "ok") {
                $evento = "RESPONDIDO";
                $id_consulta = $this->model->IdRespuesta($correo_usuario);
                $id = $id_consulta['id'];
                $data2 = $this->model->h_respuesta($id, $id, $correo_usuario,$usuario_activo, $evento);
                $msg = array('msg' => 'Respuesta enviada', 'icono' => 'success');
                
                $mail = new PHPMailer(true);
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = $correo_usuario; // Reemplaza con tu dirección de correo electrónico de Gmail
                $mail->Password = $clave_correo; // Reemplaza con tu contraseña de Gmail
                $mail->SMTPSecure = 'ssl';
                $mail->Port = 465;

                // Configuración del correo electrónico
                $mail->setFrom('zgroupsistemas@gmail.com', 'ZTRACK');
                $mail->addAddress($email_existente); // Reemplaza con la dirección de correo electrónico del destinatario
                $mail->send();
            } else {
                $msg = array('msg' => 'Error al registrar', 'icono' => 'error');
            }
        }
        echo json_encode($msg, JSON_UNESCAPED_UNICODE);
        die();
    }
   
  
    public function LiveData()
    {
        // aqui debe llegar todo los datos si es user 1 sino de acuedo a loq ue esta permitido 
		$id_user = $_SESSION['id_ztrack'];
        /*
        $perm = $this->model->verificarPermisos($id_user, "Live");
        if (!$perm && $id_user != 1) {
            $this->views->getView($this, "permisos");
            exit;
        }
        */
        /*
        //forma de recibir un json desde js     
        $datosRecibidos = file_get_contents("php://input");
        //$resultado = $_POST['data'];
        //echo json_encode($datosRecibidos, JSON_UNESCAPED_UNICODE);
        $resultado1 = json_decode($datosRecibidos);
        //enviar el resultado1 a api para procesar si existe algun cambio
        $VerificarLive = $this->model->VerificarLive($resultado1);
        $resultado = $resultado1->data;
        echo json_encode($VerificarLive, JSON_UNESCAPED_UNICODE);
        */
        if (empty($_SESSION['data']) || !is_array($_SESSION['data'])) {
            echo json_encode(array(), JSON_UNESCAPED_UNICODE);
            die();
        }
        $datosW = $_SESSION['data'];
        $resultado1 = array('data' => $datosW);
        $verificarJson = $this->model->VerificarLive($resultado1);
        $verificarApi = json_decode($verificarJson);
        if (!is_object($verificarApi) || !isset($verificarApi->data)) {
            echo json_encode(array(), JSON_UNESCAPED_UNICODE);
            die();
        }
        $Verificar = $verificarApi->data;
        if (!is_array($Verificar)) {
            if (is_object($Verificar)) {
                $Verificar = array($Verificar);
            } else {
                $Verificar = array();
            }
        }
        $d = 0;
        foreach ($datosW as $clave => $valor) {
            if (!is_object($valor) || !isset($valor->telemetria_id)) {
                continue;
            }
            foreach ($Verificar as $dat) {
                if (!is_object($dat) || !isset($dat->telemetria_id)) {
                    continue;
                }
                if ((string) $valor->telemetria_id === (string) $dat->telemetria_id) {
                    $_SESSION['data'][$clave]->ultima_fecha = $dat->ultima_fecha;
                    $dat->ultima_fecha = fechaPro($dat->ultima_fecha);
                    $dat->temp_supply_1 = tempNormal($dat->temp_supply_1);
                    $dat->return_air = tempNormal($dat->return_air);
                    $dat->set_point = tempNormal($dat->set_point);
                    $dat->relative_humidity = porNormal($dat->relative_humidity);
                    $dat->humidity_set_point = porNormal($dat->humidity_set_point);
                    $dat->evaporation_coil = tempNormal($dat->evaporation_coil);
                    $dat->ambient_air = tempNormal($dat->ambient_air);
                    $dat->cargo_1_temp = tempNormal($dat->cargo_1_temp);
                    $dat->cargo_2_temp = tempNormal($dat->cargo_2_temp);
                    $dat->cargo_3_temp = tempNormal($dat->cargo_3_temp);
                    $dat->cargo_4_temp = tempNormal($dat->cargo_4_temp);
                    $d++;
                }
            }
        }
        echo json_encode($Verificar, JSON_UNESCAPED_UNICODE);
        die();
    } 
    
    
    public function ListaDispositivoEmpresa()
    {
        $empresaId = (isset($_SESSION['empresa_id']) && (int) $_SESSION['empresa_id'] > 0)
            ? (int) $_SESSION['empresa_id'] : 61;
        $rawLista = $this->model->ListaDispositivoEmpresa($empresaId);
        $decLista = json_decode($rawLista);
        if (!is_object($decLista) || !isset($decLista->data)) {
            $data = array();
        } else {
            $data = $decLista->data;
        }
        if (!is_array($data) && is_object($data)) {
            $data = array($data);
        }
        if (!is_array($data)) {
            $data = array();
        }
        /** Total en empresa (la API puede devolver miles; la UI principal solo usa el último bloque). */
        $totalDispositivosLista = count($data);
        /** Último ítem de la lista: es el que corresponde a “el último” en flujos que recorren en orden. */
        $ultimo = null;
        if ($totalDispositivosLista > 0) {
            $ultimo = $data[$totalDispositivosLista - 1];
            if (!is_object($ultimo)) {
                $ultimo = null;
            }
        }
        /** Trama en vivo: `TermoKing/ConsultarUltimaTrama/{imei}` (url_nueva). Prioridad: IMEI del último contenedor; si no hay, CERRO_PRIETO_SERIES_IMEI / constante. */
        $imeiParaTrama = '';
        if ($ultimo !== null) {
            if (isset($ultimo->imei) && trim((string) $ultimo->imei) !== '') {
                $imeiParaTrama = trim((string) $ultimo->imei);
            } elseif (isset($ultimo->IMEI) && trim((string) $ultimo->IMEI) !== '') {
                $imeiParaTrama = trim((string) $ultimo->IMEI);
            }
        }
        if ($imeiParaTrama === '' && defined('cerro_prieto_series_imei') && trim((string) cerro_prieto_series_imei) !== '') {
            $imeiParaTrama = trim((string) cerro_prieto_series_imei);
        }
        /** Cerro Prieto: trama siempre con IMEI de referencia si no vino de la API (evita text_ok en blanco). */
        if ($imeiParaTrama === '') {
            $imeiParaTrama = '860389053949943';
        }
        $rawTrama = $this->model->ConsultarUltimaTrama($imeiParaTrama);
        $decPlus = json_decode($rawTrama);
        if (!is_object($decPlus) || !isset($decPlus->data) || $decPlus->data === null) {
            $dataPlus = (object) array();
        } else {
            $dataPlus = is_object($decPlus->data) ? $decPlus->data : (object) (array) $decPlus->data;
        }
        $conjunto = ContenedorGruposEspeciales($dataPlus);
        
        $text = "";
        $url = base_url;
        $fecha = array();
        $dataz = "";
        $enlace = null;
        if ($ultimo !== null) {
            $tipo = $ultimo->extra_1;
            $enlace = ContenedorMadurador_2($ultimo);
            $fecha = determinarEstado($ultimo->ultima_fecha, $id = 1, $fecha);
            $text = $enlace['text'];
            $dataz = $ultimo;
        }
        /** Solo el último dispositivo para no inflar JSON ni recorrer miles de filas en el cliente. */
        $dataRespuesta = ($ultimo !== null) ? array($ultimo) : array();
        /** Sesión: solo la fila del último dispositivo (por telemetria_id), no toda la lista. */
        $extraerRespuesta = array();
        if ($ultimo !== null && isset($ultimo->telemetria_id) && !empty($_SESSION['data']) && is_array($_SESSION['data'])) {
            foreach ($_SESSION['data'] as $k => $row) {
                if (is_object($row) && isset($row->telemetria_id)
                    && (string) $row->telemetria_id === (string) $ultimo->telemetria_id) {
                    $extraerRespuesta[$k] = $row;
                    break;
                }
            }
        }
        $data1 = array(
            'data' => $dataRespuesta,
            'total_dispositivos' => $totalDispositivosLista,
            'imei_trama' => $imeiParaTrama,
            'text' => $text,
            'text_extra' => $enlace,
            'text_ok' => isset($conjunto['text2']) ? $conjunto['text2'] : '',
            'dataPlus' => $dataPlus,
            'extraer' => $extraerRespuesta,
        );

        $flags = JSON_UNESCAPED_UNICODE;
        if (defined('JSON_INVALID_UTF8_SUBSTITUTE')) {
            $flags |= JSON_INVALID_UTF8_SUBSTITUTE;
        }
        $jsonOut = json_encode($data1, $flags);
        if ($jsonOut === false) {
            unset($data1['extraer']);
            $jsonOut = json_encode($data1, $flags);
        }
        if ($jsonOut === false) {
            $jsonOut = json_encode(
                array(
                    'data' => array(),
                    'text' => '',
                    'text_extra' => null,
                    'text_ok' => '<p class="text-muted">Respuesta de dispositivos no pudo serializarse. Verifique la sesión y la API.</p>',
                    'dataPlus' => (object) array(),
                ),
                $flags
            );
        }
        header('Content-Type: application/json; charset=utf-8');
        echo $jsonOut;
        die();
    }   
    public function ListaD() {
        $empId = (isset($_SESSION['empresa_id']) && (int) $_SESSION['empresa_id'] > 0)
            ? (int) $_SESSION['empresa_id'] : 61;
        $raw = $this->model->ListaDispositivoEmpresa($empId);
        $dec = json_decode($raw);
        if (!is_object($dec) || !isset($dec->data)) {
            echo json_encode(array('estados' => array()), JSON_UNESCAPED_UNICODE);
            die();
        }
        $data = $dec->data;
        if (!is_array($data)) {
            $data = is_object($data) ? array($data) : array();
        }
        $estados = array();
        
        foreach($data as $val2) {
            $estado = $this->determinarEstado($val2->ultima_fecha);
            $estados[] = (object) ['estado' => $estado];
        }
        
        $n_array = array(
            'estados' => $estados
        );
        
        echo json_encode($n_array, JSON_UNESCAPED_UNICODE);
        die();
    }

    private function determinarEstado($ultima_fecha) {
        
        $fechaActual = new DateTime();
        $fechaUltima = new DateTime($ultima_fecha);
        $diferencia = $fechaActual->getTimestamp() - $fechaUltima->getTimestamp();
        
        //tiempo en segundos
        if ($diferencia >= 1800) { 
            return 'Online';
        } elseif ($diferencia <= 86400) { 
            return 'Wait';
        } else {
            return 'Offline';
        }
    }
    public function ListaComandos()
    {
        $raw = $this->model->ListaComandos();
        $data = json_decode($raw, true);
        if (!is_array($data) || !isset($data['data']) || !is_array($data['data'])) {
            $data = array(
                'data' => array(
                    'lista' => array(),
                    'contador' => 0,
                ),
            );
        } else {
            if (!isset($data['data']['lista']) || !is_array($data['data']['lista'])) {
                $data['data']['lista'] = array();
            }
            if (!isset($data['data']['contador'])) {
                $data['data']['contador'] = count($data['data']['lista']);
            }
        }
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        die();
    }
    /*
    public function ListaComandos(){
        $arrayComandos = array(
            "id" => 1,
            "comando" => "Humedad",
            "hora_solicitud" => "2021-07-01 12:00:00",
            "hora_ejecucion" => "2021-07-01 12:00:00",
            "hora_validacion" => "2021-07-01 12:00:00",
        );
        //hace que el array se convierta en un objeto json
        echo json_encode($arrayComandos, JSON_UNESCAPED_UNICODE);
    }*/

    public function generarCardAnalytic(){
        $data = $this->model->generarComandos(8);
        $cards = json_decode($data);
        echo json_encode($cards, JSON_UNESCAPED_UNICODE);
    }

    public function TablaEstadoDispositivos(){
        $data = $this->model->ListaDispositivoEmpresa(61);
        $data = json_decode($data);
        $data = $data->data;
        $text = '';
        $obj = [];
        $text .="
            <table class='table table-hover table-bordered text-center'>
                <thead>
                    <tr>
                        <th>Equipo</th>
                        <th>Estado</th>
                        <th>Última conexión</th>
                    </tr>
                </thead>
            <tbody>
        ";
        foreach($data as $val){
            $evaluarEstado = evaluarEstado($val->ultima_fecha);
            $colorEstado = evaluarEstadoColor($val->ultima_fecha);
            $ultimaF = convertirFecha($val->ultima_fecha);
            array_push($obj, array('estado' => $evaluarEstado));
            //condicionar para evaluar imprimir solo si el estado es 'WAIT' o 'OFFLINE'
            if($evaluarEstado == 'WAIT' || $evaluarEstado == 'OFFLINE'){
                $text .= "
                <tr>
                    <td>{$val->nombre_contenedor}</td>
                    <td class='$colorEstado fw-bold'>{$evaluarEstado}</td>
                    <td>{$ultimaF}</td>
                </tr>";
            }
        }
        $text .= "</tbody>
                </table>";
        $res = array(
            'text' => $text,
            'estado' => $obj
        );
        echo json_encode($res, JSON_UNESCAPED_UNICODE);
    }
    

}