<?php 
    function formatearFecha($fecha) {
        $dateTime = new DateTime($fecha);   
        return $dateTime->format('H:i:s d/m/Y');
    }
    function status($dato){
        if($dato==1){$res ="Required";
        }elseif($dato==2){$res ="Executed";
        }elseif($dato==3){$res ="Validated";    
        }else{$res ="canceled";}
        return $res;
    }
    function validarco2($dato){
        if($dato<0 ||$dato>30){$dato="NA";}
        return $dato;
    }
    function parametro($dato){
        if($dato==1){$res ="PPM";
        }elseif($dato==2){$res ="%";
        }elseif($dato==3){$res ="F°";    
        }else{$res ="H";}
        return $res;
    }
    //pasar_celcius
    function pasar_celcius($dato){
        $celsius = (5 / 9) * ($dato - 32);  
        return number_format($celsius, 1);
    }
    function validarModal($matriz){
        $text1 ="<div class='container'>
            <div class='row'>
                <div class='col'></div>
                <div class='col-3'>
                    <h3 class='text-warning'><strong>".$matriz[0]." ".parametro($matriz[2])."</strong></h3></div>
                <div class='col-2'>
                    <i class='bi bi-box-arrow-right'></i>
                </div>
                <div class='col-3'><h3 class='text-success'><strong>".$matriz[1]." ".parametro($matriz[2])."</strong></h3></div>
                <div class='col'></div>
            </div>
        </div>";
        return $text1;
    }
    function comandos_pendientes($testComandos){
        $text_comanos="<div> no pending commands </div>";
        if($testComandos){
            $resultadoComando = json_decode($testComandos);
            $resultadoComando = $resultadoComando->data;
            if($resultadoComando){
                $epa =" hay en total estos  elementos :  ".count($resultadoComando);
                $text_comanos ="<p></p>
                <div><h1>Pending Commands, please wait  :</h1></div>
                <table class='table'>
                <thead>
                  <tr>
                    <th scope='col'>#</th>
                    <th scope='col'>Resumen</th>
                    <th scope='col'>Date</th>
                    <th scope='col'>Status</th>
                  </tr>
                </thead>
                <tbody>";
                //aqui ocurre el foreach 
                foreach ($resultadoComando as $index => $valor) {
                    $index_id=$index +1;
                    $text_comanos =$text_comanos."<tr>
                    <th scope='row'>".$index_id."</th>
                    <td>".$valor->evento."</td>
                    <td>".formatearFecha($valor->fecha_creacion)."</td>
                    <td>".status($valor->status)."</td>
                  </tr>";
                }
                $text_comanos =$text_comanos."</tbody>
              </table>";

            }else{
                $epa = "No hay resultados";
            }
        } 
        return $text_comanos;
    }

    function fechaGrafica($dateI,$dateF){
        // Crear objetos DateTime a partir de las cadenas de fecha
        $dateInicial = new DateTime($dateI);
        $dateFinal = new DateTime($dateF);
        //$actual = new DateTime("now");
        if($dateFinal<$dateInicial ){
            $dif="mal";
        }else{
            //si paso 2 años decir que deb contactarse con el administrador
            $interval = $dateInicial->diff($dateFinal);
            $colosal = $interval->format('%Y');
            if($colosal>=2){ $dif="rango";
            }else{ $dif="ok";}
        }
        return $dif;

    }
function tempNormal($val){ 
    // Verificar si el valor está dentro del rango permitido
    if($val < -40 || $val > 120) {
        return "NA";
    }

    if($val>=0 && $val<120){
        	
        //(0 °C × 9/5) + 32 = 32 °F
        $val = ($val*9)/5+32 ;
        $valor="+".$val ;
    }elseif($val>-40 && $val<0){
        if($val==-38.5){
            $valor="NA";
        }else{
            $val = ($val*9)/5+32 ;
            $valor=$val ;
        }
    }else{
        $valor="NA";
    }
    return $valor;
}

function porcentaje($val){
    //Si val es menor a 0 y mayor a 100 = NA
    if($val>=0 && $val<=100){
        $valor=$val ;
    }else{
        $valor="NA";
    }
    return $valor;
}

function validateDate($date, $format = 'Y-m-d\TH:i:s'){
    $d = DateTime::createFromFormat($format, $date);
    if($d && $d->format($format) == $date){
        return $date;
    }else{
        return $date.":00";   
    }
    //return $d && $d->format($format) == $date;
}
function gmtFecha($val){
    if($_SESSION['utc']!=300){
        $val1 =  strtotime($val);
        $dif =300-$_SESSION['utc'];
        $minutes = $dif." minutes";
        $puntoA1 = strtotime($minutes,$val1);
        $val = date('Y-m-d\TH:i:s', $puntoA1);
    }
    return $val;
}

function determinarEstado($ultima_fecha ,$id,$est) {
    if($est==[]){
        $est=[0,0,0];
    }
    date_default_timezone_set('UTC');
    $hoy = date("Y-m-d H:i:s");                   
    $fechaActual = new DateTime($hoy);
    $fechaUltima = new DateTime($ultima_fecha);
    #$diferencia = $fechaActual->getTimestamp() - $fechaUltima->getTimestamp();
    $diferencia = $fechaActual->diff($fechaUltima);
    
    // Convertir la diferencia en minutos
    $diferenciaEnMinutos = ($diferencia->days * 24 * 60) + ($diferencia->h * 60) + $diferencia->i;
    
    //tiempo en segundos
    if ($diferenciaEnMinutos <= 30+300) { 
        $est[0]=$est[0]+1;
        #return 'Online';
    } elseif ($diferenciaEnMinutos <= 1440+300) { 
        $est[1]=$est[1]+1;
        #return 'Wait';
    } else {
        $est[2]=$est[2]+1;
        #return 'Offline';
    }
    //array_push($est,$ultima_fecha,$diferenciaEnMinutos,$fechaActual);
    return $est;
}

function porNormal($val){
    if($val>=0 && $val<100){$valor=$val ;}else{$valor="NA";}
    return $valor;
}
function val_eti($val){
    if($val>=0 && $val<280){$valor=$val ;}else{$valor="NA";}
    return $valor;
}
function validateP($val){
    /*
    if($val == 5){
        $valor = "Active";
    }else{
        $valor = "Inactive";
    }*/
   // $valor="Ripener Mode";
    $valor = "Inactive";
    //$valor = "Cooling Mode";


    return $valor;
} 
function ContenedorPlantilla($val,$url=0, $tipo=1){      
    if($tipo==0){
        $result = ContenedorReefer($val,$url);
    }elseif($tipo==1){
        $result = ContenedorMadurador($val,$url);
    }elseif($tipo==2){
        $result = ContenedorTunel($val,$url);
    }
    return $result;
}
function fechaPro($val){
    //echo $val;
    //previa validacion de GMT  "Y-m-d\TH:i:s
    $_SESSION['utc']=300;
    if($_SESSION['utc']!=300){
        $val1 =  strtotime($val);
        $dif =300-(int)$_SESSION['utc'];
        $minutes = $dif." minutes";
        $puntoA1 = strtotime($minutes,$val1);
        $val = date('Y-m-d\TH:i:s', $puntoA1);
    }
    $ultima = explode("T",$val) ;
    $fech = explode("-",$ultima[0]);
    //echo $ultima[0];
    //echo " luis ";
    //echo $fech;
    $fech1 = $fech[2]."/".$fech[1]."/".$fech[0] ; 
    //echo $fech1;
    $fechita =$ultima[1]." - ".$fech1;           

    //$fech1 = $fech[2]."/".$fech[1]."/".$fech[0] ; 
    //$fechita =$ultima[1]." del  ".$fech1;
    return $fechita;
}

