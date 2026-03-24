<?php

class Control extends Controller
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
        // aqui debe llegar todo los datos si es user 1 sino de acuedo a loq ue esta permitido 
		$id_user = $_SESSION['id_ztrack'];
        $this->views->getView($this, "index");

    }
    public function ProcesarModal($param){
        $dataControl="";
        if($param!=""){
            $matriz = explode("|", $param);
            $text1 = validarModal($matriz);
            //$testComandos =  $this->model->ComandosTest("866782048942516");
            $testComandos =  $this->model->ComandosOficial("866782048942516");
            $text_comanos =comandos_pendientes($testComandos);
            $text1 =$text1.$text_comanos; 
            $dataControl =array(
                "data"=>$text1,
                "matriz"=>$matriz,
                "comandos"=>$testComandos
            );
        }
        echo json_encode($dataControl, JSON_UNESCAPED_UNICODE);
    }
    //    trama = "Trama_Writeout(4,"+SP_Setpoint+",100)"
    //cambiar humedad
    public function ComandoHumedad($param){
        // "Trama_Writeout(0,"+fato_f+",100)"
        $dataControl="";
        if($param!=""){
                $comando = $param;
                $coman ="Trama_Writeout(4,".$param.",100)";
                $event ="Humidity level change to ".$comando." %";
                $dato=$comando;
                $tipo =6 ;

            $cadena = array(
                'imei'=>"866782048942516",
                //'estado' =>3,
                'user'=>"jhonvena",
                'tipo'=>$tipo,
                'dato'=>$dato,
                'evento' =>$event,
                'comando'=>$coman      
            );
            $dataControl =  $this->model->EnvioComando($cadena);
            $dataControl =array(
                "data"=>$dataControl,
                "mensaje"=>"loading ".$event
            );
        }
        echo json_encode($dataControl, JSON_UNESCAPED_UNICODE);
    }
    //    trama = "Temporizadores(0,"+SP_Setpoint+","+sp_sp_ethy1+")";
    public function ComandoHoras($param){
        // "Trama_Writeout(0,"+fato_f+",100)"
        $dataControl="";
        if($param!=""){
                $comando1 = $param;
                $comando = explode("|", $comando1);;
                $coman ="Temporizadores(0,".$comando[0].",".$comando[1].")";
                $event ="is being programmed to ".$comando[0]." hours";
                $dato=$comando[0];
                $tipo =5 ;
            $cadena = array(
                'imei'=>"866782048942516",
                //'estado' =>3,
                'user'=>"jhonvena",
                'tipo'=>$tipo,
                'dato'=>$dato,
                'evento' =>$event,
                'comando'=>$coman      
            );
            $dataControl =  $this->model->EnvioComando($cadena);
            $dataControl =array(
                "data"=>$dataControl,
                "mensaje"=>"loading ".$event
            );
        }
        echo json_encode($dataControl, JSON_UNESCAPED_UNICODE);
    }
    //DefrostOK
    public function DefrostOK($param){
        //trama = "Trama_Writeout(21,0,0)";
        $dataControl="";
        if($param!=""){
                $comando = $param;
                $coman ="Trama_Writeout(21,0,0)";
                $event =" ACTIVE DEFROST MODE";
                $dato=0;
                $tipo =0 ;
            $cadena = array(
                'imei'=>"866782048942516",
                //'estado' =>3,
                'user'=>"DEFROST",
                'tipo'=>$tipo,
                'dato'=>$dato,
                'evento' =>$event,
                'comando'=>$coman      
            );
            $dataControl =  $this->model->EnvioComando($cadena);
            $dataControl =array(
                "data"=>$dataControl,
                "mensaje"=>"loading ".$event
            );
        }
        echo json_encode($dataControl, JSON_UNESCAPED_UNICODE);
    }
    //AVLOK
    public function AVLOK($param){
        //trama = "Trama_Writeout(21,0,0)";
        $dataControl="";
        if($param!=""){
            if($param=="NO"){
                $event ="CLOSING VENTILATION ";
                $cadena = array(
                    'imei'=>"866782048942516",
                    //'estado' =>3,
                    'user'=>"jhonvena",
                    'tipo'=>12,
                    'dato'=>0,
                    'evento' =>$event,
                    'comando'=>"Trama_Writeout(9,0,1)"      
                );
                $dataControl =  $this->model->EnvioComando($cadena);
            }else{
                $event =" ACTIVATING FULL VENTILATION ";
                $cadena = array(
                    'imei'=>"866782048942516",
                    //'estado' =>3,
                    'user'=>"jhonvena",
                    'tipo'=>12,
                    'dato'=>1,
                    'evento' =>"ACTIVATE VENTILATION",
                    'comando'=>"Trama_Writeout(9,1,1)"      
                );
                $cadena1 = array(
                    'imei'=>"866782048942516",
                    //'estado' =>3,
                    'user'=>"jhonvena",
                    'tipo'=>9,
                    'dato'=>200,
                    'evento' =>$event,
                    'comando'=>"Trama_Writeout(5,200,1)"      
                );
                $dataControl1 =  $this->model->EnvioComando($cadena);
                sleep(1);
                $dataControl = $this->model->EnvioComando_libre($cadena1);
            }
            $dataControl =array(
                "data"=>$dataControl,
                "mensaje"=>"loading ".$event
            );
        }
        echo json_encode($dataControl, JSON_UNESCAPED_UNICODE);
    }
    //cambiar temperatura 
    public function ComandoTemperatura($param){
        // "Trama_Writeout(0,"+fato_f+",100)"
        $dataControl="";
        if($param!=""){
                $comando = $param;
                $coman = "Trama_Writeout(0,".pasar_celcius($comando).",100)";
                $event ="Temperature level change to ".$comando." F°";
                $dato=pasar_celcius($comando);
                $tipo =7 ;
            $cadena = array(
                'imei'=>"866782048942516",
                //'estado' =>3,
                'user'=>"jhonvena",
                'tipo'=>$tipo,
                'dato'=>$dato,
                'evento' =>$event,
                'comando'=>$coman      
            );
            $dataControl =  $this->model->EnvioComando($cadena);
            $dataControl =array(
                "data"=>$dataControl,
                "mensaje"=>"loading ".$event
            );
        }
        echo json_encode($dataControl, JSON_UNESCAPED_UNICODE);
    }

    //COMANDO DE CO2
    public function CO2Comando($param){
        //trama = "Trama_Writeout(3,"+SP_Setpoint+",100)";
        //trama2 ="Trama_Writeout(9,2,1)";
        if($param!=""){
        $comando = $param;
        $matriz = explode("|", $comando);
        $coman = "Trama_Writeout(3,".$comando.",100)";
        $event ="co2 limit level change to ".$comando." %";
        $dato=$comando;
        $tipo =3 ;
        $cadena1 = array(
            'imei'=>"866782048942516",
            //'estado' =>3,
            'user'=>"jhonvena",
            'tipo'=>12,
            'dato'=>2,
            'evento' =>"activating afamplus process",
            'comando'=>"Trama_Writeout(9,2,1)"    
        );
        $cadena2 = array(
            'imei'=>"866782048942516",
            //'estado' =>3,
            'user'=>"jhonvena",
            'tipo'=>$tipo,
            'dato'=>$dato,
            'evento' =>$event,
            'comando'=>$coman      
        );
        $dataControl = $this->model->EnvioComando_libre($cadena1);
        sleep(1);
        $dataControl1 = $this->model->EnvioComando_libre($cadena2);
        $dataControl1 =array(
            "data"=>$dataControl1,
            "mensaje"=>"loading ".$event
            );
        }
        echo json_encode($dataControl1, JSON_UNESCAPED_UNICODE);
    }

    /**
     * SP O2 (atmósfera controlada). Trama 14: verificar con documentación del equipo.
     */
    public function ComandoO2($param){
        $dataControl1 = null;
        if($param!=""){
            $comando = $param;
            $coman = "Trama_Writeout(14,".$comando.",100)";
            $event ="o2 limit level change to ".$comando." %";
            $dato=$comando;
            $tipo =10 ;
            $cadena1 = array(
                'imei'=>"866782048942516",
                'user'=>"jhonvena",
                'tipo'=>12,
                'dato'=>2,
                'evento' =>"activating afamplus process",
                'comando'=>"Trama_Writeout(9,2,1)"
            );
            $cadena2 = array(
                'imei'=>"866782048942516",
                'user'=>"jhonvena",
                'tipo'=>$tipo,
                'dato'=>$dato,
                'evento' =>$event,
                'comando'=>$coman
            );
            $this->model->EnvioComando_libre($cadena1);
            sleep(1);
            $dataControl1 = $this->model->EnvioComando_libre($cadena2);
            $dataControl1 =array(
                "data"=>$dataControl1,
                "mensaje"=>"loading ".$event
            );
        }
        if ($dataControl1 === null) {
            echo json_encode(['data' => null, 'mensaje' => ''], JSON_UNESCAPED_UNICODE);
            return;
        }
        echo json_encode($dataControl1, JSON_UNESCAPED_UNICODE);
    }
    //ComandoEthy
    public function ComandoEthy($param){
        $dataControl="";
        if($param!=""){
                $comando = $param;
                $coman = "SP_ETILENO(".$comando .")";
                $event ="ethylene level change to ".$comando." ppm";
                $dato=$comando;
                $tipo =4 ;

            $cadena = array(
                'imei'=>"866782048942516",
                //'estado' =>3,
                'user'=>"jhonvena",
                'tipo'=>$tipo,
                'dato'=>$dato,
                'evento' =>$event,
                'comando'=>$coman      
            );
            $dataControl =  $this->model->EnvioComando($cadena);
            $dataControl =array(
                "data"=>$dataControl,
                "mensaje"=>"loading ".$event
            );
        }
        echo json_encode($dataControl, JSON_UNESCAPED_UNICODE);
    }
    public function ComandoPower($param){
        $dataControl="";
        if($param!=""){
            $comando = $param;
            if($comando=="ON"){
                $coman = "Trama_Writeout(29,1,1)";
                $event ="turn on the reefer machine";
                $dato=1;
                $tipo =1 ;
            }else {
                $coman = "Trama_Writeout(29,0,1)";
                $event ="reefer machine shutdown";
                $dato=0;
                $tipo =2 ;
            }
            $cadena = array(
                'imei'=>"866782048942516",
                //'estado' =>3,
                'user'=>"jhonvena",
                'tipo'=>$tipo,
                'dato'=>$dato,
                // comando para proceso integral =>command for integral process 
                'evento' =>$event,
                'comando'=>$coman      
            );
            $dataControl =  $this->model->EnvioComando($cadena);
            $dataControl =array(
                "data"=>$dataControl,
                "mensaje"=>"loading ".$event
            );
        }
        echo json_encode($dataControl, JSON_UNESCAPED_UNICODE);

    }
    //ComandoTest
    public function ComandoTest($param){
        if($param!=""){
        $comando = $param;

        $cadena = array(
            'imei'=>"866782048942516",
            'estado' =>0,
            'evento' =>"command tipo 1",
            'comando'=>$comando      
        );
        $dataControl =  $this->model->EnvioComando($cadena);
        //$resultadoMadurador = json_decode($dataMadurador);
        //$resultadoMadurador = $resultadoMadurador->data;
        //echo json_encode($dataControl);
    }
    //echo json_encode($cadena);
    echo json_encode($dataControl, JSON_UNESCAPED_UNICODE);


    }
    //Comandoco2Test
    public function Comandoco2Test($param){
        if($param!=""){
        $comando = $param;
        $matriz = explode("|", $comando);

        $cadena1 = array(
            'imei'=>"866782048942516",
            'estado' =>0,
            'evento' =>"command tipo 2",
            'comando'=>$matriz[0]      
        );
        $cadena2 = array(
            'imei'=>"866782048942516",
            'estado' =>0,
            'evento' =>"command tipo 2",
            'comando'=>$matriz[1]      
        );
        $dataControl = $this->model->EnvioComando_libre($cadena2);
        sleep(1);
        $dataControl1 = $this->model->EnvioComando_libre($cadena1);

        }
        echo json_encode($dataControl1, JSON_UNESCAPED_UNICODE);
        //echo json_encode($matriz[1], JSON_UNESCAPED_UNICODE);

    }

    public function Comando($param){
        if($param!=""){
        $comando = $param;

        $text ="viene de afuera".$comando;
        $cadena = array(
            'imei'=>"866782048942516",
            //'estado' =>0,
            'evento' =>"command tipo 1",
            'comando'=>$comando      
        );
        $dataControl =  $this->model->EnvioComando($cadena);
        //$resultadoMadurador = json_decode($dataMadurador);
        //$resultadoMadurador = $resultadoMadurador->data;
        //echo json_encode($dataControl);
    }
    //echo json_encode($cadena);
    echo json_encode($dataControl, JSON_UNESCAPED_UNICODE);


    }
    //Comandoco2
    public function Comandoco2($param){
        if($param!=""){
        $comando = $param;
        $matriz = explode("|", $comando);

        $cadena1 = array(
            'imei'=>"866782048942516",
            //'estado' =>0,
            // comando para proceso integral =>command for integral process 
            'evento' =>"command tipo 2",
            'comando'=>$matriz[0]      
        );
        $cadena2 = array(
            'imei'=>"866782048942516",
            //'estado' =>0,
            'evento' =>"command tipo 2",
            'comando'=>$matriz[1]      
        );
        $dataControl = $this->model->EnvioComando_libre($cadena2);
        sleep(1);
        $dataControl1 = $this->model->EnvioComando_libre($cadena1);

        }
        echo json_encode($dataControl1, JSON_UNESCAPED_UNICODE);
        //echo json_encode($matriz[1], JSON_UNESCAPED_UNICODE);

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
        $datosW =$_SESSION['data'] ;
        $resultado1 = array('data'=>$datosW);
        $VerificarLive = $this->model->VerificarLive($resultado1);
        $Verificar = json_decode($VerificarLive);
        $Verificar = $Verificar->data;
        //$resultado = $VerificarLive->data;
        /*
        $text ="";
        $datosW =$_SESSION['data'] ;
        foreach ($datosW as $dat) {
            $text.=$dat->telemetria_id.",";
        }
        */
        $d =0 ;
        foreach ($datosW as $clave => $valor) {
            // $array[3] se actualizará con cada valor de $array...
            //echo "{$clave} => {$valor} ";
            //print_r($array);
            foreach ($Verificar as $dat) {
                if($valor->telemetria_id==$dat->telemetria_id){
                    //va haber reemplazo en session en la fecha pa continuar actualizando
                    $_SESSION['data'][$clave]->ultima_fecha =$dat->ultima_fecha ;
                    $dat->ultima_fecha = fechaPro($dat->ultima_fecha);
                    //echo $dat->ultima_fecha;
                    $dat->temp_supply_1 =tempNormal($dat->temp_supply_1) ; 
                    $dat->return_air =tempNormal($dat->return_air) ; 
                    $dat->set_point =tempNormal($dat->set_point);
                    $dat->relative_humidity =porNormal($dat->relative_humidity) ; 
                    $dat->humidity_set_point =porNormal($dat->humidity_set_point) ; 
                    $dat->evaporation_coil =tempNormal($dat->evaporation_coil) ; 
                    $dat->cargo_1_temp =tempNormal($dat->cargo_1_temp) ; 
                    $dat->cargo_2_temp =tempNormal($dat->cargo_2_temp) ; 
                    $dat->cargo_3_temp =tempNormal($dat->cargo_3_temp) ; 
                    $dat->cargo_4_temp =tempNormal($dat->cargo_4_temp) ; 
                    $d++;
                }
            }
        }        
        //echo json_encode($_SESSION['data'][0]->telemetria_id, JSON_UNESCAPED_UNICODE);
        echo json_encode($Verificar , JSON_UNESCAPED_UNICODE);
        die();
    } 
    public function ControlContent(){
        $devices = $this->dispositivosControlDesdeSesion();
        $devices = array_slice($devices, 0, 1);
        $text = "";
        $dataJson = [];

        foreach ($devices as $val) {
            $tid = isset($val->telemetria_id) ? (int) $val->telemetria_id : 0;
            $sid = $tid > 0 ? (string) $tid : 'demo';
            $nombre = isset($val->nombre_contenedor) ? htmlspecialchars((string) $val->nombre_contenedor, ENT_QUOTES, 'UTF-8') : 'Sector';
            $ultima = isset($val->ultima_fecha) ? fechaPro($val->ultima_fecha) : '—';

            $spTmp = $this->controlSpTemperatura($val);
            $spHum = $this->controlSpHumedad($val);
            $spCo2 = $this->controlSpCo2($val);
            $spO2 = $this->controlSpO2($val);

            $readT = $this->controlLecturaTemperatura($val);
            $readH = isset($val->relative_humidity) ? porcentaje($val->relative_humidity) : 'NA';
            $readCo2 = isset($val->co2_reading) ? porcentaje($val->co2_reading) : 'NA';
            $readO2 = isset($val->o2_reading) ? porcentaje($val->o2_reading) : 'NA';

            $ps = isset($val->power_state) ? (int) $val->power_state : 0;
            $avlRaw = isset($val->avl) ? $val->avl : 0;
            $avlNum = is_numeric($avlRaw) ? (int) $avlRaw : 0;

            $dataJson[] = [
                'telemetria_id' => $tid,
                'power_state' => $ps,
                'avl' => $avlNum,
            ];

            $text .= "<div class='row control-sector-panel g-3 mb-4 pb-4 border-bottom' data-telemetria-id='{$sid}'>
                <div class='col-12'>
                    <h4 class='fw-bold text-center mb-1'>{$nombre}</h4>
                    <p class='text-center text-muted small mb-0'>ID telemetría: {$sid} · Última actividad: {$ultima}</p>
                </div>
                <div class='col-12'>
                    <div class='d-flex flex-wrap justify-content-center align-items-center gap-3'>
                        <div class='btn btn-group border-0'>
                            <button type='button' class='btn btn-primary border-0' disabled>°F</button>
                            <button type='button' class='btn disabled'>°C</button>
                        </div>
                    </div>
                </div>
                <div class='col-12 col-md-6 col-lg-3 px-2'>
                    <div class='text-center border rounded p-2 h-100'>
                        <i class='bi bi-power fs-3' id='power_icon_{$sid}'></i>
                        <h6 class='text-uppercase'>Power</h6>
                        <select class='form-select' id='select_power_{$sid}' data-sector='{$sid}'>
                            <option value='1'>ON</option>
                            <option value='0'>OFF</option>
                        </select>
                        <div class='mt-2' id='btnPower_{$sid}'></div>
                    </div>
                </div>
                <div class='col-12 col-md-6 col-lg-3 px-2'>
                    <div class='text-center border rounded p-2 h-100'>
                        <h6 class='text-uppercase'>Ventilation</h6>
                        <select class='form-select' id='select_avl_ok_{$sid}' data-sector='{$sid}'>
                            <option value='0'>OFF</option>
                            <option value='1'>ON</option>
                        </select>
                        <div class='mt-2' id='btnAVL_{$sid}'></div>
                    </div>
                </div>
                <div class='col-12 col-md-6 col-lg-4 px-2'>
                    <div class='border rounded p-3 h-100'>
                        <h6 class='text-uppercase text-center mb-3'>Lecturas</h6>
                        <div class='row text-center small'>
                            <div class='col-6 col-md-3 mb-2'><span class='text-muted d-block'>Temp. retorno</span><strong>{$readT} °F</strong></div>
                            <div class='col-6 col-md-3 mb-2'><span class='text-muted d-block'>Humedad</span><strong>{$readH} %</strong></div>
                            <div class='col-6 col-md-3 mb-2'><span class='text-muted d-block'>CO2</span><strong>{$readCo2} %</strong></div>
                            <div class='col-6 col-md-3 mb-2'><span class='text-muted d-block'>O2</span><strong>{$readO2} %</strong></div>
                        </div>
                    </div>
                </div>
                <div class='col-12 col-md-6 col-lg-2 px-2'>
                    <div class='text-center border rounded p-2 h-100'>
                        <h6 class='text-uppercase'>Defrost</h6>
                        <button type='button' class='btn btn-success btn-sm text-uppercase' onclick='defrost_p_ok()'>Active</button>
                    </div>
                </div>
                <div class='col-12 col-md-4 col-lg-3 px-2'>
                    <div class='text-center'>
                        <i class='bi bi-thermometer-half icon-params'></i>
                        <h6 class='text-uppercase'>SP Temperature</h6>
                        <input type='hidden' id='tmp_SP_a_{$sid}' value='{$spTmp}'>
                        <input type='text' class='text-center' id='tmp_SP_{$sid}' value='{$spTmp}'>
                        <div class='mt-2' id='btnProcesarTMP_{$sid}'></div>
                    </div>
                </div>
                <div class='col-12 col-md-4 col-lg-3 px-2'>
                    <div class='text-center'>
                        <i class='bi bi-moisture icon-params'></i>
                        <h6 class='text-uppercase'>SP Humidity</h6>
                        <input type='hidden' id='humidity_SP_a_{$sid}' value='{$spHum}'>
                        <input type='text' class='text-center' id='humidity_SP_{$sid}' value='{$spHum}'>
                        <div class='mt-2' id='btnProcesarHumidity_{$sid}'></div>
                    </div>
                </div>
                <div class='col-12 col-md-4 col-lg-3 px-2'>
                    <div class='text-center'>
                        <svg class='icon-params-co2 icon px-1'><use xlink:href='sprite.svg#co2'></use></svg>
                        <h6 class='text-uppercase'>SP CO2</h6>
                        <input type='hidden' id='co2_SP_a_{$sid}' value='{$spCo2}'>
                        <input type='text' class='text-center' id='co2_SP_{$sid}' value='{$spCo2}'>
                        <div class='mt-2' id='btnProcesarCO2_{$sid}'></div>
                    </div>
                </div>
                <div class='col-12 col-md-4 col-lg-3 px-2'>
                    <div class='text-center'>
                        <i class='bi bi-circle-half icon-params'></i>
                        <h6 class='text-uppercase'>SP O2</h6>
                        <input type='hidden' id='o2_SP_a_{$sid}' value='{$spO2}'>
                        <input type='text' class='text-center' id='o2_SP_{$sid}' value='{$spO2}'>
                        <div class='mt-2' id='btnProcesarO2_{$sid}'></div>
                    </div>
                </div>
            </div>";
        }

        $data1 = [
            'data' => $dataJson,
            'text' => $text,
        ];
        echo json_encode($data1, JSON_UNESCAPED_UNICODE);
        die();
    }

    private function dispositivosControlDesdeSesion(){
        if (!empty($_SESSION['data']) && is_array($_SESSION['data'])) {
            return $_SESSION['data'];
        }
        $d = new stdClass();
        $d->telemetria_id = 0;
        $d->nombre_contenedor = 'Vista previa (inicie sesión con equipos)';
        $d->ultima_fecha = date('Y-m-d\TH:i:s');
        $d->power_state = 0;
        $d->avl = 0;
        $d->set_point = 2;
        $d->humidity_set_point = 85;
        $d->set_point_co2 = 5;
        $d->set_point_o2 = 18;
        $d->return_air = 2.5;
        $d->relative_humidity = 90;
        $d->co2_reading = 5;
        $d->o2_reading = 21;
        return [$d];
    }

    private function controlSpCo2($val){
        if (!isset($val->set_point_co2) || !is_numeric($val->set_point_co2)) {
            return 'NA';
        }
        return validarco2((float) $val->set_point_co2);
    }

    private function controlSpO2($val){
        if (!isset($val->set_point_o2) || !is_numeric($val->set_point_o2)) {
            return 'NA';
        }
        $n = (float) $val->set_point_o2;
        if ($n < 0 || $n > 100) {
            return 'NA';
        }
        return $n;
    }

    private function controlSpHumedad($val){
        if (!isset($val->humidity_set_point) || !is_numeric($val->humidity_set_point)) {
            return 'NA';
        }
        return porNormal((float) $val->humidity_set_point);
    }

    private function controlSpTemperatura($val){
        if (!isset($val->set_point) || !is_numeric($val->set_point)) {
            return 'NA';
        }
        return tempNormal((float) $val->set_point);
    }

    private function controlLecturaTemperatura($val){
        if (!isset($val->return_air) || !is_numeric($val->return_air)) {
            return 'NA';
        }
        return tempNormal((float) $val->return_air);
    }
}

