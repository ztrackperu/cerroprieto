<?php
class GraphModel extends Query{
    public function __construct()
    {
        parent::__construct();
    }
    //ListaDispositivoEmpresa
    
    public function ListaDispositivoEmpresa($id)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, urlapiMysql."/contenedores/ListaDispositivoEmpresa/".$id);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $res = curl_exec($ch);
        curl_close($ch);   
        return $res;
    }
    //ContenedorData
    public function ContenedorData($id)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, urlapiMysql."/contenedores/ContenedorData/".$id);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $res = curl_exec($ch);
        curl_close($ch);   
        return $res;
    }
    //DatosGraficaTabla
    public function DatosGraficaTabla($data)
    {
        $ch = curl_init();
        $data =json_encode($data);
        curl_setopt($ch, CURLOPT_URL, urlapiMongo."/maduradores/DatosGraficaTablaF/");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $res = curl_exec($ch);
        curl_close($ch);   
        return $res;
    }

}

?>