function ContenedorReefer($val, $url){
}

function ContenedorTunel($val, $url){
}
function avl_1($dato){
    if($dato==0){
        $res = 0 ;
    }else{
        if($dato>0&& $dato<200){
            $res =$dato/2;
        }else{
            $res="NA";
        }
    }
    return $res;
}
function horas_simuladas($dato){
   //return 24 ;
   // return 12 ;
    return 0 ;


}

function evaluarEstado($dato){
    $fecha_de_hoy = date("Y-m-d H:i:s");
    $fecha1 = strtotime($fecha_de_hoy);
    $fecha2 = strtotime($dato);
    $diferencia = $fecha1 - $fecha2;

    if($diferencia <= 30*60){
        $estado = "ONLINE";
    }elseif($diferencia <= 12*60*60){
        $estado = "WAIT";
    }else{
        $estado = "OFFLINE";
    }
    return $estado;
}

function evaluarEstadoColor($dato){
    $fecha_de_hoy = date("Y-m-d H:i:s");
    $fecha1 = strtotime($fecha_de_hoy);
    $fecha2 = strtotime($dato);
    $diferencia = $fecha1 - $fecha2;

    if($diferencia <= 30*60){
        $estado = "text-success";
    }elseif($diferencia <= 12*60*60){
        $estado = "text-warning";
    }else{
        $estado = "text-danger";
    }
    return $estado;
}

function convertirFecha($fecha){
    $fecha = explode("T", $fecha);
    $fecha1 = explode("-", $fecha[0]);
    $hora = explode(".", $fecha[1]);
    $hora = $hora[0];
    $hoy = $fecha1[2] . "-" . $fecha1[1] . "-" . $fecha1[0] . " " . $hora;
    return $hoy;
}

