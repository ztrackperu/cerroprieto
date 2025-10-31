<?php

class Cooling extends Controller
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

            $cadena1 = array(
                'imei'=>"866782048942516",
                'estado' =>0,
                //verificacion de encendido => ignition check
                'evento' =>"ignition check",
                'comando'=>"RELE3_ON"   

            );
            $cadena2 = array(
                'imei'=>"866782048942516",
                'estado' =>0,
                // comando para proceso integral =>command for integral process 
                'evento' =>"command for integral process ",
                'comando'=>"TEMPO_PROCESO(".$matriz[0].",".$matriz[1].",".$matriz[2].",".$matriz[3].",".$matriz[4].")"    
            );

            $dataControl = $this->model->EnvioComando_libre($cadena1);
            //sleep(1);
            $dataControl2 = $this->model->EnvioComando_libre($cadena2);
        }
        echo json_encode($dataControl2, JSON_UNESCAPED_UNICODE);            
        die();

    }

    public function Comando($param) {
        if($param!=""){
            $comando = $param;
            $matriz = explode("|", $comando);
/*
            $cadena1 = array(
                'imei'=>"866782048942516",
                'estado' =>0,
                //verificacion de encendido => ignition check
                'evento' =>"ignition check",
                'comando'=>"RELE3_ON"     
            );

            */
            $cadena2 = array(
                'imei'=>"866782048942516",
                'estado' =>0,
                // comando para proceso integral =>command for integral process 
                'evento' =>"command for integral process ",
                'comando'=>"modo_cooling(".$matriz[0].",".$matriz[1].")"    
            );

           // $dataControl = $this->model->EnvioComando_libre($cadena1);
            //sleep(1);
            $dataControl2 = $this->model->EnvioComando_libre($cadena2);
        }
        echo json_encode($dataControl2, JSON_UNESCAPED_UNICODE);            
        die();

    }
}

