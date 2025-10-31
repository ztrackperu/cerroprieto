<?php

class Ripener extends Controller
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
        /*
        $perm = $this->model->verificarPermisos($id_user, "Live");
        if (!$perm && $id_user != 1) {
            $this->views->getView($this, "permisos");
            exit;
        }
        */
        /*
        //pedimos la info 
        $data = $this->model->ListaContenedores($id_user);
        #echo $data ;
        $resultadoContenedores = json_decode($data);
        $resultadoContenedores = $resultadoContenedores->data;
        $this->views->getView($this, "index",json_encode($resultadoContenedores));
        */
        $this->views->getView($this, "index");

    }
    public function ComandoTest($param) {
        if($param!=""){
            $comando = $param;
            $matriz = explode("|", $comando);
            // (32 °F − 32) × 5/9 = 0 °C

            $matriz_conversion =  (($matriz[0]-32)*5)/9 ; 
            $cadena1 = array(
                'imei'=>"866782048942516",
                'estado' =>0,
                // comando para proceso integral =>command for integral process 
                'evento' =>"command for integral process ",
                'comando'=>"Trama_Writeout(29,1,1)"     
            );
            $cadena2 = array(
                'imei'=>"866782048942516",
                'estado' =>0,
                // orden de cambio para temperatura =>change order for temperature 
                'evento' =>"change order for temperature ",
                'comando'=>"Trama_Writeout(0,".round($matriz_conversion,2).",100)"    
            );
            $cadena3 = array(
                'imei'=>"866782048942516",
                'estado' =>0,
                // orden de cambio para etileno =>change order for ethylene 
                'evento' =>"change order for ethylene  ",
                'comando'=> "SP_ETILENO(".$matriz[1].")"
            );
            $cadena4 = array(
                'imei'=>"866782048942516",
                'estado' =>0,
                // orden para horas de inyeccion =>order for injection hours 
                'evento' =>"order for injection hours ",
                'comando'=>"Temporizadores(0,".$matriz[2].",".$matriz[1].")"    
            );
            $cadena5 = array(
                'imei'=>"866782048942516",
                'estado' =>0,
                // orden de cambio para humedad  =>change order for humidity  
                'evento' =>"change order for humidity ",
                'comando'=>"Trama_Writeout(4,".$matriz[3].",100)"    
            );
            $cadena6 = array(
                'imei'=>"866782048942516",
                'estado' =>0,
                // configure AFAM PLUS
                'evento' =>"configure AFAM PLUS ",
                'comando'=>"Trama_Writeout(9,2,1)"      
            );
            $cadena7 = array(
                'imei'=>"866782048942516",
                'estado' =>0,
                // orden de cambio para CO2 =>change order for CO2
                'evento' =>"change order for CO2",
                'comando'=>"Trama_Writeout(3,".$matriz[4].",100)"     
            );
            $dataControl = $this->model->EnvioComando_libre($cadena1);
            //sleep(1);
            $dataControl2 = $this->model->EnvioComando_libre($cadena2);
            //sleep(1);
            $dataControl3 = $this->model->EnvioComando_libre($cadena3);
            //sleep(1);
            $dataControl4 = $this->model->EnvioComando_libre($cadena4);
            //sleep(1);
            $dataControl5 = $this->model->EnvioComando_libre($cadena5);
            //sleep(1);
            $dataControl6 = $this->model->EnvioComando_libre($cadena6);
            //sleep(1);
            $dataControl7 = $this->model->EnvioComando_libre($cadena7);
            //sleep(1);
    
        }
        echo json_encode($dataControl7, JSON_UNESCAPED_UNICODE);
            //echo json_encode($matriz[1], JSON_UNESCAPED_UNICODE);
            
        die();

    }

    public function Comando($param) {
        if($param!=""){
            $comando = $param;
            $matriz = explode("|", $comando);
            $matriz_conversion =  (($matriz[0]-32)*5)/9 ; 

            $cadena1 = array(
                'imei'=>"866782048942516",
                //'estado' =>3,
                'user'=>"jhonvena",
                'tipo'=>1,
                'dato'=>1,
                // comando para proceso integral =>command for integral process 
                'evento' =>"command for integral process ",
                'comando'=>"Trama_Writeout(29,1,1)"     
            );
            $cadena2 = array(
                'imei'=>"866782048942516",
                //'estado' =>3,
                'user'=>"jhonvena",
                'tipo'=>7,
                'dato'=>round($matriz_conversion,1),
                // orden de cambio para temperatura =>change order for temperature 
                'evento' =>"change order for temperature ",
                'comando'=>"Trama_Writeout(0,".round($matriz_conversion,1).",100)"    
            );
            $cadena3 = array(
                'imei'=>"866782048942516",
                //'estado' =>3,
                'user'=>"jhonvena",
                'tipo'=>4,
                'dato'=>$matriz[1],

                // orden de cambio para etileno =>change order for ethylene 
                'evento' =>"change order for ethylene  ",
                'comando'=> "SP_ETILENO(".$matriz[1].")"
            );
            $cadena4 = array(
                'imei'=>"866782048942516",
                //'estado' =>3,
                'user'=>"jhonvena",
                'tipo'=>5,
                'dato'=>$matriz[2],
                // orden para horas de inyeccion =>order for injection hours 
                'evento' =>"order for injection hours ",
                'comando'=>"Temporizadores(0,".$matriz[2].",".$matriz[1].")"    
            );
            $cadena5 = array(
                'imei'=>"866782048942516",
                //'estado' =>3,
                'user'=>"jhonvena",
                'tipo'=>6,
                'dato'=>$matriz[3],
                // orden de cambio para humedad  =>change order for humidity  
                'evento' =>"change order for humidity ",
                'comando'=>"Trama_Writeout(4,".$matriz[3].",100)"    
            );
            $cadena6 = array(
                'imei'=>"866782048942516",
                //'estado' =>3,
                'user'=>"jhonvena",
                'tipo'=>12,
                'dato'=>2,
                // configure AFAM PLUS
                'evento' =>"configure AFAM PLUS ",
                'comando'=>"Trama_Writeout(9,2,1)"      
            );
            $cadena7 = array(
                'imei'=>"866782048942516",
                //'estado' =>3,
                'user'=>"jhonvena",
                'tipo'=>3,
                'dato'=>$matriz[4],
                // orden de cambio para CO2 =>change order for CO2
                'evento' =>"change order for CO2",
                'comando'=>"Trama_Writeout(3,".$matriz[4].",100)"     
            );
            $dataControl = $this->model->EnvioComando_libre($cadena1);
            //sleep(1);
            $dataControl2 = $this->model->EnvioComando_libre($cadena2);
            //sleep(1);
            $dataControl3 = $this->model->EnvioComando_libre($cadena3);
            //sleep(1);
            $dataControl4 = $this->model->EnvioComando_libre($cadena4);
            //sleep(1);
            $dataControl5 = $this->model->EnvioComando_libre($cadena5);
            //sleep(1);
            $dataControl6 = $this->model->EnvioComando_libre($cadena6);
            //sleep(1);
            $dataControl7 = $this->model->EnvioComando_libre($cadena7);
            //sleep(1);
    
        }
        echo json_encode($dataControl7, JSON_UNESCAPED_UNICODE);
            //echo json_encode($matriz[1], JSON_UNESCAPED_UNICODE);
            
        die();

    }
    public function ListaDispositivoEmpresa()
    {
        $data = $this->model->ListaDispositivoEmpresa($_SESSION['empresa_id']);
        $data = json_decode($data);
        $data = $data->data;
        $data=$data[0];

        /*
        $text ="";
        $data2 =[];
        $url = base_url;
        $fecha=[];
        
        foreach($data as $val){
            $tipo = $val->extra_1;
            $enlace = ContenedorPlantilla($val,$url, $tipo) ;
            $fecha =  determinarEstado($val->ultima_fecha ,$id =1,$fecha);
            $text.=$enlace['text'];
            array_push($data2 ,array(
                'latitud'=>$enlace['latitud'],
                'longitud'=>$enlace['longitud'],
                'nombre_contenedor'=> $enlace['nombre_contenedor'],
            ));
        }
        //$data->text = $text;
        $data1 =array(
            //'data'=>tarjetamadurador($val)
            'data'=>$data2,
            'text'=>$text,
            'extraer'=>$_SESSION['data'],
            'estadofecha'=>$fecha
        );
        */

        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        die();

    } 
}