function ContenedorMadurador_2($val, $url=0){ 
    $s_temp =tempNormal($val->set_point) ; 
    $valR ='"'.$val->nombre_contenedor.'"';
    $fechita = fechaPro($val->ultima_fecha);
    $etileno = $val->ethylene;
    $sp_ethyleno = $val->sp_ethyleno;
    $co2 = $val->co2_reading;
    $sp_co2 = $val->set_point_co2;
    $supply = $val->temp_supply_1;
    $s_temp = $val->set_point;

    // sp_ethyleno y etileno
    if(abs($etileno - $sp_ethyleno) <= $sp_ethyleno * 0.10){
        $etileno_color = "text-success";
    }else if(abs($etileno - $sp_ethyleno) <= $sp_ethyleno * 0.25){
        $etileno_color = "text-warning";
    }else{
        $etileno_color = "text-secondary";
    }   
    // sp_co2 y co2
    if(abs($co2 - $sp_co2) <= $sp_co2 * 0.10){
        $co2_color = "text-success";
    }else if(abs($co2 - $sp_co2) <= $sp_co2 * 0.25){
        $co2_color = "text-warning";
    }else{
        $co2_color = "text-secondary";
    }
     // $val->set_point y supply
     if(abs($supply - $s_temp) <= $s_temp * 0.10){
        $supply_color = "text-success";
    }else if(abs($supply - $s_temp) <= $s_temp * 0.25){
        $supply_color = "text-warning";
    }else{
        $supply_color = "text-secondary";
    }
     //humidity
     $humidity = $val->relative_humidity;
     if(abs($humidity - $val->humidity_set_point) <= $val->humidity_set_point * 0.10){
         $humidity_color = "text-success";
     }else if(abs($humidity - $val->humidity_set_point) <= $val->humidity_set_point * 0.25){
         $humidity_color = "text-warning";
     }else{
         $humidity_color = "text-secondary";
     }
 

    if($val->power_state == 1){
        $icon = "text-success";
        $textState = "ON";
    }else{
        $icon = "text-danger";
        $textState = "OFF";
    }
    $temp_supply_1_f=tempNormal($val->temp_supply_1);
    $return_air_f = tempNormal($val->return_air);
    $avl_f = avl_1($val->avl);
    $compress_coil_1_f = tempNormal($val->compress_coil_1);
    $evaporation_coil_f =tempNormal($val->evaporation_coil);
    $ambient_air_f =tempNormal($val->ambient_air);
    $cargo_1_temp_f = tempNormal($val->cargo_1_temp);
    $cargo_2_temp_f = tempNormal($val->cargo_2_temp);
    $cargo_3_temp_f = tempNormal($val->cargo_3_temp);
    //$cargo_4_temp_f = tempNormal($val->cargo_4_temp)+3;
    $cargo_4_temp_f = tempNormal($val->cargo_4_temp);

    $ultima_fecha_f=fechaPro($val->ultima_fecha);
    $ethylene_f = val_eti($val->ethylene);
    $co2_pr = porcentaje($val->co2_reading);
    $humidity_pr = porcentaje($val->relative_humidity);
    $pwd_pr = porcentaje($val->defrost_prueba);
    $process_v = validateP($val->stateProcess);
    $horasInteccion =horas_simuladas($val->ripener_prueba);
    $estado = evaluarEstado($val->ultima_fecha);
    $colorEstado = evaluarEstadoColor($val->ultima_fecha);
 
    $text = "<div class='col-12 col-lg-12'>
                        <div class='d-flex flex-wrap justify-content-center align-items-center gap-2'>
                            <h3 class='fw-bold text-center'>{$val->nombre_contenedor}</h3>
                            <h3 class='fw-bold text-center {$colorEstado}' id='estadoEquipo_{$val->telemetria_id}'>({$estado})</h3>
                        </div>
                        <div class='d-flex justify-content-center align-items-center gap-2'>
                            <!-- Última hora de actividad -->
                            <div>
                                <h6 id='fechita_{$val->telemetria_id}'>{$ultima_fecha_f}</h6>
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
                    <div class='col-6 col-lg-3 border-top'>
                        <div class='text-center'>
                            <i class='icon_params bi bi-power fs-1 {$icon}'></i>
                            <h4 class='text_params text-uppercase fw-bold mt-2 {$icon}' id='state_{$val->telemetria_id}'>POWER {$textState}</h4>
                        </div>
                    </div>
                    
                    <div class='col-6 col-lg-3 border-top'>
                        <div class='text-center'>
                            <svg version='1.0' xmlns='http://www.w3.org/2000/svg' width='60' height='60' class='icon-params icon px-1 btn' viewBox='0 0 306 236' preserveAspectRatio='xMidYMid meet'>
                                <g transform='translate(0,236) scale(0.1,-0.1)' stroke='none'>
                                    <path fill='gray' d='M472 2126 c-62 -29 -102 -68 -133 -132 -28 -57 -31 -71 -27 -127 6 -73 25 -114 76 -165 59 -59 105 -77 194 -77 l77 0 40 -63 c23 -34 41 -67 41 -72 0 -5 -24 -33 -54 -63 -80 -80 -99 -123 -104 -238 -4 -87 -2 -101 21 -150 15 -30 51 -78 81 -108 l55 -55 -31 -55 c-46 -83 -50 -86 -126 -87 -84 -2 -143 -27 -199 -85 -53 -56 -73 -103 -73 -176 0 -170 162 -295 326 -254 190 48 258 277 125 424 l-35 39 39 75 39 74 73 -6 c83 -8 158 6 218 40 46 26 121 105 147 153 l17 32 284 0 285 0 47 -64 c51 -70 91 -102 170 -137 48 -22 102 -26 210 -17 27 3 33 -3 73 -70 l44 -72 -46 -51 c-57 -64 -76 -122 -66 -200 8 -61 25 -94 74 -147 50 -55 101 -76 181 -76 80 1 124 16 179 64 177 156 58 450 -181 450 -61 0 -64 1 -90 38 -16 21 -36 54 -46 73 l-18 34 55 59 c72 76 96 139 96 245 0 70 -4 86 -35 149 -22 44 -53 86 -81 111 l-45 39 43 71 43 71 61 0 c86 0 150 25 204 80 130 129 95 333 -72 421 -50 26 -167 26 -223 0 -48 -22 -96 -71 -126 -129 -17 -34 -21 -58 -20 -114 2 -77 14 -109 68 -176 l35 -43 -41 -68 -40 -68 -98 1 c-85 0 -105 -3 -153 -25 -67 -31 -116 -73 -156 -131 -16 -24 -33 -46 -39 -50 -5 -4 -137 -7 -293 -7 -306 1 -296 -1 -326 58 -21 40 -97 106 -152 132 -40 18 -67 23 -147 23 l-98 1 -44 69 -43 69 44 63 c48 71 59 111 51 189 -8 82 -62 165 -130 201 -45 23 -182 29 -225 10z'/>
                                </g>
                            </svg>
                            <h4 class='text-uppercase text_params'>Ethylene</h4>
                            <div class='d-flex justify-content-center align-content-center'>
                                <p class='value-icon' id='eti_icon_{$val->telemetria_id}'><i class='bi bi-arrows me-2 align-items-center mb-1 text-primary value-icon'></i></p>
                                <label for='ethylene' class='fw-bold value_params value_icon {$etileno_color}' id='ethyleno_{$val->telemetria_id}'>{$ethylene_f} ppm</label>
                            </div>
                        </div>
                    </div>
                    <div class='col-6 col-lg-3 border-top'>
                        <div class='text-center'>
                            <svg width='60' height='60' class='icon-params-co2 icon px-1 btn'>
                                <use xlink:href='sprite.svg#co2'></use>
                            </svg>
                            <h4 class='text-uppercase text_params'>CO2</h4>
                            <div class='d-flex justify-content-center align-content-center'>
                                <p class='value-icon' id='co2_icon_{$val->telemetria_id}'><i class='bi bi-arrows me-2 align-items-center mb-1 text-primary value-icon'></i></p>
                                <label for='co2' class='fw-bold value_icon value_params {$co2_color}' id='co2_{$val->telemetria_id}'>{$co2_pr} %</label>
                            </div>
                        </div>
                    </div>
                     <div class='col-6 col-lg-3 border-top'>
                        <div class='text-center b'>
                            <i class='ri-settings-3-line fs-1 text-secondary'></i>

                            <h4 class='text-uppercase text_params'>Process</h4>
                            <div class='d-flex justify-content-center align-content-center text-success'>
                                <p class='value-icon' id='proceso_icon_{$val->telemetria_id}'><i class='bi bi-arrows me-2 align-items-center mb-1 text-primary value-icon'></i></p>
                                <label for='proceso' class='fw-bold value_params value_icon' id='proceso_{$val->telemetria_id}'>{$process_v}</label>
                            </div>
                        </div>
                    </div>
                    <div class='col-6 col-lg-3 border-top'>
                        <div class='text-center'>
                            <i class='bi bi-thermometer fs-1'></i>
                            <h4 class='text-uppercase text_params'>Return</h4>
                            <div class='d-flex justify-content-center align-content-center'>
                                <p class='value-icon' id='return_icon_{$val->telemetria_id}'><i class='bi bi-arrows me-2 align-items-center mb-1 text-primary value-icon'></i></p>
                                <label for='return' class='fw-bold value_params value_icon' id='return_{$val->telemetria_id}'>{$return_air_f} F°</label>
                            </div>
                        </div>
                    </div>
                    <div class='col-6 col-lg-3 border-top'>
                        <div class='text-center'>
                            <svg version='1.0' xmlns='http://www.w3.org/2000/svg' class='icon-params px-1'
                                width='60' height='60' viewBox='0 0 90.000000 90.000000'
                                preserveAspectRatio='xMidYMid meet'>

                                <g transform='translate(0.000000,66.000000) scale(0.100000,-0.100000)'
                                fill='#000000' stroke='none'>
                                <path d='M432 543 c-23 -20 -41 -73 -24 -73 5 0 15 13 22 30 21 50 86 53 108
                                5 14 -29 2 -69 -23 -79 -9 -3 -104 -6 -211 -6 -123 0 -194 -4 -194 -10 0 -13
                                395 -13 420 0 25 13 41 65 30 94 -19 52 -88 72 -128 39z'/>
                                <path d='M635 465 c-24 -23 -36 -73 -15 -60 6 3 10 12 10 19 0 19 34 46 58 46
                                51 0 81 -70 44 -103 -16 -15 -50 -17 -268 -17 -143 0 -255 -4 -264 -10 -10 -6
                                78 -10 257 -10 l272 0 20 26 c29 37 26 70 -8 105 -36 36 -73 37 -106 4z'/>
                                <path d='M155 250 c4 -6 85 -10 211 -10 191 0 205 -1 224 -20 25 -25 25 -55 0
                                -80 -36 -36 -82 -21 -105 35 -8 19 -9 18 -13 -5 -8 -49 76 -90 119 -57 27 20
                                41 56 34 88 -12 55 -28 59 -263 59 -140 0 -211 -3 -207 -10z'/>
                                </g>
                            </svg>
                            <h4 class='text-uppercase text_params'>Supply</h4>
                            <div class='d-flex justify-content-center align-content-center'>
                                <p class='value-icon' id='supply_icon_{$val->telemetria_id}'><i class='bi bi-arrows me-2 align-items-center mb-1 text-primary value-icon'></i></p>
                                <label for='supply' class='fw-bold value_icon value_params {$supply_color}' id='supply_{$val->telemetria_id}'>{$temp_supply_1_f} F°</label>
                            </div>
                        </div>
                    </div>
                    <div class='col-6 col-lg-3 border-top'>
                        <div class='text-center'>
                            <svg version='1.0' xmlns='http://www.w3.org/2000/svg'
                                width='60' height='60' class='icon-params px-1 btn' viewBox='0 0 64.000000 64.000000'
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
                            <h4 class='text-uppercase text_params'>I. Hours</h4>
                            <div class='d-flex justify-content-center align-content-center'>
                                <p class='value-icon' id='i_hours_icon_{$val->telemetria_id}'><i class='bi bi-arrows me-2 align-items-center mb-1 text-primary value-icon'></i></p>
                                <label for='i_hours' class='fw-bold value_params value_icon' id='i_hours_{$val->telemetria_id}'>{$horasInteccion}h</label>
                            </div>
                        </div>
                    </div>
                    <div class='col-6 col-lg-3 border-top'>
                        <div class='text-center'>
                            <svg version='1.0' xmlns='http://www.w3.org/2000/svg'
                                width='60' height='60' class='icon-params px-1 btn' viewBox='0 0 93.000000 76.000000'
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
                            <h4 class='text-uppercase text_params'>AVL</h4>
                            <div class='d-flex justify-content-center align-content-center'>
                                <p class='value-icon' id='avl_icon_{$val->telemetria_id}'><i class='bi bi-arrows me-2 align-items-center mb-1 text-primary value-icon'></i></p>
                                <label for='avl' class='fw-bold value_params value_icon' id='avl_{$val->telemetria_id}'>{$val->avl} CFM</label>
                            </div>
                        </div>
                    </div>
                    <div class='col-6 col-lg-3 border-top hide-content' hidden>
                        <div class='text-center'>
                            <svg version='1.0' xmlns='http://www.w3.org/2000/svg' width='60' height='60' class='icon-params icon px-1' viewBox='0 0 74.000000 74.000000' preserveAspectRatio='xMidYMid meet'>
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
                            <h4 class='text-uppercase text_params'>Humidity</h4>
                            <div class='d-flex justify-content-center align-content-center'>
                                <p class='value-icon' id='humidity_icon_{$val->telemetria_id}'><i class='bi bi-arrows me-2 align-items-center mb-1 text-primary value-icon'></i></p>
                                <label for='humidity' class='fw-bold value_params value_icon {$humidity_color}' id='humidity_{$val->telemetria_id}'>{$humidity_pr} %</label>
                            </div>
                        </div>
                    </div>
                    <div class='col-6 col-lg-3 border-top hide-content' hidden>
                        <div class='text-center '>
                            <svg version='1.0' xmlns='http://www.w3.org/2000/svg'
                                width='60' height='60' class='icon-params px-1 btn' viewBox='0 0 172.000000 123.000000'
                                preserveAspectRatio='xMidYMid meet'>
                                <metadata>
                                Created by potrace 1.16, written by Peter Selinger 2001-2019
                                </metadata>
                                <g transform='translate(0.000000,123.000000) scale(0.100000,-0.100000)'
                                fill='#000000' stroke='none'>
                                <path fill='red' d='M367 1209 c-56 -42 -57 -50 -57 -392 l0 -315 -23 -15 c-36 -23 -85
                                -106 -97 -165 -25 -114 32 -234 138 -290 257 -137 499 205 300 424 l-48 52 0
                                301 c0 331 -5 362 -60 401 -38 27 -115 27 -153 -1z m127 -71 c14 -19 16 -70
                                16 -349 l0 -326 37 -24 c115 -74 116 -260 2 -336 -124 -82 -283 1 -296 155 -6
                                71 23 132 85 177 l41 30 0 329 c1 292 3 332 18 348 23 26 78 24 97 -4z'/>
                                <path fill='red'd='M400 733 l-1 -288 -38 -24 c-145 -89 -86 -321 81 -321 62 0 101 20
                                138 72 25 33 30 51 30 95 0 30 -7 67 -14 82 -18 34 -81 91 -101 91 -13 0 -15
                                42 -15 290 l0 290 -40 0 -40 0 0 -287z'/>
                                <path fill='blue' d='M1049 1194 l-34 -35 -3 -322 -3 -322 -38 -40 c-61 -63 -84 -118 -84
                                -200 0 -86 31 -155 94 -209 81 -69 171 -81 264 -36 168 81 206 306 75 438
                                l-40 40 0 301 c0 327 -3 347 -56 396 -22 21 -37 25 -83 25 -52 0 -60 -3 -92
                                -36z m136 -49 l25 -24 0 -328 1 -328 39 -27 c53 -37 80 -92 80 -165 0 -72 -28
                                -125 -88 -167 -37 -26 -52 -31 -103 -30 -49 1 -67 6 -101 31 -113 82 -117 253
                                -6 331 l38 27 0 323 c0 209 4 330 11 343 10 20 41 37 67 38 7 1 24 -10 37 -24z'/>
                                <path fill='blue' d='M1100 520 c0 -64 -3 -80 -15 -80 -8 0 -33 -16 -54 -36 -74 -67 -76
                                -188 -3 -256 68 -63 154 -63 223 -1 72 65 77 172 12 244 -20 22 -46 42 -59 45
                                -23 6 -24 10 -24 85 l0 79 -40 0 -40 0 0 -80z'/>
                                </g>
                            </svg>
                            <h4 class='text-uppercase text_params'>Compressor</h4>
                            <div class='d-flex justify-content-center align-content-center'>
                                <p class='value-icon' id='compressor_icon_{$val->telemetria_id}'><i class='bi bi-arrows me-2 align-items-center mb-1 text-primary value-icon'></i></p>
                                <label for='compressor' class='fw-bold value_params value_icon' id='compressor_{$val->telemetria_id}'>{$compress_coil_1_f} F°</label>
                            </div>
                        </div>
                    </div>
                    <div class='col-6 col-lg-3 border-top hide-content' hidden>
                        <div class='text-center'>
                            <i class='bi bi-fan fs-1 text-secondary'></i>
                            <h4 class='text-uppercase text_params'>Evaporator</h4>
                            <div class='d-flex justify-content-center align-content-center'>
                                <p class='value-icon' id='evaporator_icon_{$val->telemetria_id}'><i class='bi bi-arrows me-2 align-items-center mb-1 text-primary value-icon'></i></p>
                                <label for='evaporator' class='fw-bold value_params value_icon' id='evaporator_{$val->telemetria_id}'>{$evaporation_coil_f} F°</label>
                            </div>
                        </div>
                    </div>
                    <div class='col-6 col-lg-3 border-top hide-content' hidden>
                        <div class='text-center '>
                            <i class='bi bi-brightness-high-fill fs-1' style='color:yellow;'></i>
                            <h4 class='text-uppercase text_params'>Ambient Air</h4>
                            <div class='d-flex justify-content-center align-content-center'>
                                <p class='value-icon' id='ambient_air_icon_{$val->telemetria_id}'><i class='bi bi-arrows me-2 align-items-center mb-1 text-primary value-icon'></i></p>
                                <label for='ambient_air' class='fw-bold value_params value_icon' id='ambient_air_{$val->telemetria_id}'>{$ambient_air_f}</label>
                            </div>
                        </div>
                    </div>
                    <div class='col-6 col-lg-3 border-top border-bottom hide-content' hidden>
                        <div class='text-center'>
                            <i class='bi bi-lightning-charge-fill fs-1' style='color:yellow;'></i>
                            <h4 class='text-uppercase text_params'>PWD</h4>
                            <div class='d-flex justify-content-center align-content-center'>
                                <p class='value-icon' id='pwd_icon_{$val->telemetria_id}'><i class='bi bi-arrows me-2 align-items-center mb-1 text-primary value-icon'></i></p>
                                <label for='pwd' class='fw-bold value_params value_icon' id='pwd_{$val->telemetria_id}'>{$pwd_pr} %</label>
                            </div>
                        </div>
                    </div>
                    <div class='col-6 col-lg-3 border-top border-bottom hide-content' hidden>
                        <div class='text-center '>
                        <svg version='1.0' xmlns='http://www.w3.org/2000/svg' width='60' height='60' class='text-secondary' viewBox='0 0 262.000000 262.000000' preserveAspectRatio='xMidYMid meet'>
                            <g transform='translate(0.000000,262.000000) scale(0.100000,-0.100000)'
                            fill='#000000' stroke='none'>
                            <path d='M1150 2180 c-187 -28 -376 -144 -496 -303 -78 -104 -133 -234 -106 -251 13 -8 26 10 47 68 21 55 79 148 132 212 78 92 226 184 358 220 153 41 386 23 431 -34 19 -23 25 -25 50 -17 19 7 30 19 32 33 5 36 -15 52 -67 51 -25
                            -1 -59 4 -75 10 -38 15 -235 22 -306 11z'/>
                            <path d='M1690 1984 c-6 -14 -10 -32 -10 -40 0 -27 -19 -51 -49 -64 -28 -11 -33 -10 -71 20 l-42 33 -58 -58 -58 -57 30 -44 c27 -41 28 -46 17 -79 -11 -30
                            -19 -35 -63 -45 -28 -6 -54 -16 -58 -23 -10 -16 -10 -118 0 -134 4 -7 30 -17 58 -23 45 -10 52 -15 63 -46 12 -33 11 -38 -13 -73 -14 -20 -26 -41 -26 -47 0
                            -5 24 -33 53 -62 l53 -53 43 32 44 31 35 -19 c31 -16 38 -26 47 -68 l10 -50
                            74 0 c81 0 86 3 96 68 5 31 12 40 41 52 35 14 37 14 80 -16 l44 -30 58 55 57
                            55 -32 45 c-31 44 -32 47 -18 80 15 36 23 40 88 52 l38 6 -3 76 c-3 82 0 79
                            -80 96 -20 5 -32 17 -43 42 -15 35 -14 37 15 74 17 21 30 42 30 47 0 13 -94
                            103 -108 103 -7 0 -30 -11 -51 -25 -36 -24 -39 -25 -73 -11 -38 16 -48 32 -48
                            75 0 42 -17 51 -92 51 -64 0 -69 -2 -78 -26z m130 -33 c5 -11 10 -31 10 -45 0
                            -28 22 -46 82 -67 34 -12 40 -11 76 14 l39 26 32 -33 31 -32 -25 -36 c-30 -45
                            -30 -41 -4 -104 20 -48 24 -52 68 -60 l46 -9 0 -44 0 -45 -48 -10 c-51 -11
                            -55 -17 -84 -109 -3 -10 6 -32 22 -53 l27 -36 -28 -29 c-34 -35 -40 -36 -83
                            -6 l-34 23 -56 -22 c-43 -18 -57 -28 -59 -46 -8 -68 -9 -69 -60 -66 -38 2 -47
                            7 -50 23 -9 60 -13 66 -68 90 l-56 25 -32 -25 c-41 -32 -46 -31 -80 4 l-28 29
                            26 40 25 41 -24 54 c-25 57 -38 67 -86 67 -28 0 -29 1 -29 49 l0 50 46 7 c44
                            6 46 9 70 62 l24 55 -25 37 c-32 46 -31 53 6 84 l30 26 35 -27 35 -26 52 18
                            c59 20 77 38 77 75 0 35 17 50 57 50 22 0 36 -6 43 -19z'/>
                            <path d='M1685 1708 c-78 -45 -107 -154 -62 -233 33 -58 88 -87 160 -82 98 6
                            157 69 157 167 0 98 -59 161 -157 167 -47 3 -66 -1 -98 -19z m177 -54 c58 -57
                            58 -132 -1 -188 -80 -77 -221 -17 -221 92 0 62 33 105 97 127 45 16 86 5 125
                            -31z'/>
                            <path d='M834 1612 c-6 -4 -15 -34 -20 -66 -9 -59 -9 -60 -56 -79 l-48 -19
                            -48 31 c-27 17 -55 31 -62 31 -14 0 -106 -85 -121 -113 -8 -14 -2 -29 27 -68
                            l36 -50 -19 -47 c-19 -46 -21 -47 -84 -62 l-64 -16 -3 -85 c-3 -100 0 -105 77
                            -116 51 -8 53 -10 73 -56 l21 -48 -38 -55 -38 -55 66 -67 c52 -51 70 -65 82
                            -57 7 6 31 22 52 37 l39 28 49 -19 c47 -18 50 -22 58 -62 17 -98 7 -89 105
                            -89 98 0 112 7 112 56 0 57 15 79 64 98 l46 18 49 -36 c27 -20 53 -36 58 -36
                            6 0 38 29 72 65 l63 65 -36 50 c-20 28 -36 54 -36 59 0 5 9 29 19 54 18 42 21
                            44 71 50 77 9 81 16 78 116 l-3 86 -67 14 c-38 8 -68 18 -68 23 0 5 -8 26 -19
                            48 l-18 39 27 33 c56 68 56 68 -13 136 -35 34 -68 62 -75 62 -8 0 -33 -13 -57
                            -30 -51 -35 -48 -35 -102 -13 -43 18 -53 38 -53 104 0 18 -7 32 -19 39 -22 11
                            -160 14 -177 2z m161 -89 c4 -27 8 -53 10 -59 1 -5 32 -23 69 -38 l65 -28 53
                            36 53 35 38 -36 c20 -21 37 -45 37 -54 0 -9 -13 -33 -30 -54 -35 -45 -35 -43
                            -11 -97 11 -24 22 -51 25 -61 6 -20 14 -23 79 -32 l47 -6 0 -64 c0 -70 10 -62
                            -88 -80 -24 -5 -33 -16 -56 -75 l-27 -69 31 -42 c16 -23 30 -47 30 -53 0 -7
                            -18 -29 -40 -51 l-39 -38 -51 35 -51 36 -57 -25 c-78 -35 -79 -36 -87 -98 l-6
                            -55 -64 0 c-63 0 -63 0 -69 30 -3 17 -9 44 -12 61 -5 24 -14 34 -42 45 -21 8
                            -50 22 -65 30 -27 15 -29 14 -79 -21 l-51 -37 -43 44 -44 43 35 52 c33 49 34
                            53 20 80 -8 15 -21 45 -29 65 -13 34 -18 37 -75 49 l-61 12 0 58 c0 32 4 61
                            10 64 5 3 33 9 61 12 48 6 52 9 65 42 8 20 21 49 30 65 15 28 14 31 -22 83
                            l-37 54 44 45 45 45 46 -36 c26 -19 50 -35 55 -35 4 0 36 12 69 26 l62 26 11
                            59 12 59 64 0 64 0 6 -47z'/>
                            <path d='M870 1281 c-175 -53 -228 -256 -99 -378 185 -175 464 41 348 269 -42
                            83 -162 135 -249 109z m138 -55 c58 -30 102 -104 102 -168 0 -18 -13 -55 -28
                            -83 -45 -83 -141 -116 -229 -80 -48 20 -71 41 -94 87 -38 75 -20 158 50 221
                            39 35 45 37 107 37 36 0 77 -6 92 -14z'/>
                            <path d='M2215 1088 c-2 -7 -13 -47 -26 -88 -29 -98 -60 -159 -126 -247 -86
                            -115 -243 -219 -389 -258 -132 -36 -372 -27 -394 15 -22 41 -90 22 -90 -24 0
                            -36 25 -52 62 -38 22 8 37 8 56 0 16 -7 90 -11 177 -11 144 0 154 1 240 32
                            211 76 354 201 450 391 40 80 79 205 71 227 -7 16 -25 17 -31 1z'/>
                            </g>
                            </svg>
                            <h4 class='text-uppercase text_params'>C. Mode</h4>
                            <div class='d-flex justify-content-center align-content-center'>
                                <p class='value-icon' id='c_mode_icon_{$val->telemetria_id}'><i class='bi bi-arrows me-2 align-items-center mb-1 text-primary value-icon'></i></p>
                                <label for='c_mode' class='fw-bold value_params value_icon' id='c_mode_{$val->telemetria_id}'>{$val->controlling_mode}</label>
                            </div>
                        </div>
                    </div>
                    <div class='col-6 col-lg-3 border-top border-bottom hide-content' hidden>
                        <div class='text-center '>
                            <i class='bi bi-eyedropper fs-1'></i>
                            <h4 class='text-uppercase text_params'>USDA 1</h4>
                            <div class='d-flex justify-content-center align-content-center'>
                                <p class='value-icon' id='usda_1_icon_{$val->telemetria_id}'><i class='bi bi-arrows me-2 align-items-center mb-1 text-primary value-icon'></i></p>
                                <label for='usda_1' class='fw-bold value_params value_icon' id='usda_1_{$val->telemetria_id}'>{$cargo_1_temp_f} F°</label>
                            </div>
                        </div>
                    </div>




                    <div class='col-6 col-lg-3 border-top border-bottom hide-content' hidden>
                        <div class='text-center'>
                            <i class='bi bi-eyedropper fs-1'></i>
                            <h4 class='text-uppercase text_params'>USDA 4</h4>
                            <div class='d-flex justify-content-center align-content-center'>
                                <p class='value-icon' id='usda_4_icon_{$val->telemetria_id}'><i class='bi bi-arrows me-2 align-items-center mb-1 text-primary value-icon'></i></p>
                                <label for='usda_4' class='fw-bold value_params value_icon' id='usda_4_{$val->telemetria_id}'>{$cargo_4_temp_f} F°</label>
                            </div>
                        </div>
                    </div>
                    <div class='col-6 col-lg-3'>
                        <div class='text-center' id='change-button'>
                            <button type='button' class='btn btn-primary btn-sm view-more'>View More</button>
                        </div>
                    </div>
                    ";
        $result = array(
            'text'=>$text,
            //'latitud'=>$val->latitud,
            //'longitud'=>$val->longitud,
            //'nombre_contenedor'=> $val->nombre_contenedor,
        );
        return $result;
    }

    /*
    //parte de los usdas 2 y 3

                        <div class='col-6 col-lg-3 border-top border-bottom hide-content' hidden>
                        <div class='text-center'>
                            <i class='bi bi-eyedropper fs-1'></i>
                            <h4 class='text-uppercase text_params'>USDA 2</h4>
                            <div class='d-flex justify-content-center align-content-center'>
                                <p class='value-icon' id='usda_2_icon_{$val->telemetria_id}'><i class='bi bi-arrows me-2 align-items-center mb-1 text-primary value-icon'></i></p>
                                <label for='usda_2' class='fw-bold value_params value_icon' id='usda_2_{$val->telemetria_id}'>{$cargo_2_temp_f} F°</label>
                            </div>
                        </div>
                    </div>
                                        <div class='col-6 col-lg-3 border-top border-bottom hide-content' hidden>
                        <div class='text-center '>
                            <i class='bi bi-eyedropper fs-1'></i>
                            <h4 class='text-uppercase text_params'>USDA 3</h4>
                            <div class='d-flex justify-content-center align-content-center'>
                                <p class='value-icon' id='usda_3_icon_{$val->telemetria_id}'><i class='bi bi-arrows me-2 align-items-center mb-1 text-primary value-icon'></i></p>
                                <label for='usda_3' class='fw-bold value_params value_icon' id='usda_3_{$val->telemetria_id}'>{$cargo_3_temp_f} F°</label>
                            </div>
                        </div>
                    </div>


    */


