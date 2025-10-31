<?php

class Control extends Controller
{
    public function __construct()
    {
        session_start();
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
        $data = $this->model->ListaDispositivoEmpresa($_SESSION['empresa_id']);
        $data = json_decode($data);
        $data = $data -> data;
        $text = "";
        //$data2 = [];

        foreach($data as $val){
            $ultima_fecha_f=fechaPro($val->ultima_fecha);

            $text = "<div class='col-12 col-lg-12'>
                    <div class='d-flex justify-content-center align-items-center gap-2'>
                        <!-- Última hora de actividad -->
                        <div
                            <h6 id=''>{$ultima_fecha_f}</h6>
                        </div>
                        <div class='btn btn-group border-0'>
                            <button type='button' class='btn btn-primary border-0'>°F</button>
                            <button type='button' class='btn disabled'>°C</button>
                        </div>
                        <!--
                        <div class='text-center dropdown'>
                            <button class='btn btn-secondary' type='button' id='dropdownMenuButton' data-bs-toggle='dropdown' aria-expanded='false'>
                            <i class='ri-temp-cold-line'></i>
                            </button>
                            <ul class='dropdown-menu' aria-labelledby='dropdownMenuButton'>
                                <li><a class='dropdown-item' href='#'>°F</a></li>
                                <li><a class='dropdown-item' href='#'>°C</a></li>
                            </ul>
                        </div>-->
                    </div>
                </div>
                <!-- ANIMACION CSS DE TRES PUNTOS
                <div id='loadingSpinner' class='spinner'>
                    <div class='dot1'></div>
                    <div class='dot2'></div>
                    <div class='dot3'></div>
                </div>-->
                <div class='col-12 col-md-4 col-lg-3 px-2'>
                    <div class='text-center'>
                        <i class='bi bi-power fs-1 ' id='power_icon'></i>
                            <h5 class='text-uppercase'>Power</h5>
                                <select class='form-select' aria-label='Default select example' id='select_power' name='select_power'>
                                    <option value=1 >ON</option>
                                    <option value=0 selected>OFF</option>
                                </select>
                        <div class='mt-2' id='btnPower'></div>


                    </div>
                </div>

                <div class='col-12 col-md-4 col-lg-3 px-2'>
                    <div class='text-center'>
                        <svg version='1.0' xmlns='http://www.w3.org/2000/svg'
                         class='icon-params px-1 btn' ondblclick='apertureModal()' ontouchstart='apertureModal()' viewBox='0 0 93.000000 76.000000'
                            preserveAspectRatio='xMidYMid meet'>

                            <g transform='translate(0.000000,76.000000) scale(0.100000,-0.100000)'
                            fill='#000000' stroke='none'>
                            <path d='M287 654 c-4 -4 -7 -45 -7 -91 0 -76 2 -83 20 -83 17 0 20 7 20 53 0
                            78 7 83 128 79 l102 -3 0 -222 0 -222 -102 3 c-57 2 -109 5 -115 7 -7 2 -13
                            18 -13 35 0 23 -4 30 -20 30 -18 0 -20 -7 -20 -54 0 -30 5 -58 11 -61 5 -4 75
                            -9 154 -12 l144 -5 6 23 c3 13 4 137 3 274 l-3 250 -151 3 c-82 1 -153 -1
                            -157 -4z'/>
                            <path d='M103 527 c-29 -12 -53 -26 -53 -30 0 -5 19 -36 42 -69 l42 -60 12 34
                            c6 18 17 32 25 31 8 -1 44 -7 80 -14 60 -11 73 -11 140 9 41 12 90 23 109 24
                            27 2 35 7 35 23 0 17 -6 20 -43 18 -24 -1 -67 -10 -95 -19 -62 -21 -140 -21
                            -190 -1 -34 15 -35 17 -27 46 11 38 -1 39 -77 8z'/>
                            <path d='M620 471 c0 -12 11 -23 31 -30 41 -14 39 -14 39 9 0 13 -10 24 -26
                            30 -36 14 -44 13 -44 -9z'/>
                            <path d='M280 355 c0 -28 4 -35 20 -35 16 0 20 7 20 35 0 28 -4 35 -20 35 -16
                            0 -20 -7 -20 -35z'/>
                            <path d='M734 344 c-10 -43 -10 -43 -64 -23 l-50 19 0 -23 c0 -18 9 -26 43
                            -39 35 -13 42 -19 42 -42 -1 -14 3 -26 7 -26 13 0 108 50 108 56 0 5 -47 69
                            -71 98 -4 5 -11 -4 -15 -20z'/>
                            <path d='M388 317 c-78 -22 -105 -21 -205 9 -19 5 -23 2 -23 -14 0 -15 12 -24
                            47 -36 59 -20 125 -20 192 0 29 8 72 19 95 22 26 5 41 13 41 22 0 23 -59 21
                            -147 -3z'/>
                            </g>
                        </svg>
                        <h5 class='text-uppercase'>Ventilation</h5>
                        <select class='form-select' id='select_avl_ok' name='select_avl_ok'>
                            <option value='0'>OFF</option>
                            <option value='1'>ON</option>
                        </select>   
                        <div class='mt-2' id='btnAVL'></div>

                    </div>
                </div>
                <div class='col-12 col-md-4 col-lg-3 px-2'>
                    <div class='text-center'>
                        <svg version='1.0' xmlns='http://www.w3.org/2000/svg' class='icon-params icon px-1 btn' ondblclick='ethyModal()' ontouchstart='ethyModal()' viewBox='0 0 306 236' preserveAspectRatio='xMidYMid meet'>
                            <g transform='translate(0,236) scale(0.1,-0.1)' stroke='none'>
                                <path fill='gray' d='M472 2126 c-62 -29 -102 -68 -133 -132 -28 -57 -31 -71 -27 -127 6 -73 25 -114 76 -165 59 -59 105 -77 194 -77 l77 0 40 -63 c23 -34 41 -67 41 -72 0 -5 -24 -33 -54 -63 -80 -80 -99 -123 -104 -238 -4 -87 -2 -101 21 -150 15 -30 51 -78 81 -108 l55 -55 -31 -55 c-46 -83 -50 -86 -126 -87 -84 -2 -143 -27 -199 -85 -53 -56 -73 -103 -73 -176 0 -170 162 -295 326 -254 190 48 258 277 125 424 l-35 39 39 75 39 74 73 -6 c83 -8 158 6 218 40 46 26 121 105 147 153 l17 32 284 0 285 0 47 -64 c51 -70 91 -102 170 -137 48 -22 102 -26 210 -17 27 3 33 -3 73 -70 l44 -72 -46 -51 c-57 -64 -76 -122 -66 -200 8 -61 25 -94 74 -147 50 -55 101 -76 181 -76 80 1 124 16 179 64 177 156 58 450 -181 450 -61 0 -64 1 -90 38 -16 21 -36 54 -46 73 l-18 34 55 59 c72 76 96 139 96 245 0 70 -4 86 -35 149 -22 44 -53 86 -81 111 l-45 39 43 71 43 71 61 0 c86 0 150 25 204 80 130 129 95 333 -72 421 -50 26 -167 26 -223 0 -48 -22 -96 -71 -126 -129 -17 -34 -21 -58 -20 -114 2 -77 14 -109 68 -176 l35 -43 -41 -68 -40 -68 -98 1 c-85 0 -105 -3 -153 -25 -67 -31 -116 -73 -156 -131 -16 -24 -33 -46 -39 -50 -5 -4 -137 -7 -293 -7 -306 1 -296 -1 -326 58 -21 40 -97 106 -152 132 -40 18 -67 23 -147 23 l-98 1 -44 69 -43 69 44 63 c48 71 59 111 51 189 -8 82 -62 165 -130 201 -45 23 -182 29 -225 10z'/>
                            </g>
                        </svg>
                        <h5 class='text-uppercase'>SP Ethylene</h5>
                        <input id='ethylene_SP_a'  type='hidden' value='{$val->sp_ethyleno}' name='ethylene_SP_a'>
                        <input id='ethylene_SP' class='text-center' type='text' value='{$val->sp_ethyleno}' name='ethylene_SP'>
                        <div class='mt-2' id='btnProcesarEthy'></div>
                    </div>
                </div>
                <div class='col-12 col-md-4 col-lg-3 px-2'>
                    <div class='text-center '>
                        <svg class='icon-params-co2 icon px-1 btn'>
                            <use xlink:href='sprite.svg#co2'></use>
                        </svg>
                        <h5 class='text-uppercase'>SP CO2</h5>
                        <input id='co2_SP_a'  type='hidden' value='".validarco2($val->set_point_co2)."' name='co2_SP_a'>
                        <input id='co2_SP' class='text-center' type='text' value='".validarco2($val->set_point_co2)."' name='co2_SP'>
                        <div class='mt-2' id='btnProcesarCO2'></div>
                    </div>
                </div> 
                <div class='col-12 col-md-4 col-lg-3 px-2'>
                    <div class='text-center'>
                        <i class='bi bi-thermometer-half icon-params' ></i>
                        <h5 class='text-uppercase'>SP Temperature</h5>
                        <input id='tmp_SP_a' type='hidden'  value='".tempNormal($val->set_point)."' name='tmp_SP_a'>
                        <input id='tmp_SP' type='text' class='text-center' value='".tempNormal($val->set_point)."' name='tmp_SP'>
                        <div class='mt-2' id='btnProcesarTMP'></div>
                    </div>
                </div>
                <div class='col-12 col-md-4 col-lg-3 px-2'>
                    <div class='text-center'>
                        <svg version='1.0' xmlns='http://www.w3.org/2000/svg' class='icon-params icon px-1' ondblclick='humidityModal()' ontouchstart='humidityModal()' viewBox='0 0 74.000000 74.000000' preserveAspectRatio='xMidYMid meet'>
                            <g transform='translate(0.000000,74.000000) scale(0.100000,-0.100000)'
                            fill='#0070fc' stroke='none'>
                            <path d='M270 635 c-61 -20 -116 -67 -146 -128 -23 -44 -26 -60 -22 -127 4
                            -96 38 -159 112 -204 54 -34 156 -48 195 -27 26 15 30 57 4 46 -68 -26 -146
                            -11 -203 41 -134 121 -61 347 116 362 116 10 203 -58 229 -178 9 -40 29 -73
                            38 -63 14 13 6 90 -14 133 -55 123 -187 185 -309 145z'/>
                            <path d='M315 551 c-17 -4 -43 -16 -58 -25 -55 -37 -92 -133 -64 -170 19 -26
                            37 -12 37 30 0 84 57 130 146 119 l53 -6 -33 -30 c-19 -16 -41 -29 -50 -29
                            -26 0 -50 -45 -36 -69 19 -36 80 -22 80 18 0 9 23 39 52 68 40 40 49 56 41 64
                            -9 9 -16 9 -28 -1 -10 -8 -15 -9 -15 -2 0 25 -76 45 -125 33z'/>
                            <path d='M460 465 c-6 -8 -8 -20 -4 -27 5 -7 11 -31 14 -53 3 -27 10 -40 20
                            -40 21 0 25 67 5 104 -18 36 -18 36 -35 16z'/>
                            <path d='M516 308 c-65 -103 -69 -122 -36 -175 54 -89 194 -21 161 79 -11 33
                            -78 148 -87 148 -2 0 -19 -24 -38 -52z m81 -106 c7 -31 -7 -56 -36 -60 -51 -8
                            -68 53 -29 103 l22 28 18 -23 c10 -12 21 -34 25 -48z'/>
                            <path d='M530 210 c0 -12 35 -31 44 -23 3 3 -2 12 -12 19 -20 16 -32 17 -32 4z'/>
                            </g>
                        </svg>
                        <h5 class='text-uppercase'>SP Humidity</h5>
                        <input id='humidity_SP_a'  type='hidden' value='{$val->humidity_set_point}' name='humidity_SP_a'>
                        <input id='humidity_SP' class='text-center' type='text' value='{$val->humidity_set_point}' name='humidity_SP'>
                        <div class='mt-2' id='btnProcesarHumidity'></div>
                    </div>
                </div>
                <div class='col-12 col-md-4 col-lg-3 px-2'>
                    <div class='text-center'>
                        <svg version='1.0' xmlns='http://www.w3.org/2000/svg'
                            class='icon-params px-1 btn' ondblclick='injectionModal()' ontouchstart='injectionModal()' viewBox='0 0 64.000000 64.000000'
                            preserveAspectRatio='xMidYMid meet'>

                            <g transform='translate(0.000000,64.000000) scale(0.100000,-0.100000)'
                            fill='#000000' stroke='none'>
                            <path d='M275 579 c-40 -12 -58 -28 -84 -69 -38 -62 -21 -148 38 -191 46 -34
                            41 -53 -11 -45 -49 7 -48 7 -145 0 l-73 -6 0 -99 0 -99 68 -1 c112 0 422 -1
                            500 0 l72 1 0 99 0 99 -72 6 c-98 7 -97 7 -145 0 -52 -7 -58 11 -15 43 34 25
                            62 79 62 119 0 41 -35 107 -67 126 -37 22 -91 29 -128 17z m130 -52 c45 -45
                            53 -92 26 -145 -52 -102 -193 -93 -230 14 -18 52 -9 88 34 131 29 29 39 33 85
                            33 46 0 56 -4 85 -33z m-45 -247 c0 -5 -18 -10 -40 -10 -22 0 -40 5 -40 10 0
                            6 18 10 40 10 22 0 40 -4 40 -10z m-190 -110 c0 -53 -4 -90 -10 -90 -6 0 -10
                            37 -10 90 0 53 4 90 10 90 6 0 10 -37 10 -90z m324 41 c7 -55 -2 -131 -15
                            -131 -5 0 -9 11 -9 24 0 14 -4 28 -10 31 -5 3 -10 -3 -10 -14 0 -20 -5 -21
                            -130 -21 l-130 0 0 70 0 70 130 0 c123 0 130 -1 130 -20 0 -11 5 -20 10 -20 6
                            0 10 14 10 30 0 48 17 35 24 -19z m-364 -41 l0 -70 -55 0 -55 0 0 70 0 70 55
                            0 55 0 0 -70z m490 0 l0 -70 -55 0 -55 0 0 70 0 70 55 0 55 0 0 -70z'/>
                            <path d='M274 531 c-18 -11 -39 -30 -48 -42 -18 -26 -21 -59 -6 -59 6 0 10 7
                            10 16 0 27 56 74 88 74 36 0 78 -31 86 -65 4 -14 11 -25 17 -25 15 0 2 50 -19
                            74 -19 21 -65 46 -84 46 -7 0 -27 -9 -44 -19z'/>
                            <path d='M309 493 c0 -4 0 -27 1 -50 1 -27 -3 -43 -10 -43 -6 0 -10 -12 -8
                            -27 2 -22 8 -28 28 -28 20 0 26 6 28 28 2 15 -2 27 -8 27 -7 0 -10 17 -9 47 2
                            30 -1 48 -9 51 -7 2 -12 0 -13 -5z m21 -123 c0 -5 -4 -10 -10 -10 -5 0 -10 5
                            -10 10 0 6 5 10 10 10 6 0 10 -4 10 -10z'/>
                            </g>
                        </svg>
                        <h5 class='text-uppercase'>I. Hours</h5>
                        <input id='i_hours_a'  type='hidden' value='{$val->ripener_prueba}' name='i_hours_a'>      
                        <input id='i_hours' class='text-center' type='text' value='{$val->ripener_prueba}' name='i_hours'>
                        <div class='mt-2' id='btnProcesarIHours'></div>
                    </div>
                </div>
                
                <div class='col-12 col-md-4 col-lg-3 px-2'>
                    <div class='text-center'>
                        <svg version='1.0' xmlns='http://www.w3.org/2000/svg' class='icon-params px-1 btn'
                             viewBox='0 0 108.000000 80.000000'
                            preserveAspectRatio='xMidYMid meet'>

                            <g transform='translate(0.000000,80.000000) scale(0.100000,-0.100000)'
                            fill='#abddf1' stroke='none'>
                            <path d='M309 766 c-52 -12 -109 -28 -125 -34 -37 -16 -124 -140 -124 -177 0
                            -15 14 -68 31 -118 l30 -91 -25 -19 c-14 -10 -37 -44 -51 -75 l-26 -57 21 -15
                            c12 -8 44 -34 70 -58 56 -50 117 -72 200 -72 83 0 122 9 118 29 -5 24 -51 42
                            -126 49 -37 4 -67 11 -67 16 0 6 15 18 33 29 26 16 38 17 63 8 22 -8 43 -7 75
                            1 33 9 72 9 166 0 170 -17 174 -16 257 23 72 34 76 35 111 21 19 -8 58 -17 85
                            -20 45 -5 46 -5 15 4 -19 6 -59 17 -89 26 l-54 16 -80 -38 -79 -37 -107 12
                            c-130 14 -216 14 -241 1 -13 -7 -28 -7 -46 0 -35 14 -75 5 -103 -21 -32 -30
                            -13 -45 62 -51 61 -6 117 -24 117 -39 0 -12 -107 -22 -162 -15 -63 9 -122 38
                            -144 72 -9 14 -32 31 -50 39 -19 8 -34 18 -33 22 1 20 56 107 76 120 20 14 23
                            12 38 -16 14 -28 20 -31 63 -30 26 0 70 4 97 8 43 6 53 4 70 -14 17 -18 42
                            -23 161 -34 155 -15 175 -12 267 37 30 16 50 34 53 49 8 31 29 29 108 -10 36
                            -19 66 -30 66 -25 0 5 -38 27 -85 49 l-85 40 0 47 c-1 55 -9 157 -15 189 -4
                            19 -14 22 -81 28 -71 7 -181 2 -229 -10 -16 -4 -27 8 -59 65 -58 103 -54 101
                            -167 76z m151 -65 c22 -38 40 -73 40 -78 0 -5 -29 -20 -65 -32 -36 -13 -68
                            -28 -72 -35 -4 -6 -10 -33 -13 -61 -5 -48 -6 -50 -38 -53 -32 -3 -34 -1 -66
                            70 -45 98 -80 197 -73 205 9 9 187 52 219 52 23 1 33 -9 68 -68z m-280 -66 c7
                            -22 30 -78 51 -124 l39 -84 -22 -36 c-32 -55 -68 -101 -77 -101 -11 0 -75 160
                            -90 227 -14 55 -12 61 48 141 27 37 33 34 51 -23z m588 -15 c62 0 60 -8 -8
                            -35 -36 -13 -74 -17 -185 -17 -77 1 -142 2 -145 2 -3 1 20 11 50 22 61 22 160
                            38 204 32 16 -2 54 -4 84 -4z m76 -163 c4 -75 2 -125 -4 -137 -10 -19 -60 -49
                            -115 -70 l-25 -9 4 156 3 156 59 22 c39 13 61 17 65 10 4 -5 9 -63 13 -128z
                            m-159 -59 c0 -81 -4 -150 -8 -154 -9 -9 -278 15 -290 25 -11 11 -31 225 -23
                            255 l7 27 157 -3 157 -3 0 -147z m-331 -5 c14 -89 13 -91 -58 -98 -98 -11 -99
                            -9 -51 65 35 55 47 66 72 68 28 2 31 -1 37 -35z'/>
                            </g>
                        </svg>
                        <h5 class='text-uppercase'>Defrost</h5>
                        <button type='button' class='btn btn-success text-uppercase' onclick='defrost_p_ok()'>Active</button>
                    </div>
                </div>";
        }
        $data1 = array(
            'data'=> $data,
            'text' => $text
        );
        echo json_encode($data1, JSON_UNESCAPED_UNICODE);
        die();
        
    }  
}

