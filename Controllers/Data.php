<?php

class Data extends Controller
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

    public function GraficaInicial($param){
    
        if($param!=""){
            $pros = explode(",",$param);
            //se debe enviar id de telemetria
            $nombre = $pros[0];
            $telemetria = $pros[1];

            $fechaI =(isset($pros[2])) ? $pros[2] :"0" ;
            $fechaF =(isset($pros[3])) ? $pros[3] :"0" ;
            // consultar para nombre_contenedor y ultima fecha 
            $consultaUltima = $this->model->ContenedorData($nombre);
            $resultadoL = json_decode($consultaUltima);
            $resultadoL = $resultadoL->data;
            $ultimaFecha = $resultadoL[0]->ultima_fecha;
            if($fechaI=="0" && $fechaF=="0"){
                $cadena = array(
                    'device'=>$telemetria,
                    'ultima'=>gmtFecha($ultimaFecha),
                    'utc'=>$_SESSION['utc']
                );
            }else{
                if(fechaGrafica($fechaI,$fechaF)=="ok"){
                    $cadena = array(
                        'device'=>$telemetria,
                        'ultima'=>gmtFecha($ultimaFecha),
                        //'fechaI'=>$fechaI.":00",
                        //'fechaF'=>$fechaF.":00"
                        'fechaI'=> validateDate($fechaI),
                        'fechaF'=> validateDate($fechaF),
                        'utc'=>$_SESSION['utc']
                        
                    );
                    //validateDate($fechaI, $format = 'Y-m-d H:i:s')
                }else{
                    $cadena = array();
                }
            }
            if(count($cadena)!=0){
                //hacer peticion de data en el servidor 
                $dataMadurador = $this->model->DatosGraficaTabla($cadena);
                $resultadoMadurador = json_decode($dataMadurador);
                $resultadoMadurador = $resultadoMadurador->data;
            }else{
                $resultadoMadurador =fechaGrafica($fechaI,$fechaF);
            }
        }else{
            $resultadoMadurador ="";
        }
        echo json_encode($resultadoMadurador , JSON_UNESCAPED_UNICODE);

    }
}