/**
 * Función principal para generar la interfaz de dispositivo con grupos de datos
 * @param object $val - Objeto con toda la información del dispositivo
 * @return array - Array con el HTML generado
 */
function ContenedorGruposEspeciales($val) {
    // Información básica del dispositivo
    $imei = $val->imei ?? 'N/A';
    $device = $val->device ?? 'UNKNOWN';
    $fecha = isset($val->fecha) ? formatearFecha_Plus($val->fecha) : 'Sin fecha';
    $power_state = $val->power_state ?? 0;
    
    // Estado de energía
    $power_icon = $power_state == 1 ? "text-success" : "text-danger";
    $power_text = $power_state == 1 ? "ON" : "OFF";
    
    // Iniciar construcción del HTML
    $html = "
    <div class='container-fluid p-3'>
        <!-- HEADER PRINCIPAL -->
        <div class='row mb-4'>
            <div class='col-12'>
                <div class='card border-primary'>
                    <div class='card-header bg-primary text-white'>
                        <div class='d-flex justify-content-between align-items-center'>
                            <div>
                                <h3 class='mb-0'><i class='bi bi-cpu'></i> {$device}</h3>
                                <small>IMEI: {$imei}</small>
                            </div>
                            <div class='text-end'>
                                <h5 class='mb-0'><i class='bi bi-power fs-4 {$power_icon}'></i> POWER {$power_text}</h5>
                                <small>{$fecha}</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- DATOS PRINCIPALES -->
        <div class='row mb-4'>
            <div class='col-12'>
                <div class='card'>
                    <div class='card-body'>
                        <h5 class='card-title'><i class='bi bi-thermometer-half'></i> Parámetros Principales</h5>
                        <div class='row g-3'>
                            " . generarParametrosPrincipales($val) . "
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- GRUPOS DE DATOS ESPECIALES -->
        <div class='row g-3'>";
    
    // Generar tarjetas para cada grupo si existe
    if (isset($val->atmosfera_controlada)) {
        $html .= generarTarjetaAtmosferaControlada($val->atmosfera_controlada);
    }
    
    if (isset($val->madurador)) {
        $html .= generarTarjetaMadurador($val->madurador);
    }
    
    if (isset($val->starcool_cerro_prieto)) {
        $html .= generarTarjetaStarcool($val->starcool_cerro_prieto);
    }
    
    if (isset($val->inyectores)) {
        $html .= generarTarjetaInyectores($val->inyectores);
    }
    
    $html .= "
        </div>
    </div>";
    
    return array('text2' => $html);
}

