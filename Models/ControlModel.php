<?php
class ControlModel extends Query{
    public function __construct(){
        parent::__construct();
    }

    public function ListaDispositivoEmpresa($id)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, urlapiMysql."/contenedores/ListaDispositivoEmpresa/".$id);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $res = curl_exec($ch);
        curl_close($ch);   
        return $res;
    }
    public function VerificarLive($data)
    {
        $ch = curl_init();
        $data =json_encode($data);
        curl_setopt($ch, CURLOPT_URL, urlapiMysql."/contenedores/VerificarLive/");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $res = curl_exec($ch);
        curl_close($ch);   
        return $res;
    }
    public function EnvioComando($data)
    {
        $ch = curl_init();
        $data =json_encode($data);
        curl_setopt($ch, CURLOPT_URL, "http://161.132.206.104:9050/Comandos/");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $res = curl_exec($ch);
        curl_close($ch);   
        return $res;
    }
    //EnvioComando_libre
    public function EnvioComando_libre($data)
    {
        $ch = curl_init();
        $data =json_encode($data);
        curl_setopt($ch, CURLOPT_URL, "http://161.132.206.104:9050/Comandos/libre/");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $res = curl_exec($ch);
        curl_close($ch);   
        return $res;
    }
    public function ComandosTest($data){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, urlapiMongo2."/Comandos/buscar/testapi/".$data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $res = curl_exec($ch);
        curl_close($ch);   
        return $res;
    }
    public function ComandosOficial($data){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, urlapiMongo2."/Comandos/buscar/oficial/".$data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $res = curl_exec($ch);
        curl_close($ch);   
        return $res;
    }
}



?>