/**
 * Genera los parámetros principales del dispositivo
 */
function generarParametrosPrincipales($val) {
    $parametros = [
        ['icon' => 'bi-thermometer', 'label' => 'Set Point', 'value' => formatearTemperatura($val->set_point ?? null), 'color' => 'primary'],
        ['icon' => 'bi-wind', 'label' => 'Supply Air', 'value' => formatearTemperatura($val->temp_supply_1 ?? null), 'color' => 'info'],
        ['icon' => 'bi-arrow-return-left', 'label' => 'Return Air', 'value' => formatearTemperatura($val->return_air ?? null), 'color' => 'warning'],
        ['icon' => 'bi-sun', 'label' => 'Ambient', 'value' => formatearTemperatura($val->ambient_air ?? null), 'color' => 'success'],
        ['icon' => 'bi-lightning-charge', 'label' => 'Power KWH', 'value' => formatearNumero($val->power_kwh ?? null, ' kWh'), 'color' => 'danger'],
        ['icon' => 'bi-battery', 'label' => 'Battery', 'value' => formatearNumero($val->battery_voltage ?? null, ' V'), 'color' => 'secondary']
    ];
    
    $html = '';
    foreach ($parametros as $param) {
        $html .= "
        <div class='col-md-4 col-lg-2'>
            <div class='text-center p-3 border rounded bg-light'>
                <i class='{$param['icon']} fs-3 text-{$param['color']}'></i>
                <p class='mb-1 small text-muted'>{$param['label']}</p>
                <h5 class='mb-0'>{$param['value']}</h5>
            </div>
        </div>";
    }
    
    return $html;
}

/**
 * Genera tarjeta para Atmósfera Controlada
 */
function generarTarjetaAtmosferaControlada($datos) {
    if (!$datos) return '';
    
    $html = "
    <div class='col-lg-6'>
        <div class='card h-100 border-info'>
            <div class='card-header bg-info text-white'>
                <h5 class='mb-0'><i class='bi bi-cloud'></i> Atmósfera Controlada</h5>
            </div>
            <div class='card-body'>
                <div class='row g-2'>";
    
    // Mapeo de campos AC con nombres descriptivos
    $camposAC = [
        'ac_1' => ['label' => 'Power', 'unit' => '', 'icon' => 'bi-toggles'],
        'ac_2' => ['label' => 'Set Point', 'unit' => 'C°', 'icon' => 'bi-cpu'],
        'ac_3' => ['label' => 'Suministro', 'unit' => 'C°', 'icon' => 'bi-cpu-fill'],
        'ac_4' => ['label' => 'Retorno', 'unit' => 'C°', 'icon' => 'bi-wind'],
        'ac_5' => ['label' => 'Evaporador', 'unit' => 'C°', 'icon' => 'bi-cloud-haze2'],
        'ac_6' => ['label' => 'Condensador', 'unit' => 'C°', 'icon' => 'bi-thermometer-half'],
        'ac_7' => ['label' => 'Sensor 1', 'unit' => 'C°', 'icon' => 'bi-droplet-half'],
        'ac_8' => ['label' => 'Sensor 2', 'unit' => 'C°', 'icon' => 'bi-speedometer2'],
        'ac_9' => ['label' => 'Sensor 3', 'unit' => 'C°', 'icon' => 'bi-flower1'],
        'ac_10' => ['label' => 'Sensor 4', 'unit' => 'C°', 'icon' => 'bi-cloud-fog2'],
        'ac_11' => ['label' => 'Humedad', 'unit' => '%', 'icon' => 'bi-droplet'],
        'ac_12' => ['label' => 'Ventilacion', 'unit' => 'CFM', 'icon' => 'bi-fire'],
        'ac_13' => ['label' => 'CO2 Sensor', 'unit' => '%', 'icon' => 'bi-dash-circle'],
        'ac_14' => ['label' => 'O2 Sensor', 'unit' => '°%', 'icon' => 'bi-bullseye'],
        'ac_15' => ['label' => 'SP Humedad ', 'unit' => '%', 'icon' => 'bi-activity'],
        'ac_16' => ['label' => 'SP CO2', 'unit' => '%', 'icon' => 'bi-bell-fill']
    ];
    
    foreach ($camposAC as $campo => $info) {
        if (property_exists($datos, $campo)) {
            $valor = $datos->$campo;
            $valorFormateado = formatearValorAC($valor, $info['unit']);
            $colorValor = obtenerColorValor($valor, $campo);
            
            $html .= "
            <div class='col-6 col-md-4'>
                <div class='d-flex align-items-center p-2 border rounded'>
                    <i class='{$info['icon']} fs-5 text-info me-2'></i>
                    <div>
                        <small class='text-muted d-block'>{$info['label']}</small>
                        <span class='fw-bold {$colorValor}'>{$valorFormateado}</span>
                    </div>
                </div>
            </div>";
        }
    }
    
    $html .= "
                </div>
            </div>
        </div>
    </div>";
    
    return $html;
}

/**
 * Genera tarjeta para Madurador
 */
function generarTarjetaMadurador($datos) {
    if (!$datos) return '';
    
    $html = "
    <div class='col-lg-6'>
        <div class='card h-100 border-success'>
            <div class='card-header bg-success text-white'>
                <h5 class='mb-0'><i class='bi bi-box-seam'></i> Sistema Madurador</h5>
            </div>
            <div class='card-body'>
                <div class='row g-2'>";
    
    // Campos del madurador
    $camposMad = [
        'mad_1' => ['label' => 'Etileno', 'icon' => 'bi-1-circle'],
        'mad_2' => ['label' => 'SP Etileno', 'icon' => 'bi-2-circle'],
        'mad_3' => ['label' => 'Tiempo Programado', 'icon' => 'bi-3-circle'],
        'mad_4' => ['label' => 'Hora', 'icon' => 'bi-check-circle'],
        'mad_5' => ['label' => 'Minuto', 'icon' => 'bi-check-circle'],
        'mad_6' => ['label' => 'Segundo', 'icon' => 'bi-check-circle']
    ];
    
    foreach ($camposMad as $campo => $info) {
        if (property_exists($datos, $campo)) {
            $valor = $datos->$campo;
            $estado = $valor > 0 ? 'ACTIVO' : 'INACTIVO';
            $colorEstado = $valor > 0 ? 'text-success' : 'text-secondary';
            $bgColor = $valor > 0 ? 'bg-light-success' : '';
            
            $html .= "
            <div class='col-6 col-md-4'>
                <div class='d-flex align-items-center p-2 border rounded {$bgColor}'>
                    <i class='{$info['icon']} fs-5 {$colorEstado} me-2'></i>
                    <div>
                        <small class='text-muted d-block'>{$info['label']}</small>
                        <span class='fw-bold {$colorEstado}'>{$estado}</span>
                    </div>
                </div>
            </div>";
        }
    }
    
    $html .= "
                </div>
            </div>
        </div>
    </div>";
    
    return $html;
}

/**
 * Genera tarjeta para Starcool Cerro Prieto
 */
function generarTarjetaStarcool($datos) {
    if (!$datos) return '';
    
    $html = "
    <div class='col-lg-6'>
        <div class='card h-100 border-warning'>
            <div class='card-header bg-warning text-dark'>
                <h5 class='mb-0'><i class='bi bi-stars'></i> Starcool Cerro Prieto</h5>
            </div>
            <div class='card-body'>
                <div class='row g-2'>";
    
    // Campos de Starcool
    $camposSt = [
        'st_1' => ['label' => 'CO2 Sensor 1', 'unit' => '%', 'icon' =>'bi-speedometer2'],
        'st_2' => ['label' => 'CO2 Sensor 2', 'unit' => '%', 'icon' => 'bi-speedometer2'],
        'st_3' => ['label' => 'CO2 Sensor 3', 'unit' => '%', 'icon' =>'bi-speedometer2'],
        'st_4' => ['label' => 'O2 Sensor 1', 'unit' => '%', 'icon' => 'bi-arrows-move'],
        'st_5' => ['label' => 'O2 Sensor 2', 'unit' => '%', 'icon' => 'bi-arrows-move'],
        'st_6' => ['label' => 'O2 Sensor 3', 'unit' => '%', 'icon' => 'bi-arrows-move'],
        'st_7' => ['label' => 'Humedad Sensor 1', 'unit' => '%', 'icon' => 'bi-graph-up'],
        'st_8' => ['label' => 'Humedad Sensor 2', 'unit' => '%', 'icon' => 'bi-graph-up'],
        'st_9' => ['label' => 'Humedad Sensor 3', 'unit' => '%', 'icon' => 'bi-graph-up'],
        'st_10' => ['label' => 'Rele 1', 'unit' => '', 'icon' => 'bi-toggle-on'],
        'st_11' => ['label' => 'Rele 2', 'unit' => '', 'icon' => 'bi-toggle-on'],
        'st_12' => ['label' => 'Rele 3', 'unit' => '', 'icon' => 'bi-toggle-on'],
        'st_13' => ['label' => 'Rele 4', 'unit' => '', 'icon' => 'bi-toggle-on'],
        'st_14' => ['label' => 'Rele 5', 'unit' => '', 'icon' => 'bi-toggle-on'],

        
        'st_15' => ['label' => 'Rele 6', 'unit' => '', 'icon' => 'bi-toggle-on']
    ];
    
    foreach ($camposSt as $campo => $info) {
        if (property_exists($datos, $campo)) {
            $valor = $datos->$campo;
            $valorFormateado = formatearValorSt($valor, $info['unit'], $campo);
            $colorValor = obtenerColorValorSt($valor, $campo);
            
            $html .= "
            <div class='col-6 col-md-4'>
                <div class='d-flex align-items-center p-2 border rounded'>
                    <i class='{$info['icon']} fs-5 text-warning me-2'></i>
                    <div>
                        <small class='text-muted d-block'>{$info['label']}</small>
                        <span class='fw-bold {$colorValor}'>{$valorFormateado}</span>
                    </div>
                </div>
            </div>";
        }
    }
    
    $html .= "
                </div>
            </div>
        </div>
    </div>";
    
    return $html;
}

/**
 * Genera tarjeta para Inyectores
 */
function generarTarjetaInyectores($datos) {
    // Si inyectores está vacío o no existe
    if (!$datos || (is_array($datos) && empty($datos))) {
        $html = "
        <div class='col-lg-6'>
            <div class='card h-100 border-secondary'>
                <div class='card-header bg-secondary text-white'>
                    <h5 class='mb-0'><i class='bi bi-moisture'></i> Sistema de Inyectores</h5>
                </div>
                <div class='card-body'>
                    <div class='alert alert-info mb-0'>
                        <i class='bi bi-info-circle me-2'></i>
                        <span>No hay inyectores configurados en este momento</span>
                    </div>
                </div>
            </div>
        </div>";
        return $html;
    }
    
    // Si hay datos de inyectores
    $html = "
    <div class='col-lg-6'>
        <div class='card h-100 border-danger'>
            <div class='card-header bg-danger text-white'>
                <h5 class='mb-0'><i class='bi bi-moisture'></i> Sistema de Inyectores</h5>
            </div>
            <div class='card-body'>
                <div class='row g-2'>";
    
    // Si es un array de inyectores
    if (is_array($datos)) {
        foreach ($datos as $index => $inyector) {
            $numero = $index + 1;
            $estado = isset($inyector->estado) && $inyector->estado == 1 ? 'ACTIVO' : 'INACTIVO';
            $colorEstado = isset($inyector->estado) && $inyector->estado == 1 ? 'text-success' : 'text-secondary';
            $presion = isset($inyector->presion) ? $inyector->presion . ' bar' : 'N/A';
            
            $html .= "
            <div class='col-6'>
                <div class='border rounded p-2'>
                    <h6 class='mb-2'>Inyector {$numero}</h6>
                    <small class='d-block {$colorEstado}'><i class='bi bi-circle-fill me-1'></i> {$estado}</small>
                    <small class='d-block'>Presión: {$presion}</small>
                </div>
            </div>";
        }
    }
    
    $html .= "
                </div>
            </div>
        </div>
    </div>";
    
    return $html;
}

/**
 * FUNCIONES AUXILIARES
 */

function formatearFecha_Plus($fecha) {
    if (!$fecha) return 'Sin fecha';
    try {
        $dt = new DateTime($fecha);
        return $dt->format('d/m/Y H:i:s');
    } catch (Exception $e) {
        return $fecha;
    }
    
}

function formatearTemperatura($valor) {
    if ($valor === null || $valor === 'E01' || $valor === 'E00') {
        return 'N/A';
    }
    return number_format((float)$valor, 1) . '°C';
}

function formatearNumero($valor, $unidad = '') {
    if ($valor === null || $valor === 'E01' || $valor === 'E00') {
        return 'N/A';
    }
    return number_format((float)$valor, 1) . $unidad;
}

function formatearValorAC($valor, $unidad) {
    if ($valor === null || $valor === -1) {
        return 'N/A';
    }
    if ($unidad === '') {
        return $valor == 1 ? 'ON' : 'OFF';
    }
    return number_format((float)$valor, 1) . ' ' . $unidad;
}

function formatearValorSt($valor, $unidad, $campo) {
    if ($valor === null) {
        return 'N/A';
    }
    
    // Para campos de estado (st_10, st_11)
    if (strpos($campo, 'st_7') !== false || strpos($campo, 'st_8') !== false || strpos($campo, 'st_9') !== false) {
        return $valor != 0 ? $valor : 'INACTIVO';
    }
    
    // Para campos de compresores y evaporadores
    if (strpos($campo, 'st_10') !== false || strpos($campo, 'st_11') !== false || strpos($campo, 'st_12') !== false ||
    strpos($campo, 'st_13') !== false || strpos($campo, 'st_14') !== false || strpos($campo, 'st_15') !== false ) {
        return $valor > 0 ? 'ON' : 'OFF';
    }
    
    if ($unidad === '') {
        return $valor;
    }
    
    return number_format((float)$valor, 1) . ' ' . $unidad;
}

function obtenerColorValor($valor, $campo) {
    // Lógica para determinar el color según el valor
    if ($valor === null || $valor === -1) {
        return 'text-muted';
    }
    
    // Para campos de O2 y CO2
    if (strpos($campo, 'ac_4') !== false || strpos($campo, 'ac_5') !== false) {
        if ($valor < 0) return 'text-danger';
        if ($valor > 20) return 'text-warning';
        return 'text-success';
    }
    
    // Para temperatura
    if (strpos($campo, 'ac_6') !== false) {
        if ($valor < 10 || $valor > 40) return 'text-danger';
        if ($valor < 15 || $valor > 30) return 'text-warning';
        return 'text-success';
    }
    
    // Para humidity
    if (strpos($campo, 'ac_11') !== false) {
        if ($valor < 30 || $valor > 90) return 'text-danger';
        if ($valor < 40 || $valor > 80) return 'text-warning';
        return 'text-success';
    }
    
    return 'text-dark';
}

function obtenerColorValorSt($valor, $campo) {
    if ($valor === null) {
        return 'text-muted';
    }
    
    // Para campos de estado
    if (strpos($campo, 'st_10') !== false || strpos($campo, 'st_11') !== false) {
        return $valor == 1 ? 'text-success' : 'text-secondary';
    }
    
    // Para temperatura
    if (strpos($campo, 'st_4') !== false) {
        if ($valor < 10 || $valor > 30) return 'text-danger';
        return 'text-success';
    }
    
    // Para eficiencia
    if (strpos($campo, 'st_7') !== false) {
        if ($valor < 50) return 'text-danger';
        if ($valor < 80) return 'text-warning';
        return 'text-success';
    }
    
    return 'text-dark';
}

// CSS adicional necesario (agregar en tu archivo de estilos)




